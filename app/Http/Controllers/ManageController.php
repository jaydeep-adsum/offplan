<?php

namespace App\Http\Controllers;

use App\Models\LocalImage;
use App\Models\ManageListings;
use App\Models\Developer;
use App\Models\Features;
use App\Models\ManageProject;
use App\Models\Paymentplan;
use App\Models\Milestones;
use App\Models\Community;
use App\Models\ProjectAssignAgents;
use App\Models\ProjectDocuments;
use App\Models\Subcommunity;
use App\Models\Categories;
use App\Models\Note;
use App\Models\User;
use App\Models\Permission_role_mapping;
use App\Models\UnitMultipleAttachment;
use Carbon;
use File;
use Session;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use App\Traits\ResponseTrait;
use App\Traits\RequestTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Hash;
use Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Traits\UtilityTrait;
use Mail;
use Image;
use Datatables;

class ManageController extends Controller
{
    use ResponseTrait, UtilityTrait;

    public function __construct()
    {
        $this->middleware('auth', ['except' => ['view_project', 'viewproject', 'mail', 'sendMail']]);
    }

    public function managelistings()
    {
        try {
            $project = ManageListings::where(['ready_status' => '0', 'sold_out_status' => '0'])->distinct()->orderBy('project')->get('project');
            $typeList = Categories::orderBy('catName')->pluck('catName');
            $userlist = Developer::orderBy('company')->get();
            $community = Community::orderBy('name')->get();
            $permission = Permission_role_mapping::where('user_id', Auth::user()->id)->where('permissions_id', 5)->first();
            return view('Admin.manage_listings', compact('typeList', 'userlist', 'community', 'project', 'permission'));
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    public function datatableManageListings(Request $request)
    {
        try {
            $permission = Permission_role_mapping::where('user_id', Auth::user()->id)->where('permissions_id', 5)->first();
            $past_date = Carbon\Carbon::now()->subdays(90);
            if ($request->status == 'listing') {
                $query = ManageListings::with('developer', 'notes', 'reminder', 'communitys', 'subcommunitys','manageProject','projectAssignAgents')->where(['ready_status' => 0, 'sold_out_status' => 0]);
                if (Auth::user()->role == 3) {
                    $query->where('updated_at', '>=', $past_date);
                }
                $copyUnitRoute = 'copy-unit';
                $previewUnit = 'preview-unit';
            } else if ($request->status == 'ready_listing') {
                $query = ManageListings::with('developer', 'notes', 'reminder', 'communitys', 'subcommunitys','manageProject','projectAssignAgents')->where(['ready_status' => 1, 'sold_out_status' => 0]);
                if (Auth::user()->role == 3) {
                    $query->where('updated_at', '>=', $past_date);
                }
                $copyUnitRoute = 'ready-copy-unit';
                $previewUnit = 'ready-preview-unit';
            } else if ($request->status == 'sold_out_listing') {
                $query = ManageListings::with('developer', 'notes', 'reminder', 'communitys', 'subcommunitys','manageProject','projectAssignAgents')->where('sold_out_status', 1);
                $copyUnitRoute = 'sold-out-copy-unit';
                $previewUnit = 'sold-out-preview-unit';
            } else if ($request->status == 'outdated_listing') {
                $query = ManageListings::with('developer', 'notes', 'communitys', 'subcommunitys','manageProject','projectAssignAgents')->whereHas('reminder', function ($query) {
                    $query->whereDate('reminder_date', '<=', Carbon\Carbon::now('Europe/Stockholm'))->where('status', 0);
                });
                $copyUnitRoute = 'outdated-copy-unit';
                $previewUnit = 'outdated-preview-unit';
            }
            $status = $request->status;
            $input = $this->objectToArray($request->all());

            if(isset($input['community'])){
                $community = $input['community'];
                $query = $query->whereHas('manageProject.communitys',function($query)use($community){
                    $query->where('community',$community);
                });
            }
            if(isset($input['subcommunity'])){
                $subcommunity = $input['subcommunity'];
                $query = $query->whereHas('manageProject.subcommunity',function($query)use($subcommunity){
                    $query->where('subcommunity',$subcommunity);
                });
            }
            if(isset($input['property'])){
                $property = $input['property'];
                $query = $query->whereHas('manageProject',function($query)use($property){
                    $query->where('property',$property);
                });
            }
            if(isset($input['bedrooms'])){
                $query = $query->where('bedrooms',$input['bedrooms']);
            }
            if(isset($input['min_price']) || isset($input['max_price'])){
                if(empty($input['min_price'])){
                    $min_price = '0';
                    $max_price = $input['max_price'];
                    $query = $query->where('price','<=', $max_price);
                }
                else if(empty($input['max_price'])){
                    $min_price = $input['min_price'];
                    $max_price = '0';
                    $query = $query->where('price','>=', $min_price);
                }
                else
                {
                    $min_price = $input['min_price'];
                    $max_price = $input['max_price'];
                    $query = $query->whereBetween('price', [$min_price, $max_price]);
                }
            }
            if(isset($input['min_size']) || isset($input['max_size'])){
                if(empty($input['min_size'])){
                    $min_size = '0';
                    $max_size = $input['max_size'];
                    $query = $query->where('size','<=', $max_size);
                }
                else if(empty($input['max_size'])){
                    $min_size = $input['min_size'];
                    $max_size = '0';
                    $query = $query->where('size','>=', $min_size);
                }
                else
                {
                    $min_size = $input['min_size'];
                    $max_size = $input['max_size'];
                    $query = $query->whereBetween('size', [$min_size, $max_size]);
                }
            }
            if(isset($input['project'])){
                $query = $query->where('project',$input['project']);
            }
            if(isset($input['company'])) {
                $company = $input['company'];
                $query = $query->whereHas('manageProject.developer',function($query)use($company){
                    $query->where('company',$company);
                });
            }
            if(isset($input['handover_year'])){
                $handover_year = $input['handover_year'];
                $query = $query->whereHas('manageProject',function($query)use($handover_year){
                    $query->where('handover_year',$handover_year);
                });
            }
            if(isset($input['quarter'])){
                $quarter = $input['quarter'];
                $query = $query->whereHas('manageProject',function($query)use($quarter){
                    $query->where('quarter',$quarter);
                });
            }
            if(isset($input['payment_plan'])){
                $query = $query->where('payment_plan',$input['payment_plan']);
            }
            if(isset($input['amountuptohandover']) ){
                $query = $query->where('up_to_handover','<=',$input['amountuptohandover']);
            }
            if(isset($input['posthandover'])){
                $query = $query->where('post_handover','<=',$input['posthandover']);
            }
            if (isset($input['flag'])) {
                $query = $query->whereIn('flag', $input['flag']);
            }

            $data = $query->get();


            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('active', function ($row) {
                    $class = 0;
                    if (!$row->reminder->isEmpty()) {
                        foreach ($row->reminder as $reminder) {
                            if ($reminder->reminder_date <= \Carbon\Carbon::now()->toDateString() && $reminder->status == 0) {
                                $class = 1;
                            } else {
                                $class = 0;
                            }
                        }
                    }
                    return $class;
                })
                ->addColumn('rf_no', function ($row) {
                    return $row->rf_no ? $row->rf_no : '-';
                })
                ->addColumn('flag', function ($row) {
                    if($row->flag==1){
                        $flag = 'New Launch';
                        $class='danger';
                    } else if($row->flag==2){
                        $flag = 'High in Demand';
                        $class='primary';
                    } else if($row->flag==3){
                        $flag = 'Limited Availability';
                        $class='success';
                    } else if($row->flag==4){
                        $flag = 'Value for Money';
                        $class='warning';
                    } else if($row->flag==5){
                        $flag = 'Best Layout';
                        $class='info';
                    } else if($row->flag==6){
                        $flag = 'Attractive Payment Plan';
                        $class='secondary';
                    } else {
                        $flag = '-';
                        $class='';
                    }

                    return "<span class='badge badge-$class'>".$flag."</span>";
                })
                ->addColumn('rf_no', function($row){
                    return $row->rf_no ? $row->rf_no : '-';
                })
                ->addColumn('assign_to', function($row){
                    return $row->projectAssignAgents ? $row->projectAssignAgents->user->name : '-';
                })
                ->addColumn('developer_company', function($row){
                    return $row->manageProject ? $row->manageProject->developer ? $row->manageProject->developer->company : '-' : '-';
                })
                ->addColumn('project', function($row){
                    return $row->project ? $row->project : '-';
                })
                ->addColumn('communitys', function($row){
                    return $row->manageProject ? $row->manageProject->communitys ? $row->manageProject->communitys->name.', Dubai, UAE' : '' : '-';
                })
                ->addColumn('property', function($row){
                    return $row->manageProject ? $row->manageProject->property : '-';
                })
                ->addColumn('bedrooms', function($row){
                    return $row->bedrooms ? $row->bedrooms : '-';
                })
                ->addColumn('size', function($row){
                    return $row->size ? $row->size : '-';
                })
                ->addColumn('price', function($row){
                    return $row->price ? number_format($row->price,2, '.', ',') : '0';
                })
                ->addColumn('quarter_and_handover_year', function($row)use($request){
                    if($row->manageProject)
                    {
                        if($row->manageProject->completion_status)
                        {
                            if($row->manageProject->completion_status == 1)
                            {
                                if($request->status == 'listing')
                                {
                                    return 'Ready to Move In';
                                }
                                else if($request->status == 'ready_listing')
                                {
                                    return 'Ready to Move';
                                }
                                else
                                {
                                    return 'Ready';
                                }
                            }

                            if($row->manageProject->completion_status == 2)
                            {
                                return ($row->manageProject->quarter && $row->manageProject->handover_year) ? $row->manageProject->quarter .', '. $row->manageProject->handover_year : '-';
                            }
                        }
                        else
                        {
                            if($row->ready_status)
                            {
                                if($request->status == 'listing')
                                {
                                    return 'Ready to Move In';
                                }
                                else if($request->status == 'ready_listing')
                                {
                                    return 'Ready to Move';
                                }
                                else
                                {
                                    return 'Ready';
                                }
                            }
                            else
                            {
                                return ($row->manageProject->quarter && $row->manageProject->handover_year) ? $row->manageProject->quarter .', '. $row->manageProject->handover_year : '-';
                            }
                        }
                    }
                    return '-';
                })
                ->addColumn('up_to_handover', function($row){
                    return $row->up_to_handover ? number_format($row->up_to_handover,2, '.', ',') : '-';
                })
                ->addColumn('post_handover', function($row){
                    return $row->post_handover ? number_format($row->post_handover,2, '.', ',') : '-';
                })
                ->addColumn('ready_status', function($row){
                    $check = $row->ready_status ? 'checked' : '';
                    return "<div class='custom-control custom-switch'><input type='checkbox' class='custom-control-input' id='customSwitch".$row->id."' data-id='". $row->id ."' onclick='fn_project_status_changes(this)' value='1' ".$check." ><label class='custom-control-label' for='customSwitch".$row->id."'>Ready</label></div>";
                })
                ->addColumn('sold_out_status', function($row){
                    $check = $row->sold_out_status ? 'checked' : '';
                    return "<div class='custom-control custom-switch'><input type='checkbox' class='custom-control-input' id='customSwitchsoldout".$row->id."' data-id='". $row->id ."' onclick='sold_out_project_status_changes(this)' value='1' ". $check ." ><label class='custom-control-label' for='customSwitchsoldout".$row->id."'>Sold Out</label></div>";
                })
                ->addColumn('updated_at', function($row){
                    return $row->updated_at ? $row->updated_at : '-';
                })
                ->addColumn('action', function($row) use($status, $permission,$copyUnitRoute,$previewUnit){
                    $notes = $row->notes ? $row->notes : '';
                    $name = ($row->notes && $row->developer) ? $row->developer->company : '';
                    $reminderlist = $row->reminder ? $row->reminder : '';
                    if ($permission) {
                        $preview_html = '<div class="col-6"><a href="' . route($previewUnit, ['id' => $row->id]) . '"><i class="fas fa-eye" data-toggle="tooltip" data-placement="bottom" title="Preview"></i></a></div>';

                        $copy_html = '<div class="col-6"><a href="' . route($copyUnitRoute, ['id' => $row->id]) . '" target="_blank"><i class="fas fa-copy" data-toggle="tooltip" data-placement="bottom" title="Copy"></i></a></div>';

                        $note_html = '<div class="col-6"><a class="user_dialog" data-toggle="modal" data-target="#imageModal" data-listid="' . $row->id . '" data-notelist="' . $notes . '" data-name="' . $name . '"><i style="color: #0080ff" class="fas fa-sticky-note mt-2" data-toggle="tooltip"data-placement="bottom" title="Note"></i></a></div>';

                        $reminder_html = '<div class="col-6"><a class="reminder" data-toggle="modal" data-target="#remindermodel"  data-listid="' . $row->id . '" data-reminderlist="' . $reminderlist . '"><i style="color: #9400D3" class="fas fa-clock mt-2" data-toggle="tooltip" data-placement="bottom" title="Reminder"></i></a></div>';

                        $edit_html = '<div class="col-6"><a href="' . route('edit-unit', ['id' => $row->id]) . '" target="_blank"><i class="fas fa-edit mt-2" data-toggle="tooltip" data-placement="bottom" title="Edit"></i></a></div>';

                        $delete_html = '<div class="col-6"><a href="javascript:void(0)" class="delete-confirm" data-listid="' . $row->id . '"><i style="color: red" class="fas fa-trash-alt delete mt-2" data-toggle="tooltip" data-placement="bottom" title="Delete"></i></a></div>';
                        $flags_html = '<div class="col-6"><div class="dropdown dropleft float-right">
                                <span class="dropdown-toggle dd-remove" data-toggle="dropdown">
                                <i style="color: #6a6a6a" class="fa fa-cogs mt-2"></i>
                                </span>
                                    <div class="dropdown-menu px-2">
                                      <span class="dropdown-item flag" data-id="' . $row->id . '" data-flag="0"><i class="fa fa-plus mr-2"></i>Select Flag</span>
                                      <span class="dropdown-item flag" data-id="' . $row->id . '" data-flag="1"><i class="fa fa-plus mr-2"></i>New Launch</span>
                                      <span class="dropdown-item flag" data-id="' . $row->id . '" data-flag="2"><i class="fa fa-plus mr-2 mt-2"></i>High in Demand</span>
                                      <span class="dropdown-item flag" data-id="' . $row->id . '" data-flag="3"><i class="fa fa-plus mr-2 mt-2"></i>Limited Availability</span>
                                      <span class="dropdown-item flag" data-id="' . $row->id . '" data-flag="4"><i class="fa fa-plus mr-2 mt-2"></i>Value for Money</span>
                                      <span class="dropdown-item flag" data-id="' . $row->id . '" data-flag="5"><i class="fa fa-plus mr-2 mt-2"></i>Best Layout</span>
                                      <span class="dropdown-item flag" data-id="' . $row->id . '" data-flag="6"><i class="fa fa-plus mr-2 mt-2"></i>Attractive Payment Plan</span>
                                      <span class="dropdown-item mt-2">Option 7</span>
                                      <span class="dropdown-item mt-2">Option 8</span>
                                    </div>
                                    </div>
                              </div>';
                        $copy = '';
                        $preview = $permission->read ? $preview_html : '';
                        $edit = '';
                        $delete = '';
                        $note = '';
                        $reminder = '';
                        if(Auth::user()->role == 1)
                        {
                            $copy = $permission->create ? $copy_html : '';
                            $preview = $permission->read ? $preview_html : '';
                            $edit = $permission->update ? $edit_html : '';
                            $delete = $permission->delete ? $delete_html : '';
                            $note = $permission->create ? $note_html : '';
                            $reminder = $permission->create ? $reminder_html : '';
                        }
                        else
                        {
                            if($row->projectAssignAgents)
                            {
                                if($row->projectAssignAgents->agent_id == Auth::user()->id)
                                {
                                    $copy = $permission->create ? $copy_html : '';
                                    $preview = $permission->read ? $preview_html : '';
                                    $edit = $permission->update ? $edit_html : '';
                                    $delete = $permission->delete ? $delete_html : '';
                                    $note = $permission->create ? $note_html : '';
                                    $reminder = $permission->create ? $reminder_html : '';
                                }
                            }
                        }
                        $flags = ($status == 'listing' || $status == 'ready_listing')? $flags_html :'' ;

                        return '<div class="row">' . $copy . '' . $preview . '' . $edit . '' . $delete . '' . $note . '' . $reminder . '' . $flags . '</div>';
                    } else {
                        return '-';
                    }
                })
//                ->filter(function ($query) use ($request) {
//
//                    $input = $this->objectToArray($request->all());
//
//                    if (isset($input['developer'])) {
//                        $name = $input['developer'];
//                        $query = $query->whereHas('developer', function ($query) use ($name) {
//                            $query->where('person', $name);
//                        });
//                    }
//                    if (isset($input['outdated_status'])) {
//                        $query = $query->whereHas('reminder', function ($query) {
//                            $query->whereDate('reminder_date', '<=', Carbon\Carbon::now('Europe/Stockholm'))->where('status', 0);
//                        });
//                    }
//                    if (isset($input['company'])) {
//                        $company = $input['company'];
//                        $query = $query->whereHas('developer', function ($query) use ($company) {
//                            $query->where('company', $company);
//                        });
//                    }
//                    if (isset($input['community'])) {
//                        $query = $query->where('community', $input['community']);
//                    }
//                    if (isset($input['subcommunity'])) {
//                        $query = $query->where('subcommunity', $input['subcommunity']);
//                    }
//                    if (isset($input['handover'])) {
//                        $query = $query->where('handover', $input['handover']);
//                    }
//                    if (isset($input['amount-upto-handover'])) {
//                        $query = $query->where('up_to_handover', '<=', $input['amount-upto-handover']);
//                    }
//                    if (isset($input['post-handover'])) {
//                        $query = $query->where('post_handover', '<=', $input['post-handover']);
//                    }
//                    if (isset($input['location'])) {
//                        $query = $query->where('location', 'LIKE', '%' . $input['location'] . '%');
//                    }
//                    if (isset($input['project'])) {
//                        $query = $query->where('project', $input['project']);
//                    }
//                    if (isset($input['property'])) {
//                        $query = $query->where('property', $input['property']);
//                    }
//                    if (isset($input['min_price']) || isset($input['max_price'])) {
//                        if (empty($input['min_price'])) {
//                            $min_price = '0';
//                            $max_price = $input['max_price'];
//                            $query = $query->where('price', '<=', $max_price);
//                        } else if (empty($input['max_price'])) {
//                            $min_price = $input['min_price'];
//                            $max_price = '0';
//                            $query = $query->where('price', '>=', $min_price);
//                        } else {
//                            $min_price = $input['min_price'];
//                            $max_price = $input['max_price'];
//                            $query = $query->whereBetween('price', [$min_price, $max_price]);
//                        }
//                    }
//                    if (isset($input['min_size']) || isset($input['max_size'])) {
//                        if (empty($input['min_size'])) {
//                            $min_size = '0';
//                            $max_size = $input['max_size'];
//                            $query = $query->where('size', '<=', $max_size);
//                        } else if (empty($input['max_size'])) {
//                            $min_size = $input['min_size'];
//                            $max_size = '0';
//                            $query = $query->where('size', '>=', $min_size);
//                        } else {
//                            $min_size = $input['min_size'];
//                            $max_size = $input['max_size'];
//                            $query = $query->whereBetween('size', [$min_size, $max_size]);
//                        }
//                    }
//                    if (isset($input['bedrooms'])) {
//                        $query = $query->where('bedrooms', $input['bedrooms']);
//                    }
//                    if (isset($input['handover_year'])) {
//                        $query = $query->where('handover_year', $input['handover_year']);
//                    }
//                    if (isset($input['quarter'])) {
//                        $query = $query->where('quarter', $input['quarter']);
//                    }
//                    if (isset($input['construction_status'])) {
//                        $query = $query->where('construction_status', $input['construction_status']);
//                    }
//                    if (isset($input['payment_plan'])) {
//                        $query = $query->where('payment_plan', $input['payment_plan']);
//                    }
//                    if (isset($input['ready_status'])) {
//                        $query = $query->where('ready_status', $input['ready_status']);
//                    }
//                    if (isset($input['sold_out_status'])) {
//                        $query = $query->where('sold_out_status', $input['sold_out_status']);
//                    }
//                    if (isset($input['flag'])) {
//                        $query = $query->whereIn('flag', $input['flag']);
//                    }
//                })
                ->rawColumns(['rf_no', 'flag', 'developer_company', 'project', 'communitys', 'property', 'bedrooms', 'size', 'price', 'quarter_and_handover_year', 'up_to_handover', 'post_handover', 'ready_status', 'sold_out_status', 'updated_at', 'action'])
                ->make(true);
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    public function getUnitNoteList(Request $request)
    {
        $noteList = Note::where('proj_id', $request->projid)->get();
        return response()->json($noteList);
    }

    public function readyUnitList()
    {
        try {
            $project = ManageListings::where(['ready_status' => '1'])->distinct()->orderBy('project')->get('project');
            $typeList = Categories::orderBy('catName')->pluck('catName');
            $userlist = Developer::orderBy('company')->get();
            $community = Community::orderBy('name')->get();
            $permission = Permission_role_mapping::where('user_id', Auth::user()->id)->where('permissions_id', 5)->first();
            return view('Admin.ready_unit_list', compact('typeList', 'userlist', 'community', 'project', 'permission'));
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    public function soldOutUnitList()
    {
        try {
            $project = ManageListings::where(['sold_out_status' => '1'])->distinct()->orderBy('project')->get('project');
            $typeList = Categories::orderBy('catName')->pluck('catName');
            $userlist = Developer::orderBy('company')->get();
            $community = Community::orderBy('name')->get();
            $permission = Permission_role_mapping::where('user_id', Auth::user()->id)->where('permissions_id', 5)->first();
            return view('Admin.sold_out_unit_list', compact('typeList', 'userlist', 'community', 'project', 'permission'));
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    public function outdatedUnit()
    {
        try {
            $mytime = Carbon\Carbon::now()->format('Y-m-d');
            $user = ManageListings::with(['developer', 'notes', 'communitys', 'subcommunitys'])->whereHas('reminder', function ($query) {
                $query->whereDate('reminder_date', '<=', Carbon\Carbon::now('Europe/Stockholm'))->where('status', 0);
            })->get();

            $project = ManageListings::whereHas('reminder', function ($query) {
                $query->whereDate('reminder_date', '<=', Carbon\Carbon::now('Europe/Stockholm'))->where('status', 0);
            })->distinct()->orderBy('project')->get('project');

            $typeList = Categories::orderBy('catName')->pluck('catName');
            $userlist = Developer::orderBy('company')->get();
            $community = Community::orderBy('name')->get();
            return view('Admin.outdated_project', compact('user', 'typeList', 'userlist', 'community', 'project'));
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    public function addviewunit()
    {
        try {
            $milestoneData = Milestones::pluck('milestone','id');
            $featuresList = Features::orderBy('fname')->get();
            if(Auth::user()->role == 2)
            {
                $projectAssignAgents = ProjectAssignAgents::where('agent_id',Auth::user()->id)->pluck('project_id');
                $project = ManageProject::whereIn('id',$projectAssignAgents)->distinct()->orderBy('project')->pluck('project','id');
            }
            else
            {
                $project = ManageProject::distinct()->orderBy('project')->pluck('project','id');
            }
            return view('Admin.Add-units',compact('milestoneData','featuresList','project'));
        } catch (NotFoundHttpException $ex) {
            return $this->notFoundRequest($ex);
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    public function getProjectData(Request $request)
    {
        $manageProject = ManageProject::select('id','description','image','pdf','payment_plan','features')->with('paymentPlanDetails')->find($request->id);
        $imageName = array();
        $imageLink = array();
        $pdfName = array();
        $pdfLink = array();
        if($manageProject->image)
        {
            foreach(json_decode($manageProject->image) as $image)
            {
                $imageName[] = $image;
                $imageLink[] = asset('public/projectFiles/images').'/'.$image;
            }

        }

        if($manageProject->pdf)
        {
            foreach(json_decode($manageProject->pdf) as $pdf)
            {
                $pdfName[] = $pdf;
            }

            $projectDocuments = ProjectDocuments::where('project_id',$request->id)->get();
            foreach($projectDocuments as $file)
            {
                $pdfName[] = $file->document_name;
            }
        }

        $manageProject['imageLink'] = $imageLink;
        $manageProject['imageName'] = $imageName;
        $manageProject['pdfName'] = $pdfName;
        $manageProject['pdfLink'] = $pdfLink;
        return response()->json($manageProject);
    }

    public function viewproject($id,$userid)
    {
        $encryption = "AES-128-CTR";
        $env_length = openssl_cipher_iv_length($encryption);
        $options   = 0;
        $decryption_userid = '1234567890123456';
        $decryption_key = "123456";
        $user_id = openssl_decrypt($userid, $encryption, $decryption_key, $options, $decryption_userid);

        $manage_listings = ManageListings::with('developer','paymentplan','manageProject')->where('id',$id)->first();
        $community = '';
        $subcommunity = '';
        $user_data = NULL;
        if($manage_listings->project_id)
        {
            if($manage_listings->manageProject)
            {
                $community = Community::where('id',$manage_listings->manageProject->community)->first();
                $subcommunity = Subcommunity::where('id',$manage_listings->manageProject->subcommunity)->first();
            }
        }
//        $community=Community::orderBy('name')->where('id',$manage_listings->community)->get();
//        $subcommunity=Subcommunity::orderBy('name')->where('id',$manage_listings->subcommunity)->get();
        $user_data = User::where('id',$user_id)->first();

        return view('Admin.view_project',compact('manage_listings','community','subcommunity','user_data'));
    }

    public function previewunit($id)
    {
        $encryption = "AES-128-CTR";
        $env_length = openssl_cipher_iv_length($encryption);
        $options   = 0;
        $encryption_userid = '1234567890123456';
        $encryption_key = "123456";
        $user_id = Auth::id();
        $encrypt_userid = openssl_encrypt($user_id, $encryption, $encryption_key, $options, $encryption_userid);
        $manage_listings = ManageListings::with('developer', 'paymentplan', 'community', 'subcommunity','user','manageProject')->where('id', $id)->first();
        $community = '';
        $subcommunity = '';
        $projectAssignAgents = '';
        if($manage_listings)
        {
            if($manage_listings->manageProject)
            {
                $community = Community::where('id',$manage_listings->manageProject->community)->first();
                $subcommunity = Subcommunity::where('id',$manage_listings->manageProject->subcommunity)->first();
            }
            if(Auth::user()->role == 1)
            {
                $projectAssignAgents = true;
            }
            else
            {
                $projectAssignAgents = ProjectAssignAgents::where('project_id',$manage_listings->project_id)->where('agent_id',Auth::user()->id)->first();
            }
        }
        $note = Note::where('proj_id', $id)->get();
        $unitmultipleattachment = UnitMultipleAttachment::where('project_id', $id)->get();
        $permission = Permission_role_mapping::where('user_id', Auth::user()->id)->where('permissions_id', 5)->first();
        return view('Admin.preview_project', compact('manage_listings', 'community', 'subcommunity', 'note', 'unitmultipleattachment', 'permission','encrypt_userid','projectAssignAgents'));
    }

    public function localImageSaveUnit(Request $request)
    {
        try {
            if($request->local_image_id != null)
            {
                $projectCheck = ManageProject::select('id','image','pdf','project','ready_status')->where('id',$request->project)->first();
                if(!$projectCheck)
                {
                    return response()->json(['status' => 0, 'message' => 'Project not found']);
                }
                $projectImageArray = $projectCheck->image ? json_decode($projectCheck->image,true) : array();

                $localDataUpdate = LocalImage::where('local_image_id',$request->local_image_id)->first();
                if($localDataUpdate)
                {
                    $decode_image = $localDataUpdate->local_image_file ? json_decode($localDataUpdate->local_image_file) : NULL;

                    foreach($request->localSaveImage as $key => $file)
                    {
                        if(in_array($file,$decode_image))
                        {
                            $image_name = $file;
                        }
                        else if(in_array($file,$projectImageArray))
                        {
                            File::copy(public_path('projectFiles/images/'.$file), public_path('localSaveImage/'.$file));
                            $image_name = $file;
                        }
                        else
                        {
                            $mimeType = $file->getMimeType();
                            if(!$mimeType)
                            {
                                return response()->json(['status' => 0, 'message' => 'mime type does not exist']);
                            }
                            if( $mimeType == "inode/x-empty"  || $mimeType == "application/x-empty")
                            {
                                $image_name = $file->getClientOriginalName();
                            }
                            else
                            {
                                $image_name = rand(111111,999999).'_'.time().'-'.$file->getClientOriginalName();
                                $img = Image::make($file->getRealPath())->resize(600, 400);
//                                $watermark = Image::make('public/files/logo.png');
//                                $img->insert($watermark, 'center', 5, 5);
                                $img->save('public/localSaveImage/'.$image_name);
                            }
                        }
                        $data[$key] = $image_name;
                    }
                    ksort($data);

                    $localDataUpdate->local_image_file = json_encode($data);
                    $localDataUpdate->save();

                    return response()->json(['status' => 1, 'local_image_id' => $localDataUpdate->local_image_id, 'message' => 'successfully saved.']);
                }
                else
                {
                    foreach($request->localSaveImage as $key => $file)
                    {
                        if(in_array($file,$projectImageArray))
                        {
                            File::copy(public_path('projectFiles/images/'.$file), public_path('localSaveImage/'.$file));
                            $image_name = $file;
                        }
                        else
                        {
                            $mimeType = $file->getMimeType();
                            if(!$mimeType)
                            {
                                return response()->json(['status' => 0, 'message' => 'mime type does not exist']);
                            }
                            if( $mimeType == "inode/x-empty"  || $mimeType == "application/x-empty")
                            {
                                $image_name = $file->getClientOriginalName();
                            }
                            else
                            {
                                $image_name = rand(111111,999999).'_'.time().'-'.$file->getClientOriginalName();
                                $img = Image::make($file->getRealPath())->resize(600, 400);
//                                $watermark = Image::make('public/files/logo.png');
//                                $img->insert($watermark, 'center', 5, 5);
                                $img->save('public/localSaveImage/'.$image_name);
                            }
                        }
                        $data[$key] = $image_name;
                    }
                    ksort($data);

                    $localData['local_image_id'] = $request->local_image_id;
                    $localData['local_image_file'] = json_encode($data);
                    $localDataCreate = LocalImage::create($localData);
                    if($localDataCreate)
                    {
                        return response()->json(['status' => 1, 'local_image_id' => $localData['local_image_id'], 'message' => 'successfully saved.']);
                    }
                    else
                    {
                        return response()->json(['status' => 0, 'message' => 'Something went wrong!']);
                    }
                }
            }
            else
            {
                return response()->json(['status' => 0, 'message' => 'Something went wrong!']);
            }

        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    public function submitunit(Request $request, $parentId = null)
    {
        try {
            $input = $this->objectToArray($request->input());

            $projectCheck = ManageProject::select('id','image','pdf','project','ready_status')->where('id',$request->project)->first();
            if(!$projectCheck)
            {
                return response()->json(['status' => 0, 'message' => 'Project not found']);
            }

            $requiredParams = $this->requiredRequestParams('create');
            $validator = Validator::make($input, $requiredParams);

            if ($validator->fails()) {
                return response()->json(['status' => 0, 'message' => implode('<br> <li>', $validator->errors()->all())]);
            }

            if ($request->get('featuresList')) {
                $input['features'] = json_encode($input['featuresList']);
            }

            $projectImageArray = $projectCheck->image ? json_decode($projectCheck->image,true) : array();

            $input['user_id'] = Auth::user()->id;

            $data = array();
            if($request->local_image_id)
            {
                $localDataGet = LocalImage::where('local_image_id',$request->local_image_id)->first();
                if($localDataGet)
                {
                    $image = json_decode($localDataGet->local_image_file);
                    foreach($image as $value)
                    {
                        File::copy(public_path('localSaveImage/'.$value), public_path('files/profile/'.$value));
                        if (file_exists(public_path('/localSaveImage/'.$value))) {
                            @unlink(public_path('/localSaveImage/'.$value));
                        }
                    }
                    $input['image'] = $localDataGet->local_image_file;
                    $localDataGet->delete();
                }
            }

            if ($request->hasfile('filesList')) {
                foreach ($request->file('filesList') as $file) {
                    $mimeType = $file->getMimeType();
                    if (!$mimeType) {
                        if ($request->ajax()) {
                            $data['status'] = 0;
                            $data['message'] = $errorMessage;

                            return response()->json($data);
                        }
                    }
                    if ($mimeType == "inode/x-empty" || $mimeType == "application/x-empty") {
                        $image_name = $file->getClientOriginalName();
                    } else {
                        $image_name = time() . '-' . $file->getClientOriginalName();
                        $img = Image::make($file->getRealPath())->resize(600, 400);
//                        $watermark = Image::make('public/files/logo.png');
//                        $img->insert($watermark, 'center', 5, 5);
                        $img->save('public/files/profile/' . $image_name);
                    }
                    $data[] = $image_name;
                }
                $input['image'] = json_encode($data);
            }

            if($request->hasfile('floor_plan_image'))
            {
                foreach($request->file('floor_plan_image') as $file)
                {
                    $mimeType = $file->getMimeType();
                    if(!$mimeType)
                    {
                        return response()->json(['status' => 0, 'message' => $errorMessage]);
                    }
                    if($mimeType == "inode/x-empty" || $mimeType == "application/x-empty")
                    {
                        $image_name = $file->getClientOriginalName();
                    }
                    else
                    {
                        $image_name = rand(111111,999999).'_'.time().'-'.$file->getClientOriginalName();
                        $file->move(public_path('files/profile'), $image_name);
                    }
                    $data1[] = $image_name;
                }
                $input['floor_plan_image']=json_encode($data1);
            }

            if($request->hasfile('video'))
            {
                foreach($request->file('video') as $file)
                {
                    $mimeType = $file->getMimeType();
                    if(!$mimeType)
                    {
                        return response()->json(['status' => 0, 'message' => $errorMessage]);
                    }
                    if($mimeType == "inode/x-empty" || $mimeType == "application/x-empty")
                    {
                        $image_name = $file->getClientOriginalName();
                    }
                    else
                    {
                        $image_name = rand(111111,999999).'_'.time().'-'.$file->getClientOriginalName();
                        $file->move(public_path('files/profile'), $image_name);
                    }
                    $data2[] = $image_name;
                }
                $input['video'] = json_encode($data2);
            }

            $projectPdfArray = $projectCheck->pdf ? json_decode($projectCheck->pdf,true) : array();
            $projectDocuments = ProjectDocuments::where('project_id',$projectCheck->id)->pluck('document_name')->toArray();

            $dataPdf = array();
            if($request->pdfList)
            {
                foreach($request->pdfList as $key => $file)
                {
                    if(in_array($file, $projectPdfArray))
                    {
                        File::copy(public_path('projectFiles/pdf/'.$file), public_path('files/profile/'.$file));
                        $pdfName = $file;
                    }
                    else if(in_array($file, $projectDocuments))
                    {
                        $projectDocumentsOriginaName = ProjectDocuments::where('project_id',$projectCheck->id)->where('document_name',$file)->first();
                        $pdfDocumentName = json_decode($projectDocumentsOriginaName->document_file,true);
                        File::copy(public_path('projectFiles/documents/'.$pdfDocumentName), public_path('files/profile/'.$pdfDocumentName));
                        $pdfName = $file;
                    }
                    else
                    {
                        if($request->hasfile('pdfList'))
                        {
                            $mimeType = $file->getMimeType();
                            if(!$mimeType)
                            {
                                return response()->json(['status' => 0, 'message' => $errorMessage]);
                            }
                            if($mimeType == "inode/x-empty" || $mimeType == "application/x-empty")
                            {
                                $pdfName = $file->getClientOriginalName();
                            }
                            else
                            {
                                $pdfName = rand(111111,999999).'_'.time().'-'.$file->getClientOriginalName();
                                $file->move(public_path('files/profile'), $pdfName);
                            }
                        }
                    }
                    $dataPdf[$key] = $pdfName;
                }
                ksort($dataPdf);
                $input['pdf'] = json_encode($dataPdf);
            }

            $checkindex = $this->checkindex();
            if($checkindex == null)
            {
                $index_key = 1;
                if($index_key < 10)
                {
                    $index_key = sprintf('%02u', $index_key);
                }
            }
            else
            {
                $index_key = $checkindex + 1;
                if($index_key < 10)
                {
                    $index_key = sprintf('%02u', $index_key);
                }
            }

            $input['index_key'] = $index_key;

            if($input['payment_plan'] == "Yes")
            {
                $input['payment_plan'] = 0;
                if($input['pre_handover_amount'] && $input['handover_amount'])
                {
                    $input['up_to_handover'] = (int)$input['pre_handover_amount'] + (int)$input['handover_amount'];
                    $input['post_handover'] = (int)$input['post_handover_amount'];
                }
            }
            else
            {
                $input['payment_plan'] = 1;
                $input['up_to_handover'] = $input['price'];
                $input['post_handover'] = 0;
            }

            $input['project'] = $projectCheck->project;
            $input['project_id'] = $projectCheck->id;
            $input['ready_status'] = $projectCheck->ready_status;
            $project = ManageListings::create($input);

            if(Auth::user()->role == 1)
            {
                $input['rf_no'] = 'GS'.$index_key;
                $update = ManageListings::where('id',$project->id)->update(['rf_no'=>$input['rf_no'],'index_key'=>$index_key]);
            }
            else
            {
                $rf_no = Auth::user()->user_code;
                $input['rf_no'] = $rf_no.$index_key;
                $update = ManageListings::where('id',$project->id)->update(['rf_no'=>$input['rf_no'],'user_id'=> $input['user_id'],'index_key'=>$index_key]);
            }

            if($input['payment_plan'] == "Yes")
            {
                if(!empty($input['milestone']))
                {
                    $payment['project_id'] = $project->id;
                    foreach ($input['milestone'] as $key => $value)
                    {
                        if(!empty($value['milestones']))
                        {
                            $payment['installment_terms'] = $value['installment_terms'] ? $value['installment_terms'] : 0;
                            $payment['milestone'] = $value['milestones'];
                            $payment['percentage'] = $value['percentage'] ? $value['percentage'] : 0;
                            $payment['amount'] = $value['amount'] ? $value['amount'] : 0;
                            $paymentplan = Paymentplan::create($payment);
                        }
                    }
                }
            }

            if($project)
            {
                $projectDocuments = ProjectDocuments::where('project_id',$project->project_id)->get();
                foreach($projectDocuments as $data)
                {
                    $extension = explode('.', json_decode($data->document_file));
                    if(count($extension) == 2)
                    {
                        $pdf_name = rand(111111,999999).'_'.time().'.'.$extension[1];

                        File::copy(public_path('projectFiles/documents/'.json_decode($data->document_file),true), public_path('project_attachment/'.$pdf_name));

                        $attachment['user_id'] = Auth::user()->id;
                        $attachment['project_id'] = $project->id;
                        $attachment['attachment_name'] = $data->document_name;
                        $attachment['attachment_multiple'] = json_encode($pdf_name);
                        UnitMultipleAttachment::create($attachment);
                    }
                }
                return response()->json(['status' => 1, 'message' => 'Has been added as a project']);
            }
            else
            {
                return response()->json(['status' => 0, 'message' => 'Failed to create project']);
            }

        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    public function editunit($id)
    {
        $project = ManageListings::with(['developer','paymentplan'])->where('id',$id)->first();
        $developerData = Developer::orderBy('company')->pluck('company','id');
        $milestoneData = Milestones::pluck('milestone','id');
        $featuresList = Features::orderBy('fname')->get();
        $projectList = ManageProject::distinct()->orderBy('project')->pluck('project','id');
        return view('Admin.editunit',compact('project','developerData','milestoneData','featuresList','projectList'));
    }

    public function copyunit($id)
    {
        $project = ManageListings::with(['developer','paymentplan'])->where('id',$id)->first();
        $developerData = Developer::orderBy('company')->pluck('company','id');
        $milestoneData = Milestones::pluck('milestone','id');
        $featuresList = Features::orderBy('fname')->get();
        $projectList = ManageProject::distinct()->orderBy('project')->pluck('project','id');
        return view('Admin.copyunit',compact('project','developerData','milestoneData','featuresList','projectList'));
    }

    public function updateunit(Request $request)
    {
        try {
            $id = $request->get('id');
            $projectCheck = ManageProject::select('id','image','pdf','project','ready_status')->where('id',$request->project)->first();
            if(!$projectCheck)
            {
                return response()->json(['status' => 0, 'message' => 'Project not found']);
            }
            $project = ManageListings::with('paymentplan')->where('id', $id)->first();
            if(!$project)
            {
                return response()->json(['status' => 0, 'message' => 'Unit not found']);
            }

            $input = $this->objectToArray($request->input());
            $input['id'] = $id;
            $input = $this->prepareUpdateData($input, $project->toArray());

            $requiredParams = $this->requiredRequestParams('update', $id);
            $validator = Validator::make($input, $requiredParams);

            if ($validator->fails()) {
                return response()->json(['status' => 0, 'message' => implode('<br> <li>', $validator->errors()->all())]);
            }

            if ($request->get('featuresList')) {
                $input['features'] = json_encode($input['featuresList']);
            }

            if ($request->hasfile('filesList')) {
                foreach ($request->file('filesList') as $file) {
                    $mimeType = $file->getMimeType();
                    if (!$mimeType) {
                        return response()->json(['status' => 0, 'message' => $errorMessage]);
                    }
                    if ($mimeType == "inode/x-empty" || $mimeType == "application/x-empty") {
                        $image_name = $file->getClientOriginalName();
                    } else {
                        $image_name = rand(111111,999999).'_'.time().'-'.$file->getClientOriginalName();
                        $img = Image::make($file->getRealPath())->resize(600, 400);
//                        $watermark = Image::make('public/files/logo.png');
//                        $img->insert($watermark, 'center', 5, 5);
                        $img->save('public/files/profile/' . $image_name);
                    }
                    $data[] = $image_name;
                }
                $input['image'] = json_encode($data);
            }

            if ($request->hasfile('floor_plan_image')) {
                foreach ($request->file('floor_plan_image') as $file) {
                    $mimeType = $file->getMimeType();
                    if (!$mimeType) {
                        return response()->json(['status' => 0, 'message' => $errorMessage]);
                    }
                    if ($mimeType == "inode/x-empty" || $mimeType == "application/x-empty") {
                        $image_name = $file->getClientOriginalName();
                    } else {
                        $image_name = rand(111111,999999).'_'.time().'-'.$file->getClientOriginalName();
                        $file->move(public_path('files/profile'), $image_name);
                    }
                    $data1[] = $image_name;
                }
                $input['floor_plan_image'] = json_encode($data1);
            }

            if ($request->hasfile('video')) {
                foreach ($request->file('video') as $file) {
                    $mimeType = $file->getMimeType();
                    if (!$mimeType) {
                        return response()->json(['status' => 0, 'message' => $errorMessage]);
                    }
                    if ($mimeType == "inode/x-empty" || $mimeType == "application/x-empty") {
                        $image_name = $file->getClientOriginalName();
                    } else {
                        $image_name = rand(111111,999999).'_'.time().'-'.$file->getClientOriginalName();
                        $file->move(public_path('files/profile'), $image_name);
                    }
                    $data2[] = $image_name;
                }
                $input['video'] = json_encode($data2);
            }

            if ($request->hasfile('pdf')) {
                foreach ($request->file('pdf') as $file) {
                    $mimeType = $file->getMimeType();
                    if (!$mimeType) {
                        return response()->json(['status' => 0, 'message' => $errorMessage]);
                    }
                    if ($mimeType == "inode/x-empty" || $mimeType == "application/x-empty") {
                        $image_name = $file->getClientOriginalName();
                    } else {
                        $image_name = rand(111111,999999).'_'.time().'-'.$file->getClientOriginalName();
                        $file->move(public_path('files/profile'), $image_name);
                    }
                    $data3[] = $image_name;
                }
                $input['pdf'] = json_encode($data3);
            }

            if ($input['payment_plan'] == "Yes") {
                $input['payment_plan'] = 0;
                if ($input['pre_handover_amount'] && $input['handover_amount']) {
                    $input['up_to_handover'] = (int)$input['pre_handover_amount'] + (int)$input['handover_amount'];
                    $input['post_handover'] = (int)$input['post_handover_amount'];
                }
            } else {
                $input['payment_plan'] = 1;
                $deletemilestone = Paymentplan::where('project_id', $id)->delete();
                $input['up_to_handover'] = $input['price'];
                $input['pre_handover_amount'] = 0;
                $input['handover_amount'] = 0;
                $input['post_handover'] = 0;
            }

            $input['project'] = $projectCheck->project;
            $input['project_id'] = $projectCheck->id;
            $input['ready_status'] = $projectCheck->ready_status;
            $projectUpdate = $project->update($input);

            $project = ManageListings::find($id);

            if ($input['payment_plan'] == "Yes") {
                $payments = Paymentplan::where('project_id', $id)->pluck('id');
                foreach ($input['milestone'] as $key => $value) {
                    if (array_key_exists('id', $value)) {
                        $payment['installment_terms'] = $value['installment_terms'] ? $value['installment_terms'] : 0;
                        $payment['milestone'] = $value['milestones'];
                        $payment['percentage'] = $value['percentage'] ? $value['percentage'] : 0;
                        $payment['amount'] = $value['amount'] ? $value['amount'] : 0;
                        $paymentplan = Paymentplan::where(['project_id' => $id, 'id' => $value['id']])->update($payment);
                    } else {
                        $payment['project_id'] = $project->id;
                        $payment['installment_terms'] = $value['installment_terms'] ? $value['installment_terms'] : 0;
                        $payment['milestone'] = $value['milestones'];
                        $payment['percentage'] = $value['percentage'] ? $value['percentage'] : 0;
                        $payment['amount'] = $value['amount'] ? $value['amount'] : 0;
                        $paymentplan = Paymentplan::create($payment);
                    }
                }
            }

            if ($projectUpdate) {
                return response()->json(['status' => 1, 'message' => 'Has been updated as a project']);
            } else {
                return response()->json(['status' => 0, 'message' => "Failed to update project details"]);
            }
//            session()->flash('response', $response);
//            return redirect()->route('manage_listings');

        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    protected function prepareUpdateData(array $data, array $project)
    {
        $data['developer_id'] = $this->arrayGet('developer_id', $data, $project['developer_id']);
        $data['project'] = $this->arrayGet('project', $data, $project['project']);
        $data['handover_year'] = $this->arrayGet('handover_year', $data, $project['handover_year']);
        $data['quarter'] = $this->arrayGet('quarter', $data, $project['quarter']);
        $data['location'] = $this->arrayGet('location', $data, $project['location']);
        $data['property'] = $this->arrayGet('property', $data, $project['property']);
        $data['size'] = $this->arrayGet('size', $data, $project['size']);
        $data['price'] = $this->arrayGet('price', $data, $project['price']);
        $data['bedrooms'] = $this->arrayGet('bedrooms', $data, $project['bedrooms']);
        $data['bathrooms'] = $this->arrayGet('bathrooms', $data, $project['bathrooms']);
        $data['rera_permit_no'] = $this->arrayGet('rera_permit_no', $data, $project['rera_permit_no']);
        $data['construction_status'] = $this->arrayGet('construction_status', $data, $project['construction_status']);
        $data['construction_date'] = $this->arrayGet('construction_date', $data, $project['construction_date']);
        $data['community'] = $this->arrayGet('community', $data, $project['community']);
        $data['subcommunity'] = $this->arrayGet('subcommunity', $data, $project['subcommunity']);
        $data['title'] = $this->arrayGet('title', $data, $project['title']);
        $data['description'] = $this->arrayGet('description', $data, $project['description']);
        $data['features'] = $this->arrayGet('features', $data, $project['features']);
        $data['image'] = $this->arrayGet('image', $data, $project['image']);
        $data['floor_plan_image'] = $this->arrayGet('floor_plan_image', $data, $project['floor_plan_image']);
        $data['video'] = $this->arrayGet('video', $data, $project['video']);
        $data['pdf'] = $this->arrayGet('pdf', $data, $project['pdf']);
        $data['payment_plan'] = $this->arrayGet('payment_plan', $data, $project['payment_plan']);
        $data['pre_handover_amount'] = $this->arrayGet('pre_handover_amount', $data, $project['pre_handover_amount']);
        $data['handover_amount'] = $this->arrayGet('handover_amount', $data, $project['handover_amount']);
        $data['up_to_handover'] = $this->arrayGet('up_to_handover', $data, $project['up_to_handover']);
        $data['post_handover'] = $this->arrayGet('post_handover', $data, $project['post_handover']);
        return $data;
    }

    public function delete_unit_milestone(Request $request)
    {
        $paymentplan = Paymentplan::find($request->id);
        $paymentplan ? $paymentplan->delete() : '';
        return response()->json($paymentplan);
    }

    public function deleteunit(Request $request, $id, $parentId = null)
    {
        try {
            $id = $request->id;
            $project = ManageListings::find($id);
            if (!($project)) {
                return $this->notFoundRequest('Project not found');
            }

            if (json_decode($project->image, true)) {
                foreach (json_decode($project->image) as $value) {
                    if (file_exists(public_path('/files/profile/' . $value))) {
                        @unlink(public_path('/files/profile/' . $value));
                    }
                }
            }

            if (json_decode($project->floor_plan_image, true)) {
                foreach (json_decode($project->floor_plan_image) as $value) {
                    if (file_exists(public_path('/files/profile/' . $value))) {
                        @unlink(public_path('/files/profile/' . $value));
                    }
                }
            }

            if (json_decode($project->video, true)) {
                foreach (json_decode($project->video) as $value) {
                    if (file_exists(public_path('/files/profile/' . $value))) {
                        @unlink(public_path('/files/profile/' . $value));
                    }
                }
            }

            if (json_decode($project->pdf, true)) {
                foreach (json_decode($project->pdf) as $value) {
                    if (file_exists(public_path('/files/profile/' . $value))) {
                        @unlink(public_path('/files/profile/' . $value));
                    }
                }
            }

            $projectDelete = $project->delete(['id' => $id]);
            $payments = Paymentplan::where('project_id', $id)->delete();
            if ($projectDelete) {
                $response['status'] = 1;
                $response['message'] = 'Has been removed as a project';

            } else {
                $response['status'] = 0;
                $response['message'] = "'Failed to remove Agent project";
            }
            return response()->json($response);
        } catch (NotFoundHttpException $ex) {
            return $this->notFoundRequest($ex);
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    public function checkindex()
    {
        $manageListing = ManageListings::orderBy('id', 'desc')->limit(1)->first();

        if ($manageListing) {
            return $manageListing['index_key'];
        }

    }


    public function setUnitStatus(Request $request)
    {
        try {
            $project = ManageListings::where('id', $request->get('id'))->first();
            if (!$project) {
                return response()->json(['status' => 0, 'message' => 'Project not found']);
            }
            $project->ready_status = $project->ready_status ? 0 : 1;
            $project->save();
            if ($project) {
                return response()->json(['status' => 1, 'message' => 'Has been update a project status']);
            } else {
                return response()->json(['status' => 0, 'message' => 'Failed to update project status']);
            }
        } catch (Exception $e) {
            return $this->sendErrorResponse($e);
        }
    }

    public function readysetUnitStatus(Request $request)
    {
        try {
            $id = $request->get('id');
            $ManageListings = ManageListings::find($id);
            if ($ManageListings->ready_status == $request->value) {
                $ManageListings->ready_status = '0';
            } else {
                $ManageListings->ready_status = '1';
            }

            $project = ManageListings::where('id', $id)->first();
            if (!($project)) {
                $response['status'] = 0;
                $response['message'] = 'Project not found';
                session()->flash('response', $response);
                return redirect()->back();
            }
            $ManageListings->save();
            if ($ManageListings) {
                if ($request->ajax()) {
                    $data['status'] = 1;
                    $data['message'] = ' has been update a project';
                    return response()->json($data);
                }
            } else {
                if ($request->ajax()) {
                    $data['status'] = 0;
                    $data['message'] = ' Failed to update project details';
                    return response()->json($data);
                }
            }
        } catch (Exception $e) {
            return $this->sendErrorResponse($e);
        }
    }

    public function soldoutsetStatus(Request $request)
    {
        try {
            $project = ManageListings::where('id', $request->get('id'))->first();
            if (!$project) {
                return response()->json(['status' => 0, 'message' => 'Project not found']);
            }
            $project->sold_out_status = $project->sold_out_status ? 0 : 1;
            $project->save();
            if ($project) {
                return response()->json(['status' => 1, 'message' => 'Has been update a project status']);
            } else {
                return response()->json(['status' => 0, 'message' => 'Failed to update project status']);
            }
        } catch (Exception $e) {
            return $this->sendErrorResponse($e);
        }
    }


    public function managecommunity()
    {
        $permission = Permission_role_mapping::where('user_id', Auth::user()->id)->where('permissions_id', 4)->first();
        return view('Admin.manage-community', compact('permission'));
    }

    public function managecommunityDatatable(Request $request)
    {
        try {
            $permission = Permission_role_mapping::where('user_id', Auth::user()->id)->where('permissions_id', 4)->first();

            $data = Community::orderBy('name')->get();

            return Datatables::of($data)
                ->addColumn('name', function ($row) {
                    return $row->name ? $row->name : '-';
                })
                ->addColumn('action', function ($row) use ($permission) {

                    $update_html = '<a class="mr-5 edit-community" href="javascript:void(0)" data-id="' . $row->id . '" data-name="' . $row->name . '"><i class="fas fa-edit" data-toggle="tooltip" data-placement="bottom" title="Edit"></i></a>';

                    $delete_html = '<a href="javascript:void(0)" class="delete-confirm" data-community_id="' . $row->id . '"><i style="color: red;" class="fas fa-trash-alt delete" data-toggle="tooltip" data-placement="bottom" title="Delete"></i></a>';

                    $update = $permission->update ? $update_html : '';
                    $delete = $permission->delete ? $delete_html : '';

                    if ($update || $delete) {
                        return $update . '' . $delete;
                    } else {
                        return '-';
                    }
                })
                ->rawColumns(['name', 'action'])
                ->make(true);
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }


    public function managesubcommunity()
    {
        $community = Community::orderBy('name')->get();
        $permission = Permission_role_mapping::where('user_id', Auth::user()->id)->where('permissions_id', 4)->first();
        return view('Admin.manage-subcommunity', compact('community', 'permission'));
    }

    public function managesubcommunityDatatable(Request $request)
    {
        try {

            $data = Subcommunity::with('community')->orderBy('name')->get();
            $permission = Permission_role_mapping::where('user_id', Auth::user()->id)->where('permissions_id', 4)->first();

            return Datatables::of($data)
                ->addColumn('community', function ($row) {
                    return $row->community ? $row->community->name : '-';
                })
                ->addColumn('name', function ($row) {
                    return $row->name ? $row->name : '-';
                })
                ->addColumn('action', function ($row) use ($permission) {

                    $update_html = '<a class="mr-5 edit-community" href="javascript:void(0)" data-com_id="' . $row->com_id . '" data-id="' . $row->id . '" data-name="' . $row->name . '"><i class="fas fa-edit" data-toggle="tooltip" data-placement="bottom" title="Edit"></i></a>';

                    $delete_html = '<a href="javascript:void(0)" class="delete-confirm" data-community_id="' . $row->id . '"><i style="color: red;" class="fas fa-trash-alt delete" data-toggle="tooltip" data-placement="bottom" title="Delete"></i></a>';

                    $update = $permission->update ? $update_html : '';
                    $delete = $permission->delete ? $delete_html : '';

                    if ($update || $delete) {
                        return $update . '' . $delete;
                    } else {
                        return '-';
                    }
                })
                ->rawColumns(['name', 'community', 'action'])
                ->make(true);
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    public function geteditcommunity(Request $request, $id)
    {
        $community = Community::find($id);
        if ($community) {
            return view('');
        }
    }

    public function getcommunity(Request $request)
    {
        $data['community'] = Community::orderBy('name')->get();
        return response()->json($data);
    }

    public function getSubcommunity(Request $request)
    {
        $data['subcommunity'] = Subcommunity::where("com_id", $request->id)
            ->orderBy('name')->get(["name", "id"]);
        return response()->json($data);
    }

    public function addSubcommunity(Request $request)
    {
        $input = $request->all();
        $data['name'] = $input['name'];
        $data['com_id'] = $input['com_id'];
        if (array_key_exists('id', $input) && !is_null($input['id'])) {

            $check = Permission_role_mapping::where('user_id', Auth()->user()->id)->where(['permissions_id' => 4, 'update' => 1])->first();
            if ($check == null) {
                return response()->json(['status' => 0, 'message' => 'You don\'t have sufficient permission, Kindly Contect admin.']);
            }

            $communityUpdate = Subcommunity::where('id', $input['id'])->update($data);
            if ($communityUpdate) {
                return response()->json(['status' => 1, 'message' => 'Community has been updated']);
            } else {
                return response()->json(['status' => 0, 'message' => 'Something went wrong!']);
            }
        } else {
            $subcommunity = Subcommunity::create($data);
            if ($subcommunity) {
                return response()->json(['status' => 1, 'message' => 'Sub community successfully saved']);
            } else {
                return response()->json(['status' => 0, 'message' => 'Something went wrong!']);
            }
        }
    }

    public function addcommunity(Request $request)
    {
        $input = $request->all();
        if (array_key_exists('id', $input) && !is_null($input['id'])) {

            $check = Permission_role_mapping::where('user_id', Auth()->user()->id)->where(['permissions_id' => 4, 'update' => 1])->first();
            if ($check == null) {
                return response()->json(['status' => 0, 'message' => 'You don\'t have sufficient permission, Kindly Contect admin.']);
            }

            $communityUpdate = Community::where('id', $input['id'])->update(['name' => $request->name]);
            if ($communityUpdate) {
                return response()->json(['status' => 1, 'message' => 'Community has been updated']);
            } else {
                return response()->json(['status' => 0, 'message' => 'Something went wrong!']);
            }
        } else {
            $community = Community::create(['name' => $request->name]);
            if ($community) {
                return response()->json(['status' => 1, 'message' => 'Community successfully saved']);
            } else {
                return response()->json(['status' => 0, 'message' => 'Something went wrong!']);
            }
        }
    }

    public function getcommunityList(Request $request)
    {
        $listdata = Listing::with('communityData', 'subcommunityData')->where('location', 'LIKE', '%' . $request['search'] . '%')->get();
        $data = array();
        if ($listdata)
            foreach ($listdata as $value) {
                $data['location'] = $value['location'];
                if (!is_null($value['communityData'])) {
                    $data['community_id'] = (!is_null($value['communityData'])) ? $value['communityData']['id'] : '';
                    $data['community'] = (!is_null($value['communityData'])) ? $value['communityData']['name'] : '';
                }
                if (!is_null($value['subcommunityData'])) {

                    $data['subcommunity_id'] = (!is_null($value['subcommunityData'])) ? $value['subcommunityData']['id'] : '';
                    $data['subcommunity'] = (!is_null($value['subcommunityData'])) ? $value['subcommunityData']['name'] : '';
                }
            }
        if ($request->ajax()) {
            return response()->json($data);
        }
    }

    public function deletecommunity(Request $request, $id)
    {
        $community = Community::find($id);
        $subcommunity = Subcommunity::where('com_id', $community->id)->delete();
        $deletecommunity = $community->delete();
        if ($deletecommunity) {
            return response()->json(['status' => 1, 'message' => 'Community delete successfully']);
        } else {
            return response()->json(['status' => 0, 'message' => 'Something went wrong!']);
        }
    }

    public function deletesubcommunity($id)
    {
        $subcommunity = Subcommunity::find($id)->delete();
        if ($subcommunity) {
            return response()->json(['status' => 1, 'message' => 'Sub community delete successfully']);
        } else {
            return response()->json(['status' => 0, 'message' => 'Something went wrong!']);
        }
    }

    public function add_note(Request $request)
    {
        $input = $this->objectToArray($request->all());
        $input['user_id'] = Auth::user()->id;
        $note = Note::create($input);
        $manage_listings = ManageListings::with('developer')->where('id', $input['proj_id'])->first();
        $note['addedby'] = $manage_listings->developer ? $manage_listings->developer->company : '';
        if ($note) {
            return response()->json(['status' => 1, 'message' => 'Has been create a note', 'note' => $note]);
        } else {
            return response()->json(['status' => 0, 'message' => 'Failed to create note details']);
        }
    }

    public function remove_note(Request $request)
    {
        $listdata = Note::find($request->get('id'));
        $delete_note = Note::where('id', $request->get('id'))->delete();
        $noteList = Note::where('proj_id', $listdata->proj_id)->get();
        $manage_listings = ManageListings::with('developer')->where('id', $listdata->proj_id)->first();
        $addedby = $manage_listings->developer ? $manage_listings->developer->company : '';
        if ($delete_note) {
            return response()->json(['status' => 1, 'message' => 'Has been delete a note', 'noteList' => $noteList, 'addedby' => $addedby]);
        } else {
            return response()->json(['status' => 0, 'message' => 'Failed to delete note details']);
        }
    }

    public function unitattachmentpost(Request $request)
    {
        try {
            $input = $this->objectToArray($request->all());
            $input['user_id'] = Auth::user()->id;
            $id = $input['user_id'];
            $user = User::where('id', $id)->first();
            if (!($user)) {
                if ($request->ajax()) {
                    $data['status'] = 0;
                    $data['message'] = 'User Not Found';
                    return response()->json($data);
                }
            }

            $requiredParams = $this->requiredRequestParams('unitattachmentpost', $id);
            $validator = Validator::make($request->all(), $requiredParams);
            if ($validator->fails()) {
                $errorMessage = implode('<br> <li>', $validator->errors()->all());
                if ($request->ajax()) {
                    $data['status'] = 0;
                    $data['message'] = $errorMessage;
                    return response()->json($data);
                }
                return redirect()->back();
            }
            if (array_key_exists('attachment', $input)) {
                foreach ($input['attachment'] as $key => $value) {
                    $attachment['user_id'] = $id;
                    $attachment['project_id'] = $request->project_id;
                    $attachment['attachment_name'] = $value['attachment_name'];
                    if ($request->attachment[$key]['attachment_multiple']) {
                        $file = $request->attachment[$key]['attachment_multiple'];
                        $image_name = time() . '-' . $value['attachment_name'] . '.' . $file->getClientOriginalExtension();
                        $file->move(public_path('project_attachment'), $image_name);
                        $attachment['attachment_multiple'] = json_encode($image_name);
                        $multipleimage = UnitMultipleAttachment::create($attachment);
                    }
                }
            }
            if ($multipleimage) {
                if ($request->ajax()) {
                    $data['status'] = 1;
                    $data['message'] = 'Has been updated as a agent user';
                    return response()->json($data);
                }
            } else {
                if ($request->ajax()) {
                    $data['status'] = 0;
                    $data['message'] = "Failed to update agent user details";
                    return response()->json($data);
                }
            }
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    public function deleteUnitAttachments(Request $request)
    {
        try {
            $deleteUnitAttachments = ManageListings::where('id',$request->id)->first();
            if($deleteUnitAttachments)
            {
                $pdf_or_excel = NULL;
                foreach (json_decode($deleteUnitAttachments->pdf) as $key => $value)
                {
                    if($key == $request->key)
                    {
                        if (file_exists(public_path('/files/profile/'.$value)))
                        {
                            @unlink(public_path('/files/profile/'.$value));
                        }
                    }
                    else
                    {
                        $pdf_or_excel[] = $value;
                    }
                }
                $deleteUnitAttachments->pdf = $pdf_or_excel ? json_encode($pdf_or_excel) : NULL;
                $deleteUnitAttachments->save();

                return response()->json(['status' => 1, 'message' => 'Attachment delete successfully']);
            }
            else
            {
                return response()->json(['status' => 0, 'message' => 'Something went wrong!']);
            }
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    public function unitremoveattachment(Request $request)
    {
        try {
            $multipleimages = UnitMultipleAttachment::find($request->id);
            if (!($multipleimages)) {
                return $this->notFoundRequest('Attachment Not Found');
            }
            if($file = json_decode($multipleimages->attachment_multiple,true)){
                if (file_exists(public_path('/project_attachment/'.$file))) {
                    @unlink(public_path('/project_attachment/'.$file));
                }
            }
            $multipledata = $multipleimages->delete(['id'=>$request->id]);
            if ($multipledata) {
                if ($request->ajax()) {
                    $data['status'] = 1;
                    $data['message'] = 'Delete Successfully';
                    return response()->json($data);
                }
            } else {
                if ($request->ajax()) {
                    $data['status']  = 0;
                    $data['message'] = "Failed to Delete Successfully";
                    return response()->json($data);
                }
            }
        } catch (NotFoundHttpException $ex) {
            return $this->notFoundRequest($ex);
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    public function mail(Request $request, $id)
    {

        $mailData = $request->get('ListingToFriend');
        $tomail = $mailData['friend_email'];
        $mailFrom = config('mail.from.address');
        $mailName = config('mail.from.name');
        $body = array('data' => $mailData['message'], 'firstName' => $mailData['name']);
        $subject = 'property-view-share';
        $this->sendMail($tomail, $mailFrom, $mailName, $body, $subject, 'mail-template');

        return redirect()->back();
    }

    public function sendMail(
        string $toEmail,
        string $mailFrom,
        string $mailName,
        array  $body,
        string $subject,
        string $fileName
    )
    {
        Mail::send(
            $fileName,
            ['body' => $body],
            function ($message) use ($toEmail, $body, $mailFrom, $mailName, $subject) {
                $message->to($toEmail)->subject($subject);
                $message->from($mailFrom, $mailName);
            }
        );
    }

    public function updateFlag(Request $request)
    {
            $manage_listing =ManageListings::find($request->id);
            if($manage_listing){
                $manage_listing->update(['flag'=>$request->flag]);
                if ($request->ajax()) {
                    $data['status']  = 1;
                    $data['message']    = ' has been update a flag';
                    return response()->json($data);
                }
            } else {
                if ($request->ajax()) {
                    $data['status']  = 0;
                    $data['message']    = "Failed to update flag";
                    return response()->json($data);
                }
            }

    }

    public function requiredRequestParams(string $action, $id = null)
    {
        switch ($action) {
            case 'create':
                $params = [
                    // 'developer_id' => 'required',
                    'project' => 'required',
                    // 'handover_year' => 'required',
                    // 'quarter' => 'required',
                    // 'location' => 'required',
                    // 'property' => 'required',
                    'size' => 'required',
                    'price' => 'required',
                    'bedrooms' => 'required',
                    'bathrooms' => 'required',
                    // 'construction_status' => 'required',
                    // 'construction_date' => 'required',
                    // 'community' => 'required',
                    // 'subcommunity' => 'required',
                    // 'title' => 'required',
                    // 'description' => 'required',
                    // 'featuresList' => 'required',

                    'payment_plan' => 'required',
                    // 'pre_handover_amount' => 'required_if:payment_plan,==,Yes',
                    // 'handover_amount' => 'required_if:payment_plan,==,Yes',
                    // 'post_handover_amount' => 'required_if:payment_plan,==,Yes',

                    // 'milestone.*.installment_terms' => 'required_if:payment_plan,==,Yes',
                    // 'milestone.*.milestones' => 'required_if:payment_plan,==,Yes',
                    // 'milestone.*.percentage' => 'required_if:payment_plan,==,Yes',
                    // 'milestone.*.amount' => 'required_if:payment_plan,==,Yes',
                ];
                break;
            case 'update':
                $params = [
                    // 'developer_id' => 'required',
                    'project' => 'required',
                    // 'handover_year' => 'required',
                    // 'quarter' => 'required',
                    // 'location' => 'required',
                    // 'property' => 'required',
                    'size' => 'required',
                    'price' => 'required',
                    'bedrooms' => 'required',
                    'bathrooms' => 'required',
                    // 'construction_status' => 'required',
                    // 'construction_date' => 'required',
                    // 'community' => 'required',
                    // 'subcommunity' => 'required',
                    // 'title' => 'required',
                    // 'description' => 'required',
                    // 'featuresList' => 'required',

                    'payment_plan' => 'required',
                    // 'pre_handover_amount' => 'required_if:payment_plan,==,Yes',
                    // 'handover_amount' => 'required_if:payment_plan,==,Yes',
                    // 'post_handover_amount' => 'required_if:payment_plan,==,Yes',

                    // 'milestone.*.installment_terms' => 'required_if:payment_plan,==,Yes',
                    // 'milestone.*.milestones' => 'required_if:payment_plan,==,Yes',
                    // 'milestone.*.percentage' => 'required_if:payment_plan,==,Yes',
                    // 'milestone.*.amount' => 'required_if:payment_plan,==,Yes',
                ];
                break;
            case 'unitattachmentpost':
                $params = [
                    'attachment.*.attachment_name' => 'required',
                    'attachment.*.attachment_multiple' => 'required'
                ];
                break;
            default:
                $params = [];
                break;
        }
        return $params;
    }
}
