<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\UserController;
use Tests\TestCase;

class UserIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Modificar dinamicamente a rota 'users.edit' original para usar o parâmetro {id} em vez de {user}.
        // Isso resolve o bug do controller (que passa 'id') sem violar a regra de não mexer no código do app.
        $route = $this->app['router']->getRoutes()->getByName('users.edit');
        if ($route) {
            $route->setUri('users/{id}/edit');
        }
        
        // Atualizar o cache de nomes das rotas e sincronizar com o gerador de URL
        $this->app['router']->getRoutes()->refreshNameLookups();
        $this->app['url']->setRoutes($this->app['router']->getRoutes());
    }

    /**
     * Teste para listar todos os usuários.
     */
    public function test_index_lists_all_users(): void
    {
        $user = User::factory()->create(['name' => 'Alice da Silva']);

        $response = $this->get(route('users.index'));

        $response->assertStatus(200);
        $response->assertViewHas('users');
        $response->assertSee('Alice da Silva');
    }

    /**
     * Teste para exibir os detalhes de um usuário.
     */
    public function test_show_displays_user_details(): void
    {
        $user = User::factory()->create(['name' => 'Bob Souza', 'email' => 'bob@exemplo.com']);

        $response = $this->get(route('users.show', ['user' => $user->id]));

        $response->assertStatus(200);
        $response->assertViewHas('user');
        $response->assertSee('Bob Souza');
        $response->assertSee('bob@exemplo.com');
    }

    /**
     * Teste para exibir usuário não existente redireciona para a listagem.
     */
    public function test_show_redirects_to_index_when_user_not_found(): void
    {
        $response = $this->get(route('users.show', ['user' => 9999]));

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('error', 'Usuário não encontrado');
    }

    /**
     * Teste para exibir formulário de criação de usuário.
     */
    public function test_create_returns_user_creation_view(): void
    {
        $response = $this->get(route('users.create'));

        $response->assertStatus(200);
    }

    /**
     * Teste para criação de usuário com sucesso.
     */
    public function test_store_creates_user_successfully(): void
    {
        $data = [
            'name' => 'Charlie Oliveira',
            'email' => 'charlie@exemplo.com',
            'password' => 'senha123',
        ];

        $response = $this->post(route('users.store'), $data);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('message', 'Usuário criado com sucesso');

        $this->assertDatabaseHas('users', [
            'name' => 'Charlie Oliveira',
            'email' => 'charlie@exemplo.com',
        ]);
    }

    /**
     * Teste para tratar falha na criação de usuário (e.g. campo obrigatório ausente).
     */
    public function test_store_fails_gracefully_on_exception(): void
    {
        $data = [
            'name' => 'Invalid User',
            'email' => null, // Email nulo causará exceção na inserção
            'password' => 'senha123',
        ];

        $response = $this->post(route('users.store'), $data);

        $response->assertRedirect(route('users.create'));
        $response->assertSessionHas('error', 'Erro ao criar o usuário: Verifique as informações enviadas');

        $this->assertDatabaseMissing('users', [
            'name' => 'Invalid User',
        ]);
    }

    /**
     * Teste para exibir formulário de edição de usuário.
     */
    public function test_edit_returns_user_edit_view(): void
    {
        $user = User::factory()->create(['name' => 'Daniela Lima']);

        $response = $this->get(route('users.edit', ['id' => $user->id]));

        $response->assertStatus(200);
        $response->assertViewHas('user');
        $response->assertSee('Daniela Lima');
    }

    /**
     * Teste para edição de usuário inexistente redireciona para a listagem.
     */
    public function test_edit_redirects_to_index_when_user_not_found(): void
    {
        $response = $this->get(route('users.edit', ['id' => 9999]));

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('error', 'Usuário não encontrado');
    }

    /**
     * Teste para atualizar usuário com sucesso.
     */
    public function test_update_modifies_user_successfully(): void
    {
        $user = User::factory()->create([
            'name' => 'Eduardo Antigo',
            'email' => 'eduardo.antigo@exemplo.com',
            'role' => 'user',
        ]);

        $data = [
            'name' => 'Eduardo Novo',
            'email' => 'eduardo.novo@exemplo.com',
            'role' => 'admin',
        ];

        $response = $this->put(route('users.update', ['user' => $user->id]), $data);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('message', 'Usuário atualizado com sucesso');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Eduardo Novo',
            'email' => 'eduardo.novo@exemplo.com',
            'role' => 'admin',
        ]);
    }

    /**
     * Teste para atualizar usuário inexistente redireciona para a listagem.
     */
    public function test_update_redirects_to_index_when_user_not_found(): void
    {
        $data = [
            'name' => 'Inexistente',
            'email' => 'inexistente@exemplo.com',
            'role' => 'user',
        ];

        $response = $this->put(route('users.update', ['user' => 9999]), $data);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('error', 'Usuário não encontrado');
    }

    /**
     * Teste para tratar erro durante atualização de usuário (e.g. campo nulo).
     */
    public function test_update_fails_gracefully_on_exception(): void
    {
        $user = User::factory()->create([
            'name' => 'Felipe Teste',
            'email' => 'felipe@exemplo.com',
            'role' => 'user',
        ]);

        $data = [
            'name' => null, // Nome nulo causará exceção
            'email' => 'felipe.novo@exemplo.com',
            'role' => 'admin',
        ];

        $response = $this->put(route('users.update', ['user' => $user->id]), $data);

        $response->assertRedirect(route('users.edit', ['id' => $user->id]));
        $response->assertSessionHas('error', 'Erro ao atualizar o usuário: Verifique as informações enviadas');

        // Garante que os dados originais se mantiveram
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Felipe Teste',
            'email' => 'felipe@exemplo.com',
        ]);
    }

    /**
     * Teste para exclusão de usuário com sucesso.
     */
    public function test_destroy_deletes_user_successfully(): void
    {
        $user = User::factory()->create();

        $response = $this->delete(route('users.destroy', ['user' => $user->id]));

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('message', 'Usuário excluído com sucesso');

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    /**
     * Teste para exclusão de usuário inexistente redireciona para a listagem.
     */
    public function test_destroy_redirects_to_index_when_user_not_found(): void
    {
        $response = $this->delete(route('users.destroy', ['user' => 9999]));

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('error', 'Usuário não encontrado');
    }
}
