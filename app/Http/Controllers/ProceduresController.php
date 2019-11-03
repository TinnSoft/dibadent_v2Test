<?php

namespace App\Http\Controllers;


use Auth;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Models\Procedures;

class ProceduresController extends Controller
{
    
    public function getProceduresByPatientAndDoctor($PatientId)
    {
       $data= Procedures::where('doctor_id', Auth::user()->id)
                        ->where('patient_id',$PatientId)->select('id','description','id as value','description as label')->get();      

        return response()
            ->json([
               'procedures' => $data,          
            ]);
                     
    }

  
}
