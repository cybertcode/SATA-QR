<?php

namespace App\Http\Controllers\Sata\Institution;

use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    public function index()
    {
        return view('sata.institution.settings');
    }
}
