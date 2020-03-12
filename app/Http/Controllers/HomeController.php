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
        $quantityOfProcedures=[];
        $quantityOfProceduresByDoctor=[];
        
        if(Auth::check()) {
            $mainClass=new HomeController();

            //procedimientos por mes y año consolidado
            $quantityOfProcedures= collect(["procedures_lastMonth"=>$mainClass->getQuantityOfProcedures('m'),
            "procedures_lastYear"=>$mainClass->getQuantityOfProcedures('y'),
            ]);
            
            //cantidad de procedimientos por doctor (año, mes, semana, dia)
            $quantityOfProceduresByDoctor=collect(
                ["procedures_bydoctor_today_qty"=>$mainClass->getQuantityOfProceduresByDoctor_day('quantity'),
                "procedures_bydoctor_today_labels"=>$mainClass->getQuantityOfProceduresByDoctor_day('name'),
                "procedures_bydoctor_weekly_data"=>$mainClass->getQuantityOfProceduresByDoctor_weekly()
            ]);

        }
        return response()
        ->json([
        'procedures_sum' => $quantityOfProcedures,
        'procedures_ByDoctor'=> $quantityOfProceduresByDoctor
        ]);
    }

    private static function getQuantityOfProcedures($daterange)
    {
        $total= DB::table('procedures')
        ->when($daterange=='m', function ($query) {
            return $query->whereYear('created_at', date('Y'))->whereMonth('created_at', '=', date('m'));
        })
        ->when($daterange=='y', function ($query) {
            return $query->whereYear('created_at', '=', date('Y'));
        })
        ->count('id');
        
        return $total;
    }
    
    //retorna el top 10 de doctores y su cantidad de procedimientos realizados durante determinado periodo de tiempo
    private static function getQuantityOfProceduresByDoctor_day($fieldtoFilter)
    {
        $data= DB::table('procedures')
        ->join('users', function ($join) {
            $join->on('users.id', '=', 'procedures.doctor_id');
        })
        ->select(DB::raw("count(procedures.id) as quantity,  CONCAT(users.name,' ', users.last_name) as name"))
        //->whereYear('procedures.created_at', '=', date('Y'))->whereDay('procedures.created_at', '=', date('d'))
        ->groupBy('procedures.doctor_id','users.name')
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
        ->groupBy('procedures.doctor_id','day')
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
