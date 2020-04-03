<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tracker;
use Auth;
use DB;

class TrackerController extends Controller
{
    
    
    public function getNotificationList($profile)
    {   
        $activeNotifications = null;

        if($profile)
        {
            $activeNotifications = Tracker::Join('users', 'tracker.user_id', '=', 'users.id')     
            ->Join('profiles', 'profiles.id', '=', 'users.profile_id')  
            ->where([
                ['profiles.description','=',$profile]
            ])     
            ->select('tracker.id','tracker.detail','users.id as user_id','tracker.created_at')              
            ->get(); 

            //$date->ago(); 
           
        }  

       return response()
       ->json([
           'notifications' => $activeNotifications       
       ]);
    }


}
