<?php

namespace App\Http\Controllers;

use App\Models\Developer;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use App\Traits\ResponseTrait;
use App\Traits\RequestTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon;
use Hash;
use Session;
use Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Traits\UtilityTrait;
use App\Models\ManageListings;
use App\Models\MultipleContacts;
use App\Models\MultipleImages;
use App\Models\ManageProject;
use App\Models\DeveloperNote;
use App\Models\Permission_role_mapping;
use Datatables;

class UserController extends Controller
{
    use ResponseTrait, UtilityTrait;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function datatableDeveloperList(Request $request)
    {
        try {

            $permission = Permission_role_mapping::where('user_id',Auth::user()->id)->where('permissions_id',2)->first();

            $mytime = Carbon\Carbon::now();
            if($request->page_status == 'pending_contracts')
            {
                $data = Developer::with('singleContact')->where('date', '<=', $mytime);
            }
            else
            {
                $data = Developer::with('singleContact')->where('date', '>=', $mytime);
            }

            return Datatables::eloquent($data)
                ->addColumn('company_name', function($row){
                    return $row->company ? $row->company : '-';
                })
                ->addColumn('email', function($row){
                    return $row->email ? $row->email : '-';
                })
                ->addColumn('point_of_contact', function($row){
                    return $row->singleContact ? $row->singleContact->person : '-';
                })
                ->addColumn('mobile_no', function($row){
                    return $row->singleContact ? $row->singleContact->phone : '-';
                })
                ->addColumn('expiry_date', function($row){
                    return $row->date ? $row->date : '-';
                })
                ->addColumn('action', function($row) use($permission){

                    if($permission)
                    {
                        $preview_html = '<a href="'. route('preview-user', ['id' => $row->id]) .'"><i style="color: green;" class="fas fa-eye mr-3" data-toggle="tooltip" data-placement="bottom" title="View"></i></a>';

                        $edit_html = '<a href="'. route('edit-user', ['id' => $row->id]) .'"><i class="fas fa-edit mr-3" data-toggle="tooltip" data-placement="bottom" title="Edit"></i></a>';

                        $delete_html = '<a href="javascript:void(0)" class="delete-confirm" data-developer_id="'.$row->id.'"><i style="color: red;" class="fas fa-trash-alt delete" data-toggle="tooltip" data-placement="bottom" title="Delete"></i></a>';

                        $preview = $permission->read ? $preview_html : '';
                        $edit = $permission->update ? $edit_html : '';
                        $delete = $permission->delete ? $delete_html : '';

                        return $preview .''. $edit .''. $delete;
                    }
                    else
                    {
                        return '';
                    }
                    return '<a href="'. route('preview-user', ['id' => $row->id]) .'"><i style="color: green;" class="fas fa-eye mr-3" data-toggle="tooltip" data-placement="bottom" title="View"></i></a>

                    <a href="'. route('edit-user', ['id' => $row->id]) .'"><i class="fas fa-edit mr-3" data-toggle="tooltip" data-placement="bottom" title="Edit"></i></a>

                    <a href="javascript:void(0)" class="delete-confirm" data-developer_id="'.$row->id.'"><i style="color: red;" class="fas fa-trash-alt delete" data-toggle="tooltip" data-placement="bottom" title="Delete"></i></a>';
                })
                ->filter(function ($query) use ($request) {

                    if($request->company)
                    {
                        $query = $query->where('company','LIKE', '%' . $request->company . '%');
                    }

                    if($request->date_range)
                    {
                        $date_range = explode(" - ",$request->date_range);
                        $start_date_range = date('Y-m-d', strtotime($date_range[0]));
                        $end_date_range = date('Y-m-d', strtotime($date_range[1]));
                        $query = $query->whereBetween('date',[$start_date_range, $end_date_range]);
                    }
                })
                ->rawColumns(['DT_RowIndex','company_name','email','point_of_contact','mobile_no','expiry_date','action'])
                ->make(true);
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    public function deleteDeveloper(Request $request)
    {
        try {
            $developer = Developer::where('id',$request->developer_id)->first();
            if($developer)
            {
                $deletecontact = MultipleContacts::where('developer_id',$request->developer_id)->delete();
                $developer->delete();
                return response()->json(['status' => 1, 'message' => 'has been removed as a Agent user']);
            }
            else
            {
                return response()->json(['status' => 0, 'message' => 'Failed to remove Agent user']);
            }
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    public function datatableDeveloperProjectList(Request $request)
    {
        try {

            $data = ManageProject::with('communitys','projectBedrooms')->where('developer_id',$request->developer_id);

            return Datatables::eloquent($data)
                ->addColumn('project_name', function($row){
                    return $row->project ? $row->project : '-';
                })
                ->addColumn('property_type', function($row){
                    return $row->property ? $row->property : '-';
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
                ->addColumn('payment_plan', function($row){
                    return $row->payment_plan_comments ? $row->payment_plan_comments : '-';
                })
                ->addColumn('commission', function($row){
                    return $row->commission ? $row->commission : '-';
                })
                ->rawColumns(['project_name','property_type','location','completion_status','price_range','payment_plan','commission'])
                ->make(true);
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    public function addDeveloperNote(Request $request)
    {
        try {

            $input = $request->all();
            if($input['developer_note_id'])
            {
                $data['note'] = $input['note'];
                $note = DeveloperNote::where('id',$input['developer_note_id'])->update($data);
            }
            else
            {
                $data['user_id'] = Auth::user()->id;
                $data['developer_id'] = $input['developer_id'];
                $data['note'] = $input['note'];
                $note = DeveloperNote::create($data);
            }
            if($note)
            {
                return response()->json(['status' => 1, 'message' => 'Has been create a note']);
            }
            else
            {
                return response()->json(['status' => 0, 'message' => 'Failed to create note details']);
            }
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    public function deleteDeveloperNotes(Request $request)
    {
        try {
            $noteDelete = DeveloperNote::where('id',$request->id)->delete();
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

    public function getDeveloperNote(Request $request)
    {
        $getDeveloperNotes = DeveloperNote::where('id',$request->developer_note_id)->first();
        return response()->json($getDeveloperNotes);
    }

    public function previewuser($id)
    {
        $developer = Developer::find($id);
        $contact = MultipleContacts::where('developer_id',$id)->get();
        $multipleimage = MultipleImages::where('developer_id',$id)->get();
        $developerNote = DeveloperNote::where('developer_id',$id)->get();
        return view('Admin.preview_developer',compact('developer','contact','multipleimage','developerNote'));
    }


    /**
     * It will List all the users
     *
     * @param request $request  Request
     * @param string  $parentId Id
     *
     * @return JsonResponse JsonResponse
     */
    public function listUser(Request $request, $parentId = null)
    {
        $permission = Permission_role_mapping::where('user_id',Auth::user()->id)->where('permissions_id',2)->first();
        return view('Admin.manage-user',compact('permission'));
    }


    public function addUser(){
        return view('Admin.Add-developer');
    }

    public function pendingUser(){
        $mytime = Carbon\Carbon::now();
        $user = Developer::with('multiplecontact')->where('date', '<=', $mytime)->get();
        return view('Admin.pending',compact('user'));
    }



    /**
     * Create User
     *
     * @param Request $request  Request
     * @param string  $parentId Id
     *
     * @return JsonResposne            JsonResponse
     */
    public function craeteUser(Request $request, $parentId = null)
    {
        try {
            $input = $this->objectToArray($request->all());

            $requiredParams = $this->requiredRequestParams('create');
            $validator = Validator::make($input, $requiredParams);

            if ($validator->fails())
            {
                return response()->json(['status' => 0, 'message' => implode('<br> <li>', $validator->errors()->all())]);
            }

            if($request->hasfile('pdf')) {
                foreach($request->file('pdf') as $file){
                    $pdf_name = time().'-'.$file->getClientOriginalName();
                    $file->move(public_path('files/developer'), $pdf_name);
                    $data[] = $pdf_name;
                }
                $input['pdf']=json_encode($data);
            }

            $user = Developer::create($input);

            $contact['developer_id'] = $user->id;
            foreach ($input['multiplecontact'] as $key => $value) {
                $contact['person'] = $value['person'];
                $contact['phone'] = $value['phone'];
                $multiplecontact = MultipleContacts::create($contact);
            }
            if ($user)
            {
                return response()->json(['status' => 1, 'message' => 'Has been added as a Agent user']);
            }
            else
            {
                return response()->json(['status' => 0, 'message' => 'Failed to create user']);
            }
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    /**
     * Get User Details by id
     *
     * @param Request $request  Request
     * @param int     $id       id
     * @param string  $parentId Id
     *
     * @return JsonResponse jsonresponse
     */
    public function getUser(Request $request, $id, $parentId = null)
    {
        try {
            $user = User::find($id);
            if (!($user)) {
                return $this->notFoundRequest('User not found');
            }

            $data['id'] = $id;
            if (!$this->hasReadAccess($data)) {
                return $this->sendAccessDenied('Unauthorized access');
            }
            return $this->successResponse($user->toArray(), 'User found');
        } catch (NotFoundHttpException $ex) {
            return $this->notFoundRequest($ex);
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }


    /**
     * Update user by id
     *
     * @param Request $request  Request
     * @param int     $id       id
     * @param string  $parentId Parentid
     *
     * @return JsonResponse JsonResponse
     */
    public function updateUser(Request $request, $id, $parentId = null)
    {
        try {
            $id = $request->get('id');
            $user = Developer::find($id);

            if(!$user)
            {
                return response()->json(['status' => 0, 'message' => 'Developer not found']);
            }
            $input = $this->objectToArray($request->input());
            $input['id'] = $id;

            $input = $this->prepareUpdateData($input, $user->toArray());

            $requiredParams = $this->requiredRequestParams('update', $id);
            $validator = Validator::make($input, $requiredParams);

            if ($validator->fails())
            {
                return response()->json(['status' => 0, 'message' => implode(', ', $validator->errors()->all())]);
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
                        $image_name = $file->getClientOriginalName();
                    }
                    else
                    {
                        $image_name = time().'-'.$file->getClientOriginalName();
                        $file->move(public_path('files/developer'), $image_name);
                    }
                    $data[] = $image_name;
                }
                $input['pdf'] = json_encode($data);
            }
            $userUpdate = $user->update($input);
            $user = Developer::find($id);

            if(array_key_exists('multiplecontact',$input))
            {
                foreach ($input['multiplecontact'] as $key => $value) {
                    if(array_key_exists('id',$value)){
                        $contact['person'] = $value['person'];
                        $contact['phone'] = $value['phone'];
                        $paymentplan = MultipleContacts::where(['developer_id'=>$id,'id'=>$value['id']])->update($contact);
                    } else{
                        $contact['developer_id'] = $user->id;
                        $contact['person'] = $value['person'];
                        $contact['phone'] = $value['phone'];
                        $multiplecontact = MultipleContacts::create($contact);
                    }
                }
            }

            if($userUpdate)
            {
                return response()->json(['status' => 1, 'message' => 'Has been updated as a agent user']);
            }
            else
            {
                return response()->json(['status' => 0, 'message' => 'Failed to update agent user details']);
            }
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }


    /**
     * Delete user by id
     *
     * @param Request $request  Request
     * @param int     $id       id
     * @param string  $parentId Parentid
     *
     * @return Jsonresponse JsonResponse
     */
    public function deleteUser(Request $request, $id, $parentId = null)
    {
        try {
            $user = Developer::find($id);
            if (!($user)) {
                return $this->notFoundRequest('Developer not found');
            }

            $userDelete = $user->delete(['id'=>$id]);
            if ($userDelete) {
                $response['status']  = 1;
                $response['message']    = 'has been removed as a Agent user';

            } else {
                $response['status']  = 0;
                $response['message']    = "'Failed to remove Agent user";
            }
            session()->flash('response', $response);
            return redirect()->route('manage-user');
        } catch (NotFoundHttpException $ex) {
            return $this->notFoundRequest($ex);
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    public function deletecontact($id)
    {
        $deletecontact = MultipleContacts::find($id)->delete();
        return redirect()->back();
    }

    public function edituser($id)
    {
        $data = Developer::with('multiplecontact')->find($id);
        return view('Admin.Edit-user',compact('data'));
    }
    /**
     * This will prepare Data for update process
     *
     * @param array $data Data
     * @param array $user User
     *
     * @return array       Data
     */
    protected function prepareUpdateData(array $data, array $user)
    {

        $data['company'] = $this->arrayGet('company', $data, $user['company']);
        $data['email'] = $this->arrayGet('email', $data, $user['email']);
        $data['date'] = $this->arrayGet('date', $data, $user['date']);
        $data['note'] = $this->arrayGet('note', $data, $user['note']);
        $data['pdf'] = $this->arrayGet('pdf', $data, $user['pdf']);
        return $data;
    }

    public function attachmentpost(Request $request)
    {
        try {
            $id = $request->get('id');
            $user = Developer::find($id);
            if (!($user))    {
                $response['status']  = 0;
                $response['message'] = 'Developer not found';
                session()->flash('response', $response);
                return redirect()->back();
            }
            $input = $this->objectToArray($request->all());

            $requiredParams = $this->requiredRequestParams('attachmentpost', $id);
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

            if(array_key_exists('attachment',$input))
            {
                foreach ($input['attachment'] as $key => $value)
                {
                    $attachment['developer_id'] = $user->id;
                    $attachment['attachment_name'] = $value['attachment_name'];
                    if($request->attachment[$key]['attachment_multiple'])
                    {
                        $file = $request->attachment[$key]['attachment_multiple'];
                        $image_name = time().'-'.$value['attachment_name'].'.'.$file->getClientOriginalExtension();
                        $file->move(public_path('files/developer_attachment'), $image_name);
                        $attachment['attachment_multiple']=json_encode($image_name);
                        $multipleimage = MultipleImages::create($attachment);
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
                    $data['status']  = 0;
                    $data['message'] = "Failed to update agent user details";
                    return response()->json($data);
                }
            }
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    public function removeattachment(Request $request)
    {
        try {

            $multipleimages = MultipleImages::find($request->id);

            if (!($multipleimages)) {
                return $this->notFoundRequest('Attachment Not Found');
            }

            if(json_decode($multipleimages->attachment_multiple,true)){
                if (file_exists(public_path('/files/developer_attachment/'.$multipleimages->attachment_multiple))) {
                    @unlink(public_path('/files/developer_attachment/'.$multipleimages->attachment_multiple));
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
    /**
     * Read Access permission check
     *
     * @param array $data Data
     *
     * @return boolean
     */
    protected function hasReadAccess($data = null)
    {
        return ($this->isAdmin() || $this->currentUser->id == $data['id']);
    }

    /**
     * Write Access permission check
     *
     * @param array $data Data
     *
     * @return boolean
     */
    /**
     * Delete Access permission check
     *
     * @param array $data Data
     *
     * @return boolean
     */
    protected function hasDeleteAccess($data = null)
    {
        return $this->isAdmin();
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
     * Required request parameter
     *
     * @param string  $action action
     * @param int|mix $id     ID
     *
     * @return array         array
     */
    public function requiredRequestParams(string $action, $id = null)
    {
        switch ($action) {
            case 'create':
                $params = [
                    'company' => 'required',
                    'multiplecontact.*.person' => 'required',
                    'multiplecontact.*.phone' => 'required',
                    'email' => 'required|email',
                    'date' => 'required',
                ];
                break;
            case 'attachmentpost':
                $params = [
                    'attachment.*.attachment_name' => 'required',
                    'attachment.*.attachment_multiple' => 'required'
                ];
                break;
            case 'update':
                $params = [
                    'company' => 'required',
                    'email' => 'required|email',
                    'date' => 'required',
                ];
                break;
            default:
                $params = [];
                break;
        }
        return $params;
    }
}
