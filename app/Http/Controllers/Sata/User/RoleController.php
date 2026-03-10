<?php

namespace App\Http\Controllers\Sata\User;

use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    public function index()
    {
        return view('sata.roles.index');
    }
}
