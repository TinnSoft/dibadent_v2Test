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
use Illuminate\Support\Str;

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
            

            $UserName = Patients::where([
                ['patients.id','=',$patient_id]
            ])     
            ->select(DB::raw("CONCAT(IFNULL(patients.name,''),' ',IFNULL(patients.last_name,'')) as name"))              
            ->get();

            
            
            $adminID = DB::table('users')
                ->Join('profiles', 'users.profile_id', '=', 'profiles.id')        
                ->where([
                    ['profiles.id', 1]
                ])    
                ->where('isActive',1)   
                ->select('users.id')       
                ->first();
            
            
            $nameTrans=Str::upper(Auth::user()->name).' '.Str::upper(Auth::user()->last_name);
            event(new RecordActivity($nameTrans.' cargó '.count($request->files).' radiografía(s) para el paciente: '.Str::upper($UserName[0]->name),
            'Images',null, true, $adminID->id));

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

        return response()
        ->json([
            'deleted' => true
        ]);
    }

    public function getUsedStorage($id)
    {
        $namesImages=Images::where('created_by','=',$id);
        $size=0;
        foreach ($namesImages as $image)
        {
            $name=$image['file_name'];
            $size=$size +  Storage::size($name);
           
        }
        return [
            "size" =>$size
        ];
    }    
    

}
