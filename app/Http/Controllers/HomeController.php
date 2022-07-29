<?php

namespace App\Http\Controllers;

use App\Models\LeadClient;
use App\Models\Monitorization;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
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
use Illuminate\View\View;

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
        $past_date = \Carbon\Carbon::now()->subdays(90);
        $underConstructionUnit = ManageListings::where(['ready_status' => 0, 'sold_out_status' => 0])->where('updated_at', '>=', $past_date)->count();
        $outdated_projects = ManageProject::whereHas('projectReminders',function($query){
            $query->whereDate('reminder_date','<=',Carbon\Carbon::now('Europe/Stockholm'))->where('status',0);
        })->count();

        $under_construction_projects = $projects;
        if (Auth::user()->role == 3) {
            $ready_units = ManageListings::where(['ready_status' => 1 , 'sold_out_status' => 0])->where('updated_at', '>=', $past_date)->count();
        } else {
            $ready_units = ManageListings::where(['ready_status' => 1, 'sold_out_status' => 0])->count();
        }
        $sold_out_projects = ManageProject::where(['sold_out_status' => '1'])->count();
        $project = $projects +$outdated_projects+ $sold_out_projects;

        return view('Admin.dashboard',compact('agents','developer','pending_contracts','projects','underConstructionUnit','outdated_projects','under_construction_projects','ready_units','sold_out_projects','project','associate'));
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

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function loginHistory(Request $request)
    {
        $seven_days = \Carbon\Carbon::today()->subDays(7);
        $thirty_days = \Carbon\Carbon::today()->subDays(30);

        $monitoringData= [];
        $monitoringArr = DB::select("SELECT m.*,users.name,(SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(m1.`login_time`))) as total_login_time from monitorization m1 where m1.user_id = m.user_id AND `created_at` >= '".$seven_days."') as total_seven_time,(SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(m1.`login_time`))) as total_login_time from monitorization m1 where m1.user_id = m.user_id AND `created_at` >= '".$thirty_days."') as total_thirty_time FROM `monitorization` m LEFT JOIN users ON users.id = m.user_id WHERE m.id in (select max(m2.id) from monitorization m2 group by m2.user_id) ORDER BY `m`.`id` DESC;");

        foreach($monitoringArr as $object) {
            $monitoringData[] = (array)$object;
        }

        return view('Admin.login_history',compact('monitoringData'));
    }


}
