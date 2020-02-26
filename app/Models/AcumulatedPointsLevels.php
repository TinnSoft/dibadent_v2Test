<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AcumulatedPointsLevels extends Model
{   
    protected $dates = ['deleted_at','created_at','updated_at'];
    
    protected $table = 'acumulated_points_levels';
    
    protected $fillable=['points_level_id','user_id','acumulated_points',
    'created_by','modified_by','created_at','updated_at','deleted_at'];


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
        return $this->hasOne(PointsLevels::class, 'id', 'points_level_id')->select(array('id', 'level_name','required_points','limit_months'));
    }

    public function user()
    {
        return $this->hasOne(Users::class, 'id', 'user_id')->select(array('id', 'name','email'));
    }
    
    public static function initialize()
    {
        return [
			'points_level_id'=>null,
			'user_id'=>null,			
            'acumulated_points'=>null
        ];
    }

}
