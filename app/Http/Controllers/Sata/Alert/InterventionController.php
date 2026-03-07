<?php

namespace App\Http\Controllers\Sata\Alert;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InterventionController extends Controller
{
    public function index()
    {
        return view('sata.interventions.index');
    }
}
