<?php

namespace App\Observers;

use App\Models\Notification;
use App\Models\TicketSection;
use Illuminate\Support\Facades\Auth;

class TicketSectionObserver
{
    /**
     * Handle the ticket section "created" event.
     *
     * @param  \App\Models\TicketSection  $ticketSection
     * @return void
     */
    public function created(TicketSection $ticketSection)
    {
        $authUser = null;
        if( Auth::check() )
            $authUser = Auth::user();

        $adminId = (!empty($authUser))?$authUser->id:null;
        $notificationField = [
            'notified_by'=>$adminId,
            'owner_id' => $ticketSection->user_id->id,
            'seen'=>0,
            'status'=>1,
            'type'=>'ticket_answered',
            'extera'=>"the admin with id `".$adminId."` answered the user `".$ticketSection->user_id->full_name."` ticket",
            'description'=>"dear `".$ticketSection->user_id->full_name."` your ticket answered by supporters",
            'link'=>url('/')
        ];
        Notification::create($notificationField);
        unset($authUser,$notificationField,$user);
    }

    /**
     * Handle the ticket section "updated" event.
     *
     * @param  \App\Models\TicketSection  $ticketSection
     * @return void
     */
    public function updated(TicketSection $ticketSection)
    {
        //
    }

    /**
     * Handle the ticket section "deleted" event.
     *
     * @param  \App\Models\TicketSection  $ticketSection
     * @return void
     */
    public function deleted(TicketSection $ticketSection)
    {
        //
    }

    /**
     * Handle the ticket section "restored" event.
     *
     * @param  \App\Models\TicketSection  $ticketSection
     * @return void
     */
    public function restored(TicketSection $ticketSection)
    {
        //
    }

    /**
     * Handle the ticket section "force deleted" event.
     *
     * @param  \App\Models\TicketSection  $ticketSection
     * @return void
     */
    public function forceDeleted(TicketSection $ticketSection)
    {
        //
    }
}
