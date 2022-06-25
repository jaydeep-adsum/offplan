<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use Illuminate\Http\Request;
use Auth;
use App\Traits\ResponseTrait;
use App\Traits\UtilityTrait;

class ReminderController extends Controller
{
    use ResponseTrait, UtilityTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input= $request->all();
        $input['user_id']=Auth()->user()->id;
        $exsitsdata=Reminder::where('project_id',$input['project_id'])->first();
        if($exsitsdata){
            unset($input['_token']);
            $input = $this->prepareUpdateData($input, $exsitsdata->toArray());
            $reminder=Reminder::where('project_id',$input['project_id'])->update($input);
        } else {
            $reminder=Reminder::create($input);
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

    /**
     * Display the specified resource.
     *
     * @param  \App\Reminder  $reminder
     * @return \Illuminate\Http\Response
     */
    public function show(Reminder $reminder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Reminder  $reminder
     * @return \Illuminate\Http\Response
     */
    public function edit(Reminder $reminder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Reminder  $reminder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Reminder $reminder)
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
                Reminder::where('id',$input['id'])->update(['status'=>$status]);
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Reminder  $reminder
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reminder $reminder)
    {
        //
    }

     protected function prepareUpdateData(array $data, array $project)
    {
        $data['title'] = $this->arrayGet('title', $data, $project['title']);
        $data['comment'] = $this->arrayGet('comment', $data, $project['comment']);
        $data['reminder_date'] = $this->arrayGet('reminder_date', $data, $project['reminder_date']);
        $data['status'] = $this->arrayGet('status', $data, $project['status']);
        $data['user_id'] = $this->arrayGet('user_id', $data, $project['user_id']);
        $data['project_id'] = $this->arrayGet('project_id', $data, $project['project_id']);
        return $data;
    }
}
