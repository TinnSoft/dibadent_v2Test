<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    protected $dates = ['deleted_at','created_at','updated_at'];
    
    protected $table = 'products';
    
    protected $fillable=['description','required_points','created_by','modified_by','created_at','updated_at','deleted_at'];


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
			'description'=>null,
            'required_points'=>null
        ];
    }
}
