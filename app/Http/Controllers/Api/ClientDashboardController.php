<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class ClientDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'isClient']);
    }
}
