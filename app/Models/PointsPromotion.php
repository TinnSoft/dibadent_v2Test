<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointsPromotion extends Model
{
    protected $dates = ['deleted_at','created_at','updated_at'];
    
    protected $table = 'points_promotion';
    
    protected $fillable=['enabled_from','enabled_to','point_multiplied_by','created_by','modified_by','created_at','updated_at','deleted_at'];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at','updated_at','deleted_at','modified_by','created_by'
    ];


    public static function initialize()
    {patient_id
        return [
			'enabled_from'=>null,
            'enabled_to'=>null,
            'point_multiplied_by'=>null
        ];
    }
}
