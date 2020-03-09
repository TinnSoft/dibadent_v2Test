<?php

namespace App\Http\Controllers;
use Auth;
use DB;
use App\Models\PointsLevels;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Events\RecordActivity;

class PointsLevelsController extends Controller
{
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

        event(new RecordActivity(Auth::user()->name.' creó un nuevo nivel de puntos',
        'PointsLevels',null));

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

        event(new RecordActivity(Auth::user()->name.' actualizó un registro de puntos',
        'PointsLevels',null));

                
        return response()
        ->json([
            'updated' => true,
            'id' => $item->id             
        ]);
    }
    public function destroy($id)
    {   
        $post = PointsLevels::find($id);
        $post->delete();

        event(new RecordActivity(Auth::user()->name.' eliminó un registro de puntos',
        'PointsLevels',null));


        return response()
        ->json([
            'deleted' => true
        ]);
    }
}
