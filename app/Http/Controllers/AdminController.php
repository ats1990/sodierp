<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Exibir dashboard do admin com estatísticas gerais
        return view('admin.dashboard');
    }
}
