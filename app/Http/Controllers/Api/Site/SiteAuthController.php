<?php

namespace App\Http\Controllers\Api\Site;

use App\ClientDetail;
use Illuminate\Support\Facades\Lang;
use App\Http\Controllers\Controller;
use App\Http\Resources\SiteUserResource;
use App\Http\Resources\UserRecourses;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Morilog\Jalali\Jalalian;

class SiteAuthController extends Controller
{
    public function initialRegister(Request $request)
    {
        $request->validate([
            'mobile' => 'required|unique:users|max:11',
            'name' => 'required',
        ]);
        $randomNumber = rand(10000, 99999);
        Cache::add(
            'verify_code_' . $request->mobile, // key
                $randomNumber, //value
                90 //in second
        );
        $data = [
            'receptor' => $request->mobile,
            'message' => 'به آسان تندر خوش آمدید . رمز عبور شما ' . $randomNumber . ' میباشد ',
        ];
        $response = Http::post("https://api.kavenegar.com/v1/https:/api.kavenegar.com/v1/78466B41486D46737338324E53344B3334696974306B48794F7637747970323775325A45635561667A52453D/verify/lookup.json?receptor=$request->mobile&token=$randomNumber&template=asantender", $data);

        return response()->json([
            'data' => $response
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'mobile' => 'required|unique:users|max:11',
            'name' => 'required',
            'registration_code' => 'required',
        ]);

        if (!Cache::has('verify_code_' . $request->mobile) || ($request->registration_code != Cache::get('verify_code_' . $request->mobile))) {
            abort(401, Lang::get('messages.failed_registration'));
        }

        $user = new User();
        $user->mobile = $request->mobile;
        $user->name = $request->name;
        $user->password = Hash::make(Cache::get('verify_code_' . $request->mobile));

        $user->save();

        $detail = new ClientDetail();

        $detail->user_id = $user->id;
        $detail->save();

        $user->client_detail_id = $detail->id;

        $user->save();
        $credential = ['mobile' => $request->mobile, 'password' => $request->registration_code];
        $token = Auth::attempt($credential);

        return (new UserRecourses($user))->additional([
            'token' => $token
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'mobile' => 'required|max:11',
            'password' => 'required',
        ]);

        $credential = $request->only(['mobile', 'password']);

        if ($token = Auth::attempt($credential)) {
            if (auth()->user()->detail == null) {
                abort(401);
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
        return (new SiteUserResource(auth()->user()));
    }

    public function getVerificationCode(User $user, Request $request)
    {
        $request->validate([
            'token' => 'required'
        ]);
        if (Cache::get('verify_code_' . $user->mobile) == $request->token) {
            $user->mobile_verified_at = Jalalian::now()->format('Y-m-d');
            $user->save();

            return response(200);
        } else {
            return abort(403);
        }
    }

    public function forgetPassword(Request $request)
    {
        $request->validate([
            'mobile' => 'required|exists:users'
        ]);
        // send a new random code for him
        $user = User::where('mobile', $request->mobile)->first();
        $user->password = Hash::make('2087596');
        $user->save();
    }
}
