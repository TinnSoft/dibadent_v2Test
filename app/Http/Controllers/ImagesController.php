<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Images;
use App\Models\Users;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use DB;
use Illuminate\Http\File;

class ImagesController extends Controller
{
    protected $rootFolderMain='/_Images/_radiology';
    protected $rootFolderGeneralPurposes='/_Images/_avatars';

    public function getImagesByProcedure($ProcedureId)
    {
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
    
    public function uploadAvatar(Request $request, $id_user)
    {
        //verifica si el directorio está creado
        Storage::makeDirectory($this->rootFolderGeneralPurposes);
        if (count($request->files)>0 && $id_user != null)              
        {         
            foreach($request->files as $_file) {
                $newFileName=Storage::putFile($this->rootFolderGeneralPurposes, new File($_file));
                $userModel= Users::find($id_user);
                $userModel->avatar=$newFileName;
                $userModel->save();
            }

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

}
