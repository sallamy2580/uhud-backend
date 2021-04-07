<?php

namespace App\Models;

use App\Repositories\GlobalActions\GlobalActions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'hotels';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = [
            'creator',
            'en_name',
            'name',
            'description',
            'address',
            'lat',
            'lng',
            'accomodation',
            'main_image_base64',
            'main_image_url',
            'price',
            'price_double',
            'price_triple',
            'price_quad'
        ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['time_ago', 'human_created_at','room_reserves','rooms','images'];

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
     * get accomodation value
     *
     * @param $value
     *
     * @return string
     */
    public function getAccomodationAttribute($value): string
    {
        $accomodations = [
            1 => 'one star',
            2 => 'two star',
            3 => 'three star',
            4 => 'four star',
            5 => 'five star',
            6 => 'six star'
        ];
        return $accomodations[$value];
    }

    /**
     * get user model in eluquent format
     *
     * @param $value
     *
     * @return mixed
     */
    public function getCreatorAttribute($value)
    {
        if( !empty($value) )
            return User::find($value);

        return $value;
    }

    /**
     * get all hotel images
     *
     * @param $value
     *
     * @return mixed
     */
    public function getImagesAttribute()
    {
        return HotelImage::where('hotel_id',$this->id)->get();
    }

    /**
     * get all hotel rooms
     *
     * @param $value
     *
     * @return mixed
     */
    public function getRoomsAttribute()
    {
        return HotelRoom::where('hotel_id',$this->id)->get();
    }

    /**
     * get all hotel reserves
     *
     * @param $value
     *
     * return mixed
     */
    public function getRoomReservesAttribute()
    {
        return HotelRoomReserve::where('hotel_id',$this->id)->get();
    }
}
