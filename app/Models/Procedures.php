<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Procedures extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at','created_at','updated_at'];
    
    protected $table = 'procedures';
    
    protected $fillable=['product_id','patient_id','doctor_id','radiologist_id','description','comments','procedure_date',
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
        return $this->hasOne(Products::class, 'id', 'product_id')->select(array('id','id as value', 'description as label'));
    }

    public function patient()
    {
        return $this->hasOne(Users::class, 'id', 'patient_id')->select(array('id', 'name','last_name','email'));
    }

    public function doctor()
    {
        return $this->hasOne(Users::class, 'id', 'doctor_id')->select(array('id', 'name','last_name','email'));
    }

    public function radiologist()
    {
        return $this->hasOne(Users::class, 'id', 'radiologist_id')->select(array('id', 'id as value', 'name as label','last_name','email'));
    }
 
    
    public static function initialize()
    {
        return [
			'product_id'=>null,
			'patient_id'=>null,
            'doctor_id'=>null,
            'radiologist_id'=>null,
            'description' => null,
            'comments' => null,
            'procedure_date' => null
        ];
    }
}
