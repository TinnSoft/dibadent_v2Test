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
use Illuminate\Support\Arr;
use DB;
use Illuminate\Http\File;
use App\Events\RecordActivity;

class ImagesController extends Controller
{
    protected $rootFolderMain='/_Images/_radiology';

    public function getImagesByPatient($PatientId)
    {
      
       $data= Images::where('patient_id', $PatientId)
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
    public function uploadFile(Request $request, $patient_id)
    {
        //verifica si el directorio está creado
        Storage::makeDirectory($this->rootFolderMain);   
        
        if (count($request->files)>0 && $patient_id != null)              
        {
            foreach($request->files as $_file) {
                $originalName=$_file->getClientOriginalName();            
                $newFileName=Storage::putFile($this->rootFolderMain, new File($_file));
                $imagesModel= new Images;
                $imagesModel->created_by=Auth::user()->id;
                $imagesModel->modified_by=Auth::user()->id;
                $imagesModel->title=$originalName;
                $imagesModel->file_name=$newFileName;
                $imagesModel->patient_id=$patient_id;
                $imagesModel->save();    
            }
            

            $UserName = Patients::Join('images', 'images.patient_id', '=', 'patients.id') 
            ->where([
                ['patients.id','=',$patient_id]
            ])     
            ->select('patients.name')              
            ->get();

            event(new RecordActivity(Auth::user()->name.' cargó una nueva radiografía para el paciente '.$UserName->pluck('name'),
            'Images',null, true));

            return response()
             ->json([
                'saved' => true
             ]);
        }
            
        return response()
            ->json([
                'saved' => false
            ],422) ;
     
    }

    // Actualiza la radiografía
    public function update(Request $request, $id)
    {

        //$data = $request->all();
        $data = $request->except(['file_name']);

        $item = Images::findOrFail($id);
        $item['modified_by']=Auth::user()->id;
        $item->update($data);

        return response()
        ->json([
           'saved' => true
        ]);  
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
