<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Carbon\Carbon;
use App\Models\Users;
use DB;

class UsersController extends Controller
{   


    public function create()
    {
        return response()
        ->json([
            'form' => Users::initialize()
        ]);
    }
        
    public function store(Request $request)
    {                
       
    }


    public function show()
    {               

        $UserValue = Users::where('id',  Auth::user()->id)        
        ->select('id','name','last_name','email','password','birthday','home_address','phone')              
        ->first();

        return response()
        ->json([
        'form' =>  $UserValue
        ]);         
    }

    public function getDoctors()
    {   

    }
    

    public function update(Request $request, $id)
    {   
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required'
        ]);      
        
        $newUserValues = $request->all();         
       
        if (collect($newUserValues)->isEmpty()) {
            return response()
            ->json([
                'custom_message' => 'Debe ingresar por lo menos un valor en el formulario'
            ], 422);
        };
      
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

    }
}
