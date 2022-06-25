<?php

namespace App\Exports;

use App\Bulk;
use App\Models\ManageListings;
use App\Models\LeadClient;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class BulkExport implements WithMultipleSheets
{
    protected $id;

    function __construct($id) {
            $this->id = $id;
    }

    public function sheets(): array
    {
        $sheets = [];
        $sheets['Sheet 1'] = new LeadExport($this->id);
        $sheets['Sheet 2'] = new ProjectExport($this->id);
        return $sheets;
    }
}
