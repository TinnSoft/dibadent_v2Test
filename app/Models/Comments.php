<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comments extends Model
{

    use SoftDeletes;
    
    protected $dates = ['deleted_at','created_at','updated_at'];
      
    protected $table = 'comments';
    
    protected $fillable=['image_id','user_id','comment','deleted_at','created_at'];

    public static function initialize()
    {
        return [
			'image_id'=>null,
            'user_id'=>null,
            'comment'=>null
        ];
    }
}
