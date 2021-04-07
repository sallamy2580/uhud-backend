<?php

namespace App\Models;

use App\Repositories\GlobalActions\GlobalActions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'transactions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = [
            'user_id',
            'package_id',
            'booking_id',
            'title',
            'amount',
            'status',
            'ref'
        ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['time_ago','human_created_at'];

    /**
     * show ticket time ago
     *
     * @return string
     */
    public function getTimeAgoAttribute()
    {
        $globalActionsObj = new GlobalActions();
        return $globalActionsObj->diffTime($this->created_at);
    }

    /**
     * show human format for date
     *
     * @param $value
     *
     * @return string
     */
    public function getHumanCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('g:i a l jS F Y');
    }

    /**
     * get status in string format
     *
     * @param $value
     *
     * @return string
     */
    public function getStatusAttribute($value)
    {
        /*0 paid - 1 not paid - 2 pending - 3 removed*/
        $status = [
            0 => 'paid',
            1 => 'not paid',
            2 => 'pending',
            3 => 'removed'
        ];
        return $status[intval($value)];
    }

    /**
     * get user model in eluquent format
     *
     * @param $value
     *
     * @return string
     */
    public function getUserIdAttribute($value)
    {
        return User::find($value);
    }

//    /**
//     * get package model in eluquent format
//     *
//     * @param $value
//     *
//     * @return string
//     */
//    public function getPackageIdAttribute($value)
//    {
//        if( !empty($value) )
//            return Package::find($value);
//
//        return $value;
//    }

    /**
     * get booking model in eluquent format
     *
     * @param $value
     *
     * @return string
     */
    public function getBookingIdAttribute($value)
    {
        if( !empty($value) )
            return Booking::find($value);

        return $value;
    }
}
