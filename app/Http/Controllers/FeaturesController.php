<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Features;
use App\Models\Permission_role_mapping;
use App\Traits\ResponseTrait;
use Datatables;
use App\Traits\UtilityTrait;
use Auth;

class FeaturesController extends Controller
{
    use ResponseTrait, UtilityTrait;

    public function viewfeatures()
    {
        return view('Admin.Add-features');
    }

    public function add_features(Request $req)
    {
        $features = Features::create(['fname' => $req->fname]);
        if($features)
        {
            return response()->json(['status' => 1, 'message' => 'Features successfully saved']);
        }
        else
        {
            return response()->json(['status' => 0, 'message' => 'Something went wrong!']);
        }      
    }

    public function listfeatures()
    {
        $permission = Permission_role_mapping::where('user_id',Auth::user()->id)->where('permissions_id',3)->first();
        return view('Admin.Add-features',compact('permission'));
    }

    public function listfeaturesDatatable(Request $request)
    {
        try {
            $permission = Permission_role_mapping::where('user_id',Auth::user()->id)->where('permissions_id',3)->first();

            $data = Features::all();
            
            return Datatables::of($data)
                ->addColumn('id', function($row){
                    return $row->id ? $row->id : '-';
                })
                ->addColumn('features', function($row){
                    return $row->fname ? $row->fname : '-';
                })
                ->addColumn('action', function($row) use($permission){
                    if($permission->delete)
                    {
                        return '<a href="javascript:void(0)" class="delete-confirm" data-feature_id="'.$row->id.'"><i style="color: red;" class="fas fa-trash-alt delete" data-toggle="tooltip" data-placement="bottom" title="Delete"></i></a>';
                    }
                    else
                    {
                        return '-';
                    }
                })
                ->rawColumns(['features','action'])
                ->make(true);
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }
    
    public function delete_Features($id)
    {
        $features = Features::find($id)->delete();
        if($features)
        {
            return response()->json(['status' => 1, 'message' => 'Features delete successfully']);
        }
        else
        {
            return response()->json(['status' => 0, 'message' => 'Something went wrong!']);
        }
    }



    // public function fileImportExport()
    // {
    //    return view('file-import');
    // }
   
    // /**
    // * @return \Illuminate\Support\Collection
    // */
    // public function fileImport(Request $request) 
    // {
    //     Excel::import(new FeaturesImport, $request->file('file')->store('temp'));
    //     return back();
    // }

    // /**
    // * @return \Illuminate\Support\Collection
    // */
    // public function fileExport() 
    // {
    //     return Excel::download(new FeaturesExport, 'Features.xlsx');
    // }  
}
