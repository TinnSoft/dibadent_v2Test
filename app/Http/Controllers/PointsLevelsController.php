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
use DateTime;

class PointsLevelsController extends Controller
{    

    public function getPointsSummaryByDoctor()
    {   
        
        
        $data = Users::Join('profiles', function ($join) {
            $join->on('users.profile_id', '=', 'profiles.id')->where('profiles.description','=','DOCTOR');
        })
        ->leftJoin('points_history', 'points_history.USER_ID', '=', 'users.id') 
        ->select('users.id', DB::raw("CONCAT(IFNULL(users.name,''),' ',IFNULL(users.last_name,'')) as user_name"),
            'users.identification_number',DB::raw("SUM(points_history.value) as available_points"),
            DB::raw("'0' as new_points"))      
        ->whereNull('users.deleted_at') 
        ->groupBy('users.id','users.name','users.last_name','users.identification_number')
        ->get();

        $redeemedPoints= PointsRedemption::select('user_id',DB::raw("SUM(points_redeemed) as points_redeemed"))
        ->whereNull('deleted_at')
        ->groupBy('user_id')->get();


        foreach($data as $_item) {        
            try {
                $val = $redeemedPoints->firstWhere('user_id', '=', $_item->id)['points_redeemed'];
                if($val)
                {
                    $_item['available_points'] = (int)$_item['available_points'] - (int)$val;
                }   
            } catch (\Throwable $th) {
                //throw $th;
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
       //Pendiente adicionar las fechas de creacion y actualizacion
        $newLevelsValues = $request->all();                    
     
        PointsHistory::insert($newLevelsValues);

        foreach($newLevelsValues as $value) {
            $this->updatePointLevels($value['user_id'],$value['value']);
        }        

        return response()
        ->json([
            'created' => true,
            'test' => $newLevelsValues
        ]);
    }
    

    private function updatePointLevels($userId, $pointsToStore)
    {

        $levelName=null;

        //buscar si existe el usuario en AcumulatedPointsLevels    
        $acumulatedPoints = DB::table('acumulated_points_levels')->where('user_id', $userId)->select('acumulated_points')->get()->toArray();      
        $acumulatedPoints =isset($acumulatedPoints[0]->acumulated_points) ? $acumulatedPoints[0]->acumulated_points : 0;

        if($acumulatedPoints>0)
        {           
            //Actualiza el monto de puntos
            $incrementPoints=DB::table('acumulated_points_levels')->where('user_id', $userId)->increment('acumulated_points', $pointsToStore); 
            $acumulatedPoints = DB::table('acumulated_points_levels')->where('user_id', $userId)->select('acumulated_points')->get()->toArray();      
            $acumulatedPoints =isset($acumulatedPoints[0]->acumulated_points) ? $acumulatedPoints[0]->acumulated_points : 0;

            //Obtiene el listado de niveles del sistema
            $currentLevels= PointsLevels::whereNull('deleted_at')->select('level_name','required_points','id')->orderBy('required_points', 'asc')->get();
   
            if ($currentLevels)
            {
                foreach($currentLevels as $value) {
                    $levelId=$value['id'];
                    $levelPoints=$value['required_points'];
                    $levelName=$value['level_name'];

                    if ((int)$acumulatedPoints>=(int)$levelPoints)
                    {
                        $updateLevel = DB::table('acumulated_points_levels')
                        ->where('user_id', $userId)
                        ->update(['points_level_id' => $levelId,'created_by' => Auth::user()->id,'updated_at' => now()]);
                    }
                }
            }
        }
        else
        {
            if ($pointsToStore>0)
            {
                DB::table('acumulated_points_levels')->insert([
                    ['points_level_id' => null, 
                    'user_id' => $userId,
                    'acumulated_points'=>$pointsToStore,
                    'created_by'=>Auth::user()->id,
                    'modified_by'=>Auth::user()->id,
                    'created_at'=>now(),
                    'updated_at'=>now()]
                ]);

                $this->updatePointLevels($userId, 0);
            }
        }
    }

    
    public function confirmCoupon($redemptionID){

        $valuestoupdate['modified_by'] = Auth::user()->id;
        $valuestoupdate['is_code_confirmed'] = true;
        $item = PointsRedemption::findOrFail($redemptionID);
        $item->update($valuestoupdate);

        return response()
        ->json([
            'updated' => true
        ]);
    }

    public function rejectCoupon($redemptionID){

        $valuestoupdate['modified_by'] = Auth::user()->id;
        $item = PointsRedemption::findOrFail($redemptionID);
        $item->update($valuestoupdate);

        //Puntos para restaurar
        $this->updatePointLevels($item->user_id,$item->points_redeemed); 
        $item->delete();

        return response()
        ->json([
            'updated' => true,
            'test'=>$item
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
