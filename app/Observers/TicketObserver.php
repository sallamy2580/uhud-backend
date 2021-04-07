<?php

namespace App\Observers;

use App\Models\Notification;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TicketObserver
{
    /**
     * Handle the ticket "created" event.
     *
     * @param \App\Models\Ticket $ticket
     * @return void
     */
    public function created(Ticket $ticket)
    {
        $authUser = null;
        if (Auth::check())
            $authUser = Auth::user();

        $notificationField = [
            'notified_by' => (!empty($authUser)) ? $authUser->id : null,
            'owner_id' => $ticket->user_id->id,
            'seen' => 0,
            'status' => 1,
            'type' => 'ticket_created',
            'extera' => "ticket with subject: `" . $ticket->subject . "` created by `" . $ticket->user_id->full_name . "`",
            'description' => "dear `" . $ticket->user_id->full_name . "` your ticket created successfully, You will be contacted by supporters soon.",
            'link' => url('/')
        ];
        Notification::create($notificationField);
        unset($authUser, $notificationField, $user);
    }


    /**
     * Handle the ticket "updated" event.
     *
     * @param \App\Models\Ticket $ticket
     * @return void
     */
    public function saved(Ticket $ticket)
    {

    }

    /**
     * Handle the ticket "updated" event.
     *
     * @param \App\Models\Ticket $ticket
     * @return void
     */
    public function updated(Ticket $ticket)
    {
        $changes = $ticket->getChanges();
        $updateFields = array_keys($changes);

        if (in_array('status', $updateFields)) {
            $updateStatus = $ticket->status;
//            $original = $ticket->getOriginal();

            if (trim($updateStatus) === 'closed') {
                $authUser = null;
                if (Auth::check())
                    $authUser = Auth::user();

                $adminId = (!empty($authUser)) ? $authUser->id : null;
                $adminName = (!empty($authUser)) ? $authUser->full_name : '';

                $lastExtera = '';
                $lastDescription = '';
                if (!empty($adminName)) {
                    $lastExtera = $adminName;
                }
                if ($adminId === $ticket->user_id->id) {
                    $lastExtera = 'own';
                } else {
                    if (!empty($adminName)) {
                        $lastDescription = 'by `' . $adminName."`";
                    }
                }

                $notificationField = [
                    'notified_by' => $adminId,
                    'owner_id' => $ticket->user_id->id,
                    'seen' => 0,
                    'status' => 1,
                    'type' => 'ticket_closed',
                    'extera' => "ticket with subject: `" . $ticket->subject . "`created by `" . $ticket->user_id->full_name . "` closed by `" . $lastExtera."`",
                    'description' => "dear `" . $ticket->user_id->full_name . "` your ticket closed successfully. " . $lastDescription,
                    'link' => url('/')
                ];
                Notification::create($notificationField);
                unset($authUser, $notificationField, $user);
            }
        }
        unset($changes, $updateFields);
    }

    /**
     * Handle the ticket "deleted" event.
     *
     * @param \App\Models\Ticket $ticket
     * @return void
     */
    public function deleted(Ticket $ticket)
    {
        $authUser = null;
        if (Auth::check())
            $authUser = Auth::user();

        $adminName = (!empty($authUser)) ? $authUser->full_name : '';
        $notificationField = [
            'notified_by' => (!empty($authUser)) ? $authUser->id : null,
            'owner_id' => $ticket->user_id->id,
            'seen' => 0,
            'status' => 1,
            'type' => 'ticket_deleted',
            'extera' => "ticket with subject: `" . $ticket->subject . "` created by `" . $ticket->user_id->full_name . "` deleted by `".$adminName."`",
            'description' => "dear `" . $ticket->user_id->full_name . "` your ticket deleted by `".$adminName."`",
            'link' => url('/')
        ];
        Notification::create($notificationField);
        unset($authUser, $notificationField, $user);
    }

    /**
     * Handle the ticket "restored" event.
     *
     * @param \App\Models\Ticket $ticket
     * @return void
     */
    public function restored(Ticket $ticket)
    {
        //
    }

    /**
     * Handle the ticket "force deleted" event.
     *
     * @param \App\Models\Ticket $ticket
     * @return void
     */
    public function forceDeleted(Ticket $ticket)
    {
        //
    }
}
