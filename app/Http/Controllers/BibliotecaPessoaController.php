<?php

namespace App\Http\Controllers;

use App\Models\Biblioteca;
use App\Models\Pessoa;
use Illuminate\Http\Request;

class BibliotecaPessoaController extends Controller
{
    public function create(int $biblioteca)
    {
        $bibliotecaModel = Biblioteca::find($biblioteca);

        if (!$bibliotecaModel) {
            return redirect()->route('bibliotecas.index')->with('error', 'Biblioteca não encontrada');
        }

        $pessoas = Pessoa::whereDoesntHave('bibliotecas', function ($query) use ($biblioteca) {
            $query->where('bibliotecas.id', $biblioteca);
        })->get();

        return view('bibliotecas.pessoas.new', [
            'biblioteca' => $bibliotecaModel,
            'pessoas' => $pessoas,
        ]);
    }

    public function store(Request $request, int $biblioteca)
    {
        $bibliotecaModel = Biblioteca::find($biblioteca);

        if (!$bibliotecaModel) {
            return redirect()->route('bibliotecas.index')->with('error', 'Biblioteca não encontrada');
        }

        $validated = $request->validate([
            'pessoa_id' => 'required|exists:pessoas,id',
        ]);

        $alreadyAssociated = $bibliotecaModel
            ->pessoas()
            ->where('pessoas.id', $validated['pessoa_id'])
            ->exists();

        if ($alreadyAssociated) {
            return redirect()
                ->route('bibliotecas.pessoas.create', ['biblioteca' => $bibliotecaModel->id])
                ->with('error', 'Pessoa já está associada a esta biblioteca.');
        }

        $bibliotecaModel->pessoas()->attach($validated['pessoa_id']);

        return redirect()
            ->route('bibliotecas.edit', ['id' => $bibliotecaModel->id])
            ->with('message', 'Pessoa adicionada à biblioteca com sucesso.');
    }
}
