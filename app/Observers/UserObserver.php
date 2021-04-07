<?php

namespace App\Observers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserObserver
{
    /**
     * Handle the user "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        $authUser = null;
        if( Auth::check() )
            $authUser = Auth::user();

        $notificationField = [
            'notified_by'=>(!empty($authUser))?$authUser->id:null,
            'owner_id' => $user->id,
            'seen'=>0,
            'status'=>1,
            'type'=>'user_created',
            'extera'=>"the user with name `".$user->full_name."` and email `".$user->email.
                "` created.",
            'description'=>"dear `".$user->full_name."`, <br> thank you for join to our system. We hope you have some great moments and experiences on our system. <br> Alzuhud technical team.",
            'link'=>url('/')
        ];
        Notification::create($notificationField);
        unset($notificationField,$authUser);
    }

    /**
     * Handle the user "updated" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        $changes = $user->getChanges();
        $fields = array_keys($changes);

        $authUser = null;
        if( Auth::check() )
            $authUser = Auth::user();

        $exteraString = "the user ";
        $description = "dear `".$user->full_name."` <br> your";

        foreach ($fields as $field){
            $exteraString = $exteraString."".$field." " ;
            if( trim($field) === 'updated_at'){
                $description = $description." ".$field.", ";
            }else{
                $description = $description." ".$field." and ";
            }

        }
        $exteraString = $exteraString."fields updated";
        $description = $description." updated";
        $notificationField = [
            'notified_by'=>(!empty($authUser))?$authUser->id:null,
            'owner_id' => $user->id,
            'seen'=>0,
            'status'=>1,
            'type'=>'user_updated',
            'extera'=>$exteraString,
            'description'=>$description,
            'link'=>url('/')
        ];
        Notification::create($notificationField);
        unset($description,$exteraString,$notificationField,$authUser);
    }

    /**
     * Handle the user "deleted" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function deleted(User $user)
    {

        $authUser = null;
        if( Auth::check() )
            $authUser = Auth::user();

        $notificationField = [
            'notified_by'=>(!empty($authUser))?$authUser->id:null,
            'owner_id' => null,
            'seen'=>0,
            'status'=>1,
            'type'=>'user_deleted',
            'extera'=>"the user with id:".$user->id."|name:".$user->full_name."|email:".$user->email." deleted.",
            'description'=>"one user deleted from system!",
            'link'=>url('/')
        ];
        Notification::create($notificationField);
        unset($notificationField,$authUser);
    }

    /**
     * Handle the user "restored" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function restored(User $user)
    {
        //
    }

    /**
     * Handle the user "force deleted" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }
}
