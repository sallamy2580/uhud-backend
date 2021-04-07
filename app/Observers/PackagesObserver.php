<?php

namespace App\Observers;

use App\Models\Notification;
use App\Models\Package;
use Illuminate\Support\Facades\Auth;

class PackagesObserver
{
    /**
     * Handle the packages "created" event.
     *
     * @param  \App\Models\Package  $packages
     * @return void
     */
    public function created(Package $packages)
    {
        $authUser = null;
        if (Auth::check())
            $authUser = Auth::user();

        $notificationField = [
            'notified_by' => (!empty($authUser)) ? $authUser->id : null,
            'owner_id' => $packages->creator->id,
            'seen' => 0,
            'status' => 1,
            'type' => 'package_created',
            'extera' => "the package with name `".$packages->name."` and price `".$packages->price."` created by `".$packages->creator->full_name."`",
            'description' => "dear `" . $packages->creator->full_name . "` your package created successfully.",
            'link' => url('/')
        ];
        Notification::create($notificationField);
        unset($authUser, $notificationField, $user);
    }

    /**
     * Handle the packages "updated" event.
     *
     * @param  \App\Models\Package  $packages
     * @return void
     */
    public function updated(Package $packages)
    {
        $authUser = null;
        if (Auth::check())
            $authUser = Auth::user();

        $adminName = (!empty($authUser)) ? $authUser->full_name : '';
        $notificationField = [
            'notified_by' => (!empty($authUser)) ? $authUser->id : null,
            'owner_id' => $packages->creator->id,
            'seen' => 0,
            'status' => 1,
            'type' => 'package_updated',
            'extera' => "the package with name `".$packages->name."` and price `".$packages->price."` updated by `".$adminName."`",
            'description' => "dear `" . $packages->creator->full_name . "` your package updated by `".$adminName."`",
            'link' => url('/')
        ];
        Notification::create($notificationField);
        unset($authUser, $notificationField, $user);
    }

    /**
     * Handle the packages "deleted" event.
     *
     * @param  \App\Models\Package  $packages
     * @return void
     */
    public function deleted(Package $packages)
    {
        $authUser = null;
        if (Auth::check())
            $authUser = Auth::user();

        $adminName = (!empty($authUser)) ? $authUser->full_name : '';
        $notificationField = [
            'notified_by' => (!empty($authUser)) ? $authUser->id : null,
            'owner_id' => $packages->creator->id,
            'seen' => 0,
            'status' => 1,
            'type' => 'package_deleted',
            'extera' => "the package with name `".$packages->name."` and price `".$packages->price."` deleted by `".$adminName."`",
            'description' => "dear `" . $packages->creator->full_name . "` your package deleted by `".$adminName."`",
            'link' => url('/')
        ];
        Notification::create($notificationField);
        unset($authUser, $notificationField, $user);
    }

    /**
     * Handle the packages "restored" event.
     *
     * @param  \App\Models\Package  $packages
     * @return void
     */
    public function restored(Package $packages)
    {
        //
    }

    /**
     * Handle the packages "force deleted" event.
     *
     * @param  \App\Models\Package  $packages
     * @return void
     */
    public function forceDeleted(Package $packages)
    {
        //
    }
}
