<?php

namespace App\Http\Middleware\Milestone;

use Closure;
use App\Models\Permission_role_mapping;
use App\Models\Permission;

class Delete_milestone
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $check = Permission_role_mapping::where('user_id',Auth()->user()->id)->where(['permissions_id'=>1,'delete'=>1])->first();
        if($check == null)
        {
            if($request->ajax())
            {
                $data['status']  = 0;
                $data['message'] = "You don't have sufficient permission, Kindly Contect admin.";
                return response()->json($data);
            }
            else
            {
                $response['status'] = 0;
                $response['message'] = "You don't have sufficient permission, Kindly Contect admin.";
                session()->flash('response', $response);
                return redirect()->back();
            }
        }else{
            return $next($request);
        }
    }
}
