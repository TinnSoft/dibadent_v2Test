<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Comments extends Model
{
    protected $dates = ['deleted_at','created_at','updated_at'];
    
    protected $table = 'comments';
    
    protected $fillable=['procedure_id','comment','created_by','modified_by','created_at','updated_at','deleted_at'];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at','updated_at','deleted_at','modified_by','created_by'
    ];


    public static function initialize()
    {
        return [
			'procedure_id'=>null,
			'comment'=>null
        ];
    }
}
