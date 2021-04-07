<?php

namespace App\Observers;

use App\Models\Booking;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class BookingObserver
{
    /**
     * Handle the booking "created" event.
     *
     * @param  \App\Models\Booking  $booking
     * @return void
     */
    public function created(Booking $booking)
    {
        $authUser = null;
        if (Auth::check())
            $authUser = Auth::user();

        $notificationField = [
            'notified_by' => (!empty($authUser)) ? $authUser->id : null,
            'owner_id' => $booking->user_id->id,
            'seen' => 0,
            'status' => 1,
            'type' => 'booking_created',
            'extera' => "new booking created by `".$booking->user_id->full_name."`",
            'description' => "dear `" . $booking->user_id->full_name . "` your booking created successfully.",
            'link' => url('/')
        ];
        Notification::create($notificationField);
        unset($authUser, $notificationField, $user);
    }

    /**
     * Handle the booking "updated" event.
     *
     * @param  \App\Models\Booking  $booking
     * @return void
     */
    public function updated(Booking $booking)
    {
        $changes = $booking->getChanges();
        $updateFields = array_keys($changes);

        if (in_array('status', $updateFields)){
            $updateStatus = $booking->status;
            $authUser = null;
            if (Auth::check())
                $authUser = Auth::user();

            $adminId = (!empty($authUser)) ? $authUser->id : null;
            $adminName = (!empty($authUser)) ? $authUser->full_name : '';

            if (trim($updateStatus) === 'accepted') {
                $notificationField = [
                    'notified_by' => $adminId,
                    'owner_id' => $booking->user_id->id,
                    'seen' => 0,
                    'status' => 1,
                    'type' => 'booking_accepted',
                    'extera' => "booking created by `".$booking->user_id->full_name."` accepted by `".$adminName."`",
                    'description' => "dear `" . $booking->user_id->full_name . "` your booking accepted by " . $adminName,
                    'link' => url('/')
                ];
                Notification::create($notificationField);
                unset( $notificationField);
            }else if(trim($updateStatus) === 'rejected'){
                $notificationField = [
                    'notified_by' => $adminId,
                    'owner_id' => $booking->user_id->id,
                    'seen' => 0,
                    'status' => 1,
                    'type' => 'booking_rejected',
                    'extera' => "booking created by `".$booking->user_id->full_name."` rejected by `".$adminName."`",
                    'description' => "dear `" . $booking->user_id->full_name . "` your booking rejected by " . $adminName,
                    'link' => url('/')
                ];
                Notification::create($notificationField);
                unset( $notificationField);
            }
            unset($authUser, $adminId, $adminName);
        }
    }

    /**
     * Handle the booking "deleted" event.
     *
     * @param  \App\Models\Booking  $booking
     * @return void
     */
    public function deleted(Booking $booking)
    {
        $authUser = null;
        if (Auth::check())
            $authUser = Auth::user();

        $adminName = (!empty($authUser)) ? $authUser->full_name : '';
        $notificationField = [
            'notified_by' => (!empty($authUser)) ? $authUser->id : null,
            'owner_id' => $booking->user_id->id,
            'seen' => 0,
            'status' => 1,
            'type' => 'booking_deleted',
            'extera' => "the booking created by `".$booking->user_id->full_name."` deleted by `".$adminName."`",
            'description' => "dear `" . $booking->user_id->full_name . "` your booking deleted by `".$adminName."`",
            'link' => url('/')
        ];
        Notification::create($notificationField);
        unset($authUser, $notificationField, $user);
    }

    /**
     * Handle the booking "restored" event.
     *
     * @param  \App\Models\Booking  $booking
     * @return void
     */
    public function restored(Booking $booking)
    {
        //
    }

    /**
     * Handle the booking "force deleted" event.
     *
     * @param  \App\Models\Booking  $booking
     * @return void
     */
    public function forceDeleted(Booking $booking)
    {
        //
    }
}
