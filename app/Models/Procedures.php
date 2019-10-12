<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Procedures extends Model
{
    protected $dates = ['deleted_at','created_at','updated_at'];
    
    protected $table = 'procedures';
    
    protected $fillable=['product_id','patient_id','doctor_id','description',
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

    public function patient()
    {
        return $this->hasOne(Patients::class, 'id', 'patient_id')->select(array('id', 'name','last_name','email'));
    }

    public function doctor()
    {
        return $this->hasOne(Doctors::class, 'id', 'doctor_id')->select(array('id', 'name','last_name','email'));
    }
    
    
    public static function initialize()
    {
        return [
			'product_id'=>null,
			'patient_id'=>null,
			'doctor_id'=>null,
			'description' => null
        ];
    }
}
