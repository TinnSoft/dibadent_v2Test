<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    public $timestamps = false;
      
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
