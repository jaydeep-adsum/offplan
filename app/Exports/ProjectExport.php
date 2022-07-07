<?php

namespace App\Exports;

use App\Models\LeadClient;
use App\Models\ManageListings;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Auth;

class ProjectExport implements FromView,WithTitle
{
    protected $id;

    function __construct($id) {
            $this->id = $id;
    }

    public function view(): View
    {
        $data = [];
        $encryption = "AES-128-CTR";
        $env_length = openssl_cipher_iv_length($encryption);
        $options   = 0;
        $encryption_userid = '1234567890123456';
        $encryption_key = "123456";
        $user_id = Auth::id();
        $encrypt_userid = openssl_encrypt($user_id, $encryption, $encryption_key, $options, $encryption_userid);
        $project_id = LeadClient::where('id',$this->id)->first('project_id');
        if(json_decode($project_id['project_id']))
        {
            $data = ManageListings::with('paymentplan','developer')->whereIn('id',json_decode($project_id['project_id'],true))->get();
        }
        return view('Admin.project_print_excel', [
            'project_data' => $data,
            'user_id'=>$encrypt_userid
        ]);
    }

    public function title(): string
    {
        return 'Report';
    }
}
