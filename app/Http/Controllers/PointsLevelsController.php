<?php

namespace App\Http\Controllers;
use Auth;
use DB;
use App\Models\PointsLevels;
use App\Models\Users;
use App\Models\PointsHistory;
use App\Models\PointsRedemption;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Events\RecordActivity;

class PointsLevelsController extends Controller
{    

    public function getPointsSummaryByDoctor()
    {   
        
        
        $data = Users::Join('profiles', function ($join) {
            $join->on('users.profile_id', '=', 'profiles.id')->where('PROFILES.description','=','DOCTOR');
        })
        ->leftJoin('points_history', 'points_history.USER_ID', '=', 'users.id') 
        ->select('users.id', DB::raw("CONCAT(users.name,' ',users.last_name) as user_name"),
            'users.identification_number',DB::raw("SUM(points_history.value) as available_points"),
            DB::raw("'0' as new_points"))      
        ->whereNull('users.deleted_at') 
        ->groupBy('users.id')
        ->get();

        $redeemedPoints= PointsRedemption::select('user_id',DB::raw("SUM(points_redeemed) as points_redeemed"))
        ->whereNull('deleted_at')
        ->groupBy('user_id')->get();


        foreach($data as $_item) {        
            $val = $redeemedPoints->firstWhere('user_id', '=', $_item->id)['points_redeemed'];
            if($val)
            {
                $_item['available_points'] = (int)$_item['available_points'] - (int)$val;
            }            
       }


       return response()
       ->json([
           'records' => $data
       ]);
    }

    public function getPointsLevels()
    {   
        $data = PointsLevels::whereNull('deleted_at')      
        ->select('points_levels.id',
        'points_levels.level_name',
        'points_levels.required_points',
        'points_levels.limit_months',
        'points_levels.created_at')              
        ->get();

       return response()
       ->json([
           'records' => $data
       ]);
    }

   
    public function store(Request $request)
    {     
        $newID=(int)PointsLevels::max('id')+1;

        $data = [
            'level_name' => 'NEW_LEVEL_'.$newID,
            'created_by' => Auth::user()->id,
        ];         

        $item = PointsLevels::create($data);

        event(new RecordActivity(Auth::user()->name.' creó el nivel de puntos '.$data['level_name'],
        'PointsLevels',null, true));

        return response()
            ->json([
                'created' => true,
                'id' => $item->id
            ]);
    }
    public function update(Request $request, $id)
    {   

        $this->validate($request, [
            'id' => 'required'
        ]);      

        $newLevelsValues = $request->all();   

        $newLevelsValues['modified_by'] = Auth::user()->id;
        $item = PointsLevels::findOrFail($id);
        $item->update($newLevelsValues);

        event(new RecordActivity(Auth::user()->name.' actualizó el registro de puntos '.$item->level_name,
        'PointsLevels',null, true));

                
        return response()
        ->json([
            'updated' => true,
            'id' => $item->id             
        ]);
    }

    public function store_NewPoints(Request $request)
    {   
       
        $newLevelsValues = $request->all();   
        
        PointsHistory::insert($newLevelsValues);

        return response()
        ->json([
            'created' => true,
            'test' => $newLevelsValues
        ]);
    }
    public function destroy($id)
    {   
        $post = PointsLevels::find($id);
        $post->delete();

        event(new RecordActivity(Auth::user()->name.' eliminó el registro de puntos '.$post->level_name,
        'PointsLevels',null, true));


        return response()
        ->json([
            'deleted' => true
        ]);
    }
}
