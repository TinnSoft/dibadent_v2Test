<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $dates = ['deleted_at','created_at','updated_at'];    
    
    protected $table = 'company';
    
    protected $fillable=['name','address','phone','corporate_email'];

    public static function initialize()
    {
        return [
			'name'=>null,
            'address'=>null,
            'phone'=>null,
            'corporate_email'=>null
        ];
    }
}
