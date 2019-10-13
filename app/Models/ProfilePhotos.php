<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilePhotos extends Model
{
    protected $table = 'profile_photos';
    
    protected $dates = ['deleted_at','created_at','updated_at'];
    
    protected $fillable=['user_id','title','file_name','other_details',
    'created_by','modified_by','created_at','updated_at','deleted_at'];


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
            'user_id'=>null,
            'title'=>null,
            'file_name'=>null,
            'other_details'=>null
        ];
    }
}
