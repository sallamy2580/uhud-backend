<?php

namespace App\Models;

use App\Repositories\GlobalActions\GlobalActions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class HotelRoomReserve extends Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'hotel_room_reservs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = [
            'hotel_id',
            'room_id',
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
    protected $appends = ['time_ago', 'human_created_at'];

    /**
     * show hotel time ago
     *
     * @return string
     */
    public function getTimeAgoAttribute(): string
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
    public function getHumanCreatedAtAttribute($value): string
    {
        return Carbon::parse($value)->format('g:i a l jS F Y');
    }

    /**
     * attach hotel room
     *
     * @param $value
     *
     * @return \App\Models\HotelRoom
     */
    public function getRoomIdAttribute($value): HotelRoom
    {
        return HotelRoom::find($value);
    }

    /**
     * attach user
     *
     * @param $value
     *
     * @return \App\Models\User
     */
    public function getUserIdAttribute($value): User
    {
        return User::find($value);
    }

    /**
     * attach transaction
     *
     * @param $value
     *
     * @return \App\Models\Transaction
     */
    public function getTransactionIdAttribute($value): Transaction
    {
        return Transaction::find($value);
    }
}
