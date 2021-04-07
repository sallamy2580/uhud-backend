<?php

namespace App\Models;

use App\Repositories\GlobalActions\GlobalActions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class HotelRoom extends Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'hotel_rooms';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = [
            'hotel_id',
            'name',
            'room_type',
            'description',
            'view',
            'extera',
            'main_img_base64',
            'main_img_url'
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
}
