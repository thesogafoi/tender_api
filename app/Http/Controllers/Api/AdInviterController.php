<?php

namespace App\Http\Controllers\Api;

use App\AdInviter;
use App\Http\Controllers\Controller;
use App\Imports\AdInviterImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AdInviterController extends Controller
{
    public function index()
    {
        return AdInviter::all();
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:ad_inviters'
        ]);
        $this->authorize('create', AdInviter::class);
        $adInviter = new AdInviter();
        $adInviter->name = $request->name;
        $adInviter->save();
    }

    public function createFromExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required'
        ]);

        Excel::import(new AdInviterImport, $request->file('excel_file'));
    }
}
