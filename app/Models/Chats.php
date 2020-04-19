<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chats extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at','created_at','updated_at'];
    
    protected $table = 'chats';
    
    protected $fillable=['user_id','comment','created_at','updated_at','deleted_at','doctor_id_parent'];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at','updated_at','deleted_at'
    ];   

    public static function initialize()
    {
        return [
			'user_id'=>null,
			'comment'=>null
        ];
    }
}
