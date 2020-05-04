<?php

namespace App\Http\Controllers;
use Auth;
use Carbon\Carbon;
use App\Models\Patients;
use App\Models\Users;
use App\Models\PatientsDoctos;
use DB;
use App\Events\RecordActivity;
use Illuminate\Http\Request;

class PatientsController extends Controller
{

    public function create()
    {
        return response()
        ->json([
            'form' => Patients::initialize(),
            'genders'=> $this->getGenders(),
            'doctorList'=> $this->getDoctors(),
        ]);
    }

    public function show()
    {         
        return response()
        ->json([
        'form' =>  $this->getPatientByID(Auth::user()->id),
        'genders'=> $this->getGenders(),
        'doctorList'=> $this->getDoctors(),
        ]);         
    }
    public function getDoctors()
    {
        return DB::table('users')      
        ->select('users.id as value', DB::raw("CONCAT(IFNULL(users.name,users.email,''),' ',IFNULL(users.last_name,'')) as label"),
        DB::raw("CONCAT('CC: ',IFNULL(users.identification_number,'')) as description"))->get()->toArray()
        ->where('profile_id','=',  3) 
        ->whereNull('deleted_at');
    }
    public function getGenders()
    {
        return DB::table('genders')
            ->select('genders.id as value', 'genders.description as label')->get()->toArray();
    }

    public function getPatientsAndDoctors()
    {   
        $data = patients::whereNull('patients.deleted_at')
        ->Join('users', 'patients.doctor_id', '=', 'users.id')
        ->Join('profiles', 'users.profile_id', '=', 'profiles.id')
        ->select('patients.id as value', DB::raw("CONCAT(IFNULL(patients.name,''),' ',IFNULL(patients.last_name,'')) as label"),
        DB::raw("CONCAT(IFNULL(users.name,''),' ',IFNULL(users.last_name,'')) as doctor_name"),'patients.doctor_id', 'users.identification_number','users.avatar')      
        ->orderBy('patients.id','desc')        
        ->get();

        $imagescreatedQTY= DB::table('images')
        ->whereYear('images.created_at', '=', date('Y'))
        ->where('images.created_by',"=", Auth::user()->id)
        ->whereNull('deleted_at')
        ->count('id');

       return response()
       ->json([
           'patients' => $data,
           'images_created_qty'=>$imagescreatedQTY
       ]);
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

        return  Patients::with('gender','doctor')->where('id',  $id)               
                ->select( 'id','name','gender_id','doctor_id','email','last_name','home_address','birthday','phone','comments')->first();  
    }

    public function edit($id)
    {
        $data = $this->getPatientByID($id);
        
        return response()
        ->json([
            'form' =>  $data,
            'genders'=> $this->getGenders(),            
            'doctorList'=> $this->getDoctors(),
        ]);         
    }

 
    public function store(Request $request)
    {                
        $this->validate($request, [     
            'name' => 'required'
            ]);        
        
        $data = $request->all();           
        $data['created_by'] = Auth::user()->id;    
        $data['user_id'] = Auth::user()->id;
        $data['gender_id'] = $request->input('gender.value');
        $data['doctor_id'] = $request->input('doctor.value');
        
        $item = Patients::create($data);

        $emailData['email'] = $data['email'];
        
        
        event(new RecordActivity(Auth::user()->name.' creó el paciente '.$item->name,
        'Patients',null, true));

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
        ]);      

      
        //pendiente adicionar validación para que genere alerta de cuando el email ya está creado y/o es diferente al original
        
        $newPatientValues = $request->except(['gender']);         
       
        if (collect($newPatientValues)->isEmpty()) {
            return response()
            ->json([
                'custom_message' => 'Debe ingresar por lo menos un valor en el formulario'
            ], 422);
        };

        
        $newPatientValues['modified_by'] = Auth::user()->id;
        $newPatientValues['gender_id'] = $request->input('gender.value');
        $newPatientValues['doctor_id'] = $request->input('doctor.value');
   

        $item = Patients::findOrFail($id);
        $item->update($newPatientValues);
        
        event(new RecordActivity(Auth::user()->name.' actualizó el paciente '.$item->name,
        'Patients',null, true));

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

        event(new RecordActivity(Auth::user()->name.' eliminó el paciente '.$post->name,
        'Patients',null, true));

        return response()
        ->json([
            'deleted' => true
        ]);
    }
}
