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
            else {
                $profile='DOCTOR';
            }

            $activeNotifications = Tracker::Join('users', 'tracker.user_id', '=', 'users.id')     
            ->Join('profiles', 'profiles.id', '=', 'users.profile_id') 
            ->leftJoin('read_notifications', function ($leftJoin) {
                $leftJoin->on('read_notifications.tracker_id', '=', 'tracker.id');
            })
            ->where([
                ['profiles.description','=',$profile]
            ])     
            ->where('tracker.notify',true)
            ->whereNull('read_notifications.id')
            ->select('tracker.id','tracker.detail','users.id as user_id','tracker.created_at')              
            ->get();  
            
            if (collect($activeNotifications)->isEmpty())
            {
                $activeNotifications = null;
            }
        }  

       return response()
       ->json([
           'notifications' => $activeNotifications       
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

            /*$ProfileName = Users::Join('profiles', 'profiles.id', '=', 'users.id') 
            ->where([
                ['users.id','=',Auth::user()->id]
            ])     
            ->select('profiles.description')              
            ->get(); */

            return response()
            ->json([
                'created' => true,
                //'newNotificationList'=>$this->getNotificationList($ProfileName)
            ]);
           }
               
           return response()
               ->json([
                   'updated' => false
               ],422) ;
       }
   

}
