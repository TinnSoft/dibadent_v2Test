<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Carbon\Carbon;
use App\Models\Images;
use Illuminate\Support\Facades\Storage;
use DB;

class ImagesController extends Controller
{

    protected $rootFolderMain='public/storage/_Images/_radiology';
    protected $rootFolderGeneralPurposes='public/storage/_Images/_general';

    public function getImagesByProcedure($ProcedureId)
    {
       $data= Images::where('procedure_id', $ProcedureId)
       ->select('id','title','file_name','other_details')->get();      

       return response()
       ->json([
          'images' => $data,          
       ]);

    }

    

    public function upload(Request $request)
    {
        //verifica si el directorio está creado
        Storage::makeDirectory($this->rootFolderMain);
       

       // if ($request->hasfile('file') && $request->file('file')->isValid()) {
            
        //     $originalName=$request->file->getClientOriginalName();
        //     $model=$request->all()['model'];
        //     $publicID=$request->all()['publicID'];
        //     $lastfilename=Storage::putFile($this->rootFolder, $request->file('file'));

        //     $imagesModel= new Images;
        //     $imagesModel->user_id=Auth::user()->id;
        //     $imagesModel->model_id=$publicID;
        //     $imagesModel->model=$model;
        //     $imagesModel->filename=$originalName;
        //     $imagesModel->new_filename=$lastfilename;
        //     $imagesModel->save();

        //     return response()
        //     ->json([
        //        'saved' => true,
        //        'list' => $this->documentList($publicID, $model)
        //     ]);
        // }

        return response()
        ->json([
            'saved' => true
        ], 422);
    }

}
