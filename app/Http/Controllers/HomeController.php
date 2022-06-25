<?php

namespace App\Http\Controllers;

use App\Models\LeadClient;
use App\Models\Monitorization;
use Illuminate\Http\Request;
use App\Models\ManageListings;
use App\Models\Developer;
use App\Models\Features;
use App\Models\Milestones;
use App\Models\ManageProject;
use App\Models\User;
use Carbon;
use Auth;
use Illuminate\Support\Facades\DB;

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
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $mytime = Carbon\Carbon::now();

        $agents = User::where('role',2)->count();
        $associate = User::where('role',3)->count();
        $developer = Developer::where('date', '>=', $mytime)->count();
        $pending_contracts = Developer::where('date', '<=', $mytime)->count();
        $projects = ManageProject::where(['ready_status' => 0, 'sold_out_status' => 0])->count();

        $outdated_projects = ManageProject::whereHas('projectReminders',function($query){
            $query->whereDate('reminder_date','<=',Carbon\Carbon::now('Europe/Stockholm'))->where('status',0);
        })->count();

        $under_construction_projects = $projects;
        $ready_units = ManageListings::where(['ready_status' => 1 , 'sold_out_status' => 0])->count();
        $sold_out_units = ManageListings::where(['sold_out_status' => 1])->count();
        $project = $projects +$outdated_projects+ $sold_out_units;

        return view('Admin.dashboard',compact('agents','developer','pending_contracts','projects','outdated_projects','under_construction_projects','ready_units','sold_out_units','project','associate'));
    }
    /**
     * This will chek user is superadmin or not
     *
     * @return boolean
     */
    public function isAdmin() :bool
    {
        return (  Auth::user()->role == 1);
    }


    /**
     * Ths will check user is company admin or not
     *
     * @return boolean
     */
    public function isAgentUser() :bool
    {
        return ( Auth::user()->role == 2);
    }

    /**
     * @return bool
     */
    public function isAssociateUser() :bool
    {
        return ( Auth::user()->role == 3);
    }

    public function loginHistory(Request $request)
    {
        $seven_days = \Carbon\Carbon::today()->subDays(7);
        $thirty_days = \Carbon\Carbon::today()->subDays(30);
        $sum = strtotime('00:00:00');
        $totaltime = 0;
        $arr = [];

        $monitoringArr = Monitorization::select('login_time','user_id','last_login','last_logout')->where('created_at', '>=', $seven_days)->where('login_time','!=','null')->toSql();
dd($monitoringArr);
        $result = array();

        foreach($monitoringArr as $key=>$element) {
//            $timeinsec = strtotime($element['login_time']) - $sum;
//            $totaltime = $totaltime + $timeinsec;
            if ($element['user_id']==$element['user_id']) {
                $arr[$element['user_id']][$key] = $element;
            }
//
        }
        ksort($arr, SORT_NUMERIC);
        dd($arr);
        $arr_1=[];
        foreach ($arr as $id=>$a){
            $arr_1[$id] =$a;
        }
        dd($arr_1);
        $h = intval($totaltime / 3600);
        $totaltime = $totaltime - ($h * 3600);
        $m = intval($totaltime / 60);
        $s = $totaltime - ($m * 60);
dd($h,$s,$m);
//                return view('Admin.login_history',compact('monitoringData'));
        }


}
