<?php

namespace App\Observers;

use App\Models\Airline;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class AirlineObserver
{
    /**
     * Handle the airline "created" event.
     *
     * @param \App\Models\Airline $airline
     * @return void
     */
    public function created(Airline $airline)
    {
        $authUser = Auth::user();

        $notificationField = [
            'notified_by' => (!empty($authUser)) ? $authUser->id : null,
            'owner_id' => $airline->creator->id,
            'seen' => 0,
            'status' => 1,
            'type' => 'airline_created',
            'extera' => "new airline created by `" . $airline->creator->full_name . "`",
            'description' => "dear `" . $airline->creator->full_name . "` your booking created successfully.",
            'link' => url('/')
        ];
        Notification::create($notificationField);
        unset($authUser, $notificationField, $user);
    }

    /**
     * Handle the airline "updated" event.
     *
     * @param \App\Models\Airline $airline
     * @return void
     */
    public function updated(Airline $airline)
    {
        $authUser = Auth::user();

        $adminId = (!empty($authUser)) ? $authUser->id : null;
        $adminName = (!empty($authUser)) ? $authUser->full_name : '';

        $notificationField = [
            'notified_by' => $adminId,
            'owner_id' => $airline->creator->id,
            'seen' => 0,
            'status' => 1,
            'type' => 'airline_updated',
            'extera' => "airline created by `" . $airline->creator->full_name . "` updated by `" . $adminName . "`",
            'description' => "dear `" . $airline->creator->full_name . "` your booking updated by " . $adminName,
            'link' => url('/')
        ];
        Notification::create($notificationField);
        unset($notificationField, $airline, $adminName, $adminId);
    }

    /**
     * Handle the airline "deleted" event.
     *
     * @param \App\Models\Airline $airline
     * @return void
     */
    public function deleted(Airline $airline)
    {
        $authUser = Auth::user();

        $notificationField = [
            'notified_by' => $authUser->id,
            'owner_id' => $airline->creator->id,
            'seen' => 0,
            'status' => 1,
            'type' => 'airline_deleted',
            'extera' => "the airline created by `" . $airline->creator->full_name . "` deleted by `" . $authUser->full_name . "`",
            'description' => "dear `" . $airline->creator->full_name . "` your airline deleted by `" . $authUser->full_name . "`",
            'link' => url('/')
        ];
        Notification::create($notificationField);
        unset($authUser, $notificationField, $user);
    }
}
