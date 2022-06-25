<?php
namespace App\Http\Controllers;

use App\Models\Milestones;
use App\Models\Permission_role_mapping;
use Session;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use App\Traits\ResponseTrait;
use App\Traits\RequestTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon;
use Hash;
use Auth;
use Datatables;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Traits\UtilityTrait;

class MilestonesController extends Controller
{
    use ResponseTrait, UtilityTrait;

 
    public function listmilestones()
    {
       return view('Admin.managemilestones');
    }

    public function addviewmilestones()
    {
        $permission = Permission_role_mapping::where('user_id',Auth::user()->id)->where('permissions_id',1)->first();
        return view('Admin.Add-milestones',compact('permission'));
    }

    public function listmilestonesDatatable(Request $request)
    {
        try {

            $data = Milestones::all();

            $permission = Permission_role_mapping::where('user_id',Auth::user()->id)->where('permissions_id',1)->first();
            
            return Datatables::of($data)
                ->addColumn('id', function($row){
                    return $row->id ? $row->id : '-';
                })
                ->addColumn('milestone', function($row){
                    return $row->milestone ? $row->milestone : '-';
                })
                ->addColumn('action', function($row) use($permission){
                    
                    if($permission->delete)
                    {
                        return '<a href="javascript:void(0)" class="delete-confirm" data-milestone_id="'.$row->id.'"><i style="color: red;" class="fas fa-trash-alt delete" data-toggle="tooltip" data-placement="bottom" title="Delete"></i></a>';
                    }
                    else
                    {
                        return '-';
                    }
                })
                ->rawColumns(['milestone','action'])
                ->make(true);
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    public function submitmilestones(Request $request, $parentId = null)
    {
            $input = $request->input();
            $request->validate([
                'milestone' => 'required'
            ]);
            $milestones = Milestones::create($input);
            if($milestones)
        {
            return response()->json(['status' => 1, 'message' => 'Features successfully saved']);
        }
        else
        {
            return response()->json(['status' => 0, 'message' => 'Something went wrong!']);
        }   
        if(Session::has('isUserSubmitAfterCheckout')){
                Session::forget('isUserSubmitAfterCheckout');
        }
    }

    public function editunit($id)
    {
        $project = ManageListings::find($id);
        return view('Admin.editunit',compact('project'));
    }

    public function updateunit(Request $request, $id, $parentId = null)
    {
        // print_r($id);
        // dd();
        try {
            $project = ManageListings::find($id);
            if (!($project))    {
                $response['status']  = 0;
                $response['message']    = 'Project not found';
                session()->flash('response', $response);
                return redirect()->back();

            }

            $input = $this->objectToArray($request->input());
            $input['id'] = $id;

            $input = $this->prepareUpdateData($input, $project->toArray());
            $requiredParams = $this->requiredRequestParams('update', $id);
            $validator = Validator::make($input, $requiredParams);

            if ($validator->fails()) {
                $errorMessage =implode(', ', $validator->errors()->all());
                $response['status']     = 0;
                $response['message']    = $errorMessage;
                session()->flash('response', $response);
                return redirect()->back();
            }

            $projectUpdate = $project->update($input);

            $project = ManageListings::find($id);
            if ($projectUpdate) {

                $response['status']  = 1;
                $response['message']    = 'has been updated as a project';

            } else {
                $response['status']  = 0;
                $response['message']    = "Failed to update project details";

            }
            session()->flash('response', $response);
            return redirect()->route('manage_listings');

        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    protected function prepareUpdateData(array $data, array $user)
    {

        $data['developer_id'] = $this->arrayGet('developer_id', $data, $user['developer_id']);
        $data['project'] = $this->arrayGet('project', $data, $user['project']);
        return $data;
    }

    public function deletemilestones(Request $request, $id, $parentId = null)
    {
        $milestonesDelete = Milestones::find($id)->delete();
        if($milestonesDelete)
        {
            return response()->json(['status' => 1, 'message' => 'Milestones delete successfully']);
        }
        else
        {
            return response()->json(['status' => 0, 'message' => 'Something went wrong!']);
        }
    }

    public function requiredRequestParams(string $action, $id = null)
    {
        switch ($action) {
            case 'create':
                $params = [
                    'milestone' => 'required'
                    // 'developer_id' => 'required',
                    // 'project' => 'required',
                ];
                break;
            case 'update':
                $params = [
                    // 'developer_id' => 'required',
                    // 'project' => 'required',
                    // 'handover_year' => 'required',
                ];
                break;
            default:
                $params = [];
                break;
        }
        return $params;
    }
}
