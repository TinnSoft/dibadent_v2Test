<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PointsLevels extends Model
{
    use SoftDeletes;
    
    protected $dates = ['deleted_at','created_at','updated_at'];
    
    protected $table = 'points_levels';
    
    protected $fillable=['level_name','required_points','limit_months','created_by','modified_by','created_at','updated_at','deleted_at'];


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
            'limit_months'=>null,
        ];
    }
}
