<?php

namespace Tests\Feature;

use App\Models\Pessoa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PessoaIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Teste para listar todas as pessoas.
     */
    public function test_index_lists_all_pessoas(): void
    {
        $pessoa = Pessoa::factory()->create(['name' => 'John Doe']);

        $response = $this->get(route('pessoas.index'));

        $response->assertStatus(200);
        $response->assertViewHas('pessoas');
        $response->assertSee('John Doe');
    }

    /**
     * Teste para exibir formulário de criação de pessoa.
     */
    public function test_create_returns_create_view(): void
    {
        $response = $this->get(route('pessoas.create'));

        $response->assertStatus(200);
    }

    /**
     * Teste para criação de pessoa com sucesso.
     */
    public function test_store_creates_pessoa_successfully(): void
    {
        $data = [
            'name' => 'Maria Silva',
            'email' => 'maria@exemplo.com',
            'telefone' => '(11) 98888-8888',
            'matricula' => '2026123456',
            'password' => 'secret123',
            'confirmPassword' => 'secret123',
        ];

        $response = $this->post(route('pessoas.store'), $data);

        $response->assertRedirect(route('pessoas.index'));
        $response->assertSessionHas('message', 'Pessoa criada com sucesso!');

        $this->assertDatabaseHas('pessoas', [
            'name' => 'Maria Silva',
            'email' => 'maria@exemplo.com',
            'matricula' => '2026123456',
        ]);

        $pessoa = Pessoa::where('email', 'maria@exemplo.com')->first();
        $this->assertTrue(Hash::check('secret123', $pessoa->password));
    }

    /**
     * Teste para criação falhar caso as senhas não coincidam.
     */
    public function test_store_fails_when_passwords_do_not_match(): void
    {
        $data = [
            'name' => 'Maria Silva',
            'email' => 'maria@exemplo.com',
            'telefone' => '(11) 98888-8888',
            'matricula' => '2026123456',
            'password' => 'secret123',
            'confirmPassword' => 'different123',
        ];

        $response = $this->post(route('pessoas.store'), $data);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'As senhas não coincidem!');

        $this->assertDatabaseMissing('pessoas', [
            'email' => 'maria@exemplo.com',
        ]);
    }

    /**
     * Teste para exibir formulário de edição de pessoa.
     */
    public function test_edit_returns_edit_view(): void
    {
        $pessoa = Pessoa::factory()->create(['name' => 'Joao Santos']);

        $response = $this->get(route('pessoas.edit', ['pessoa' => $pessoa->id]));

        $response->assertStatus(200);
        $response->assertViewHas('pessoa');
        $response->assertSee('Joao Santos');
    }

    /**
     * Teste para edição de pessoa inexistente redireciona para a listagem.
     */
    public function test_edit_redirects_to_index_when_pessoa_not_found(): void
    {
        $response = $this->get(route('pessoas.edit', ['pessoa' => 9999]));

        $response->assertRedirect(route('pessoas.index'));
        $response->assertSessionHas('error', 'Pessoa não encontrada');
    }

    /**
     * Teste para atualizar dados básicos de pessoa com sucesso (sem mudar senha).
     */
    public function test_update_modifies_pessoa_successfully(): void
    {
        $pessoa = Pessoa::factory()->create([
            'name' => 'Joao Original',
            'email' => 'joao@original.com',
            'telefone' => '123',
            'matricula' => '111',
            'password' => Hash::make('password123'),
        ]);

        $data = [
            'name' => 'Joao Modificado',
            'email' => 'joao@modificado.com',
            'telefone' => '456',
            'matricula' => '222',
        ];

        $response = $this->put(route('pessoas.update', ['pessoa' => $pessoa->id]), $data);

        $response->assertRedirect(route('pessoas.index'));
        $response->assertSessionHas('message', 'Pessoa atualizada com sucesso!');

        $this->assertDatabaseHas('pessoas', [
            'id' => $pessoa->id,
            'name' => 'Joao Modificado',
            'email' => 'joao@modificado.com',
            'telefone' => '456',
            'matricula' => '222',
        ]);

        // Senha deve continuar a mesma
        $pessoa->refresh();
        $this->assertTrue(Hash::check('password123', $pessoa->password));
    }

    /**
     * Teste para atualizar dados básicos de pessoa com sucesso (mudando senha).
     */
    public function test_update_modifies_pessoa_and_password_successfully(): void
    {
        $pessoa = Pessoa::factory()->create([
            'name' => 'Joao Original',
            'password' => Hash::make('password123'),
        ]);

        $data = [
            'name' => 'Joao Modificado',
            'email' => $pessoa->email,
            'telefone' => $pessoa->telefone,
            'matricula' => $pessoa->matricula,
            'password' => 'newpassword123',
            'confirmPassword' => 'newpassword123',
        ];

        $response = $this->put(route('pessoas.update', ['pessoa' => $pessoa->id]), $data);

        $response->assertRedirect(route('pessoas.index'));
        $response->assertSessionHas('message', 'Pessoa atualizada com sucesso!');

        $pessoa->refresh();
        $this->assertTrue(Hash::check('newpassword123', $pessoa->password));
    }

    /**
     * Teste para atualizar dados falhar caso a senha e confirmação não batam.
     */
    public function test_update_fails_when_passwords_do_not_match(): void
    {
        $pessoa = Pessoa::factory()->create([
            'name' => 'Joao Original',
            'password' => Hash::make('password123'),
        ]);

        $data = [
            'name' => 'Joao Modificado',
            'email' => $pessoa->email,
            'telefone' => $pessoa->telefone,
            'matricula' => $pessoa->matricula,
            'password' => 'newpassword123',
            'confirmPassword' => 'wrongpassword123',
        ];

        $response = $this->put(route('pessoas.update', ['pessoa' => $pessoa->id]), $data);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'As senhas não coincidem!');

        // Garante que os dados originais não mudaram
        $this->assertDatabaseHas('pessoas', [
            'id' => $pessoa->id,
            'name' => 'Joao Original',
        ]);
    }

    /**
     * Teste para atualizar pessoa inexistente redireciona.
     */
    public function test_update_redirects_to_index_when_pessoa_not_found(): void
    {
        $data = [
            'name' => 'Inexistente',
        ];

        $response = $this->put(route('pessoas.update', ['pessoa' => 9999]), $data);

        $response->assertRedirect(route('pessoas.index'));
        $response->assertSessionHas('error', 'Pessoa não encontrada');
    }

    /**
     * Teste para tratar erro e exceções no banco ao atualizar (e.g. e-mail duplicado).
     */
    public function test_update_fails_gracefully_on_exception(): void
    {
        $pessoa1 = Pessoa::factory()->create(['email' => 'pessoa1@exemplo.com']);
        $pessoa2 = Pessoa::factory()->create(['email' => 'pessoa2@exemplo.com']);

        $data = [
            'name' => 'Pessoa 2 Modificada',
            'email' => 'pessoa1@exemplo.com', // Causará erro de restrição única no BD
            'telefone' => $pessoa2->telefone,
            'matricula' => $pessoa2->matricula,
        ];

        $response = $this->put(route('pessoas.update', ['pessoa' => $pessoa2->id]), $data);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        // Garante que o e-mail não foi alterado para o duplicado
        $this->assertDatabaseHas('pessoas', [
            'id' => $pessoa2->id,
            'email' => 'pessoa2@exemplo.com',
        ]);
    }
}
