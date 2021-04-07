<?php

namespace App\Models;

use App\Repositories\GlobalActions\GlobalActions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'notifications';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'notified_by',
        'owner_id',
        'seen',
        'status',
        'type',
        'extera',
        'description',
        'link'
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
        $status = [
            0 => 'deactive',
            1 => 'active',
            2 => 'banned',
            3 => 'removed',
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
    public function getNotifiedByAttribute($value)
    {
        return User::find($value);
    }

    /**
     * get user model in eluquent format
     *
     * @param $value
     *
     * @return string
     */
    public function getOwnerIdAttribute($value)
    {
        return User::find($value);
    }

    /**
     * get user model in eluquent format
     *
     * @param $value
     *
     * @return string
     */
    public function getSeenAttribute($value)
    {
        $seen = [
            0 => 'seen',
            1 => 'never seen'
        ];
        return $seen[intval($value)];
    }
}
