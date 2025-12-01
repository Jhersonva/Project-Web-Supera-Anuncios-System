<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class EmployeeController extends Controller
{
    /**
     * Mostrar lista de empleados
     */
    public function index()
    {
        // Solo empleados
        $employees = User::where('role_id', 3)->orderBy('full_name', 'asc')->get();

        return view('admin.config.employee.index', compact('employees'));
    }
}
