<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\SoftDeletes;


class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use SoftDeletes;

    const EMAILLOGINTYPE = 'email';
    const ACTIVESTATUS = 'Active';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        // 'name', 'email', 'password',
        'full_name',
        'email',
        'mobile_number',
        'country_code',
        'login_type',
        'otp',
        'password',
        'refrence_id',
        'calender_id',
        'profile_image',
        'device_token',
        'status',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','country_code','device_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function addEdit($data)
    {
        return User::updateOrCreate(
            ['id' => @$data['id']],
            [
                'full_name' => @$data['full_name'] ?: '',
                'email' => @$data['email'] ?: '',
                'mobile_number' => @$data['mobile_number'] ?: '',
                'login_type' => @$data['login_type'] ?: '',
                'otp' => @$data['otp'] ?: '',
                'password' => @$data['password'] ?: '',
                'refrence_id' => @$data['refrence_id'] ?: '',
                'calender_id' => @$data['calender_id'] ?: '',
                'profile_image' => @$data['profile_image'] ?: '',
                'device_token' => @$data['device_token'] ?: '',
                'status' => @$data['status'] ?: '',
            ]
        );
    }

    public function getProfileImageAttribute($value)
    {
        if ($value) {
            return asset('uploads/' . $value);
        }
        return  asset('no_profile.jpeg');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

}
