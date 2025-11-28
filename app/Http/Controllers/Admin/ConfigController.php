<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ConfigController extends Controller
{
    public function index()
    {
        return view('admin.config.index');
    }
}