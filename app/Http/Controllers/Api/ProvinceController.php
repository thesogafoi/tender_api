<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Province;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{
    public function create(Request $request)
    {
        $this->authorize('create', Province::class);
        $province = new Province();
        $request->validate([
            'name' => 'required|unique:provinces'
        ]);
        $province->name = $request->name;
        $province->save();
    }

    public function update(Request $request, Province $province)
    {
        $this->authorize('create', Province::class);
        $request->validate([
            'name' => 'required'
        ]);
        $province->name = $request->name;
        $province->save();
    }
}
