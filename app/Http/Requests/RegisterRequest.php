<?php

namespace App\Http\Requests;

use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return !Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:255',
            'mobile' => 'required|unique:users|max:11',
            'password' => 'required|max:255|confirmed',
        ];
    }

    public function persist()
    {
        $user = new User();
        $user->name = request()->name;
        $user->mobile = request()->mobile;
        $user->password = Hash::make(request()->password);

        return $user;
    }
}
