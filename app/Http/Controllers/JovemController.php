<?php

namespace App\Http\Controllers;

use App\Models\Jovem;
use Illuminate\Http\Request;

class JovemController extends Controller
{
    public function index()
    {
        $jovens = Jovem::all();
        return view('jovens.index', compact('jovens'));
    }

    public function create()
    {
        return view('jovens.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required',
            'email' => 'required|email|unique:jovens',
        ]);
        Jovem::create($request->all());
        return redirect()->route('jovens.index')->with('success', 'Jovem cadastrado!');
    }

    public function show(Jovem $jovem)
    {
        return view('jovens.show', compact('jovem'));
    }

    public function edit(Jovem $jovem)
    {
        return view('jovens.edit', compact('jovem'));
    }

    public function update(Request $request, Jovem $jovem)
    {
        $request->validate([
            'nome' => 'required',
            'email' => 'required|email|unique:jovens,email,' . $jovem->id,
        ]);
        $jovem->update($request->all());
        return redirect()->route('jovens.index')->with('success', 'Jovem atualizado!');
    }

    public function destroy(Jovem $jovem)
    {
        $jovem->delete();
        return redirect()->route('jovens.index')->with('success', 'Jovem removido!');
    }
}
