<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointsLevels extends Model
{
    protected $dates = ['deleted_at','created_at','updated_at'];
    
    protected $table = 'points_levels';
    
    protected $fillable=['level_name','required_points','limit_date','created_by','modified_by','created_at','updated_at','deleted_at'];


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
			'level_name'=>null,
            'required_points'=>null,
            'limit_date'=>null,
        ];
    }
}
