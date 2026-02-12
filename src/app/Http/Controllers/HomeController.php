<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __invoke()
    {
        return Auth::check()
            ? redirect()->route('domains.index')
            : redirect()->route('login');
    }
}