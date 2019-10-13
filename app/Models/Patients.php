<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patients extends Model
{
    protected $dates = ['deleted_at','created_at','updated_at'];
    
    protected $table = 'patients';
    
    protected $fillable=['user_id','gender_id','name','last_name','email','birthday','home_address','phone','comments',
    'created_by','modified_by','created_at','updated_at','deleted_at'];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at','updated_at','deleted_at','modified_by','created_by'
    ];

    public static function initialize()
    {
        return [
            'user_id'=>null,
            'gender_id'=>null,
            'name'=>null,
            'last_name'=>null,
            'email'=>null,
            'birthday'=>null,
            'home_address'=>null,
            'phone'=>null,
            'comments'=>null
        ];
    }
}
