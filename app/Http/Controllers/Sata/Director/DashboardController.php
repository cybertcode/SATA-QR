<?php

namespace App\Http\Controllers\Sata\Director;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('sata.director.dashboard');
    }
}
