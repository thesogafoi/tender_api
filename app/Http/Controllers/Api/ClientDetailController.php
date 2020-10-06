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
    public function index(Request $request){
        $result=[];
        $user_details = ClientDetail::all();
        foreach ($user_details as $user_detail) {
            $temp["client_code"] = $user_detail->user->id;
            $temp["client_name"] = $user_detail->user->name;
            $temp["company_name"] = $user_detail->company_name;
            $temp["mobile"] = $user_detail->user->mobile;
            $temp["register_date"] = $user_detail->user->created_at;
            $temp["user_type"] = $user_detail->type;
            $temp["tel"] = $user_detail->phone;
            array_push($result, $temp);
        }
        return $result;
    }
}
