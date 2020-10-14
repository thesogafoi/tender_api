<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'mobile' => 'required',
            'type' => 'required',
            'password' => 'required',
            'status' => 'required',
        ]);
        $user = new User();
        $user->name = $request->name;
        $user->mobile = $request->mobile;
        $user->type = $request->type;
        $user->password = Hash::make($request->password);
        $user->status = $request->status;
        $user->save();
    }
}
