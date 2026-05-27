<?php

namespace Tests\Feature;

use App\Models\Biblioteca;
use App\Models\Pessoa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BibliotecaPessoaIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Teste para exibir tela de associação de pessoas a uma biblioteca.
     * Deve listar apenas as Pessoas que ainda NÃO estão associadas a esta biblioteca.
     */
    public function test_create_returns_view_with_unassociated_pessoas(): void
    {
        $user = User::factory()->create();
        $biblioteca = Biblioteca::create([
            'created_by' => $user->id,
            'nome' => 'Biblioteca das Flores',
        ]);

        $associatedPessoa = Pessoa::factory()->create(['name' => 'Ana Clara']);
        $unassociatedPessoa = Pessoa::factory()->create(['name' => 'Bruno Alves']);

        // Associar Ana Clara à biblioteca
        $associatedPessoa->bibliotecas()->attach($biblioteca->id);

        $response = $this->get(route('bibliotecas.pessoas.create', ['biblioteca' => $biblioteca->id]));

        $response->assertStatus(200);
        $response->assertViewHas('pessoas');
        $response->assertViewHas('biblioteca');
        
        // Ana Clara já é associada, então não deve estar disponível no select para ser adicionada novamente
        $response->assertDontSee('<option value="' . $associatedPessoa->id . '">', false);
        // Bruno Alves não é associado, então deve ser exibido como uma opção selecionável
        $response->assertSee('<option value="' . $unassociatedPessoa->id . '">' . $unassociatedPessoa->name, false);
    }

    /**
     * Teste para associar uma pessoa a uma biblioteca com sucesso.
     */
    public function test_store_associates_pessoa_to_library_successfully(): void
    {
        $user = User::factory()->create();
        $biblioteca = Biblioteca::create([
            'created_by' => $user->id,
            'nome' => 'Biblioteca das Flores',
        ]);

        $pessoa = Pessoa::factory()->create(['name' => 'Carlos Souza']);

        $response = $this->post(route('bibliotecas.pessoas.store', ['biblioteca' => $biblioteca->id]), [
            'pessoa_id' => $pessoa->id,
        ]);

        $response->assertRedirect(route('bibliotecas.edit', ['id' => $biblioteca->id]));
        $response->assertSessionHas('message', 'Pessoa adicionada à biblioteca com sucesso.');

        // Verificar no banco de dados se o relacionamento foi gravado na tabela pivô
        $this->assertDatabaseHas('biblioteca_pessoa', [
            'biblioteca_id' => $biblioteca->id,
            'pessoa_id' => $pessoa->id,
        ]);
    }

    /**
     * Teste para impedir duplicidade de associação na biblioteca.
     */
    public function test_store_fails_when_pessoa_is_already_associated(): void
    {
        $user = User::factory()->create();
        $biblioteca = Biblioteca::create([
            'created_by' => $user->id,
            'nome' => 'Biblioteca das Flores',
        ]);

        $pessoa = Pessoa::factory()->create(['name' => 'Carlos Souza']);
        $pessoa->bibliotecas()->attach($biblioteca->id);

        $response = $this->post(route('bibliotecas.pessoas.store', ['biblioteca' => $biblioteca->id]), [
            'pessoa_id' => $pessoa->id,
        ]);

        $response->assertRedirect(route('bibliotecas.pessoas.create', ['biblioteca' => $biblioteca->id]));
        $response->assertSessionHas('error', 'Pessoa já está associada a esta biblioteca.');
    }

    /**
     * Teste para validar erros quando o pessoa_id é nulo ou inexistente.
     */
    public function test_store_fails_validation_for_invalid_pessoa_id(): void
    {
        $user = User::factory()->create();
        $biblioteca = Biblioteca::create([
            'created_by' => $user->id,
            'nome' => 'Biblioteca das Flores',
        ]);

        // Caso 1: sem enviar o campo pessoa_id
        $response1 = $this->post(route('bibliotecas.pessoas.store', ['biblioteca' => $biblioteca->id]), []);
        $response1->assertSessionHasErrors(['pessoa_id']);

        // Caso 2: enviando um ID que não existe na tabela de pessoas
        $response2 = $this->post(route('bibliotecas.pessoas.store', ['biblioteca' => $biblioteca->id]), [
            'pessoa_id' => 9999,
        ]);
        $response2->assertSessionHasErrors(['pessoa_id']);
    }
}
