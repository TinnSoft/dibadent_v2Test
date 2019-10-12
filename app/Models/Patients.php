<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patients extends Model
{
    protected $dates = ['deleted_at','created_at','updated_at'];
    
    protected $table = 'patients';
    
    protected $fillable=['name','last_name','email','birthday','home_address','phone1','phone2','created_by','modified_by','created_at','updated_at','deleted_at'];


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
			'name'=>null,
            'last_name'=>null,
            'email'=>null,
            'birthday'=>null,
            'home_address'=>null,
            'phone1'=>null,
            'phone2'=>null
        ];
    }
}
