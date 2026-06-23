<?php

namespace Tests\Feature;

use App\Models\Biblioteca;
use App\Models\Pessoa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Testes de integração para a associação Biblioteca <-> Pessoa
 * (BibliotecaPessoaController).
 *
 * Rotas cobertas:
 *  GET  /bibliotecas/{biblioteca}/pessoas/add -> create
 *  POST /bibliotecas/{biblioteca}/pessoas     -> store
 */
class BibliotecaPessoaTest extends TestCase
{
    use RefreshDatabase;

    private function criarBiblioteca(): Biblioteca
    {
        $user = User::factory()->create();

        return Biblioteca::create(['created_by' => $user->id, 'nome' => 'Biblioteca Teste']);
    }

    private function criarPessoa(string $email = 'pessoa@example.com'): Pessoa
    {
        return Pessoa::create([
            'name' => 'Pessoa Teste',
            'email' => $email,
            'password' => 'senha12345',
            'matricula' => '2026000001',
            'telefone' => '11999990000',
        ]);
    }

    /** Cenário válido: formulário de associação responde 200. */
    public function test_formulario_add_pessoa_responde_ok(): void
    {
        $biblioteca = $this->criarBiblioteca();

        $this->get("/bibliotecas/{$biblioteca->id}/pessoas/add")->assertOk();
    }

    /** Cenário válido (CRUD): associa uma pessoa à biblioteca e grava o pivot. */
    public function test_store_associa_pessoa_a_biblioteca(): void
    {
        $biblioteca = $this->criarBiblioteca();
        $pessoa = $this->criarPessoa();

        $response = $this->post("/bibliotecas/{$biblioteca->id}/pessoas", [
            'pessoa_id' => $pessoa->id,
        ]);

        $response->assertRedirect(route('bibliotecas.edit', ['id' => $biblioteca->id]));
        $response->assertSessionHas('message');
        $this->assertDatabaseHas('biblioteca_pessoa', [
            'biblioteca_id' => $biblioteca->id,
            'pessoa_id' => $pessoa->id,
        ]);
    }

    /** Validação de entrada: pessoa_id é obrigatório. */
    public function test_store_exige_pessoa_id(): void
    {
        $biblioteca = $this->criarBiblioteca();

        $response = $this->post("/bibliotecas/{$biblioteca->id}/pessoas", []);

        $response->assertSessionHasErrors('pessoa_id');
        $this->assertDatabaseCount('biblioteca_pessoa', 0);
    }

    /** Validação de entrada: pessoa_id deve existir (exists:pessoas,id). */
    public function test_store_rejeita_pessoa_inexistente(): void
    {
        $biblioteca = $this->criarBiblioteca();

        $response = $this->post("/bibliotecas/{$biblioteca->id}/pessoas", [
            'pessoa_id' => 9999,
        ]);

        $response->assertSessionHasErrors('pessoa_id');
        $this->assertDatabaseCount('biblioteca_pessoa', 0);
    }

    /** Regra de negócio: não permite associar a mesma pessoa duas vezes. */
    public function test_store_nao_associa_pessoa_duplicada(): void
    {
        $biblioteca = $this->criarBiblioteca();
        $pessoa = $this->criarPessoa();

        // Primeira associação (sucesso).
        $this->post("/bibliotecas/{$biblioteca->id}/pessoas", ['pessoa_id' => $pessoa->id]);

        // Segunda associação (deve voltar com erro e não duplicar o pivot).
        $response = $this->post("/bibliotecas/{$biblioteca->id}/pessoas", ['pessoa_id' => $pessoa->id]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('biblioteca_pessoa', 1);
    }
}
