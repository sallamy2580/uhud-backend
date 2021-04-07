<?php

namespace App\Models;

use App\Repositories\GlobalActions\GlobalActions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class FlightSeatReserved extends Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'flight_seat_reserves';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = [
            'flight_id',
            'seat_id',
            'user_id',
            'transaction_id',
            'description',
            'extera',
            'final_price'
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

//    /**
//     * get status in string format
//     *
//     * @param $value
//     *
//     * @return string
//     */
//    public function getStatusAttribute($value)
//    {
//        $status = [
//            0 => 'paid',
//            1 => 'not paid',
//            2 => 'pending',
//            3 => 'removed',
//            4 => 'banned',
//        ];
//        return $status[intval($value)];
//    }

    /**
     * get flight model in eluquent format
     *
     * @param $value
     *
     * @return string
     */
    public function getFlightIdAttribute($value)
    {
        return Flight::find($value);
    }

    /**
     * get seat model in eluquent format
     *
     * @param $value
     *
     * @return string
     */
    public function getSeatIdAttribute($value)
    {
        return FlightSeat::find($value);
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

    /**
     * get transactions model in eluquent format
     *
     * @param $value
     *
     * @return string
     */
    public function getTransactionIdAttribute($value)
    {
        return Transaction::find($value);
    }
}
