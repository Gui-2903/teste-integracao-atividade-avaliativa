<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return view('users.index', ['users' => User::all()]);
    }

    public function show(int $user)
    {
        $userModel = User::find($user);

        if (!$userModel) {
            return redirect()->route('users.index')->with('error', 'Usuário não encontrado');
        }

        return view('users.show', ['user' => $userModel]);
    }

    public function create()
    {
        return view('users.new');
    }

    public function store(Request $request)
    {
        try {
            User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'role' => $request->input('role', 'user'),
            ]);
        } catch (\Exception $e) {
            return redirect()->route('users.create')->with('error', 'Erro ao criar o usuário: Verifique as informações enviadas');
        }

        return redirect()->route('users.index')->with('message', 'Usuário criado com sucesso');
    }

    public function edit(int $id)
    {
        $user = User::find($id);

        if (!$user) {
            return redirect()->route('users.index')->with('error', 'Usuário não encontrado');
        }

        return view('users.edit', ['user' => $user]);
    }

    public function update(Request $request, int $user)
    {
        $userModel = User::find($user);

        if (!$userModel) {
            return redirect()->route('users.index')->with('error', 'Usuário não encontrado');
        }

        try {
            $userModel->name = $request->input('name', $userModel->name);
            $userModel->email = $request->input('email', $userModel->email);
            $userModel->role = $request->input('role', $userModel->role);

            if ($request->filled('password')) {
                $userModel->password = Hash::make($request->input('password'));
            }

            $userModel->save();
        } catch (\Exception $e) {
            return redirect()->route('users.edit', ['id' => $userModel->id])->with('error', 'Erro ao atualizar o usuário: Verifique as informações enviadas');
        }

        return redirect()->route('users.index')->with('message', 'Usuário atualizado com sucesso');
    }

    public function destroy(int $user)
    {
        $userModel = User::find($user);

        if (!$userModel) {
            return redirect()->route('users.index')->with('error', 'Usuário não encontrado');
        }

        $userModel->delete();

        return redirect()->route('users.index')->with('message', 'Usuário excluído com sucesso');
    }
}
