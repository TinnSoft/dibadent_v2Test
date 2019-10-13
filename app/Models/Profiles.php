<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profiles extends Model
{
    protected $table = 'profiles';
    
    protected $fillable=['description'];

    public static function initialize()
    {
        return [
			'description'=>null
        ];
    }
}
