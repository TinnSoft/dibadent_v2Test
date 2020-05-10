<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PointsRedemption;
use App\Models\AcumulatedPointsLevels;
use App\Models\Products;
use Auth;
use Illuminate\Support\Str;
use App\Events\RecordActivity;
use DB;

class PointsRedemptionController extends Controller
{
    public function redemptPoint($product_id)
    {   
        //$id= id del producto que va a ser redimido
        if ($product_id)
        {

            
             //1. Obtener valores base de puntos
            $pointsToDecrement = Products::where('id',$product_id )->select('required_points','description')->get()->toArray();

             //2. Descontar puntos en la tabla accumulated_points_levels
            DB::table('acumulated_points_levels')->where('user_id', Auth::user()->id)->decrement('acumulated_points', $pointsToDecrement[0]['required_points']);

            //4. Generar codigo aleatorio para reclamar los puntos y verificar si existe en la tabla creado
            $redemptionCodeStr = Str::upper(Str::random(7));

            //5. Actualizar tabla Points_redemption  
            $data['user_id'] = Auth::user()->id;
            $data['product_id'] = $product_id;
            $data['points_redeemed'] = $pointsToDecrement[0]['required_points'];
            $data['code'] = $redemptionCodeStr;
            $data['is_code_confirmed'] = false;
            $data['created_by'] =  Auth::user()->id;
            $item = PointsRedemption::create($data);

             //3. Agregar logs
            event(new RecordActivity(Auth::user()->name.' redimió el producto '.Str::upper($pointsToDecrement[0]['description']).' de '.$pointsToDecrement[0]['required_points'].'puntos',
            'points_redepmtion',null, true));

            //6. Retornar mensaje
            return response()
            ->json([
                'created' => true,
                'redemptionCode' => $redemptionCodeStr          
            ]);
        }     

        return response()
        ->json([
            'updated' => false            
        ],422);

        
    }

    public function getProductRedemptionHistory(){
        
        $data = DB::table('products')
            ->Join('points_redemption', 'products.id', '=', 'points_redemption.product_id')    
            ->Join('users', 'users.id', '=', 'points_redemption.user_id')        
            ->whereNull('points_redemption.deleted_at')
            ->select('products.description','points_redemption.points_redeemed','points_redemption.id',
            'points_redemption.code','points_redemption.is_code_confirmed as state','points_redemption.created_at',
            'points_redemption.updated_at', DB::raw("CONCAT(IFNULL(users.name,''),' ',IFNULL(users.last_name,'')) as doctor_name")
            )      
            ->orderByRaw('points_redemption.is_code_confirmed  ASC')      
            ->get();
        
        return response()
        ->json([
            'redemptionHistory' => $data
        ]);
    }

}
