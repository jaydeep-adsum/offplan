<?php

namespace App\Exports;

use App\Models\LeadClient;
use App\Models\ManageListings;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class ProjectExport implements FromView,WithTitle
{
    protected $id;

    function __construct($id) {
            $this->id = $id;
    }

    public function view(): View
    {   
        $data = [];
        $project_id = LeadClient::where('id',$this->id)->first('project_id');
        if(json_decode($project_id['project_id']))
        {
            $data = ManageListings::with('paymentplan','developer')->whereIn('id',json_decode($project_id['project_id'],true))->get();
        }
        return view('Admin.project_print_excel', [
            'project_data' => $data
        ]);
    } 

    public function title(): string
    {
        return 'Report';
    }
}
