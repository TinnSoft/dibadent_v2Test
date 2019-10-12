<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Doctors extends Model
{   
    protected $dates = ['deleted_at','created_at','updated_at'];
    
    protected $table = 'doctors';
    
    protected $fillable=['points_level_id','name','last_name','email','birthday','home_address','acumulated_points',
    'phone1','phone2','created_by','modified_by','created_at','updated_at','deleted_at'];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at','updated_at','deleted_at','modified_by','created_by'
    ];


     public function points_level()
    {
        return $this->hasOne(PointsLevels::class, 'id', 'points_level_id')->select(array('id', 'level_name','required_points','limit_date'));
    }
    
    public static function initialize()
    {
        return [
			'points_level_id'=>null,
			'name'=>null,
			'last_name'=>null,
			'email' => null, 
            'birthday' => null,
            'home_address'=>null,
            'acumulated_points'=>0,
            'phone1'=>null,
            'phone2'=>null
        ];
    }

}
