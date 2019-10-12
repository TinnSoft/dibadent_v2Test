<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointsRedemption extends Model
{
    protected $dates = ['deleted_at','created_at','updated_at'];
    
    protected $table = 'points_redemption';
    
    protected $fillable=['product_id','user_id','points_redeemed','code','is_code_confirmed',
    'created_by','modified_by','created_at','updated_at','deleted_at'];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at','updated_at','deleted_at','modified_by','created_by'
    ];


     public function product()
    {
        return $this->hasOne(Products::class, 'id', 'product_id')->select(array('id', 'description','required_points'));
    }

    public function user()
    {
        return $this->hasOne(Users::class, 'id', 'user_id')->select(array('id', 'name','last_name','email'));
    }
    
    public static function initialize()
    {
        return [
			'product_id'=>null,
			'user_id'=>null,
			'points_redeemed'=>null,
			'code' => null, 
            'is_code_confirmed' => null
        ];
    }
}
