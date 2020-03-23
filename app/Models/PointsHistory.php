<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PointsHistory extends Model
{   
    protected $dates = ['created_at','updated_at','deleted_at'];
    
    protected $table = 'points_history';
    
    protected $fillable=['value','user_id','created_at','updated_at','deleted_at'];

}
