<?php

namespace App\Models;

use App\Repositories\GlobalActions\GlobalActions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'packages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'creator',
        'airline_id',
        'hotel_id',
        'name',
        'type',
        'price',
        'status',
        'code',
        'rate',
        'start_date',
        'end_date'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['time_ago', 'human_created_at'];

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
        $status = [
            0 => 'deactive',
            1 => 'active',
            2 => 'banned',
            3 => 'expired',
            4 => 'removed'
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
    public function getCreatorAttribute($value)
    {
        return User::find($value);
    }
    /**
     * get lights model in eluquent format
     *
     * @param $value
     *
     * @return string
     */
    public function getAirlineIdAttribute($value)
    {
        return Flight::find($value);
    }
    /**
     * get hotel model in eluquent format
     *
     * @param $value
     *
     * @return string
     */
    public function getHotelIdAttribute($value)
    {
        return Hotel::find($value);
    }

}
