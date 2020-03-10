<?php

namespace App\Repositories;
use Auth;
use Illuminate\Support\Facades\DB;

class ActivityRepository
{
    public function create($model)
    {
        
       DB::transaction(function() use ($model) {
          
           DB::table('tracker')->insert(
                [
                'user_id' => Auth::user()->id,
                'detail'=>$model->detail,
                'model'=>$model->model,
                'route'=>$model->route
                ]
            );
      
      
    });   
    }
}