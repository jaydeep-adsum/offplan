<?php

namespace App\Http\Controllers;

use App\Models\Developer;
use App\Models\LocalImage;
use App\Models\ManageProject;
use App\Models\Features;
use App\Models\Milestones;
use App\Models\Community;
use App\Models\Categories;
use App\Models\Subcommunity;
use App\Models\ProjectPaymentPlan;
use App\Models\ProjectBedRooms;
use App\Models\ProjectNotes;
use App\Models\ProjectDocuments;
use App\Models\ProjectReminders;
use App\Models\User;
use App\Models\ProjectAssignAgents;
use Illuminate\Routing\Controller;
use App\Traits\ResponseTrait;
use App\Traits\RequestTrait;
use Illuminate\Http\Request;
use App\Traits\UtilityTrait;
use Carbon;
use Datatables;
use Illuminate\Support\Facades\Validator;
use Auth;
use Image;

class ManageProjectController extends Controller
{
    use ResponseTrait, UtilityTrait;

    public function projectIndex(Request $request)
    {
        $project = ManageProject::where(['ready_status' => '0', 'sold_out_status' => '0'])->distinct()->orderBy('project')->get('project');
        $typeList = Categories::orderBy('catName')->pluck('catName');
        $userlist = Developer::orderBy('company')->get();
        $community = Community::orderBy('name')->get();
        $agents = User::where('role',2)->get();
        return view('Admin.project.manageProject',compact('project','typeList','userlist','community','agents'));
    }

    public function datatableManageProject(Request $request)
    {
        try {

            if($request->page_status == "manage_ready_project")
            {
                $editRoute = 'editReadyProject';
                $previewRoute = 'previewReadyProject';
                $data = ManageProject::with('developer','projectBedrooms','communitys','projectReminders','projectAssignAgents.user')->where(['ready_status' => 1 , 'sold_out_status' => 0])->orderBy('updated_at', 'desc');
            }
            else if($request->page_status == "manage_sold_out_project")
            {
                $editRoute = 'editSoldOutProject';
                $previewRoute = 'previewSoldOutProject';
                $data = ManageProject::with('developer','projectBedrooms','communitys','projectReminders','projectAssignAgents.user')->where('sold_out_status',1)->orderBy('updated_at', 'desc');
            }
            else if($request->page_status == "manage_overdue_project")
            {
                $editRoute = 'editOverdueProject';
                $previewRoute = 'previewOverdueProject';
                $data = ManageProject::with('developer','projectBedrooms','communitys','projectReminders','projectAssignAgents.user')->whereHas('projectReminders',function($query){
                    $query->whereDate('reminder_date','<=',Carbon\Carbon::now('Europe/Stockholm'))->where('status',0);
                });
            }
            else
            {
                $editRoute = 'editProject';
                $previewRoute = 'previewProject';
                $data = ManageProject::with('developer','projectBedrooms','communitys','projectReminders','projectAssignAgents.user')->where(['ready_status' => 0 , 'sold_out_status' => 0])->orderBy('updated_at', 'desc');
            }

            return Datatables::eloquent($data)
                ->addIndexColumn()
                ->addColumn('active', function ($row) {
                    $class = 0;
                    if(!$row->projectReminders->isEmpty())
                    {
                        foreach($row->projectReminders as $item)
                        {
                            if($item->reminder_date <= \Carbon\Carbon::now()->toDateString() && $item->status == 0)
                            {
                                $class = 1;
                                return $class;
                            }
                            else
                            {
                                $class = 0;
                            }
                        }
                    }
                    return $class;
                })
                ->addColumn('rf_no', function($row){
                    return $row->rf_no ? $row->rf_no : '-';
                })
                ->addColumn('assign_to', function($row){
                    return $row->projectAssignAgents ? $row->projectAssignAgents->user->name : '-';
                })
                ->addColumn('project', function($row){
                    return $row->project ? $row->project : '-';
                })
                ->addColumn('developer_company', function($row){
                    return $row->developer ? $row->developer->company : '-';
                })
                ->addColumn('location', function($row){
                    return $row->communitys ? $row->communitys->name.', Dubai, UAE' : ', Dubai, UAE';
                })
                ->addColumn('completion_status', function($row){
                    if($row->completion_status == 1)
                    {
                        return 'Ready';
                    }
                    else
                    {
                        return ($row->quarter && $row->handover_year) ? $row->quarter .', '. $row->handover_year : '-';
                    }
                })
                ->addColumn('price_range', function($row){
                    $bed = array();
                    foreach($row->projectBedrooms as $beddata)
                    {
                        if($beddata->min_price && $beddata->max_price)
                        {
                            if($beddata->bed_rooms == "Studio")
                            {
                                $bedrooms = $beddata->bed_rooms.' : ';
                            }
                            else
                            {
                                $bedrooms = $beddata->bed_rooms.'BR : ';
                            }
                            array_push($bed,$bedrooms.number_format($beddata->min_price,0, '.', ',').' - '.number_format($beddata->max_price,0, '.', ',').', ');
                        }
                    }
                    return $bed ? join("<br>",$bed) : '-';
                })
                ->addColumn('payment_plan_comments', function($row){
                    return $row->payment_plan_comments ? $row->payment_plan_comments : '-';
                })
                ->addColumn('updated_at', function($row){
                    return $row->updated_at ? date('d-M-y',strtotime($row->updated_at)) : '-';
                })
                ->addColumn('action', function($row) use($editRoute, $previewRoute){

                    $visibilityReady = "";
                    $visibilitySoldOut = "";

                    if($row->ready_status && $row->sold_out_status)
                    {
                        $visibilityReady = '<i class="fa fa-check" aria-hidden="true"></i>';
                        $visibilitySoldOut = '<i class="fa fa-check" aria-hidden="true"></i>';
                    }
                    else
                    {
                        if($row->ready_status)
                        {
                            $visibilityReady = '<i class="fa fa-check" aria-hidden="true"></i>';
                            $visibilitySoldOut = '<i class="fa fa-check" aria-hidden="true" style="visibility: hidden;"></i>';
                        }

                        if($row->sold_out_status)
                        {
                            $visibilityReady = '<i class="fa fa-check" aria-hidden="true" style="visibility: hidden;"></i>';
                            $visibilitySoldOut = '<i class="fa fa-check" aria-hidden="true"></i>';
                        }
                    }

                    $agent_id = 0;
                    if($row->projectAssignAgents)
                    {
                        $agent_id = $row->projectAssignAgents->agent_id;
                    }

                    if(Auth::user()->role == 2)
                    {
                        if(Auth::user()->id == $agent_id)
                        {
                            return '<div class="row">
                                        <div class="col-6">
                                            <a href="'. route($editRoute, ['id' => $row->id]) .'" target="_blank">
                                                <i class="fas fa-edit" data-toggle="tooltip" data-placement="bottom" title="Edit"></i>
                                            </a>
                                        </div>
                                        <div class="col-6 pl-1">
                                            <a href="'. route($previewRoute, ['id' => $row->id]) .'">
                                                <i class="fas fa-eye" data-toggle="tooltip" data-placement="bottom" title="Preview"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 mt-2 pl-2">
                                            <div class="btn-group dropleft">
                                                <a href="javascript:void(0)" class="btn dropdown-toggle-split status-change" data-toggle="dropdown" >
                                                    <i class="fas fa-info" data-toggle="tooltip" data-placement="bottom" title="Status Change">
                                                    </i>
                                                </a>
                                                <div class="dropdown-menu" style="min-width: auto;">
                                                    <button class="dropdown-item readyclick" data-listid="'.$row->id.'" type="button" style="padding: 5px 15px;">'. $visibilityReady .' Ready</button>
                                                    <button class="dropdown-item soldoutclick" data-listid="'.$row->id.'" type="button" style="padding: 5px 15px;">'. $visibilitySoldOut .' Sold Out</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>';
                        }
                        else
                        {
                            return '<div class="col-6 pl-0">
                                        <a href="'. route($previewRoute, ['id' => $row->id]) .'">
                                            <i class="fas fa-eye mt-2" data-toggle="tooltip" data-placement="bottom" title="Preview"></i>
                                        </a>
                                    </div>';
                        }
                    }
                    else
                    {
                        return '<div class="row">
                                    <div class="col-6">
                                        <a href="'. route($editRoute, ['id' => $row->id]) .'" target="_blank">
                                            <i class="fas fa-edit" data-toggle="tooltip" data-placement="bottom" title="Edit"></i>
                                        </a>
                                    </div>
                                    <div class="col-6 pl-1">
                                        <a href="javascript:void(0)" class="delete-confirm" data-listid="'.$row->id.'">
                                        <i style="color: red" class="fas fa-trash-alt delete" data-toggle="tooltip" data-placement="bottom" title="Delete"></i></a>
                                    </div>
                                </div>
                                <div class="row align-items-end">
                                    <div class="col-6">
                                        <div class="btn-group dropleft">
                                            <a href="javascript:void(0)" class="mt-2 btn dropdown-toggle-split status-change p-0" data-toggle="dropdown" >
                                                <i class="fas fa-info" data-toggle="tooltip" data-placement="bottom" title="Status Change">
                                                </i>
                                            </a>
                                            <div class="dropdown-menu" style="min-width: auto;">
                                                <button class="dropdown-item readyclick" data-listid="'.$row->id.'" type="button" style="padding: 5px 15px;">'. $visibilityReady .' Ready</button>
                                                <button class="dropdown-item soldoutclick" data-listid="'.$row->id.'" type="button" style="padding: 5px 15px;">'. $visibilitySoldOut .' Sold Out</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 pl-1">
                                        <a href="'. route($previewRoute, ['id' => $row->id]) .'">
                                            <i class="fas fa-eye mt-2" data-toggle="tooltip" data-placement="bottom" title="Preview"></i>
                                        </a>
                                    </div>
                                </div><div class="row">
                                <div class="col-6">
                                    <a class="assignedProject" data-listid="'.$row->id.'" data-project_name="'. $row->project .'" data-agent_id="'. $agent_id .'">
                                        <i style="color: #9400D3;" class="fa fa-user-plus mt-2" data-toggle="tooltip" data-placement="bottom" title="Assign Project"></i>
                                    </a>
                                </div>
                            </div>';
                    }

                })
                ->filter(function ($query) use ($request) {

                    $input = $this->objectToArray($request->all());

                    if(isset($input['company'])){
                        $company = $input['company'];
                        $query = $query->whereHas('developer',function($query)use($company){
                            $query->where('company',$company);
                        });
                    }
                    if(isset($input['project'])){
                        $query = $query->where('project',$input['project']);
                    }
                    if(isset($input['community'])){
                        $query = $query->where('community',$input['community']);
                    }
                    if(isset($input['property'])){
                        $query = $query->where('property',$input['property']);
                    }
                    if(isset($input['project_status']))
                    {
                        $query = $query->where('completion_status',$input['project_status']);

                        if($input['project_status'] == 2)
                        {
                            if(isset($input['quarter'])){
                                $query = $query->where('quarter',$input['quarter'] );
                            }
                            if(isset($input['handover_year'])){
                                $query = $query->where('handover_year',$input['handover_year'] );
                            }
                        }
                    }
                    else
                    {
                        if(isset($input['quarter'])){
                            $query = $query->where('quarter',$input['quarter'] );
                        }
                        if(isset($input['handover_year'])){
                            $query = $query->where('handover_year',$input['handover_year'] );
                        }
                    }
                    if(isset($input['payment_plan'])){
                        $query = $query->where('payment_plan',$input['payment_plan']);
                    }
                    if(isset($input['assigned_agents'])){
                        $agent = $input['assigned_agents'];
                        $query = $query->whereHas('projectAssignAgents',function($query)use($agent){
                            $query->where('agent_id',$agent);
                        });
                    }
                    if(isset($input['number_of_bedrooms'])){
                        $number_of_bedrooms = $input['number_of_bedrooms'];
                        $query = $query->whereHas('projectBedrooms',function($query)use($number_of_bedrooms){
                            $query->where('bed_rooms',$number_of_bedrooms);
                        });
                    }
                    if(isset($input['min_price']) && isset($input['max_price']))
                    {
                        $min_price = $input['min_price'];
                        $max_price = $input['max_price'];
                        $query = $query->whereHas('projectBedrooms',function($query)use($min_price, $max_price){
                            $query->whereBetween('min_price', [$min_price, $max_price])->orWhereBetween('max_price', [$min_price, $max_price]);
                        });
                    }
                    else
                    {
                        if(isset($input['min_price']) || isset($input['max_price']))
                        {
                            if(isset($input['min_price']))
                            {
                                $min_price = $input['min_price'];
                                $query = $query->whereHas('projectBedrooms',function($query)use($min_price){
                                    $query->where('min_price', '>=', $min_price);
                                });
                            }
                            else
                            {
                                $max_price = $input['max_price'];
                                $query = $query->whereHas('projectBedrooms',function($query)use($max_price){
                                    $query->where('max_price', '<=', $max_price);
                                });
                            }
                        }
                    }
                })
                ->rawColumns(['rf_no','project','developer_company','communitys','completion_status','price_range','updated_at','action'])
                ->make(true);
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    public function addProject(Request $request)
    {
        $developerData = Developer::orderBy('company')->pluck('company','id');
        $community = Community::orderBy('name')->get();
        $milestoneData = Milestones::pluck('milestone','id');
        $typeList = Categories::orderBy('catName')->pluck('catName');
        $featuresList = Features::orderBy('fname')->get();
        return view('Admin.project.addProject',compact('developerData','milestoneData','community','typeList','featuresList'));
    }

    public function editProject($id)
    {
        $developerData = Developer::orderBy('company')->pluck('company','id');
        $milestoneData = Milestones::pluck('milestone','id');
        $community = Community::orderBy('name')->get();
        $typeList = Categories::orderBy('catName')->pluck('catName');
        $featuresList = Features::orderBy('fname')->get();
        $project = ManageProject::with(['developer','paymentPlanDetails','projectBedrooms'])->where('id',$id)->first();
        $subcommunity = Subcommunity::where(['com_id' => $project['community']])->get();
        return view('Admin.project.editProject',compact('developerData','typeList','community','project','featuresList','milestoneData','subcommunity'));
    }

    public function getLocalImageProduct(Request $request)
    {
        try {
            if($request->local_image_id)
            {
                $localDataGet = LocalImage::where('local_image_id',$request->local_image_id)->first();
                if($localDataGet)
                {
                    $image = json_decode($localDataGet->local_image_file);
                    $imageArray = array();
                    foreach($image as $value)
                    {
                        array_push($imageArray, asset('public/localSaveImage/'.$value));
                    }
                    return response()->json(['status' => 1, 'data' => ['imageArray' => $imageArray, 'imageName' => $image] ,'message' => 'successfully fetch.']);
                }
                else
                {
                    return response()->json(['status' => 0, 'message' => 'Something went wrong!']);
                }
            }
            else
            {
                $id = LocalImage::get_random_string();
                return response()->json(['status' => 1, 'data' => $id ,'message' => 'get random string successfully.']);
            }
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    public function addProjectSubmit(Request $request)
    {
        try {
            $input = $this->objectToArray($request->input());
            $requiredParams = $this->requiredRequestParams('create');
            $meassage = [
                            'quarter.required_if' => 'The quarter field is required',
                            'handover_year.required_if' => 'The handover year field is required'
                        ];
            $validator = Validator::make($input, $requiredParams, $meassage);

            if($validator->fails())
            {
                $errorMessage = implode('<br> <li>', $validator->errors()->all());
                return response()->json(['status' => 0, 'message' => $errorMessage]);
            }

            if($request->get('featuresList'))
            {
                $input['features'] = json_encode($input['featuresList']);
            }

            $input['user_id'] = Auth::user()->id;

            if($request->hasfile('filesList'))
            {
                foreach($request->file('filesList') as $file)
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
                        $watermark = Image::make('public/files/logo.png');
                        $img->insert($watermark, 'center', 5, 5);
                        $img->save('public/projectFiles/images/'.$image_name);
                    }
                    $data[] = $image_name;
                }
                $dataProject['image'] = json_encode($data);
            }

            if($request->hasfile('floor_plan_image'))
            {
                foreach($request->file('floor_plan_image') as $file)
                {
                    $mimeType = $file->getMimeType();
                    if(!$mimeType)
                    {
                        return response()->json(['status' => 0, 'message' => 'mime type does not exist']);
                    }
                    if( $mimeType == "inode/x-empty"  || $mimeType == "application/x-empty")
                    {
                        $floor_plan_image = $file->getClientOriginalName();
                    }
                    else
                    {
                        $floor_plan_image = rand(111111,999999).'_'.time().'-'.$file->getClientOriginalName();
                        $file->move(public_path('projectFiles/floor_plan_image'), $floor_plan_image);
                    }
                    $data1[] = $floor_plan_image;
                }
                $dataProject['floor_plan_image'] = json_encode($data1);
            }

            if($request->hasfile('video'))
            {
                foreach($request->file('video') as $file)
                {
                    $mimeType = $file->getMimeType();
                    if(!$mimeType)
                    {
                        return response()->json(['status' => 0, 'message' => 'mime type does not exist']);
                    }
                    if( $mimeType == "inode/x-empty"  || $mimeType == "application/x-empty")
                    {
                        $video_name = $file->getClientOriginalName();
                    }
                    else
                    {
                        $video_name = rand(111111,999999).'_'.time().'-'.$file->getClientOriginalName();
                        $file->move(public_path('projectFiles/video'), $video_name);
                    }
                    $data2[] = $video_name;
                }
                $dataProject['video'] = json_encode($data2);
            }

            if($request->hasfile('pdf'))
            {
                foreach($request->file('pdf') as $file)
                {
                    if(!$mimeType)
                    {
                        return response()->json(['status' => 0, 'message' => 'mime type does not exist']);
                    }
                    if( $mimeType == "inode/x-empty"  || $mimeType == "application/x-empty")
                    {
                        $pdf_name = $file->getClientOriginalName();
                    }
                    else
                    {
                        $pdf_name = rand(111111,999999).'_'.time().'-'.$file->getClientOriginalName();
                        $file->move(public_path('projectFiles/pdf'), $pdf_name);
                    }
                    $data3[] = $pdf_name;
                }
                $dataProject['pdf'] = json_encode($data3);
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


            if(empty($input['ready_status']))
            {
                $dataProject['ready_status'] = ($input['completion_status'] == 1) ? 1 : 0;
            }
            else
            {
                $dataProject['ready_status'] = 1;
                $input['completion_status'] = 1;
            }

            $dataProject['user_id'] = $input['user_id'] ? $input['user_id'] : 0;
            $dataProject['developer_id'] = $input['developer_id'] ? $input['developer_id'] : 0;
            $dataProject['project'] = $input['project'];
            $dataProject['completion_status'] = $input['completion_status'] ? $input['completion_status'] : 0;
            if($dataProject['completion_status'] == 2)
            {
                $dataProject['quarter'] = !empty($input['quarter']) ? $input['quarter'] : NULL;
                $dataProject['handover_year'] = !empty($input['handover_year']) ? $input['handover_year'] : NULL;
            }
            else
            {
                $dataProject['quarter'] = NULL;
                $dataProject['handover_year'] = NULL;
            }
            $dataProject['commission'] = $input['commission'] ? $input['commission'] : 0;
            $dataProject['location'] = $input['location'];
            $dataProject['latitude'] = $input['latitude'];
            $dataProject['longitude'] = $input['longitude'];
            $dataProject['property'] = !empty($input['property']) ? $input['property'] : NULL;
            $dataProject['rf_no'] = (Auth::user()->role == 1) ? 'GS'.$index_key : Auth::user()->user_code.$index_key;
            $dataProject['rera_permit_no'] = $input['rera_permit_no'];
            $dataProject['index_key'] = $input['index_key'];
            $dataProject['construction_status'] = !empty($input['construction_status']) ? $input['construction_status'] : 0;
            $dataProject['construction_date'] = $input['construction_date'];
            $dataProject['community'] = !empty($input['community']) ? $input['community'] : 0;
            $dataProject['subcommunity'] = !empty($input['subcommunity']) ? $input['subcommunity'] : 0;
            $dataProject['description'] = $input['description'];
            $dataProject['features'] = !empty($input['features']) ? $input['features'] : NULL;
            $dataProject['payment_plan'] = ($input['payment_plan'] == "Yes") ? 1 : 0;
            $dataProject['payment_plan_comments'] = !empty($input['payment_plan_comments']) ? $input['payment_plan_comments'] : NULL;
            $dataProject['sold_out_status'] = !empty($input['sold_out_status']) ? 1 : 0;

            $project = ManageProject::create($dataProject);

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
                            $paymentplan = ProjectPaymentPlan::create($payment);
                        }
                    }
                }
            }

            $bedrooms['project_id'] = $project->id;
            foreach ($input['bedrooms'] as $key => $value)
            {
                if(!empty($value['bed_rooms']) && !empty($value['min_price']) && !empty($value['max_price']))
                {
                    $bedrooms['bed_rooms'] = $value['bed_rooms'];
                    $bedrooms['min_price'] = $value['min_price'];
                    $bedrooms['max_price'] = $value['max_price'];
                    $createBedrooms = ProjectBedRooms::create($bedrooms);
                }
            }

            if($project)
            {
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

    public function editProjectSubmit(Request $request)
    {
        try {
            $id = $request->get('id');
            $project = ManageProject::where('id',$id)->first();
            if(!$project)
            {
                return response()->json(['status' => 0, 'message' => 'Project not found']);
            }

            $input = $this->objectToArray($request->input());
            $input['id'] = $id;

            $requiredParams = $this->requiredRequestParams('update', $id);
            $validator = Validator::make($input, $requiredParams);

            if ($validator->fails())
            {
                return response()->json(['status' => 0, 'message' => implode('<br> <li>', $validator->errors()->all())]);
            }

            if($request->get('featuresList'))
            {
                $input['features'] = json_encode($input['featuresList']);
            }

            if($request->hasfile('filesList'))
            {
                foreach($request->file('filesList') as $file)
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
                        $watermark = Image::make('public/files/logo.png');
                        $img->insert($watermark, 'center', 5, 5);
                        $img->save('public/projectFiles/images/'.$image_name);
                    }
                    $data[] = $image_name;
                }
                $dataProject['image'] = json_encode($data);
            }

            if($request->hasfile('floor_plan_image'))
            {
                foreach($request->file('floor_plan_image') as $file)
                {
                    $mimeType = $file->getMimeType();
                    if(!$mimeType)
                    {
                        return response()->json(['status' => 0, 'message' => 'mime type does not exist']);
                    }
                    if( $mimeType == "inode/x-empty"  || $mimeType == "application/x-empty")
                    {
                        $floor_plan_image = $file->getClientOriginalName();
                    }
                    else
                    {
                        $floor_plan_image = rand(111111,999999).'_'.time().'-'.$file->getClientOriginalName();
                        $file->move(public_path('projectFiles/floor_plan_image'), $floor_plan_image);
                    }
                    $data1[] = $floor_plan_image;
                }
                $dataProject['floor_plan_image'] = json_encode($data1);
            }

            if($request->hasfile('video'))
            {
                foreach($request->file('video') as $file)
                {
                    $mimeType = $file->getMimeType();
                    if(!$mimeType)
                    {
                        return response()->json(['status' => 0, 'message' => 'mime type does not exist']);
                    }
                    if( $mimeType == "inode/x-empty"  || $mimeType == "application/x-empty")
                    {
                        $video_name = $file->getClientOriginalName();
                    }
                    else
                    {
                        $video_name = rand(111111,999999).'_'.time().'-'.$file->getClientOriginalName();
                        $file->move(public_path('projectFiles/video'), $video_name);
                    }
                    $data2[] = $video_name;
                }
                $dataProject['video'] = json_encode($data2);
            }

            if($request->hasfile('pdf'))
            {
                foreach($request->file('pdf') as $file)
                {
                    $mimeType = $file->getMimeType();
                    if(!$mimeType)
                    {
                        return response()->json(['status' => 0, 'message' => 'mime type does not exist']);
                    }
                    if( $mimeType == "inode/x-empty"  || $mimeType == "application/x-empty")
                    {
                        $pdf_name = $file->getClientOriginalName();
                    }
                    else
                    {
                        $pdf_name = rand(111111,999999).'_'.time().'-'.$file->getClientOriginalName();
                        $file->move(public_path('projectFiles/pdf'), $pdf_name);
                    }
                    $data3[] = $pdf_name;
                }
                $dataProject['pdf'] = json_encode($data3);
            }

            if(empty($input['ready_status']))
            {
                $dataProject['ready_status'] = ($input['completion_status'] == 1) ? 1 : 0;
            }
            else
            {
                $dataProject['ready_status'] = 1;
                $input['completion_status'] = 1;
            }


            $dataProject['developer_id'] = $input['developer_id'] ? $input['developer_id'] : 0;
            $dataProject['project'] = $input['project'];
            $dataProject['completion_status'] = $input['completion_status'] ? $input['completion_status'] : 0;
            if($dataProject['completion_status'] == 2)
            {
                $dataProject['quarter'] = !empty($input['quarter']) ? $input['quarter'] : NULL;
                $dataProject['handover_year'] = !empty($input['handover_year']) ? $input['handover_year'] : NULL;
            }
            else
            {
                $dataProject['quarter'] = NULL;
                $dataProject['handover_year'] = NULL;
            }
            $dataProject['commission'] = $input['commission'] ? $input['commission'] : 0;
            $dataProject['location'] = $input['location'];
            $dataProject['latitude'] = $input['latitude'];
            $dataProject['longitude'] = $input['longitude'];
            $dataProject['property'] = !empty($input['property']) ? $input['property'] : NULL;
            $dataProject['rera_permit_no'] = $input['rera_permit_no'];
            $dataProject['construction_status'] = !empty($input['construction_status']) ? $input['construction_status'] : 0;
            $dataProject['construction_date'] = $input['construction_date'];
            $dataProject['community'] = !empty($input['community']) ? $input['community'] : 0;
            $dataProject['subcommunity'] = !empty($input['subcommunity']) ? $input['subcommunity'] : 0;
            $dataProject['description'] = $input['description'];
            $dataProject['features'] = !empty($input['features']) ? $input['features'] : NULL;
            $dataProject['payment_plan'] = ($input['payment_plan'] == "Yes") ? 1 : 0;
            $dataProject['payment_plan_comments'] = !empty($input['payment_plan_comments']) ? $input['payment_plan_comments'] : NULL;

            $projectUpdate = $project->update($dataProject);

            $project = ManageProject::find($id);

            if($input['payment_plan'] == "Yes")
            {
                foreach ($input['milestone'] as $key => $value) {
                    if(!empty($value['milestones']))
                    {
                        if(array_key_exists('id',$value))
                        {
                            $payment['installment_terms'] = $value['installment_terms'] ? $value['installment_terms'] : 0;
                            $payment['milestone'] = $value['milestones'];
                            $payment['percentage'] = $value['percentage'] ? $value['percentage'] : 0;
                            $projectPaymentPlan = ProjectPaymentPlan::where(['project_id'=> $id, 'id' => $value['id']])->update($payment);
                        } else{
                            $payment['project_id'] = $project->id;
                            $payment['installment_terms'] = $value['installment_terms'] ? $value['installment_terms'] : 0;
                            $payment['milestone'] = $value['milestones'];
                            $payment['percentage'] = $value['percentage'] ? $value['percentage'] : 0;
                            $projectPaymentPlan = ProjectPaymentPlan::create($payment);
                        }
                    }
                }
            }
            else
            {
                $deleteProjectMilestone = ProjectPaymentPlan::where('project_id',$project->id)->delete();
            }

            foreach ($input['bedrooms'] as $key => $value)
            {
                if(!empty($value['bed_rooms']) && !empty($value['min_price']) && !empty($value['max_price']))
                {
                    if(array_key_exists('id',$value))
                    {
                        $bedrooms['project_id'] = $project->id;
                        $bedrooms['bed_rooms'] = $value['bed_rooms'];
                        $bedrooms['min_price'] = $value['min_price'];
                        $bedrooms['max_price'] = $value['max_price'];
                        $createBedrooms = ProjectBedRooms::where(['id' => $value['id'], 'project_id' => $project->id])->update($bedrooms);
                    }
                    else
                    {
                        $bedrooms['project_id'] = $project->id;
                        $bedrooms['bed_rooms'] = $value['bed_rooms'];
                        $bedrooms['min_price'] = $value['min_price'];
                        $bedrooms['max_price'] = $value['max_price'];
                        $createBedrooms = ProjectBedRooms::create($bedrooms);
                    }
                }
            }

            if ($projectUpdate)
            {
                return response()->json(['status' => 1, 'message' => 'Has been updated as a project']);
            }
            else
            {
                return response()->json(['status' => 0, 'message' => 'Failed to update project details']);
            }
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    public function bedroomsDelete(Request $request)
    {
        $deleteBedrooms = ProjectBedRooms::where('id', $request->id)->delete();
        if($deleteBedrooms)
        {
            return response()->json(['status' => 1, 'message' => 'Bedrooms delete successfully']);
        }
        else
        {
            return response()->json(['status' => 0, 'message' => 'Something went wrong!']);
        }
    }

    public function deleteProjectmilestone($id)
    {
        $ProjectPaymentPlan = ProjectPaymentPlan::find($id)->delete();
        return redirect()->back();
    }

    public function deleteProject(Request $request)
    {
        try {
            $project = ManageProject::where('id',$request->id)->first();

            if(!$project)
            {
                return response()->json(['status' => 0, 'message' => 'Project not found']);
            }

            if(json_decode($project->image,true)){
                foreach (json_decode($project->image) as $value) {
                    if (file_exists(public_path('/projectFiles/image/'.$value))) {
                        @unlink(public_path('/projectFiles/image/'.$value));
                    }
                }
            }

            if(json_decode($project->floor_plan_image,true)){
                foreach (json_decode($project->floor_plan_image) as $value) {
                    if (file_exists(public_path('/projectFiles/floor_plan_image/'.$value))) {
                        @unlink(public_path('/projectFiles/floor_plan_image/'.$value));
                    }
                }
            }

            if(json_decode($project->video,true)){
                foreach (json_decode($project->video) as $value) {
                    if (file_exists(public_path('/projectFiles/video/'.$value))) {
                        @unlink(public_path('/projectFiles/video/'.$value));
                    }
                }
            }

            if(json_decode($project->pdf,true)){
                foreach (json_decode($project->pdf) as $value) {
                    if (file_exists(public_path('/projectFiles/pdf/'.$value))) {
                        @unlink(public_path('/projectFiles/pdf/'.$value));
                    }
                }
            }

            $projectDelete = $project->delete();

            $projectBedroomsDelete = ProjectBedRooms::where('project_id',$request->id)->delete();
            $projectPaymentPlanDelete = ProjectPaymentPlan::where('project_id',$request->id)->delete();

            if($projectDelete)
            {
                return response()->json(['status' => 1, 'message' => 'Has been removed as a project']);
            }
            else
            {
                return response()->json(['status' => 0, 'message' => 'Failed to remove Agent project']);
            }
        } catch (NotFoundHttpException $ex) {
            return $this->notFoundRequest($ex);
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    public function moveToReadyProject(Request $request)
    {
        $moveToReadyProject = ManageProject::where('id',$request->id)->first();
        if($moveToReadyProject)
        {
            if($moveToReadyProject->ready_status)
            {
                $moveToReadyProject->ready_status = 0;
                if($moveToReadyProject->quarter && $moveToReadyProject->handover_year)
                {
                    $moveToReadyProject->completion_status = 2;
                }
                else
                {
                    $moveToReadyProject->completion_status = 0;
                    $moveToReadyProject->quarter = NULL;
                    $moveToReadyProject->handover_year = NULL;
                }
            }
            else
            {
                $moveToReadyProject->ready_status = 1;
                $moveToReadyProject->completion_status = 1;
            }
            $moveToReadyProject->save();
            return response()->json(['status' => 1, 'message' => 'Has been update a project status']);
        }
        else
        {
            return response()->json(['status' => 0, 'message' => 'Project Not Found']);
        }
    }

    public function moveToSoldOutProject(Request $request)
    {
        $moveToSoldOutProject = ManageProject::where('id',$request->id)->first();
        if($moveToSoldOutProject)
        {
            $moveToSoldOutProject->sold_out_status = $moveToSoldOutProject->sold_out_status ? 0 : 1;
            $moveToSoldOutProject->save();
            return response()->json(['status' => 1, 'message' => 'Has been update a project status']);
        }
        else
        {
            return response()->json(['status' => 0, 'message' => 'Project Not Found']);
        }
    }

    public function checkindex()
    {
        $manageProject = ManageProject::orderBy('id','desc')->limit(1)->first();
        if($manageProject)
        {
            return $manageProject['index_key'];
        }
    }

    public function requiredRequestParams(string $action, $id = null)
    {
        switch ($action) {
            case 'create':
                $params = [
                    'project' => 'required',
                    'developer_id' => 'required',
                    'completion_status' => 'required',

                    'quarter' => 'required_if:completion_status,==,2',
                    'handover_year' => 'required_if:completion_status,==,2',

                    'location' => 'required',
                    'community' => 'required',
                    'subcommunity' => 'required',

                    'payment_plan' => 'required',

                    // 'milestone.*.installment_terms' => 'required_if:payment_plan,==,Yes',
                    // 'milestone.*.milestones' => 'required_if:payment_plan,==,Yes',
                    // 'milestone.*.percentage' => 'required_if:payment_plan,==,Yes',
                ];
                break;
            case 'update':
                $params = [
                    'project' => 'required',
                    'developer_id' => 'required',
                    'completion_status' => 'required',

                    'quarter' => 'required_if:completion_status,==,2',
                    'handover_year' => 'required_if:completion_status,==,2',

                    'location' => 'required',
                    'community' => 'required',
                    'subcommunity' => 'required',

                    'payment_plan' => 'required',

                    // 'milestone.*.installment_terms' => 'required_if:payment_plan,==,Yes',
                    // 'milestone.*.milestones' => 'required_if:payment_plan,==,Yes',
                    // 'milestone.*.percentage' => 'required_if:payment_plan,==,Yes',
                ];
                break;
            case 'project_document':
                $params = [
                    'attachment.*.document_name' => 'required',
                    'attachment.*.document_file' => 'required'
                ];
                break;
            case 'assignProject':
                $params = [
                    'agents' => 'required',
                ];
                break;
            default:
                $params = [];
                break;
        }
        return $params;
    }

    public function readyProjectIndex()
    {
        $project = ManageProject::where(['ready_status' => '1', 'sold_out_status' => '0'])->distinct()->orderBy('project')->get('project');
        $typeList = Categories::orderBy('catName')->pluck('catName');
        $userlist = Developer::orderBy('company')->get();
        $community = Community::orderBy('name')->get();
        $agents = User::where('role',2)->get();
        return view('Admin.project.manageReadyProject',compact('project','typeList','userlist','community','agents'));
    }

    public function soldOutProjectIndex()
    {
        $project = ManageProject::where(['sold_out_status' => '1'])->distinct()->orderBy('project')->get('project');
        $typeList = Categories::orderBy('catName')->pluck('catName');
        $userlist = Developer::orderBy('company')->get();
        $community = Community::orderBy('name')->get();
        $agents = User::where('role',2)->get();
        return view('Admin.project.manageSoldOutProject',compact('project','typeList','userlist','community','agents'));
    }

    public function overdueProjectIndex()
    {
        $project = ManageProject::whereHas('projectReminders',function($query){
            $query->whereDate('reminder_date','<=',Carbon\Carbon::now('Europe/Stockholm'))->where('status',0);
        })->distinct()->orderBy('project')->get('project');
        $typeList = Categories::orderBy('catName')->pluck('catName');
        $userlist = Developer::orderBy('company')->get();
        $community = Community::orderBy('name')->get();
        $agents = User::where('role',2)->get();
        return view('Admin.project.manageOverdue',compact('project','typeList','userlist','community','agents'));
    }

    public function previewProject($id)
    {
        $manage_project = ManageProject::with('developer', 'multipleContact', 'paymentPlanDetails', 'community', 'subcommunity', 'projectBedrooms', 'projectReminders', 'projectReminders.user' , 'projectAssignAgents')->where('id',$id)->first();
        $community = Community::orderBy('name')->where('id',$manage_project->community)->get();
        $subcommunity = Subcommunity::orderBy('name')->where('id',$manage_project->subcommunity)->get();
        $projectNote = ProjectNotes::with('agentName:id,name')->where('proj_id',$id)->get();
        $projectDocuments = ProjectDocuments::where('project_id',$id)->get();
        return view('Admin.project.previewProject',compact('manage_project','community','subcommunity','projectNote','projectDocuments'));
    }

    public function addProjectNote(Request $request)
    {
        try {
            $input = $request->all();
            $input['user_id'] = Auth::user()->id;
            $note = ProjectNotes::create($input);

            $noteData['id'] = $note->id;
            $noteData['agent_name'] = Auth::user()->name;
            $noteData['date'] = date('d-M-Y H:i A',strtotime($note->updated_at));
            $noteData['note'] = $note->note;
            if($noteData)
            {
                return response()->json(['status' => 1, 'message' => 'Has been create a note', 'note' => $noteData]);
            }
            else
            {
                return response()->json(['status' => 0, 'message' => 'Failed to create note details']);
            }
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    public function deleteProjectNotes(Request $request)
    {
        try {
            $noteDelete = ProjectNotes::where('id',$request->id)->delete();
            if($noteDelete)
            {
                return response()->json(['status' => 1, 'message' => 'Notes delete successfully']);
            }
            else
            {
                return response()->json(['status' => 0, 'message' => 'Something went wrong!']);
            }
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    public function addProjectDocument(Request $request)
    {
        try {
            $input = $request->all();
            $input['user_id'] = Auth::user()->id;

            $requiredParams = $this->requiredRequestParams('project_document');
            $validator = Validator::make($request->all(), $requiredParams);
            if ($validator->fails()) {
                $errorMessage = implode('<br> <li>', $validator->errors()->all());
                return response()->json(['status' => 0, 'message' => $errorMessage]);
            }

            if(array_key_exists('attachment',$input))
            {
                foreach ($input['attachment'] as $key => $value)
                {
                    $attachment['user_id'] = $input['user_id'];
                    $attachment['project_id'] = $request->project_id;
                    $attachment['document_name'] = $value['document_name'];
                    if($file = $request->attachment[$key]['document_file'])
                    {
                        $document_name = time().'-'.$value['document_name'].'.'.$file->getClientOriginalExtension();
                        $file->move(public_path('projectFiles/documents/'), $document_name);
                        $attachment['document_file'] = json_encode($document_name);
                        $multipleDocument = ProjectDocuments::create($attachment);
                    }
                }
            }
            else
            {
                return response()->json(['status' => 0, 'message' => 'Something went wrong!']);
            }

            if($multipleDocument)
            {
                return response()->json(['status' => 1, 'message' => 'Has been added as a project document']);
            }
            else
            {
                return response()->json(['status' => 0, 'message' => 'Failed to create project document']);
            }
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    public function deleteProjectDocuments(Request $request)
    {
        try {
            $deleteProjectDocuments = ProjectDocuments::where('id',$request->id)->first();
            if($deleteProjectDocuments)
            {
                if(json_decode($deleteProjectDocuments->document_file,true)){
                    if (file_exists(public_path('/projectFiles/documents/'.$deleteProjectDocuments->document_file))) {
                        @unlink(public_path('/projectFiles/documents/'.$deleteProjectDocuments->document_file));
                    }
                }

                $deleteProjectDocuments->delete();
                return response()->json(['status' => 1, 'message' => 'Documents delete successfully']);
            }
            else
            {
                return response()->json(['status' => 0, 'message' => 'Something went wrong!']);
            }
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    public function addProjectReminder(Request $request)
    {
        try {

            $input = $request->all();
            $input['user_id'] = Auth::user()->id;

            $exsitsdata = ProjectReminders::where('id',$input['project_reminder_id'])->where('project_id', $input['project_id'])->where('is_delete',0)->first();
            if($exsitsdata)
            {
                $exsitsdata->title = $input['title'];
                $exsitsdata->reminder_date = $input['reminder_date'];
                $exsitsdata->comment = $input['comment'];
                $exsitsdata->save();
                $reminder = 1;
            }
            else
            {
                $reminder = ProjectReminders::create($input);
            }

            if($reminder)
            {
                return response()->json(['status' => 1, 'message' => 'has been create a reminder']);
            }
            else
            {
                return response()->json(['status' => 0, 'message' => "Failed to create reminder"]);
            }
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    public function changeProjectReminderStatus(Request $request)
    {
        try {
            $checkData = ProjectReminders::where('is_delete',0)->find($request->id);
            if($checkData)
            {
                $checkData->status = $checkData->status ? 0 : 1;
                $checkData->is_delete = 1;
                $checkData->save();
                return response()->json(['status' => 1, 'message' => "Reminder status updated"]);
            }
            else
            {
                return response()->json(['status' => 0, 'message' => "Something went wrong!"]);
            }

        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    public function deleteProjectAttachments(Request $request)
    {
        try {
            $deleteProjectAttachments = ManageProject::where('id',$request->id)->first();
            if($deleteProjectAttachments)
            {
                $pdf_or_excel = NULL;
                foreach (json_decode($deleteProjectAttachments->pdf) as $key => $value)
                {
                    if($key == $request->key)
                    {
                        if (file_exists(public_path('/projectFiles/pdf/'.$value)))
                        {
                            @unlink(public_path('/projectFiles/pdf/'.$value));
                        }
                    }
                    else
                    {
                        $pdf_or_excel[] = $value;
                    }
                }
                $deleteProjectAttachments->pdf = $pdf_or_excel ? json_encode($pdf_or_excel) : NULL;
                $deleteProjectAttachments->save();

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

    public function getProjectReminder(Request $request)
    {
        $getProjectReminder = ProjectReminders::where('id',$request->reminder_id)->where('project_id',$request->project_id)->where('is_delete',0)->first();
        return response()->json($getProjectReminder);
    }

    public function addAssignProject(Request $request)
    {
        try {

            $input = $request->all();

            $requiredParams = $this->requiredRequestParams('assignProject');
            $validator = Validator::make($input, $requiredParams);

            if($validator->fails())
            {
                $errorMessage = implode('<br> <li>', $validator->errors()->all());
                return response()->json(['status' => 0, 'message' => $errorMessage]);
            }

            $projectAssignAgents = ProjectAssignAgents::where('project_id',$input['project_id'])->first();
            if($projectAssignAgents)
            {
                $projectAssignAgents->agent_id = $input['agents'];
                $projectAssignAgents->save();
            }
            else
            {
                $data['project_id'] = $input['project_id'];
                $data['agent_id'] = $input['agents'];
                $projectAssignAgents = ProjectAssignAgents::create($data);
            }

            if($projectAssignAgents)
            {
                return response()->json(['status' => 1, 'message' => 'Project assign successfully saved']);
            }
            else
            {
                return response()->json(['status' => 0, 'message' => 'Something went wrong!']);
            }

        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }
}
