<?php

namespace App\Models;

use App\Repositories\GlobalActions\GlobalActions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
  /**
   * Table name
   *
   * @var string
   */
  protected $table = 'bookings';
  
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable
    = [
      'user_id',
      'country_id',
      'city_id',
      'package_id',
      'total_price',
      'name',
      'email',
      'phone',
      'region',
      'num_childs',
      'num_adults',
      'remarks',
      'status',
      'num_nights',
      'is_price_applied'
    ];
  
  /**
   * The accessors to append to the model's array form.
   *
   * @var array
   */
  protected $appends = ['time_ago', 'human_created_at', 'passengers'];
  
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
   * show booking time ago
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
      1 => 'pending',
      2 => 'accepted',
      3 => 'rejected'
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
  
  /**
   * get package model in eluquent format
   *
   * @param $value
   *
   * @return string
   */
  public function getPackageIdAttribute($value)
  {
    return Package::find($value);
  }
  
  /**
   * attach adults informations to result
   *
   * @param $value
   *
   * @return string
   */
  public function getPassengersAttribute()
  {
    return BookingAdult::where('booking_id', $this->id)->get();
  }
}
