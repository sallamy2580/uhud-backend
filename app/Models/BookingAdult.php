<?php

namespace App\Models;

use App\Repositories\GlobalActions\GlobalActions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class BookingAdult extends Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'booking_adults';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = [
            'booking_id',
            'full_name',
            'gender',
            'birth_date',
            'passenger_type',
            'passport_img_url',
            'passport_img_b64'
        ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['time_ago','human_created_at'];

    /**
     * show booking adult time ago
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
    public function getGenderAttribute($value)
    {
        $genders = [
            1 => 'male',
            2 => 'female',
            3 => 'other'
        ];
        return $genders[intval($value)];
    }

    /**
     * get passenger type in string format
     *
     * @param $value
     *
     * @return string
     */
    public function getPassengerTypeAttribute($value)
    {
        $passengerType = [
            1 => 'adult',
            2 => 'child'
        ];
        return $passengerType[intval($value)];
    }
}
