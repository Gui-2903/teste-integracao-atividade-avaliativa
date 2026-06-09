<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Autor;

class AutorController extends Controller
{
    //
    public function index()
    {
        $autores = Autor::all();
        return view('autores.index', compact('autores'));
    }

    public function create()
    {
        return view('autores.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|max:200',
            'nacionalidade' => 'nullable|max:100',
            'data_nascimento' => 'nullable|date',
        ]);

        Autor::create($request->all());

        return redirect()->route('autores.index')->with('success', 'Autor criado com sucesso.');
    }

    public function edit($id)
    {
        $autor = Autor::findOrFail($id);
        return view('autores.edit', compact('autor'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nome' => 'required|max:200',
            'nacionalidade' => 'nullable|max:100',
            'data_nascimento' => 'nullable|date',
        ]);

        $autor = Autor::findOrFail($id);
        $autor->update($request->all());

        return redirect()->route('autores.index')->with('success', 'Autor atualizado com sucesso.');
    }
}
