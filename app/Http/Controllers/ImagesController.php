<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Images;
use App\Models\Users;
use App\Models\PointsHistory;
use App\Models\PointsLevels;
use App\Models\AcumulatedPointsLevels;
use App\Models\Patients;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use DB;
use Illuminate\Http\File;
use App\Events\RecordActivity;

class ImagesController extends Controller
{
    protected $rootFolderMain='/_Images/_radiology';

    public function getImagesByPatient($PatientId)
    {
        //$data= Images::where('procedure_id', $ProcedureId)
       $data= Images::where('procedure_id', $ProcedureId)
       ->select('id', 'title', 'other_details', 'file_name')->get();      
      
       $data->transform(function ($d) {
           $d->file_name = asset('storage/' . $d->file_name);
           return $d;
       });
       return response()
       ->json([
          'images' => $data,          
       ]);

    }
    //Para eliminar
    public function getImagesByProcedure($ProcedureId)
    {
        //$data= Images::where('procedure_id', $ProcedureId)
       $data= Images::where('procedure_id', $ProcedureId)
       ->select('id', 'title', 'other_details', 'file_name')->get();      
      
       $data->transform(function ($d) {
           $d->file_name = asset('storage/' . $d->file_name);
           return $d;
       });
       return response()
       ->json([
          'images' => $data,          
       ]);

    }
    
    //Carga una nueva imagen de radiologia 
    public function uploadFile(Request $request, $id_procedure)
    {
        //verifica si el directorio está creado
        Storage::makeDirectory($this->rootFolderMain);   
        
        if (count($request->files)>0 && $id_procedure != null)              
        {
            foreach($request->files as $_file) {
                $originalName=$_file->getClientOriginalName();            
                $newFileName=Storage::putFile($this->rootFolderMain, new File($_file));
                $imagesModel= new Images;
                $imagesModel->created_by=Auth::user()->id;
                $imagesModel->title=$originalName;
                $imagesModel->file_name=$newFileName;
                $imagesModel->procedure_id=$id_procedure;
                $imagesModel->save();    
            }
            
            $storePoint=$this->storePoints();

            $UserName = Patients::Join('procedures', 'procedures.patient_id', '=', 'patients.id') 
            ->where([
                ['procedures.id','=',$id_procedure]
            ])     
            ->select('patients.name')              
            ->get();

            event(new RecordActivity(Auth::user()->name.' cargó una nueva radiografía para el paciente '.$UserName->pluck('name'),
            'Images',null, true));

            return response()
             ->json([
                'saved' => true,
                'results'=>$storePoint
             ]);
        }
            
        return response()
            ->json([
                'saved' => false
            ],422) ;
     
    }

    private static function storePoints()
    {

        $pointsToStore=1;
        $data=[];        
        $data['user_id']= Auth::user()->id;   
        $data['value'] = $pointsToStore;
        $data['read'] = true;
        $item = PointsHistory::create($data);
        $levelName=null;

        //buscar si existe el usuario en AcumulatedPointsLevels    
        $acumulatedPoints = DB::table('acumulated_points_levels')->where('user_id', Auth::user()->id)->select('acumulated_points')->get()->toArray();      
        $acumulatedPoints =isset($acumulatedPoints[0]->acumulated_points) ? $acumulatedPoints[0]->acumulated_points : 0;

        if($acumulatedPoints>0)
        {           
            //Actualiza el monto de puntos
            //$incrementPoints=DB::table('acumulated_points_levels')->where('user_id', Auth::user()->id)->increment('acumulated_points', $pointsToStore); 

            //Obtiene el listado de niveles del sistema
            $currentLevels= PointsLevels::whereNull('deleted_at')->select('level_name','required_points','id')->orderBy('required_points', 'asc')->get();
           $test=[];
            if ($currentLevels)
            {
                foreach($currentLevels as $value) {
                    $levelId=$value['id'];
                    $levelPoints=(int)$value['required_points'];
                    $levelName=$value['level_name'];

                    if ($acumulatedPoints>=$levelPoints)
                    {
                        $updateLevel = DB::table('acumulated_points_levels')
                        ->where('user_id', Auth::user()->id)
                        ->update(['points_level_id' => $levelId]);
                        break;
                    }
                }
            }
        }
        else
        {
            DB::table('acumulated_points_levels')->insert([
                ['points_level_id' => null, 
                'user_id' => Auth::user()->id,
                'acumulated_points'=>$pointsToStore,
                'created_by'=>Auth::user()->id,
                'modified_by'=>Auth::user()->id]
            ]);
        }
        
        $acumulatedPoints = DB::table('acumulated_points_levels')->where('user_id', Auth::user()->id)->select('acumulated_points')->get()->toArray();      
        $acumulatedPoints =isset($acumulatedPoints[0]->acumulated_points) ? $acumulatedPoints[0]->acumulated_points : 0;

        $datatoReturn=[];
        $datatoReturn['levelName']=$levelName;
        $datatoReturn['acumulatedPoints']=$acumulatedPoints;

        return $datatoReturn;
        //retornar consulta de puntos y nivel


    }
    
    // Actualiza la radiografía
    public function update(Request $request, $id)
    {
        
        $image = Images::find($id);

        //verifica si el directorio está creado
        Storage::makeDirectory($this->rootFolderMain);
        if ($request->hasFile('image') && $image)              
        {
            $newFileName = Storage::putFile($this->rootFolderMain, $request->file('image'));
            $image->file_name = $newFileName;
            $image->save();

            return response()
             ->json([
                'saved' => true,
                'image' => asset('storage/' . $newFileName),
             ]);            
        }
            
        return response()
            ->json([
                'saved' => false
            ],422) ;
    }

    public function destroy($id)
    {
        
        $imgData = Images::find($id);

        Storage::delete($imgData->file_name);

        $imgData->delete();

        event(new RecordActivity(Auth::user()->name.' eliminó la imagen '.$imgData->file_name,
        'Images',null, false));

        return response()
        ->json([
            'deleted' => true
        ]);
    }
    

}
