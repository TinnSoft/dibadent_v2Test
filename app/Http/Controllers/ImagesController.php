<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Images;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use DB;
use Illuminate\Http\File;

class ImagesController extends Controller
{
    protected $rootFolderMain='/public/_Images/_radiology';
    protected $rootFolderGeneralPurposes='/public/_Images/_general';

    public function getImagesByProcedure($ProcedureId)
    {
       $data= Images::where('procedure_id', $ProcedureId)
       ->select('id','title','file_name','other_details')->get();      
      

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

}
