<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Tracker extends Model
{   
    protected $dates = ['created_at','updated_at'];
    
    protected $table = 'tracker';
    
    protected $fillable=['detail','user_id','route','model',
    'created_at','updated_at'];


}
