<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use App\Traits\ResponseTrait;
use App\Traits\RequestTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Traits\UtilityTrait;
use App\Models\Listing;
use App\Models\Developer;
use Auth;
use Carbon;
use App\Models\Categories;
use App\Models\Features;
use App\Models\User;

class ListingController extends Controller
{
    use ResponseTrait, UtilityTrait;
    /**
     * Constructor
     */
    public function __construct()
    {
        //parent::__construct();
         $this->middleware('auth',['except' => ['getlisting','genratePdf','mail','sendMail']]);
    }



    /**
     * It will List all the Listing
     *
     * @param request $request  Request
     * @param string  $parentId Id
     *
     * @return JsonResponse JsonResponse
     */


    public function search(Request $request){
        try{
            $input = $this->objectToArray($request->all());
            $data = new Developer;

            $mytime = Carbon\Carbon::now();
            $data = $data->where('date', '>=', $mytime);

            if(isset($input['company'])){
                $data=$data->where('company','LIKE', '%' .$input['company']. '%');
            }
            if(isset($input['min_date']) && isset($input['max_date'])){
                $min_date = $input['min_date'];
                $max_date = $input['max_date'];
                $data=$data->whereBetween('date', array($min_date, $max_date));
            }
            $data = $data->get();
            if($data){
                return response()->json($data);
            }
            else
            {
                return response()->json( "No data Found");
            }
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    public function pendingsearch(Request $request){
        try{
            $input = $this->objectToArray($request->all());
            $data = new Developer;

            $mytime = Carbon\Carbon::now();
            $data = $data->where('date', '<=', $mytime);

            if(isset($input['company'])){
                $data=$data->where('company','LIKE', '%' .$input['company']. '%');
            }
            if(isset($input['min_date']) && isset($input['max_date'])){
                $min_date = $input['min_date'];
                $max_date = $input['max_date'];
                $data=$data->whereBetween('date', array($min_date, $max_date));
            }
            $data = $data->get();
            if($data){
                return response()->json($data);
            }
            else
            {
                return response()->json( "No data Found");
            }
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }


    /**
     * Get Listing Details by id
     *
     * @param Request $request  Request
     * @param int     $id       id
     * @param string  $parentId Id
     *
     * @return JsonResponse jsonresponse
     */


    /**
     * Update Listing by id
     *
     * @param Request $request  Request
     * @param int     $id       id
     * @param string  $parentId Parentid
     *
     * @return JsonResponse JsonResponse
     */


    /**
     * Delete Listing by id
     *
     * @param Request $request  Request
     * @param int     $id       id
     * @param string  $parentId Parentid
     *
     * @return Jsonresponse JsonResponse
     */


     /**
     * This will prepare Data for update process
     *
     * @param array $data Data
     * @param array $user User
     *
     * @return array       Data
     */





    /**
    * [genratePdf description]
    * @param  [type] $id      [description]
    * @param  [type] $user_id [description]
    * @return [type]          [description]
    */


     /**
     * This will chek user is superadmin or not
     *
     * @return boolean
     */
    public function isAdmin() :bool
    {
        return (Auth::user()->role == 1);
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
     * Read Access permission check
     *
     * @param array $data Data
     *
     * @return boolean
     */
    protected function hasReadAccess($data = null)
    {
        return ($this->isAdmin() || $this->isAgentUser() || $this->isAssociateUser());
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
        return ($this->isAdmin() || $this->isAgentUser() || $this->isAssociateUser());
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
        return ($this->isAdmin() || $this->isAgentUser() || $this->isAssociateUser());
    }
}
