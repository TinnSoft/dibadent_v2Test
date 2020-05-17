<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Collection;

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
 
        $sumOfImages= collect(["images_lastMonth"=>$this->getQuantityOfImages('m'),
            "images_lastYear"=>$this->getQuantityOfImages('y')]);
      
        $imagesByDoctor_values_day= $this->getImagesLoadedByDoctor('d');
   
        $backgroundColor= [
            "#126A8C",
            "#369DC4",
            "#2BBAF0",
            "#136DF0",
            "#9FC5FC",
            "#88B3F0",
            "#99A1DF",
            "#132AED",
            "#13C5ED",
            "#42C8D8"
        ];

        //Chart semanal
         //Labels De los ultimos 7 días de la semana
         $week_ImagesByDoctor_labels= collect([]);
       
         $startDate = today()->subDays(7);
         $endDate = today(); 
         while($startDate<= $endDate)
         {                    
            $week_ImagesByDoctor_labels->push($startDate->locale('es')->getTranslatedDayName());
            $startDate->addDay();
         }
        
         
        //Obtener lista de doctores (top 10) con mas carga de imágenes
        $weekly_topDoctors= DB::table('images')
        ->join('patients', function ($join) {
            $join->on('patients.id', 'images.patient_id');
        })
        ->join('users', function ($join) {
            $join->on('users.id', 'patients.doctor_id');
        })
        ->select('images.patient_id',DB::raw("CONCAT(IFNULL(users.name,''),' ',IFNULL(users.last_name,'')) as label, COUNT(images.id) as quantityOfImages"))
        ->whereNull('images.deleted_at')
        ->groupBy('images.patient_id','patients.doctor_id','users.name','users.last_name')
        ->where('images.created_at', '>=', today()->subDays(7))
        ->orderBy('quantityOfImages', 'desc')        
        ->take(10)
        ->get();

        $weekly_topDoctors = collect($weekly_topDoctors);     

        foreach ($weekly_topDoctors as $key => $row) {  
            $row->backgroundColor=$backgroundColor[$key];
            $row->data=$this->getLastSevenDaysData($row->patient_id);
        }

        
        $_ImagesByDoctor=collect(
            ["today_ImagesByDoctor_qty"=>collect($imagesByDoctor_values_day->pluck('quantity')),
            "today_ImagesByDoctor_labels"=>collect($imagesByDoctor_values_day->pluck('name')),
            "week_ImagesByDoctor_labels"=>$week_ImagesByDoctor_labels
        ]);
      
        return response()
        ->json([
        'images_sum' => $sumOfImages,
        'images_ByDoctor'=> $_ImagesByDoctor,
        'tracking_Doctors'=>$this->getDoctorsTrack(),
        'topRedemedPoints'=>$this->getTopRedemedPoints(),
        'weekly_doctorsData'=>$weekly_topDoctors,
        'backgroundColors'=>$backgroundColor
        ]);
    }

    private function getLastSevenDaysData($patiendID)
    {
        $startDate = today()->subDays(7);
        $endDate = today(); 

        $basedata = DB::table('images')        
        ->select(DB::raw("COUNT(images.id) as quantityOfImages, date(images.created_at) as dateCreation"))
        ->whereNull('images.deleted_at')
        ->where('images.patient_id', $patiendID)
        ->where('images.created_at', '>=', today()->subDays(7))
        ->groupBy('images.patient_id','dateCreation')        
        ->orderBy('quantityOfImages', 'desc')        
        ->take(10)
        ->get();

        $data = collect([]);
        while($startDate<= $endDate)
        {                   
                $_resultval = $basedata->firstWhere('dateCreation', '==',  $startDate->format('Y-m-d'));            
                $_resultval = (is_null($_resultval)) ? 0 : $_resultval->quantityOfImages;
            
                $data->push($_resultval);
                $startDate->addDay();
        }

         return $data;
    }

    private static function getTopRedemedPoints()
    {  
       
        $data= DB::table('points_redemption')
        ->join('users', function ($join) {
            $join->on('users.id', '=', 'points_redemption.user_id');
        })
        ->select(DB::raw("SUM(points_redemption.points_redeemed) as points_redeemed, CONCAT(IFNULL(users.name,''),' ',IFNULL(users.last_name,'')) as name"))
        ->groupBy('points_redemption.user_id','users.name','users.last_name')
        ->orderBy('points_redeemed', 'desc')        
        ->take(5)
        ->get();

        $dataFinal['labels']=collect($data)->pluck('name')->all();
        $dataFinal['data']=collect($data)->pluck('points_redeemed')->all();
        
        return $dataFinal;
    }

    //retorna los movimientos realizados por los doctores
    private  function getDoctorsTrack()
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

    private  function getQuantityOfImages($filter)
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
    
    private  function getImagesLoadedByDoctor_transformToweekly($filter){
        $startDate = Carbon::now()->startOfWeek();
        $endDate = Carbon::now()->endOfWeek();

        $testData=[];
        while($startDate<= $endDate)
        {
            $testData['dia']=$startDate;
            $startDate->addDay();
        }
        
        /*$WeekDataArray=[];
        $WeekDataArray_out=[];
        
        
        while($startDate<= $endDate)
        {
            $WeekDataArray2=$mainClass->FilterDataByArray($dtabyWeek,$startDate,'day');;
            $WeekDataArray2_out=$mainClass->FilterDataByArray($dtabyWeek_outcome,$startDate,'day');
            
            $WeekDataArray[]=(count($WeekDataArray2))>0?$WeekDataArray2:0;
            $WeekDataArray_out[]=(count($WeekDataArray2_out))>0?$WeekDataArray2_out:0;
            
            $startDate->addDay();
        }*/
    }
    private  function getImagesLoadedByDoctor($filter)
    {

        $data= DB::table('images')        
        ->join('patients', function ($join) {
            $join->on('patients.id', '=', 'images.patient_id');
        })
        ->join('users', function ($join) {
            $join->on('users.id', '=', 'patients.doctor_id');
        })
        ->when($filter=='d', function ($query) {
            return $query->whereYear('images.created_at', date('Y'))->whereDay('images.created_at', date('d'))
            ->select(DB::raw("count(images.id) as quantity,  CONCAT(IFNULL(users.name,''),' ',IFNULL(users.last_name,'')) as name, users.id"))
            ->orderBy('quantity', 'desc')
            ->whereNull('images.deleted_at')
            ->groupBy('name','users.id');  
        })      
        ->when($filter=='w', function ($query) {
            return $query->whereYear('images.created_at', date('Y'))->where(DB::Raw('week(images.created_at)'), Carbon::now()->weekOfYear)
            ->select(DB::raw("count(images.id) as quantity,  CONCAT(IFNULL(users.name,''),' ',IFNULL(users.last_name,'')) as name, DAY(images.created_at) as created_day, users.id"))
            ->orderBy('quantity', 'desc')
            ->whereNull('images.deleted_at')
            ->groupBy('name','created_day','users.id');  
        })          
        ->take(10)
        ->get();
        
        return $data;
    }


    //retorna el top 10 de doctores y su cantidad de procedimientos realizados durante determinado periodo de tiempo
    private  function getQuantityOfProceduresByDoctor_day($fieldtoFilter)
    {
        $data= DB::table('images')
        ->join('users', function ($join) {
            $join->on('users.id', '=', 'images.doctor_id');
        })
        ->select(DB::raw("count(images.id) as quantity,  CONCAT(IFNULL(users.name,''),' ',IFNULL(users.last_name,'')) as name"))
        ->whereYear('images.created_at', '=', date('Y'))->whereDay('images.created_at', '=', date('d'))
        ->groupBy('images.patient_id')
        ->orderBy('quantity', 'desc')
        ->take(10)
        ->get();

        return array($data->pluck($fieldtoFilter));
    }

    private  function getQuantityOfProceduresByDoctor_Weekly()
    {

        //SELECT p.doctor_id,u.name, DAYOFWEEK(p.created_at) AS dia, COUNT(p.id) cantidad FROM procedures p 
        //INNER JOIN users u ON u.id=p.doctor_id GROUP BY p.doctor_id, dia ORDER BY dia

        $data= DB::table('procedures')
        ->join('users', function ($join) {
            $join->on('users.id', '=', 'procedures.doctor_id');
        })
        ->select(DB::raw("procedures.doctor_id, DAYOFWEEK(procedures.created_at) as day, count(procedures.id) as quantity, 
        CONCAT(IFNULL(users.name,users.email),' ',IFNULL(users.last_name,'')) as name"))
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
