<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ReadNotifications extends Model
{   
    protected $dates = ['created_at','updated_at'];
    
    protected $table = 'read_notifications';
    
    protected $fillable=['user_id','tracker_id','created_at','updated_at'];


}
