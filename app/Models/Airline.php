<?php

namespace App\Models;

use App\Repositories\GlobalActions\GlobalActions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Airline extends Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'airlines';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = [
            'creator',
            'country_id',
            'name',
            'logo_base64',
            'logo_url',
            'rate_avg'
        ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['time_ago','human_created_at'];

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
     * get country model in eluquent format
     *
     * @param $value
     *
     * @return string
     */
    public function getCountryIdAttribute($value)
    {
        return Country::find($value);
    }
}
