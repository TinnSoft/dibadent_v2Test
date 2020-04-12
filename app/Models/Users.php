<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Users extends Authenticatable implements JWTSubject
{
    use  Notifiable;
 
    use SoftDeletes;
    
    
    protected $dates = ['deleted_at'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'profile_id','name', 'last_name','email', 'email_verified_at','password','birthday','home_address','phone',
        'isActive','last_login','remember_token','modified_by','created_by','identification_number'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token','password',
        'created_at','updated_at','deleted_at','modified_by','created_by'
    ];

    public function rules()
    {
        return [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:user',
            'password' => 'required|min:6|confirmed',
        ];
    }
    
    public function getJWTIdentifier()
    {
    
        return $this->getKey();
    }

    /**
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function profile()
    {
      return $this->hasOne(Profiles::class,'id','profile_id');
    }

    public static function initialize()
    {
        return [
            'email'=>null,
            'name'=>null,
            'last_name'=>null,
            'identification_number'=>null,
            'password'=>null,
            'birthday'=>null,
            'home_address'=>null,
            'phone'=>null,
        ];
    }

    public function getAvatarAttribute()
    {
        if ($this->attributes['avatar']){
        return $this->attributes['avatar'] = asset('storage/' . $this->attributes['avatar']);
        }
        else
        {
            return null;
        }
    }

    public function points()
    {
        return $this->hasOne();
    }
   
}
