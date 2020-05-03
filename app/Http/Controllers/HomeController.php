<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       // return view('home');
    }


    public function getDashboardInfo()
    {
        $sumOfImages=[];
        $_ImagesByDoctor=[];
        
        
            //$mainClass=new HomeController();

            //procedimientos por mes y año consolidado
            $sumOfImages= collect(["images_lastMonth"=>$this->getQuantityOfImages('m'),
            "images_lastYear"=>$this->getQuantityOfImages('y'),
            ]);
            
            //cantidad de procedimientos por doctor (año, mes, semana, dia)
            $imagesByDoctor_values_day= $this->getImagesLoadedByDoctor('d');

            $_ImagesByDoctor=collect(
                ["today_ImagesByDoctor_qty"=>collect($imagesByDoctor_values_day->pluck('quantity')),
                "today_ImagesByDoctor_labels"=>collect($imagesByDoctor_values_day->pluck('name')),
                "procedures_bydoctor_weekly_data"=>null,//$mainClass->getQuantityOfProceduresByDoctor_weekly()
            ]);

      
        return response()
        ->json([
        'images_sum' => $sumOfImages,
        'images_ByDoctor'=> $_ImagesByDoctor,
        'tracking_Doctors'=> $this->getDoctorsTrack(),
        'topRedemedPoints'=>$this->getTopRedemedPoints()
        ]);
    }

    private static function getTopRedemedPoints()
    {  
       
        $data= DB::table('points_redemption')
        ->join('users', function ($join) {
            $join->on('users.id', '=', 'points_redemption.user_id');
        })
        ->select(DB::raw("SUM(points_redemption.points_redeemed) as points_redeemed, CONCAT(users.name,' ', users.last_name) as name"))
        ->groupBy('points_redemption.user_id')
        ->orderBy('points_redeemed', 'desc')        
        ->take(5)
        ->get();

        $dataFinal['labels']=collect($data)->pluck('name')->all();
        $dataFinal['data']=collect($data)->pluck('points_redeemed')->all();
        
        return $dataFinal;
    }

    //retorna los movimientos realizados por los doctores
    private static function getDoctorsTrack()
    {
        $data= DB::table('tracker')
        ->join('users', function ($join) {
            $join->on('users.id', '=', 'tracker.user_id');
        })
        ->join('profiles', function ($join) {
            $join->on('profiles.id', '=', 'users.profile_id');
        })
        ->where('profiles.description','=', 'DOCTOR')  
        ->select('tracker.id','tracker.detail', 'tracker.created_at')
        ->orderBy('tracker.id', 'desc')
        ->take(100)
        ->get();
        
        return $data;
    }

    private static function getQuantityOfImages($filter)
    {
        $data= DB::table('images')
        ->when($filter=='m', function ($query) {
            return $query->whereYear('images.created_at', date('Y'))->whereMonth('images.created_at', '=', date('m'));
        })
        ->when($filter=='y', function ($query) {
            return $query->whereYear('images.created_at', '=', date('Y'));
        })
        ->whereNull('deleted_at')
        ->count('id');

        return $data;
    }
    
    private static function getImagesLoadedByDoctor($filter)
    {

        $data= DB::table('images')
        ->join('users', function ($join) {
            $join->on('users.id', '=', 'images.created_by');
        })
        ->join('patients', function ($join) {
            $join->on('patients.doctor_id', '=', 'users.id');
        })
        ->when($filter=='d', function ($query) {
            return $query->whereYear('images.created_at', date('Y'))->whereDay('images.created_at', '=', date('d'))
            ->select(DB::raw("count(images.id) as quantity,  CONCAT(users.name,' ', users.last_name) as name"));
        }) 
        ->whereNull('images.deleted_at')
        ->groupBy('users.id')              
        ->take(10)
        ->get();
        
        return $data;
    }


    //retorna el top 10 de doctores y su cantidad de procedimientos realizados durante determinado periodo de tiempo
    private static function getQuantityOfProceduresByDoctor_day($fieldtoFilter)
    {
        $data= DB::table('images')
        ->join('users', function ($join) {
            $join->on('users.id', '=', 'images.doctor_id');
        })
        ->select(DB::raw("count(images.id) as quantity,  CONCAT(users.name,' ', users.last_name) as name"))
        ->whereYear('images.created_at', '=', date('Y'))->whereDay('images.created_at', '=', date('d'))
        ->groupBy('images.patient_id')
        ->orderBy('quantity', 'desc')
        ->take(10)
        ->get();

        return array($data->pluck($fieldtoFilter));
    }

    private static function getQuantityOfProceduresByDoctor_Weekly()
    {

        //SELECT p.doctor_id,u.name, DAYOFWEEK(p.created_at) AS dia, COUNT(p.id) cantidad FROM procedures p 
        //INNER JOIN users u ON u.id=p.doctor_id GROUP BY p.doctor_id, dia ORDER BY dia

        $data= DB::table('procedures')
        ->join('users', function ($join) {
            $join->on('users.id', '=', 'procedures.doctor_id');
        })
        ->select(DB::raw("procedures.doctor_id, DAYOFWEEK(procedures.created_at) as day, count(procedures.id) as quantity, 
        CONCAT(users.name,' ', users.last_name) as name"))
        //->whereYear('procedures.created_at', '=', date('Y'))->whereDay('procedures.created_at', '=', date('d'))
        ->groupBy('procedures.doctor_id','day','users.name','users.last_name')
        ->orderBy('procedures.doctor_id', 'desc')
        ->take(7)
        ->get();

        $WeekDataArray=[];
        $WeekDataArray_out=[];        
        /*
        while($startDate<= $endDate)
        {
            $WeekDataArray2=$mainClass->FilterDataByArray($dtabyWeek,$startDate,'day');;
            $WeekDataArray2_out=$mainClass->FilterDataByArray($dtabyWeek_outcome,$startDate,'day');
            
            $WeekDataArray[]=(count($WeekDataArray2))>0?$WeekDataArray2:0;
            $WeekDataArray_out[]=(count($WeekDataArray2_out))>0?$WeekDataArray2_out:0;
            
            $startDate->addDay();
        }
        */
        return array($data);
    }

    
}
