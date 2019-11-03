<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Mail\PasswordGenerated;
use Illuminate\Database\QueryException;
use Carbon\Carbon;
use App\Models\Users;
use App\Models\AcumulatedPointsLevels;
use App\Models\PatientsDoctors;
use App\Models\Patients;
use App\Models\PointsRedemption;
use Illuminate\Support\Str;
use DB;
use Illuminate\Support\Facades\Mail;


class UsersController extends Controller
{   
    private $doctor_code=3;
    private $radiologist_code=2;

    public function create()
    {
        return response()
        ->json([
            'form' => Users::initialize()
        ]);
    }

    public function show()
    {               
        return response()
        ->json([
        'form' =>  $this->getUserInfo()
        ]);         
    }

    public function getDoctorDashboardData()
    {   
        $dataAcumulatedPoints = AcumulatedPointsLevels::with('points_level')->where('user_id',  Auth::user()->id)        
        ->select('id','points_level_id','acumulated_points')              
        ->first();   

        $acumulatedPoints=0;
        $redeemedPointsLastYear= DB::table('points_redemption')->sum('points_redeemed');
        $pointsNextToBeat=0;
        $level='Aún no tienes Nivel';
        
        if($dataAcumulatedPoints)
        {
            $acumulatedPoints=$dataAcumulatedPoints->acumulated_points;
            $level=$dataAcumulatedPoints->points_level->level_name;
        }
        
       $patientsAsociatedToDoctor = PatientsDoctors::where('doctor_id', Auth::user()->id)->select('patient_id')->get();
       $dataPacientList = Patients::whereIn('id',  $patientsAsociatedToDoctor)
       ->select('id','id as value', DB::raw("CONCAT(patients.name,' ',patients.last_name) as label"),'home_address',
       'name','last_name','phone','email','comments')->get();

       //$dataProcedures=Procedures::where('doctor_id', Auth::user()->id)->select()-get();
       //'medicalProcedures'=> $dataProcedures

       $pointsSummary=['level'=>$level, 
                        'acumulatedPoints'=>$acumulatedPoints,
                        'redeemedPoints'=>$redeemedPointsLastYear,
                        'pointsNextToBeat'=>$pointsNextToBeat,
                        'prueba'=>$dataAcumulatedPoints
                    ];

       return response()
       ->json([
           'pointsSummary' => $pointsSummary,
           'patientList'=> $dataPacientList           
       ]);
    }
    
    public function getUserInfo()
    {               
        return Users::with('profile')->where('id',  Auth::user()->id)        
        ->select('id','name','last_name','email','password','birthday','home_address','phone','profile_id')              
        ->first();      
    }

    public function getDoctors()
    {   

        $data = DB::table('users')
        ->Join('profiles', 'users.profile_id', '=', 'profiles.id')        
        ->where([
            ['profiles.id',$this->doctor_code]
        ])    
        ->where('isActive',1)    
        ->whereNull('deleted_at')
        ->select('users.id',
        'users.name',
        'users.last_name',
        'users.email',
        'users.password',
        'users.birthday',
        'users.home_address',
        'users.phone',
        'users.created_at'
        )      
        ->orderBy('users.id','desc')        
        ->get()->toArray();

       return response()
       ->json([
           'records' => $data
       ]);
    }

    public function getRadiologist()
    {   

        $data = DB::table('users')
        ->Join('profiles', 'users.profile_id', '=', 'profiles.id')        
        ->where([
        ['profiles.id',$this->radiologist_code]
        ])    
        ->where('isActive',1)    
        ->whereNull('deleted_at')
        ->select('users.id',
        'users.name',
        'users.last_name',
        'users.email',
        'users.password',
        'users.birthday',
        'users.home_address',
        'users.phone',
        'users.created_at'
        )      
        ->orderBy('users.id','desc')        
        ->get()->toArray();

       return response()
       ->json([
           'records' => $data
       ]);
    }
    
    private static function getUserByID($id){

        return  Users::where('id',  $id)               
                ->select( 'id','name','email','last_name','home_address','birthday','phone')->first();  
    }

    public function edit($id)
    {
        $user = $this->getUserByID($id);
        
        return response()
        ->json([
            'form' =>  $user
        ]);         
    }

    public function sendEmail($data)
    {    

        $body='Cordial saludo, <br/><br/>';
        $body .= 'A continuación puedes visualizar sus credenciales de acceso a la herramienta:  <br/><br/>';
        $body .= 'Usuario: <strong>'.$data['email'].' </strong> <br/>';
        $body .= 'Contraseña: <strong>'.$data['password'].'</strong><br/><br/>';
        $body .= 'Para iniciar sesion ingrese a la siguiente ruta: www.prueba.com <br/><br/>';
        $body .= 'Cordialmente,';
        $body .= '<br/><br/><br/>';
        $body .= 'Equipo Administrativo<br/>';

      
        Mail::to($data['email'])
            ->send(new PasswordGenerated($body));
    }

    public function store(Request $request)
    {                
        $this->validate($request, [     
            'name' => 'required',
            'email' => 'required'
            ]);        
        
        $randomPassword = Str::random(6);      
        
        $data = $request->all();          
        $_process = collect($data)->get('_process');     
        
        $data['profile_id']=  $_process=='DOCTOR' ? $this->doctor_code : $this->radiologist_code;
       
        $data['created_by'] = Auth::user()->id;
        $data['password'] = bcrypt($randomPassword);
        
        //email validation
        $checkIfExistEmail = Users::where('email',$data['email'])->get(); 
       
        if ($checkIfExistEmail->isNotEmpty())
        {
            return response()
            ->json([
                'emailAlreadyExists' => 'La dirección de correo ingresada ya existe, intente con una diferente'
            ], 422);
        }

        $item = Users::create($data);

        $emailData['email'] = $data['email'];
        $emailData['password'] =$randomPassword;
      
        $this->sendEmail($emailData);

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
        
        $newUserValues = $request->all();         
       
        if (collect($newUserValues)->isEmpty()) {
            return response()
            ->json([
                'custom_message' => 'Debe ingresar por lo menos un valor en el formulario'
            ], 422);
        };

        $newUserValues['modified_by'] = Auth::user()->id;
        $item = Users::findOrFail($id);
        $item->update($newUserValues);
                
        return response()
        ->json([
            'updated' => true,
            'id' => $item->id             
        ]);
    }
    
    public function destroy($id)
    {   
        $post = Users::find($id);
        $post->delete();

        return response()
        ->json([
            'deleted' => true
        ]);
    }
}
