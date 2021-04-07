<?php

namespace App\Observers;

use App\Mail\ForgetPasswordMail;
use App\Models\Contact;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ContactObserver
{
    /**
     * Handle the contact "created" event.
     *
     * @param  \App\Models\Contact  $contact
     * @return void
     */
    public function created(Contact $contact)
    {
        $authUser = null;
        if (Auth::check())
            $authUser = Auth::user();

        $notificationField = [
            'notified_by' => (!empty($authUser)) ? $authUser->id : null,
            'owner_id' => null,
            'seen' => 0,
            'status' => 1,
            'type' => 'contact_created',
            'extera' => "new contact created",
            'description' => "new contact created",
            'link' => url('/')
        ];
        Notification::create($notificationField);
        unset($authUser, $notificationField);
        $this->sendContactMail($contact);


    }

    /**
     * Handle the send email for contacter in contact "created" event.
     *
     * @param  \App\Models\Contact  $contact
     * @return void
     */
    public function sendContactMail($contact){
        try{
            Mail::send('emails.create-contact', ['contact' => $contact], function ($m) use ($contact) {
                $m->from('alzuhud.info@gmail.com', 'Alzuhud');
                $m->to($contact->email, $contact->name)->subject('Alzuhud - Thanks for contacting us');
            });
        }catch (\Exception $ex){}
      try{
        Mail::send('emails.send-contact-for-admin', ['contact' => $contact], function ($m) use ($contact) {
          $m->from('alzuhud.info@gmail.com', 'Alzuhud');
          $m->to('alzuhud.info@gmail.com', $contact->name)->subject('Alzuhud - '.$contact->name.' contacted with supporters');
        });
      }catch (\Exception $ex){}
    }

    /**
     * Handle the contact "updated" event.
     *
     * @param  \App\Models\Contact  $contact
     * @return void
     */
    public function updated(Contact $contact)
    {
        //
    }

    /**
     * Handle the contact "deleted" event.
     *
     * @param  \App\Models\Contact  $contact
     * @return void
     */
    public function deleted(Contact $contact)
    {
        //
    }

    /**
     * Handle the contact "restored" event.
     *
     * @param  \App\Models\Contact  $contact
     * @return void
     */
    public function restored(Contact $contact)
    {
        //
    }

    /**
     * Handle the contact "force deleted" event.
     *
     * @param  \App\Models\Contact  $contact
     * @return void
     */
    public function forceDeleted(Contact $contact)
    {
        //
    }
}
