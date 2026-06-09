<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pessoa;

class PessoaController extends Controller
{
    //
    
    public function index()
    {
        $pessoas = Pessoa::all();
        return view('pessoas.index', compact('pessoas'));
    }

    public function create()
    {
        return view('pessoas.new');
    }

    public function store(Request $request){

        $pessoa = new Pessoa();
        $pessoa->name = $request->input('name');
        $pessoa->email = $request->input('email');
        $pessoa->telefone = $request->input('telefone');
        $pessoa->matricula = $request->input('matricula');

        if ($request->input('password') !== $request->input('confirmPassword')) {
            return redirect()->back()->with('error', 'As senhas não coincidem!');
        } else {
            $pessoa->password = bcrypt($request->input('password'));        
        }

        $pessoa->save();
        return redirect()->route('pessoas.index')->with('message', 'Pessoa criada com sucesso!');
    }

    public function edit($id) {
        $pessoa = Pessoa::find($id);
        if (!$pessoa) {
            return redirect()->route('pessoas.index')->with('error', 'Pessoa não encontrada');
        }
        return view('pessoas.edit', compact('pessoa'));
    }

    public function update(Request $request, $id) {

        $pessoa = Pessoa::find($id);
        if (!$pessoa) {
            return redirect()->route('pessoas.index')->with('error', 'Pessoa não encontrada');
        }

        $pessoa->name = $request->input('name');
        $pessoa->email = $request->input('email');
        $pessoa->telefone = $request->input('telefone');
        $pessoa->matricula = $request->input('matricula');

        if (!is_null($request->input("password")) || !is_null($request->input("confirmPassword"))) {

            if ($request->input("password") !== $request->input("confirmPassword")) {
                return redirect()->back()->with('error', 'As senhas não coincidem!');
            } else {
                $pessoa->password = bcrypt($request->input('password'));        
            }

        }

        try {
            $pessoa->save();
            return redirect()->route('pessoas.index')->with('message', 'Pessoa atualizada com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao atualizar pessoa: ' . $e->getMessage());
        }

    }

    public function destroy($id) {

    }   


}
