<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CoordController extends Controller
{
    public function dashboard()
    {
        // Exibir dashboard da coordenação com programas, turmas e jovens
        return view('coord.dashboard');
    }
}
