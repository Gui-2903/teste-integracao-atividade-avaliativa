<?php

namespace Tests\Feature;

use App\Models\Pessoa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Testes de integração para os endpoints de Pessoas (PessoaController - resource).
 *
 * Rotas cobertas (Route::resource('pessoas')):
 *  GET    /pessoas           -> index
 *  GET    /pessoas/create    -> create
 *  POST   /pessoas           -> store
 *  GET    /pessoas/{id}/edit  -> edit (rota custom de edição usa /pessoas/edit? ver controller)
 *  PUT    /pessoas/{id}       -> update
 *  DELETE /pessoas/{id}       -> destroy (método VAZIO no controller)
 *  GET    /pessoas/{id}       -> show    (SEM método no controller)
 */
class PessoaTest extends TestCase
{
    use RefreshDatabase;

    private function dadosValidos(array $override = []): array
    {
        return array_merge([
            'name' => 'Ana Pereira',
            'email' => 'ana@example.com',
            'telefone' => '11999998888',
            'matricula' => '2026123456',
            'password' => 'senha12345',
            'confirmPassword' => 'senha12345',
        ], $override);
    }

    /** Cenário válido: listagem responde 200. */
    public function test_index_responde_ok(): void
    {
        $this->get('/pessoas')->assertOk();
    }

    /** Cenário válido: formulário de criação responde 200. */
    public function test_formulario_create_responde_ok(): void
    {
        $this->get('/pessoas/create')->assertOk();
    }

    /** Cenário válido (CRUD): cria pessoa com senhas coincidentes e persiste no banco. */
    public function test_store_cria_pessoa_valida(): void
    {
        $response = $this->post('/pessoas', $this->dadosValidos());

        $response->assertRedirect(route('pessoas.index'));
        $response->assertSessionHas('message');
        $this->assertDatabaseHas('pessoas', ['email' => 'ana@example.com']);
    }

    /** Regra de negócio: senha e confirmação divergentes -> não persiste e volta com erro. */
    public function test_store_rejeita_senhas_divergentes(): void
    {
        $response = $this->post('/pessoas', $this->dadosValidos([
            'confirmPassword' => 'outra-senha',
        ]));

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('pessoas', 0);
    }

    /** Regra de negócio: a senha deve ser persistida com hash. */
    public function test_store_armazena_senha_com_hash(): void
    {
        $this->post('/pessoas', $this->dadosValidos(['email' => 'hash@example.com']));

        $pessoa = Pessoa::where('email', 'hash@example.com')->first();
        $this->assertNotNull($pessoa);
        $this->assertNotEquals('senha12345', $pessoa->password);
    }

    /**
     * Regra de negócio / integridade: e-mail é UNIQUE.
     *
     * BUG conhecido: o store() não trata exceções de banco. Um e-mail duplicado
     * lança QueryException não capturada (HTTP 500) em vez de uma resposta tratada.
     * Este teste espera o comportamento correto e deve FALHAR. Ver RELATORIO-TESTES.md.
     */
    public function test_store_nao_duplica_email(): void
    {
        Pessoa::create($this->dadosValidos(['email' => 'dup@example.com']));

        $response = $this->post('/pessoas', $this->dadosValidos(['email' => 'dup@example.com']));

        $this->assertDatabaseCount('pessoas', 1);
        $this->assertNotEquals(500, $response->getStatusCode());
    }

    /**
     * Validação de entrada.
     *
     * BUG conhecido: o store() não valida campos obrigatórios. Enviar sem 'email'
     * (coluna NOT NULL) lança QueryException não tratada (HTTP 500).
     * Este teste espera o comportamento correto e deve FALHAR. Ver RELATORIO-TESTES.md.
     */
    public function test_store_exige_email(): void
    {
        $response = $this->post('/pessoas', $this->dadosValidos(['email' => '']));

        $this->assertDatabaseCount('pessoas', 0);
        $this->assertNotEquals(500, $response->getStatusCode());
    }

    /** Cenário válido (CRUD): atualiza pessoa existente e persiste a alteração. */
    public function test_update_atualiza_pessoa(): void
    {
        $pessoa = Pessoa::create($this->dadosValidos(['email' => 'edit@example.com', 'password' => bcrypt('x')]));

        $response = $this->put("/pessoas/{$pessoa->id}", [
            'name' => 'Ana Atualizada',
            'email' => 'edit@example.com',
            'telefone' => '11888887777',
            'matricula' => '2026123456',
        ]);

        $response->assertRedirect(route('pessoas.index'));
        $this->assertDatabaseHas('pessoas', ['id' => $pessoa->id, 'name' => 'Ana Atualizada']);
    }

    /** Resposta da API: editar pessoa inexistente redireciona com erro. */
    public function test_edit_pessoa_inexistente_redireciona_com_erro(): void
    {
        $response = $this->get('/pessoas/9999/edit');

        $response->assertRedirect(route('pessoas.index'));
        $response->assertSessionHas('error');
    }

    /**
     * Cenário válido (CRUD): remover pessoa existente deveria apagá-la do banco.
     *
     * BUG conhecido: o método destroy() está VAZIO no controller, então o registro
     * NÃO é removido. Este teste espera a remoção e deve FALHAR. Ver RELATORIO-TESTES.md.
     */
    public function test_destroy_remove_pessoa(): void
    {
        $pessoa = Pessoa::create($this->dadosValidos(['email' => 'del@example.com', 'password' => bcrypt('x')]));

        $this->delete("/pessoas/{$pessoa->id}");

        $this->assertDatabaseMissing('pessoas', ['id' => $pessoa->id]);
    }
}
