<?php

namespace App\Http\Controllers;
use Auth;
use Carbon\Carbon;
use App\Models\Patients;
use DB;

use Illuminate\Http\Request;

class PatientsController extends Controller
{

    public function create()
    {
        return response()
        ->json([
            'form' => Patients::initialize(),
            'genders'=> $this->getGenders()
        ]);
    }

    public function show()
    {         
        $PatientValue = Patients::where('id',  Auth::user()->id)        
        ->select('id','name','gender_id','last_name','email','birthday','home_address','phone','comments')              
        ->first();

        return response()
        ->json([
        'form' =>  $PatientValue,
        'genders'=> $this->getGenders()
        ]);         
    }

    public function getGenders()
    {
        return DB::table('genders')
            ->select('genders.id as value', 'genders.description as label')->get()->toArray();
    }

    public function getPatients()
    {   

        $data = DB::table('patients')
        ->LeftJoin('genders', 'patients.gender_id', '=', 'genders.id')     
        ->whereNull('deleted_at')
        ->select('patients.id',
        'patients.name',
        'patients.last_name',
        'patients.email',
        'patients.birthday',
        'patients.home_address',
        'patients.phone',
        'patients.comments',
        'patients.created_at'
        )      
        ->orderBy('patients.id','desc')        
        ->get()->toArray();

       return response()
       ->json([
           'records' => $data
       ]);
    }

    private static function getPatientByID($id){

        return  Patients::where('id',  $id)               
                ->select( 'id','name','gender_id','email','last_name','home_address','birthday','phone','comments')->first();  
    }

    public function edit($id)
    {
        $data = $this->getPatientByID($id);
        
        return response()
        ->json([
            'form' =>  $data,
            'genders'=> $this->getGenders()
        ]);         
    }

 
    public function store(Request $request)
    {                
        $this->validate($request, [     
            'name' => 'required',
            'email' => 'required'
            ]);        
        
        $data = $request->all();           
       
        $data['created_by'] = Auth::user()->id;    
        $data['user_id'] = Auth::user()->id;
        
        //email validation
        $checkIfExistEmail = Patients::where('email',$data['email'])->get(); 
       
        if ($checkIfExistEmail->isNotEmpty())
        {
            return response()
            ->json([
                'emailAlreadyExists' => 'La dirección de correo ingresada ya existe, intente con una diferente'
            ], 422);
        }        

        $item = Patients::create($data);

        $emailData['email'] = $data['email'];
      
        return response()
            ->json([
                'created' => true,
                'id' => $item->id
            ]);
    }


    public function update(Request $request, $id)
    {   
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required'
        ]);      

        //pendiente adicionar validación para que genere alerta de cuando el email ya está creado y/o es diferente al original
        
        $newPatientValues = $request->all();         
       
        if (collect($newPatientValues)->isEmpty()) {
            return response()
            ->json([
                'custom_message' => 'Debe ingresar por lo menos un valor en el formulario'
            ], 422);
        };

        $newPatientValues['modified_by'] = Auth::user()->id;
        $item = Patients::findOrFail($id);
        $item->update($newPatientValues);
                
        return response()
        ->json([
            'updated' => true,
            'id' => $item->id             
        ]);
    }
    
    public function destroy($id)
    {   
        $post = Patients::find($id);
        $post->delete();

        return response()
        ->json([
            'deleted' => true
        ]);
    }
}
