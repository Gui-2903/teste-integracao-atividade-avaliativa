<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Livro;
use App\Models\Autor;

class LivroController extends Controller
{
    //

    public function index()
    {
        $livros = Livro::all();
        return view('livros.index', compact('livros'));
    }

    public function show($id)
    {
        $livro = Livro::findOrFail($id);
        return view('livros.show', compact('livro'));
    }

    public function create()
    {
        $autores = Autor::all();
        return view('livros.create', compact(['autores']));
    }

    public function store(Request $request)
    {

        $livro = new Livro();
        $livro->autor_id = $request->input('autor_id');
        $livro->titulo = $request->input('titulo');
        $livro->isbn = $request->input('isbn');
        $livro->data_publicacao = $request->input('data_publicacao');
        $livro->save();
        return redirect()->route('livros.index');
    }

    public function edit(Request $request, $id)
    {
        $livro = Livro::find($id);
        $autores = Autor::all();
        return view('livros.edit', compact('livro', 'autores'));
    }

    public function update(Request $request, $id)
    {
        $livro = Livro::findOrFail($id);
        $livro->autor_id = $request->input('autor_id');
        $livro->titulo = $request->input('titulo');
        $livro->isbn = $request->input('isbn');
        $livro->data_publicacao = $request->input('data_publicacao');
        $livro->save();
        return redirect()->route('livros.index');
    }

    public function destroy($id)
    {
        $livro = Livro::findOrFail($id);
        $livro->delete();
        return redirect()->route('livros.index');
    }

}
