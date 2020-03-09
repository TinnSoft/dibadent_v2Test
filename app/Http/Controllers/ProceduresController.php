<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Models\Procedures;
use DB;
use App\Events\RecordActivity;

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

    public function create()
    {
        return response()
        ->json([
            'form' => Procedures::initialize(),
            'products'=>$this->getProducts(),
            'radiologists'=>$this->getRadiologists()
        ]);
    }

    public function getProducts()
    {
        return DB::table('products')
        ->whereNull('deleted_at')
        ->select('products.id as value', 'products.description as label')->get()->toArray();
    }

    public function getRadiologists()
    {
        return DB::table('users')
        ->where('profile_id','=',  2) 
        ->whereNull('deleted_at')
        ->select('users.id as value', 'users.name as label')->get()->toArray();
    }

    public function show()
    {         
        return response()
        ->json([
        'form' =>  $this->getProcedureByID(Auth::user()->id),
        ]);         
    }

    private static function getProcedureByID($id){

        return  Procedures::with('radiologist','product')->where('id',  $id)               
                ->select( 'id','doctor_id','product_id','radiologist_id','procedure_date','comments','description')->first();  
    }

    public function edit($id)
    {        
        return response()
        ->json([
            'form' =>  $this->getProcedureByID($id),
            'products'=>$this->getProducts(),
            'radiologists'=>$this->getRadiologists()
        ]);         
    }

 
    public function store(Request $request)
    {              

        $this->validate($request, [     
            'description' => 'required',
            'product_id' => 'required',
            'patient_id' => 'required',
            'radiologist_id' => 'required',
            'procedure_date' => 'required'
            ]);   
            
        $data = $request->all();           
        $data['created_by'] = Auth::user()->id;    
        $data['doctor_id'] = Auth::user()->id; 

        $item = Procedures::create($data);

        event(new RecordActivity(Auth::user()->name.' creó un nuevo procedimiento',
        'Procedures',null));
      
        return response()
            ->json([
                'created' => true,
                'id' => $item->id
            ]);
    }


    public function update(Request $request, $id)
    {   
        $this->validate($request, [     
            'description' => 'required',
            'product_id' => 'required',
            'patient_id' => 'required',
            'radiologist_id' => 'required',
            'procedure_date' => 'required'
            ]);      
        
        $data = $request->all();           
        $data['modified_by'] = Auth::user()->id;    
        $data['doctor_id'] = Auth::user()->id;           
   
        $item = Procedures::findOrFail($id);
        $item->update($data);
        
        event(new RecordActivity(Auth::user()->name.' actualizó un procedimiento',
        'Procedures',null));


        return response()
        ->json([
            'updated' => true,
            'id' => $item->id             
        ]);
    }
    
    public function destroy($id)
    {   
        $post = Procedures::find($id);
        $post->delete();

        event(new RecordActivity(Auth::user()->name.' eliminó un procedimiento',
        'Procedures',null));

        return response()
        ->json([
            'deleted' => true
        ]);
    }

  
}
