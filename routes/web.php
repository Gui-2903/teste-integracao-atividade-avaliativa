<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BibliotecasController;
use App\Http\Controllers\BibliotecaPessoaController;
use App\Http\Controllers\PessoaController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

Route::get("/bibliotecas", [BibliotecasController::class, 'index'])->name("bibliotecas.index");
Route::get("/bibliotecas/new", [BibliotecasController::class, 'create'])->name("bibliotecas.create");
Route::post("/bibliotecas/create", [BibliotecasController::class, 'store'])->name("bibliotecas.store");
Route::get("/bibliotecas/edit/{id}", [BibliotecasController::class, 'edit'])->name("bibliotecas.edit");
Route::put("/bibliotecas/update/{id}", [BibliotecasController::class, 'update'])->name("bibliotecas.update");
Route::delete("/bibliotecas/delete/{id}", [BibliotecasController::class, 'destroy'])->name("bibliotecas.destroy");

Route::get('/bibliotecas/{biblioteca}/pessoas/create', [BibliotecaPessoaController::class, 'create'])->name('bibliotecas.pessoas.create');
Route::post('/bibliotecas/{biblioteca}/pessoas', [BibliotecaPessoaController::class, 'store'])->name('bibliotecas.pessoas.store');

Route::get('/pessoas', [PessoaController::class, 'index'])->name('pessoas.index');
Route::get('/pessoas/create', [PessoaController::class, 'create'])->name('pessoas.create');
Route::post('/pessoas', [PessoaController::class, 'store'])->name('pessoas.store');
Route::get('/pessoas/{pessoa}/edit', [PessoaController::class, 'edit'])->name('pessoas.edit');
Route::put('/pessoas/{pessoa}', [PessoaController::class, 'update'])->name('pessoas.update');

Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
