<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Testes de integração para os endpoints de Users (UserController - resource).
 *
 * Rotas cobertas (Route::resource('users')):
 *  GET    /users           -> index
 *  GET    /users/create    -> create
 *  POST   /users           -> store
 *  GET    /users/{id}       -> show
 *  GET    /users/{id}/edit  -> edit
 *  PUT    /users/{id}       -> update
 *  DELETE /users/{id}       -> destroy
 */
class UserTest extends TestCase
{
    use RefreshDatabase;

    /** Cenário válido: listagem responde 200. */
    public function test_index_responde_ok(): void
    {
        $this->get('/users')->assertOk();
    }

    /** Cenário válido: formulário de criação responde 200. */
    public function test_formulario_create_responde_ok(): void
    {
        $this->get('/users/create')->assertOk();
    }

    /** Cenário válido (CRUD): cria usuário com dados válidos e persiste no banco. */
    public function test_store_cria_usuario_valido(): void
    {
        $response = $this->post('/users', [
            'name' => 'Maria Silva',
            'email' => 'maria@example.com',
            'password' => 'senha12345',
        ]);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('message');
        $this->assertDatabaseHas('users', ['email' => 'maria@example.com']);
    }

    /** Regra de negócio: a senha deve ser persistida com hash (nunca em texto puro). */
    public function test_store_armazena_senha_com_hash(): void
    {
        $this->post('/users', [
            'name' => 'Joao Souza',
            'email' => 'joao@example.com',
            'password' => 'segredo123',
        ]);

        $user = User::where('email', 'joao@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNotEquals('segredo123', $user->password);
    }

    /**
     * Regra de negócio / integridade: e-mail é UNIQUE. Cadastrar e-mail duplicado
     * não deve criar um segundo registro. O controller captura a exceção e
     * redireciona com erro.
     */
    public function test_store_nao_duplica_email(): void
    {
        User::factory()->create(['email' => 'dup@example.com']);

        $response = $this->post('/users', [
            'name' => 'Outro',
            'email' => 'dup@example.com',
            'password' => 'senha12345',
        ]);

        $this->assertDatabaseCount('users', 1);
        $response->assertSessionHas('error');
    }

    /** Resposta da API: exibir usuário inexistente redireciona com erro. */
    public function test_show_usuario_inexistente_redireciona_com_erro(): void
    {
        $response = $this->get('/users/9999');

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('error');
    }

    /** Cenário válido (CRUD): atualiza usuário existente e persiste a alteração. */
    public function test_update_atualiza_usuario(): void
    {
        $user = User::factory()->create(['name' => 'Nome Antigo']);

        $response = $this->put("/users/{$user->id}", [
            'name' => 'Nome Atualizado',
            'email' => $user->email,
            'role' => 'admin',
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Nome Atualizado', 'role' => 'admin']);
    }

    /** Resposta da API: atualizar usuário inexistente redireciona com erro. */
    public function test_update_usuario_inexistente_redireciona_com_erro(): void
    {
        $response = $this->put('/users/9999', ['name' => 'X', 'email' => 'x@example.com']);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('error');
    }

    /** Cenário válido (CRUD): remove usuário existente do banco. */
    public function test_destroy_remove_usuario(): void
    {
        $user = User::factory()->create();

        $response = $this->delete("/users/{$user->id}");

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /** Resposta da API: excluir usuário inexistente redireciona com erro. */
    public function test_destroy_usuario_inexistente_redireciona_com_erro(): void
    {
        $response = $this->delete('/users/9999');

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('error');
    }
}
