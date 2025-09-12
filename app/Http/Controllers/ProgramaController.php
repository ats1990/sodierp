<?php

namespace App\Http\Controllers;

use App\Models\Programa;
use Illuminate\Http\Request;

class ProgramaController extends Controller
{
    public function index()
    {
        $programas = Programa::all();
        return view('programas.index', compact('programas'));
    }

    public function create()
    {
        return view('programas.create');
    }

    public function store(Request $request)
    {
        $request->validate(['nome' => 'required|unique:programas']);
        Programa::create($request->all());
        return redirect()->route('programas.index')->with('success', 'Programa cadastrado!');
    }

    public function show(Programa $programa)
    {
        return view('programas.show', compact('programa'));
    }

    public function edit(Programa $programa)
    {
        return view('programas.edit', compact('programa'));
    }

    public function update(Request $request, Programa $programa)
    {
        $request->validate(['nome' => 'required|unique:programas,nome,' . $programa->id]);
        $programa->update($request->all());
        return redirect()->route('programas.index')->with('success', 'Programa atualizado!');
    }

    public function destroy(Programa $programa)
    {
        $programa->delete();
        return redirect()->route('programas.index')->with('success', 'Programa removido!');
    }
}
