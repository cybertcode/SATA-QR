<?php

namespace App\Http\Controllers\Sata\Admin;

use App\Http\Controllers\Controller;

class ConfiguracionGeneralController extends Controller
{
    public function index()
    {
        return view('sata.configuracion.index');
    }
}
