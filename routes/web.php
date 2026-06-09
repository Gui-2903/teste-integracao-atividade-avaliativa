<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BibliotecasController;
use App\Http\Controllers\BibliotecaPessoaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PessoaController;
use App\Http\Controllers\LivroController;
use App\Http\Controllers\AutorController;

Route::get('/', function () {
    return view('welcome');
});

Route::get("/bibliotecas", [BibliotecasController::class, 'index'])->name("bibliotecas.index");
Route::get("/bibliotecas/new", [BibliotecasController::class, 'create'])->name("bibliotecas.create");
Route::post("/bibliotecas/create", [BibliotecasController::class, 'store'])->name("bibliotecas.store");
Route::get("/bibliotecas/edit/{id}", [BibliotecasController::class, 'edit'])->name("bibliotecas.edit");
Route::put("/bibliotecas/update/{id}", [BibliotecasController::class, 'update'])->name("bibliotecas.update");
Route::delete("/bibliotecas/delete/{id}", [BibliotecasController::class, 'destroy'])->name("bibliotecas.destroy");
Route::get('/bibliotecas/{biblioteca}/pessoas/add', [BibliotecaPessoaController::class, 'create'])->name('bibliotecas.pessoas.create');
Route::post('/bibliotecas/{biblioteca}/pessoas', [BibliotecaPessoaController::class, 'store'])->name('bibliotecas.pessoas.store');

Route::resource('users', UserController::class);

Route::resource('pessoas', PessoaController::class);

Route::resource('livros', LivroController::class);
Route::resource('autores', AutorController::class)->except('edit');
Route::get("/autores/edit/{id}", [AutorController::class, 'edit'])->name("autores.edit");