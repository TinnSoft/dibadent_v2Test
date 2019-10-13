<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Images extends Model
{
    protected $dates = ['deleted_at','created_at','updated_at'];
    
    protected $table = 'images';
    
    protected $fillable=['procedure_id','title','file_name','other_details','created_by','modified_by','created_at','updated_at','deleted_at'];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at','updated_at','deleted_at','modified_by','created_by'
    ];


    public function procedure()
    {
        return $this->hasOne(Procedures::class, 'id', 'procedure_id')->select(array('id', 'product_id','patient_id','doctor_id','description'));
    }

    public static function initialize()
    {
        return [
			'procedure_id'=>null,
            'title'=>null,
            'file_name'=>null,
            'other_details'=>null
        ];
    }
}
