<?php

namespace App\Http\Controllers;

use App\Models\ClientNotes;
use App\Models\ClientReminder;
use Illuminate\Http\Request;
use App\Models\LeadClient;
use App\Models\ManageListings;
use App\Exports\BulkExport;
use Maatwebsite\Excel\Facades\Excel;

class LeadClientController extends Controller
{
    public function index()
    {
        $data = LeadClient::get();

        return view('Admin.manage_lead_client', compact('data'));
    }

    public function create()
    {
        $data = ManageListings::with(['reminder' => function ($query) {
            $query->whereDate('reminder_date', '<=', \Carbon\Carbon::now('Europe/Stockholm'))->where('status', 0);
        }])->pluck('title', 'id');

        // $data = ManageListings::with(['reminder'=>function($query){
        //     $query->whereDate('reminder_date','<=',\Carbon\Carbon::now('Europe/Stockholm'))->where('status',0);
        // }])->where('sold_out_status',0)->pluck('title','id');

        return view('Admin.Add_lead_clients', compact('data'));
    }

    public function store(Request $request)
    {
        $input = $request->all();

        if (array_key_exists('_', $input)) {
            unset($input['_']);
        } else {
            unset($input['_token']);
        }

        if (array_key_exists('project_id', $input)) {
            $input['project_id'] = json_encode($input['project_id']);
        }

        $lead = LeadClient::create($input);
        if ($lead) {
            $response['status'] = 1;
            $response['message'] = 'Has Been Added Client';
            session()->flash('response', $response);
            return redirect()->route('lead_index');
        } else {
            $response['status'] = 0;
            $response['message'] = "Failed to create Client";
            session()->flash('response', $response);
            return redirect()->back();
        }
    }

    public function show($id)
    {
        $lead = LeadClient::find($id);
        $data = ManageListings::with(['reminder' => function ($query) {
            $query->whereDate('reminder_date', '<=', \Carbon\Carbon::now('Europe/Stockholm'))->where('status', 0);
        }])->pluck('title', 'id');
        $projects = [];
        foreach ($data as $key => $project_title) {
            if ($key && in_array($key, json_decode($lead->project_id, true))) {
                $projects[] = $project_title;
            }
        }

        return view('Admin.view_lead', compact('lead', 'projects'));
    }

    public function edit($id)
    {
        $editlead = LeadClient::find($id);
        $data = ManageListings::with(['reminder' => function ($query) {
            $query->whereDate('reminder_date', '<=', \Carbon\Carbon::now('Europe/Stockholm'))->where('status', 0);
        }])->pluck('title', 'id');

        //  $data = ManageListings::with(['reminder'=>function($query){
        //     $query->whereDate('reminder_date','<=',\Carbon\Carbon::now('Europe/Stockholm'))->where('status',0);
        // }])->where('ready_status',0)->pluck('title','id');
        return view('Admin.Edit_lead', compact('editlead', 'data'));
    }

    public function update(Request $request, LeadClient $leadClient)
    {
        $input = $request->all();


        if (array_key_exists('_', $input)) {
            unset($input['_']);
        } else {
            unset($input['_token']);
        }

        if (array_key_exists('project_id', $input)) {
            $input['project_id'] = json_encode($input['project_id']);
        } else {
            $input['project_id'] = NULL;
        }

        $lead = LeadClient::where('id', $input['id'])->update($input);
        if ($lead) {
            $response['status'] = 1;
            $response['message'] = 'Has Been Update Client';
            session()->flash('response', $response);
            return redirect()->route('lead_index');
        } else {
            $response['status'] = 0;
            $response['message'] = "Failed to Update Client";
            session()->flash('response', $response);
            return redirect()->back();
        }
    }

    public function destroy($id)
    {
        LeadClient::find($id)->delete();
        return redirect()->route('lead_index');
    }

    public function export($id)
    {
        return Excel::download(new BulkExport($id), 'Project Requirement Report.xlsx');
    }

    public function add_note(Request $request)
    {
        $input = $request->all();
        $note = ClientNotes::create($input);
        if($note)
        {
            return response()->json(['status' => 1, 'message' => 'Has been create a note', 'note' => $note]);
        }
        else
        {
            return response()->json(['status' => 0, 'message' => 'Failed to create note details']);
        }
    }

    public function remove_note(Request $request)
    {
        $listdata = ClientNotes::find($request->id);
        $delete_note = ClientNotes::where('id',$request->id)->delete();
        $noteList = ClientNotes::where('client_id',$listdata->client_id)->get();
        if($delete_note)
        {
            return response()->json(['status' => 1, 'message' => 'Has been delete a note', 'noteList' => $noteList]);
        }
        else
        {
            return response()->json(['status' => 0, 'message' => 'Failed to delete note details']);
        }
    }

    public function storeReminder(Request $request)
    {
        $input= $request->all();
        $exsitsdata = ClientReminder::where('client_id',$input['client_id'])->first();
        if($exsitsdata){
            unset($input['_token']);
            $reminder=$exsitsdata->update($input);
        } else {
            $reminder=ClientReminder::create($input);
        }
        if($reminder){
            if ($request->ajax()) {
                $data['status']  = 1;
                $data['message']    = ' has been create a reminder';
                return response()->json($data);
            }

        } else {
            if ($request->ajax()) {
                $data['status']  = 0;
                $data['message']    = "Failed to create reminder";
                return response()->json($data);
            }
        }
    }

    public function updateReminder(Request $request, ClientReminder $reminder)
    {
        $input=$request->all();
        if($input['value']=='true'){

            $status=(int)(bool)$input['value'];
        }else{
            $status=(int)$input['value'];
        }
        if($input){
            $reminderData=$reminder->where('id',$input['id'])->first();
            if($reminderData){
                ClientReminder::where('id',$input['id'])->update(['status'=>$status]);
                if ($request->ajax()) {
                    $data['status']  = 1;
                    $data['message']    = ' has been update a reminder';
                    return response()->json($data);
                }
            } else {
                if ($request->ajax()) {
                    $data['status']  = 0;
                    $data['message']    = "Failed to update reminder";
                    return response()->json($data);
                }
            }
        }
    }
}
