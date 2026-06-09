<?php

namespace App\Http\Controllers;

use App\Models\Pessoa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PessoaController extends Controller
{
    public function index()
    {
        return view('pessoas.index', ['pessoas' => Pessoa::all()]);
    }

    public function create()
    {
        return view('pessoas.new');
    }

    public function store(Request $request)
    {
        if ($request->input('password') !== $request->input('confirmPassword')) {
            return redirect()->back()->with('error', 'As senhas não coincidem!');
        }

        try {
            Pessoa::create([
                'biblioteca_id' => $request->input('biblioteca_id'),
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'telefone' => $request->input('telefone'),
                'matricula' => $request->input('matricula'),
                'password' => Hash::make($request->input('password')),
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao criar a pessoa: Verifique as informações enviadas');
        }

        return redirect()->route('pessoas.index')->with('message', 'Pessoa criada com sucesso!');
    }

    public function edit(int $pessoa)
    {
        $pessoaModel = Pessoa::find($pessoa);

        if (!$pessoaModel) {
            return redirect()->route('pessoas.index')->with('error', 'Pessoa não encontrada');
        }

        return view('pessoas.edit', ['pessoa' => $pessoaModel]);
    }

    public function update(Request $request, int $pessoa)
    {
        $pessoaModel = Pessoa::find($pessoa);

        if (!$pessoaModel) {
            return redirect()->route('pessoas.index')->with('error', 'Pessoa não encontrada');
        }

        if ($request->filled('password') && $request->input('password') !== $request->input('confirmPassword')) {
            return redirect()->back()->with('error', 'As senhas não coincidem!');
        }

        try {
            $pessoaModel->name = $request->input('name', $pessoaModel->name);
            $pessoaModel->email = $request->input('email', $pessoaModel->email);
            $pessoaModel->telefone = $request->input('telefone', $pessoaModel->telefone);
            $pessoaModel->matricula = $request->input('matricula', $pessoaModel->matricula);

            if ($request->filled('password')) {
                $pessoaModel->password = Hash::make($request->input('password'));
            }

            $pessoaModel->save();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao atualizar a pessoa: Verifique as informações enviadas');
        }

        return redirect()->route('pessoas.index')->with('message', 'Pessoa atualizada com sucesso!');
    }
}
