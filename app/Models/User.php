<?php

namespace App\Models;


use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = [
            'full_name',
            'email',
            'phone',
            'role',
            'status',
            'postal_code',
            'password',
            'country_id',
            'state_id',
            'city_id',
            'forget_pass_expire_at',
            'email_verification_expired_at',
            'is_email_verified',
            'email_verified_at',
            'remember_token',
            'created_at'
        ];

    /**
     * Hidden for arrays attributes
     *
     * @var array
     */
    protected $hidden
        = [
            'password'
        ];

    /**
     * Hash password before save
     *
     * @param $value
     */
    public function setPasswordAttribute($value)
    {
        if (!is_null($value)) {
            $this->attributes['password'] = Hash::make($value);
        }
    }

    /**
     * Trim full name before response
     *
     * @param $value
     *
     * @return string
     */
    public function getFullNameAttribute($value)
    {
        return trim($value);
    }

    /**
     * show human format for date
     *
     * @param $value
     *
     *  @return string
     */
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('g:i a l jS F Y');
    }

    /**
     * get role in string format
     *
     * @param $value
     *
     *  @return string
     */
    public function getRoleAttribute($value)
    {
        $roles = [
            0=>'admin',
            1=>'admin',
            2=>'agent',
            5=>'user',
        ];
        return $roles[intval($value)];
    }

    /**
     * get status in string format
     *
     * @param $value
     *
     *  @return string
     */
    public function getStatusAttribute($value)
    {
        $status = [
            1=>'active',
            2=>'inactive',
            3=>'removed',
            4=>'banned',
        ];
        return $status[intval($value)];
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * get country model in eluquent format
     *
     * @param $value
     *
     * @return mixed
     */
    public function getPackageIdAttribute($value)
    {
        if( !empty($value) )
            return Country::find($value);
        else
            return $value;
    }


}
