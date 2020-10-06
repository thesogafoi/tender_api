<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserRecourses;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        return response()->json(['message' => 'خروج با موفقیت انجام شد']);
    }

    public function user(Request $request)
    {
        return (new UserRecourses(auth()->user()));
    }
}
