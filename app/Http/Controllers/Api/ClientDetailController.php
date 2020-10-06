<?php

namespace App\Http\Controllers\Api;

use App\ClientDetail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClientDetailController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function create(Request $request)
    {
        $this->authorize('create', ClientDetail::class);
        $request->validate([
            'phone' => 'required',
            'type' => 'required',
            'company_name' => 'required'
        ]);
        $clientDetail = new ClientDetail();
        $clientDetail->phone = $request->phone;
        $clientDetail->type = $request->type;
        $clientDetail->expired_detail = null;
        $clientDetail->allowed_selection = 0;
        $clientDetail->company_name = $request->company_name;
        $clientDetail->save();
    }
}
