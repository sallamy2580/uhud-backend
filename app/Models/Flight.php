<?php

namespace App\Models;

use App\Repositories\GlobalActions\GlobalActions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'flights';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = [
            'creator',
            'airline_id',
            'origin_id',
            'destination_id',
            'origin_name',
            'destination_name',
            'name',
            'logo_base64',
            'logo_url',
            'takeoff_date',
            'return_date',
            'price',
            'rate_avg',
            'seat_availability',
            'is_price_applied'
        ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['time_ago','human_created_at','takeoff_time','return_time'];
  
  /**
   * return is_price_applied string
   *
   * @param $value
   *
   * @return string
   */
  public function getIsPriceAppliedAttribute($value)
  {
    $applied = [
      '0' => 'not yet',
      '1' => 'yes'
    ];
    return $applied[$value];
  }
    
    /**
     * show flight time ago
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
     * show take off time
     *
     * @param $value
     *
     * @return string
     */
    public function getTakeoffTimeAttribute()
    {
        if( !empty($this->takeoff_date) )
            return Carbon::parse($this->takeoff_date)->format('g:i a');
        else
            return '';
    }

    /**
     * show reach time
     *
     * @param $value
     *
     * @return string
     */
    public function getReturnTimeAttribute()
    {
        if( !empty($this->return_date) )
            return Carbon::parse($this->return_date)->format('g:i a');
        else
            return '';
    }

    /**
     * return airline model to response
     *
     * @param $value
     *
     * @return string
     */
    public function getAirlineIdAttribute($value)
    {
        return Airline::find($value);
    }
}
