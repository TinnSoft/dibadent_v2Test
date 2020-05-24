<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tracker;
use App\Models\Users;
use App\Models\ReadNotifications;
use Auth;
use DB;

class TrackerController extends Controller
{
    
    
    public function getNotificationList($profile)
    {   
        $activeNotifications = null;
       
        if($profile)
        {
            if ($profile=='DOCTOR')
            {
                $profile='ADMIN';
            }
            else if ($profile='ADMIN') {
                $profile='DOCTOR';
            }

            $allNotifications = Tracker::Join('users', 'tracker.user_id', '=', 'users.id')     
            ->Join('profiles', 'profiles.id', '=', 'users.profile_id') 
            ->leftJoin('read_notifications', function ($leftJoin) {
                $leftJoin->on('read_notifications.tracker_id', '=', 'tracker.id');
            })
            ->where([
                ['profiles.description','=',$profile]
            ])     
            ->where('tracker.notify',true)
            ->whereNull('read_notifications.id')
            ->whereNull('tracker.value')
            ->select('tracker.id','tracker.detail','users.id as user_id','tracker.created_at');
            
            $activeNotifications = Tracker::Join('users', 'tracker.user_id', '=', 'users.id')     
            ->Join('profiles', 'profiles.id', '=', 'users.profile_id') 
            ->leftJoin('read_notifications', function ($leftJoin) {
                $leftJoin->on('read_notifications.tracker_id', '=', 'tracker.id');
            })
            ->where([
                ['profiles.description','=',$profile]
            ])     
            ->where('tracker.notify',true)
            ->where('tracker.value',Auth::user()->id)
            ->whereNull('read_notifications.id')
            ->union($allNotifications)
            ->select('tracker.id','tracker.detail','users.id as user_id','tracker.created_at')              
            ->get();

            if (collect($activeNotifications)->isEmpty() || $profile=='RADIOLOGO')
            {
                $activeNotifications = null;
            }
        }  

       return response()
       ->json([
           'notifications' => $activeNotifications,
       ]);
    }

 
       public function markNotificationAsRead($trackerid)
       {
           
           $notification = ReadNotifications::where('read_notifications.tracker_id','=',$trackerid) 
            ->get();
          
           if (collect($notification)->isEmpty())
           {
            $data['user_id'] = Auth::user()->id;
            $data['tracker_id'] = $trackerid;
            $item = ReadNotifications::create($data);

            return response()
            ->json([
                'created' => true
            ]);
           }
               
           return response()
               ->json([
                   'updated' => false
               ],422) ;
       }
   

}
