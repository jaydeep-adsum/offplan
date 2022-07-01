<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Permission;
use App\Models\Permission_role_mapping;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use App\Traits\ResponseTrait;
use App\Traits\RequestTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Hash;
use Session;
use Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Traits\UtilityTrait;

class AgentController extends Controller
{
    use ResponseTrait, UtilityTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list_agent(Request $request, $parentId = null)
    {
        try {

            $search = request('search', '');
            $sortBy = request('sortBy', '');
            $direction = (strtoupper(request('sortDirection')) == 'ASC') ? false : true;
            if(!(Auth::user()->role == 1)){
                $response['status']  = 1;
                $response['message']    = 'Unauthorized access';
            }
            $user = User::where('role',2)->orWhere('role',3);
            if ($search) {
                $user = $user->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('email', 'LIKE', '%' . $search . '%')
                    ->orWhere('phone', 'LIKE', '%' . $search . '%');
            }
            if (!empty($sortBy)) {
                if ($sortBy == 'id') {
                    $sortType = SORT_NUMERIC;
                } else {
                    $sortType = SORT_STRING;
                }
                $user = $user->get()->sortBy($sortBy, $sortType, $direction);
            } else {
                $user = $user->get()->sortBy('id', SORT_NUMERIC, 3);
            }
            $userData = [];
            if ($user) {
                $userData = $user->toArray();
            }
            if((Auth::user()->role == 3)){
                return redirect()->back();
            }

            return view('Admin.manage_agent',compact('userData'));


        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    public function add_agent()
    {
        $permission = Permission::get();
        return view('Admin.add_agent',compact('permission'));
    }

    public function craete_agent(Request $request, $parentId = null)
    {
        try {
            $input = $this->objectToArray($request->input());
            // dd($input);

            if (Auth::user()->role == 2 || Auth::user()->role == 3) {
                $response['status']  = 0;
                $response['message']    = 'Unauthorized access';
                session()->flash('response', $response);
                return redirect()->back();
            }

            $requiredParams = $this->requiredRequestParams('create');
            $validator = Validator::make($input, $requiredParams);
            if ($validator->fails()) {
                $errorMessage =implode(', ', $validator->errors()->all());
                $response['status']     = 0;
                $response['message']    = $errorMessage;

                session()->flash('response', $response);
                return redirect()->back();
            }

            if($request->hasfile('image')) {
                $file=$request->file('image');
                $image_name = time().'-'.$file->getClientOriginalName();
                $file->move(('public/files/profile'), $image_name);
                $data = $image_name;
                $input['image']=json_encode($data);
            }

            $password = $input['password'];
            $input['password'] = Hash::make($password);

            $input['name'] =$input['firstname']." ".$input['lastname'];
            $userCode=substr($input['firstname'][0], 0, 1).substr($input['lastname'][0], 0, 1);

            $user = User::create($input);


            if(array_key_exists('permission_array',$input))
            {
                $permission=array();
                $exceptid=array();
                foreach ($input['permission_array'] as $key => $value) {
                    $permission = array();
                    $permission['user_id']=$user->id;
                    $permission['permissions_id']= $key;
                    if(array_key_exists('read',$value)){
                        $permission['read']=$value['read'];
                    }
                    if(array_key_exists('create',$value)){
                        $permission['create']=$value['create'];
                    }
                    if(array_key_exists('update',$value)){
                        $permission['update']=$value['update'];
                    }
                    if(array_key_exists('delete',$value)){
                        $permission['delete']=$value['delete'];
                    }
                    array_push($exceptid,$key);
                    Permission_role_mapping::create($permission);
                }
                $exsitpermissionid= Permission::pluck('id')->toArray();
                $diff = array_diff($exsitpermissionid, $exceptid);
                foreach($diff as $key=>$value){
                    $permission = array();
                    $permission['user_id']=$user->id;
                    $permission['permissions_id']= $value;
                    Permission_role_mapping::create($permission);
                }
            }
            else
            {
                $permission = Permission::get();
                foreach ($permission as $item)
                {

                    $permission_default['user_id'] = $user->id;
                    $permission_default['permissions_id'] = $item['id'];
                    if ($user->role==3&&$permission_default['permissions_id']==5){
                        $permission_default['read'] = 1;
                        $permission_default['create'] = 0;
                        $permission_default['update'] = 0;
                        $permission_default['delete'] = 0;
                    }
                    $permission_role_mapping = Permission_role_mapping::create($permission_default);
                }
            }


            $userCode = strtoupper($userCode);
            $update=User::where('id',$user->id)->update(['user_code'=>$userCode]);
            if ($user) {

                $response['status']  = 1;
                $response['message']    = 'Has been added as a Agent user';

            } else {
                $response['status']  = 0;
                $response['message']    = "Failed to create user";
            }
            if(Session::has('isUserSubmitAfterCheckout')){
                Session::forget('isUserSubmitAfterCheckout');
            }
            session()->flash('response', $response);
            return redirect()->route('manage_agent');

        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

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

    public function update_agent(Request $request, $id, $parentId = null)
    {
        try {
            $user = User::find($id);
            if(!$user)
            {
                session()->flash('response', ['status' => 0, 'message' => 'User not found']);
                return redirect()->back();
            }

            $input = $this->objectToArray($request->input());
            $input['id'] = $id;
            if(!$this->hasWriteAccess($input))
            {
                session()->flash('response', ['status' => 0, 'message' => 'Unauthorized access']);
                return redirect()->back();
            }

            $requiredParams = $this->requiredRequestParams('update', $id);
            $validator = Validator::make($input, $requiredParams);

            if ($validator->fails()) {
                $errorMessage = implode(', ', $validator->errors()->all());
                session()->flash('response', ['status' => 0, 'message' => $errorMessage]);
                return redirect()->back();
            }

            if($request->hasfile('image'))
            {
                $file=$request->file('image');
                $image_name = time().'-'.$file->getClientOriginalName();
                $file->move(('public/files/profile'), $image_name);
                $data['image']=json_encode($image_name);
            }

            if($input['password'])
            {
                $data['password'] = Hash::make($input['password']);
            }

            if($request->has('firstname')&& $request->has('firstname'))
            {
                $data['name'] = $input['firstname']." ".$input['lastname'];
            }

            $data['email'] = $input['email'];
            $data['phone'] = $input['phone'];

            $userUpdate = $user->update($data);

            $permission_role_mapping_delete = Permission_role_mapping::where('user_id',$id)->delete();

            if(array_key_exists('permission_array',$input))
            {
                $permission=array();
                $exceptid=array();
                foreach ($input['permission_array'] as $key => $value) {
                    $permission = array();
                    $permission['user_id']=$id;
                    $permission['permissions_id']= $key;
                    if(array_key_exists('read',$value)){
                        $permission['read']=$value['read'];
                    }
                    if(array_key_exists('create',$value)){
                        $permission['create']=$value['create'];
                    }
                    if(array_key_exists('update',$value)){
                        $permission['update']=$value['update'];
                    }
                    if(array_key_exists('delete',$value)){
                        $permission['delete']=$value['delete'];
                    }
                    array_push($exceptid,$key);
                    Permission_role_mapping::create($permission);
                    // Permission_role_mapping::where(['user_id'=>$id,'permissions_id'=>$key])->update($permission);
                }
                $exsitpermissionid= Permission::pluck('id')->toArray();
                $diff = array_diff($exsitpermissionid, $exceptid);
                foreach($diff as $key=>$value){
                    $permission = array();
                    $permission['user_id']=$id;
                    $permission['permissions_id']= $value;
                    Permission_role_mapping::create($permission);
                    // Permission_role_mapping::where(['user_id'=>$id,'permissions_id'=>$value])->update($permission);
                }
            }
            else
            {
                $permission = Permission::get();
                foreach ($permission as $item)
                {
                    $permission_default['user_id'] = $user->id;
                    $permission_default['permissions_id'] = $item['id'];
                    $permission_role_mapping = Permission_role_mapping::create($permission_default);
                }
            }

            if ($userUpdate) {

                $response['status']  = 1;
                $response['message']    = 'Has been updated as a Agent user';

            } else {
                $response['status']  = 0;
                $response['message']    = "Failed to update user details";

            }
            session()->flash('response', $response);
            return redirect()->route('manage_agent');

        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    public function delete_agent(Request $request, $id, $parentId = null)
    {
        try {
            $user = User::find($id);
            $permission_role_mapping_delete = Permission_role_mapping::where('user_id',$id)->delete();
            if (!($user)) {
                return $this->notFoundRequest('User not found');
            }

            if (!$this->hasDeleteAccess()) {
                return $this->sendAccessDenied('Unauthorized access');
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
            return redirect()->route('manage_agent');
        } catch (NotFoundHttpException $ex) {
            return $this->notFoundRequest($ex);
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    public function edit_agent($id){
        $data=User::find($id);
        $name= explode(" ", $data['name']);
        $permission_role_mapping = Permission_role_mapping::with('permission')->where('user_id',$id)->orderBy('permissions_id')->get();
        return view('Admin.edit_agent',compact('data','name','permission_role_mapping'));
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
    protected function hasWriteAccess($data = null)
    {
        return ($this->isAdmin() || $this->currentUser->id == $data['id']);
    }

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
                    'email' => 'required|email|unique:users,email,NULL',
                    'firstname' => 'required',
                    'lastname' => 'required',
                    'phone' => 'required',
                    'password' => 'required',
                ];
                break;
            case 'update':
                $params = [
                    'email' => "unique:users,email,".$id.",id",
                    'firstname' => 'required',
                    'lastname' => 'required',
                    'phone' => 'required',
                    // 'password' => 'required',
                ];
                break;
            default:
                $params = [];
                break;
        }
        return $params;
    }
}
