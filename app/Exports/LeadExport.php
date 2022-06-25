<?php

namespace App\Exports;

use App\Models\LeadClient;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class LeadExport implements FromView,WithTitle
{
    protected $id;

    function __construct($id) {
            $this->id = $id;
    }

    public function view(): View
    {
        return view('Admin.print_excel', [
            'data' => LeadClient::where('id',$this->id)->get()
        ]);
    } 
    public function title(): string
    {
        return 'Requirement';
    }
}
