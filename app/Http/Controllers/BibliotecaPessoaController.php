<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Biblioteca;
use App\Models\Pessoa;

class BibliotecaPessoaController extends Controller
{
    public function create(Biblioteca $biblioteca)
    {
        $pessoas = Pessoa::whereDoesntHave('bibliotecas', function ($query) use ($biblioteca) {
            $query->where('biblioteca_id', $biblioteca->id);
        })->get();

        return view('bibliotecas.add_pessoa', compact('biblioteca', 'pessoas'));
    }

    public function store(Request $request, Biblioteca $biblioteca)
    {
        $request->validate([
            'pessoa_id' => 'required|exists:pessoas,id',
        ]);

        $pessoaId = $request->input('pessoa_id');

        $result = $biblioteca->pessoas()->syncWithoutDetaching([$pessoaId]);

        if (empty($result['attached'])) {
            return redirect()->route('bibliotecas.pessoas.create', ['biblioteca' => $biblioteca->id])
                ->with('error', 'Pessoa já está associada a esta biblioteca.');
        }

        return redirect()->route('bibliotecas.edit', ['id' => $biblioteca->id])
            ->with('message', 'Pessoa adicionada à biblioteca com sucesso.');
    }
}
