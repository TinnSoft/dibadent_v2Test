<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersStatus extends Model
{
    protected $table = 'users_status';
    
    protected $fillable=['description'];

    public static function initialize()
    {patient_id
        return [
			'description'=>null
        ];
    }
}
