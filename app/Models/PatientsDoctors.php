<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientsDoctors extends Model
{
    protected $dates = ['deleted_at','created_at','updated_at'];
    
    protected $table = 'patients_doctors';
    
    protected $fillable=['doctor_id','patient_id','created_by','modified_by','created_at','updated_at','deleted_at'];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at','updated_at','deleted_at','modified_by','created_by'
    ];

    public function patient()
    {
        return $this->hasOne(Patients::class, 'id', 'patient_id')->select(array('id', 'name','last_name','email','birthday'));
    }

    public function doctor()
    {
        return $this->hasOne(Doctors::class, 'id', 'doctor_id')->select(array('id', 'name','last_name','email','birthday'));
    }

    public static function initialize()
    {patient_id
        return [
			'doctor_id'=>null,
            'patient_id'=>null
        ];
    }
}
