<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TicketSection extends Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'ticket_sections';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = [
            'ticket_id',
            'user_id',
            'body',
            'user_role',
            'ticket_img',
            'ticket_img_url'
        ];

    /**
     * show human format for date
     *
     * @param $value
     *
     * @return string
     */
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('g:i a l jS F Y');
    }

    /**
     * get user role in string format
     *
     * @param $value
     *
     * @return string
     */
    public function getUserRoleAttribute($value)
    {
        $userRole = [
            0 => 'admin',
            1 => 'admin',
            2 => 'agent',
            5 => 'user'
        ];
        return $userRole[intval($value)];
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
