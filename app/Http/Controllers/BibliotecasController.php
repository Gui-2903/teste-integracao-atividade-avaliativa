<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Biblioteca;

class BibliotecasController extends Controller
{
    //
    public function index(Request $request)
    {

        $busca = $request->input('nome');

        $bibliotecas = Biblioteca::where('nome', 'like', '%' . $busca . '%')->get();

        return view('bibliotecas.index', ['bibliotecas' => $bibliotecas]);
    }


    public function create()
    {
        //

        $users = \App\Models\User::all();

        return view('bibliotecas.new', compact('users'));
    }


    public function store(Request $request)
    {
        //
        $created_by = $request->input("created_by");
        $nome       = $request->input("nome");
        $endereco   = $request->input("endereco");

        try {
            $biblioteca = Biblioteca::create([
                'created_by' => $created_by,
                'nome' => $nome
            ]);

            $biblioteca->endereco = $endereco;

            $biblioteca->save();
        } catch (\Exception $e) {
            return redirect()->route('bibliotecas.new', ['error' => 'Erro ao criar a biblioteca: Verifique as informações enviadas']);
        }
        return redirect()->route('bibliotecas.index')->with('message', 'Biblioteca criada com sucesso');

    }


    public function edit(int $id)
    {
        //

        $users = \App\Models\User::all();

        $biblioteca = Biblioteca::where('id', $id)->first();
        if (!$biblioteca) {
            return redirect()->route('bibliotecas.index')->with('error', 'Biblioteca não encontrada');
        }

        // $pessoas = $biblioteca->pessoas;

        return view('bibliotecas.edit', ['biblioteca' => $biblioteca, 'users' => $users]);
    }


    public function update(Request $request, int $id)
    {
        //

        $created_by   = $request->input("created_by");
        $nome         = $request->input("nome");
        $endereco     = $request->input("endereco");
        $email        = $request->input("email");

        $biblioteca = Biblioteca::find($id);
        if (!$biblioteca) {
            return response()->json(['error' => 'Biblioteca não encontrada'], 404);
        }

        try {

            if (!is_null($created_by) && !empty($created_by)) {
                $biblioteca->created_by = $created_by;
            }
            if (!is_null($nome) && !empty($nome)) {
                $biblioteca->nome = $nome;
            }
            if (!is_null($endereco) && !empty($endereco)) {
                $biblioteca->endereco = $endereco;
            }
            if (!is_null($email) && !empty($email)) {
                $biblioteca->email = $email;
            }

            $biblioteca->save();
        } catch (\Exception $e) {
            return redirect()->route('bibliotecas.new', ['error' => 'Erro ao atualizar a biblioteca: Verifique as informações enviadas']);
        }

        return redirect()->route('bibliotecas.index')->with('message', 'Biblioteca atualizada com sucesso');

    }


    public function destroy(int $id)
    {
        //

        $biblioteca = Biblioteca::find($id);
        if (!$biblioteca) {
            return response()->json(['error' => 'Biblioteca não encontrada'], 404);
        }

        try {
            $biblioteca->delete();
        } catch (\Exception $e) {
            return redirect()->route('bibliotecas.index')->with('message', 'Erro ao excluir a biblioteca: Verifique o ID');
        }

        return redirect()->route('bibliotecas.index')->with('message', 'Biblioteca excluída com sucesso');

    }

}
