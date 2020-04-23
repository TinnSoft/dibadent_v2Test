<?php

namespace App\Models;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patients extends Model
{
    use SoftDeletes;
    
    protected $dates = ['deleted_at','created_at','updated_at'];
    
    protected $table = 'patients';
    
    protected $fillable=['user_id','gender_id','doctor_id','name','last_name','email','birthday','home_address','phone','comments',
    'created_by','modified_by','created_at','updated_at','deleted_at'];

    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at','updated_at','deleted_at','modified_by','created_by'
    ];

    
    public function getFullNameAttribute()
    {
        return "{$this->name} {$this->last_name}";
    }

    public function gender()
    {
        return $this->belongsTo(Genders::class)->withDefault()->select('id','description','id as value','description as label');
    }

    public function doctor()
    {
        return $this->belongsTo(Users::class,'doctor_id')->withDefault()->select(array('id','name','id as value','name as label'));
    }

   

    public static function initialize()
    {
        return [
            'user_id'=>null,
            'gender_id'=>null,
            'doctor_id'=>null,
            'name'=>null,
            'last_name'=>null,
            'email'=>null,
            'birthday'=>null,
            'home_address'=>null,
            'phone'=>null,
            'comments'=>null
        ];
    }
}
