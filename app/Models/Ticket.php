<?php

namespace App\Models;

use App\Repositories\GlobalActions\GlobalActions;
use App\Repositories\GlobalActions\GlobalActionsInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'tickets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = [
            'user_id',
            'subject',
            'body',
            'status',
            'ticket_img',
            'ticket_img_url'
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
            1 => 'pending',
            2 => 'answered',
            3 => 'closed'
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
}
