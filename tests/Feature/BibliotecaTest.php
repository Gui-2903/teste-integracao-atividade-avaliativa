<?php

namespace Tests\Feature;

use App\Models\Biblioteca;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Testes de integração para os endpoints de Bibliotecas (BibliotecasController).
 *
 * Rotas cobertas:
 *  GET    /bibliotecas             -> index (com busca por ?nome=)
 *  GET    /bibliotecas/new         -> create
 *  POST   /bibliotecas/create      -> store
 *  GET    /bibliotecas/edit/{id}   -> edit
 *  PUT    /bibliotecas/update/{id} -> update
 *  DELETE /bibliotecas/delete/{id} -> destroy
 */
class BibliotecaTest extends TestCase
{
    use RefreshDatabase;

    private function criarUser(): User
    {
        return User::factory()->create();
    }

    /** Cenário válido: listagem responde 200. */
    public function test_index_responde_ok(): void
    {
        $this->get('/bibliotecas')->assertOk();
    }

    /** Cenário válido: a busca por nome filtra e exibe a biblioteca correspondente. */
    public function test_index_busca_por_nome(): void
    {
        $user = $this->criarUser();
        Biblioteca::create(['created_by' => $user->id, 'nome' => 'Biblioteca Central']);

        $response = $this->get('/bibliotecas?nome=Central');

        $response->assertOk();
        $response->assertSee('Biblioteca Central');
    }

    /** Cenário válido: formulário de criação responde 200. */
    public function test_formulario_create_responde_ok(): void
    {
        $this->get('/bibliotecas/new')->assertOk();
    }

    /** Cenário válido (CRUD): cria biblioteca com dados válidos e persiste no banco. */
    public function test_store_cria_biblioteca_valida(): void
    {
        $user = $this->criarUser();

        $response = $this->post('/bibliotecas/create', [
            'created_by' => $user->id,
            'nome' => 'Biblioteca Municipal',
            'endereco' => 'Rua das Flores, 100',
        ]);

        $response->assertRedirect(route('bibliotecas.index'));
        $response->assertSessionHas('message');
        $this->assertDatabaseHas('bibliotecas', [
            'nome' => 'Biblioteca Municipal',
            'endereco' => 'Rua das Flores, 100',
        ]);
    }

    /**
     * Validação de entrada / persistência: enviar sem 'nome' (coluna NOT NULL) não
     * deve persistir e deveria retornar uma resposta tratada ao usuário.
     *
     * BUG conhecido: o controller não valida a entrada; a exceção do banco é
     * capturada e o código tenta redirecionar para a rota 'bibliotecas.new', que
     * NÃO EXISTE (o nome correto é 'bibliotecas.create'), gerando 500.
     * Este teste espera o comportamento correto (sem 500) e deve FALHAR.
     * Ver RELATORIO-TESTES.md.
     */
    public function test_store_sem_nome_nao_persiste_e_nao_quebra(): void
    {
        $user = $this->criarUser();

        $response = $this->post('/bibliotecas/create', [
            'created_by' => $user->id,
            'nome' => '',
        ]);

        $this->assertDatabaseCount('bibliotecas', 0);
        $this->assertNotEquals(500, $response->getStatusCode());
    }

    /**
     * Regra de negócio/integridade: created_by é FK obrigatória para users.
     * Sem um usuário válido a biblioteca não pode ser criada.
     * (Também afetado pelo BUG da rota 'bibliotecas.new' acima.)
     */
    public function test_store_sem_created_by_nao_persiste(): void
    {
        $response = $this->post('/bibliotecas/create', [
            'nome' => 'Biblioteca Sem Dono',
        ]);

        $this->assertDatabaseCount('bibliotecas', 0);
        $this->assertNotEquals(500, $response->getStatusCode());
    }

    /** Cenário válido: formulário de edição de biblioteca existente responde 200. */
    public function test_formulario_edit_responde_ok(): void
    {
        $user = $this->criarUser();
        $biblioteca = Biblioteca::create(['created_by' => $user->id, 'nome' => 'Biblioteca X']);

        $this->get("/bibliotecas/edit/{$biblioteca->id}")->assertOk();
    }

    /** Cenário válido (CRUD): atualiza biblioteca existente e persiste a alteração. */
    public function test_update_atualiza_biblioteca(): void
    {
        $user = $this->criarUser();
        $biblioteca = Biblioteca::create(['created_by' => $user->id, 'nome' => 'Nome Antigo']);

        $response = $this->put("/bibliotecas/update/{$biblioteca->id}", [
            'nome' => 'Nome Novo',
            'endereco' => 'Av. Brasil, 200',
        ]);

        $response->assertRedirect(route('bibliotecas.index'));
        $this->assertDatabaseHas('bibliotecas', ['id' => $biblioteca->id, 'nome' => 'Nome Novo']);
    }

    /** Resposta da API: atualizar biblioteca inexistente retorna 404 (JSON). */
    public function test_update_biblioteca_inexistente_retorna_404(): void
    {
        $response = $this->put('/bibliotecas/update/9999', ['nome' => 'Qualquer']);

        $response->assertNotFound();
        $response->assertJson(['error' => 'Biblioteca não encontrada']);
    }

    /** Cenário válido (CRUD): remove biblioteca existente do banco. */
    public function test_destroy_remove_biblioteca(): void
    {
        $user = $this->criarUser();
        $biblioteca = Biblioteca::create(['created_by' => $user->id, 'nome' => 'Para Excluir']);

        $response = $this->delete("/bibliotecas/delete/{$biblioteca->id}");

        $response->assertRedirect(route('bibliotecas.index'));
        $this->assertDatabaseMissing('bibliotecas', ['id' => $biblioteca->id]);
    }

    /** Resposta da API: excluir biblioteca inexistente retorna 404 (JSON). */
    public function test_destroy_biblioteca_inexistente_retorna_404(): void
    {
        $response = $this->delete('/bibliotecas/delete/9999');

        $response->assertNotFound();
        $response->assertJson(['error' => 'Biblioteca não encontrada']);
    }
}
