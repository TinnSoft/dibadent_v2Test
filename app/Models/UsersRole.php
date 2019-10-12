<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersRole extends Model
{
    protected $table = 'users_role';
    
    protected $fillable=['role'];

    public static function initialize()
    {patient_id
        return [
			'role'=>null
        ];
    }
}
