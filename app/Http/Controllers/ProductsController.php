<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Events\RecordActivity;
use App\Models\Products;
use Illuminate\Database\QueryException;
use Auth;

class ProductsController extends Controller
{
    public function getProducts()
    {   

        $data = DB::table('products') 
        ->whereNull('deleted_at')
        ->select('id',
        'description',
        'required_points'
        )      
        ->orderBy('id','desc')        
        ->get()->toArray();

       return response()
       ->json([
           'records' => $data
       ]);
    }

    private static function getProductByID($id){

        return  Products::where('id',  $id)               
                ->select( 'id','description','required_points')->first();  
    }

    public function create()
    {
        return response()
        ->json([
            'form' => Products::initialize()
        ]);
    }

    public function edit($id)
    {
        $data = $this->getProductByID($id);
        
        return response()
        ->json([
            'form' =>  $data
        ]);         
    }

    public function store(Request $request)
    {                
        $this->validate($request, [     
            'description' => 'required',
            ]);        
           
        
        $data = $request->all();      
        $data['created_by'] = Auth::user()->id;
             
        $item = Products::create($data);

        event(new RecordActivity(Auth::user()->name.' creó el producto '.$item->description,
        'Products',null, true));

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
            ]);        

       
        $newProductValues = $request->all();         
       
        if (collect($newProductValues)->isEmpty()) {
            return response()
            ->json([
                'custom_message' => 'Debe ingresar por lo menos un valor en el formulario'
            ], 422);
        };

        $newProductValues['modified_by'] = Auth::user()->id;
        $item = Products::findOrFail($id);
        $item->update($newProductValues);
        
        event(new RecordActivity(Auth::user()->name.' actualizó el producto '.$item->description,
        'Products',null, true));

        return response()
        ->json([
            'updated' => true,
            'id' => $item->id             
        ]);
    }
    
    public function destroy($id)
    {   
        $post = Products::find($id);
        $post->delete();

        event(new RecordActivity(Auth::user()->name.' eliminó el producto '.$post->description,
        'Products',null, true));

        return response()
        ->json([
            'deleted' => true
        ]);
    }

}
