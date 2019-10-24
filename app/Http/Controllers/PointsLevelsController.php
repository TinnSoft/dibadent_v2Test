<?php

namespace App\Http\Controllers;
use Auth;
use DB;
use App\Models\PointsLevels;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class PointsLevelsController extends Controller
{
    public function getPointsLevels()
    {   

        $data = DB::table('points_levels') 
        ->whereNull('deleted_at')
        ->select('points_levels.id',
        'points_levels.level_name',
        'points_levels.required_points',
        'points_levels.limit_date',
        'points_levels.created_at'
        )      
        ->orderBy('points_levels.id','desc')        
        ->get()->toArray();

        $data = PointsLevels::whereNull('deleted_at')      
        ->select('points_levels.id',
        'points_levels.level_name',
        'points_levels.required_points',
        'points_levels.limit_date',
        'points_levels.created_at')              
        ->get();

       return response()
       ->json([
           'records' => $data
       ]);
    }
}
