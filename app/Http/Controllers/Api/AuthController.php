<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserRecourses;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;

class AuthController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth')->only(['logout']);
    }

    public function login(Request $request)
    {
        $request->validate([
            'mobile' => 'required',
            'password' => 'required',
        ]);
        $credential = $request->only(['mobile', 'password']);

        if ($token = Auth::attempt($credential)) {
            if (auth()->user()->isClient()) {
                Auth::logout();

                return abort(401);
            }

            return (new UserRecourses(auth()->user()))->additional([
                'token' => $token
            ]);
        } else {
            return abort(401);
        }
    }

    public function logout()
    {
        Auth::logout();

        return response()->json(['message' => Lang::get('messages.success_logout')]);
    }

    public function user(Request $request)
    {
        return (new UserRecourses(auth()->user()));
    }
}
