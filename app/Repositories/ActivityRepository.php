<?php

namespace App\Repositories;
use Auth;
use App\Models\Tracker;
use Illuminate\Support\Facades\DB;

class ActivityRepository
{
    public function create($model)
    {
        
       DB::transaction(function() use ($model) {
        
        $data=[
            'user_id' => Auth::user()->id,
            'detail'=>$model->detail,
            'model'=>$model->model,
            'route'=>$model->route,
            'notify'=>$model->notify,
            'value'=>$model->value
        ];

        $item = Tracker::create($data);
      
    });   
    }
}