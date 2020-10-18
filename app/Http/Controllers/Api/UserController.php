<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserIndexResource;
use App\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'mobile' => 'required|unique:users',
            'type' => 'required',
            'password' => 'required',
        ]);
        $user = new User();
        $user->name = $request->name;
        $user->mobile = $request->mobile;
        $user->type = $request->type;
        $user->password = Hash::make($request->password);
        $user->status = $request->status == null ? 1 : $request->status;
        $user->save();
    }

    public function index(Request $request)
    {
        $users = User::where('client_detail_id', null)->orderBy('id', 'desc')->paginate($request->items_per_page);

        return UserIndexResource::collection($users);
    }

    public function update(Request $request, $user)
    {
        $user = User::find($user)->first();
        $request->validate([
            'name' => 'required',
            'mobile' => 'required',
            'type' => 'required',
        ]);
        $user->name = $request->name;
        $user->mobile = $request->mobile;
        $user->type = $request->type;
        $user->password = $request->password == null ? $user->password : Hash::make($request->password);
        $user->status = $request->status == null ? 1 : $request->status;
        $user->save();
    }

    public function delete(Request $request, $user)
    {
        $user = User::find($user)->first();

        $user->destroy($user->id);
    }
}
