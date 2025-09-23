<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

class WelcomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        return view('dashboard.welcome');
    }
}
