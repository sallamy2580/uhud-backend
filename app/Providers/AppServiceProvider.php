<?php

namespace App\Providers;

use App\Models\Airline;
use App\Models\Booking;
use App\Models\Contact;
use App\Models\Package;
use App\Models\Ticket;
use App\Models\TicketSection;
use App\Models\User;
use App\Observers\AirlineObserver;
use App\Observers\BookingObserver;
use App\Observers\ContactObserver;
use App\Observers\PackagesObserver;
use App\Observers\TicketObserver;
use App\Observers\TicketSectionObserver;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Repositories\GlobalActions\GlobalActionsInterface', 'App\Repositories\GlobalActions\GlobalActions');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        Validator::extend('image_base64', function ($attribute, $value, $parameters, $validator) {
            if( empty($value) ) return true;
            $imageExplode = explode(';',$value);
            if( !(isset($imageExplode[0]) && !empty($imageExplode[0])) ||
                !(isset($imageExplode[1]) && !empty($imageExplode[1]))  ){
                return false;
            }
            $encodeFirstStep = explode(':',$imageExplode[0]);
            if( !(isset($encodeFirstStep[0]) && !empty($encodeFirstStep[0])) ||
                !(isset($encodeFirstStep[1]) && !empty($encodeFirstStep[1]))  ){
                return false;
            }
            unset($value,$imageExplode,$attribute,$parameters,$validator);
            return ( $encodeFirstStep[1] == 'image/png' || $encodeFirstStep[1] == 'image/svg' ||
                $encodeFirstStep[1] == 'image/jpeg' || $encodeFirstStep[1] == 'image/gif' ||
                $encodeFirstStep[1] == 'image/jpg' );
        });

        /*define modal observers*/
        User::observe(UserObserver::class);
        Ticket::observe(TicketObserver::class);
        TicketSection::observe(TicketSectionObserver::class);
        Package::observe(PackagesObserver::class);
        Booking::observe(BookingObserver::class);
        Contact::observe(ContactObserver::class);
        Airline::observe(AirlineObserver::class);
    }
}
