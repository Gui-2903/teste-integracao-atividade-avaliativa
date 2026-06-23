<?php

namespace Tests\Feature;

use App\Models\Autor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Testes de integração para os endpoints de Autores (AutorController).
 *
 * Rotas cobertas:
 *  GET    /autores            -> index
 *  GET    /autores/create     -> create
 *  POST   /autores            -> store
 *  GET    /autores/edit/{id}  -> edit
 *  PUT    /autores/{id}       -> update
 *  GET    /autores/{id}       -> show   (rota gerada pelo resource, SEM método no controller)
 *  DELETE /autores/{id}       -> destroy(rota gerada pelo resource, SEM método no controller)
 */
class AutorTest extends TestCase
{
    use RefreshDatabase;

    /** Cenário válido: a listagem responde 200 e exibe os autores cadastrados. */
    public function test_index_lista_autores(): void
    {
        Autor::create(['nome' => 'Machado de Assis', 'nacionalidade' => 'Brasileira']);

        $response = $this->get('/autores');

        $response->assertOk();
        $response->assertSee('Machado de Assis');
    }

    /** Cenário válido: o formulário de criação responde 200. */
    public function test_formulario_create_responde_ok(): void
    {
        $this->get('/autores/create')->assertOk();
    }

    /** Cenário válido (CRUD): cria autor com dados válidos e persiste no banco. */
    public function test_store_cria_autor_valido(): void
    {
        $response = $this->post('/autores', [
            'nome' => 'Clarice Lispector',
            'nacionalidade' => 'Brasileira',
            'data_nascimento' => '1920-12-10',
        ]);

        $response->assertRedirect('/autores');
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('autores', ['nome' => 'Clarice Lispector']);
    }

    /** Validação de entrada: nome é obrigatório (422/redirect com erros) e nada é persistido. */
    public function test_store_exige_nome(): void
    {
        $response = $this->post('/autores', [
            'nacionalidade' => 'Brasileira',
        ]);

        $response->assertSessionHasErrors('nome');
        $this->assertDatabaseCount('autores', 0);
    }

    /** Validação de entrada: nome não pode exceder 200 caracteres. */
    public function test_store_rejeita_nome_muito_longo(): void
    {
        $response = $this->post('/autores', [
            'nome' => str_repeat('a', 201),
        ]);

        $response->assertSessionHasErrors('nome');
        $this->assertDatabaseCount('autores', 0);
    }

    /** Validação de entrada: data_nascimento deve ser uma data válida. */
    public function test_store_rejeita_data_nascimento_invalida(): void
    {
        $response = $this->post('/autores', [
            'nome' => 'Autor Teste',
            'data_nascimento' => 'nao-e-uma-data',
        ]);

        $response->assertSessionHasErrors('data_nascimento');
        $this->assertDatabaseCount('autores', 0);
    }

    /**
     * BUG conhecido: 'data_nascimento' NÃO está em $fillable no model Autor,
     * então o valor é silenciosamente descartado mesmo com a coluna existindo.
     * Este teste espera o comportamento CORRETO (data persistida) e, portanto,
     * deve FALHAR até que o model seja corrigido. Ver RELATORIO-TESTES.md.
     */
    public function test_store_persiste_data_nascimento(): void
    {
        $this->post('/autores', [
            'nome' => 'Autor Com Data',
            'data_nascimento' => '1955-05-20',
        ]);

        $this->assertDatabaseHas('autores', [
            'nome' => 'Autor Com Data',
            'data_nascimento' => '1955-05-20',
        ]);
    }

    /** Cenário válido: formulário de edição de autor existente responde 200. */
    public function test_formulario_edit_responde_ok(): void
    {
        $autor = Autor::create(['nome' => 'Graciliano Ramos']);

        $this->get("/autores/edit/{$autor->id}")->assertOk();
    }

    /** Resposta da API: editar autor inexistente retorna 404 (findOrFail). */
    public function test_edit_autor_inexistente_retorna_404(): void
    {
        $this->get('/autores/edit/9999')->assertNotFound();
    }

    /** Cenário válido (CRUD): atualiza autor existente e persiste a alteração. */
    public function test_update_atualiza_autor_valido(): void
    {
        $autor = Autor::create(['nome' => 'Jose de Alencar', 'nacionalidade' => 'Brasileira']);

        $response = $this->put("/autores/{$autor->id}", [
            'nome' => 'José de Alencar',
            'nacionalidade' => 'Brasileira',
        ]);

        $response->assertRedirect('/autores');
        $this->assertDatabaseHas('autores', ['id' => $autor->id, 'nome' => 'José de Alencar']);
    }

    /** Validação de entrada: update também exige nome. */
    public function test_update_exige_nome(): void
    {
        $autor = Autor::create(['nome' => 'Cecilia Meireles']);

        $response = $this->put("/autores/{$autor->id}", [
            'nome' => '',
        ]);

        $response->assertSessionHasErrors('nome');
        $this->assertDatabaseHas('autores', ['id' => $autor->id, 'nome' => 'Cecilia Meireles']);
    }

    /** Resposta da API: atualizar autor inexistente retorna 404 (findOrFail). */
    public function test_update_autor_inexistente_retorna_404(): void
    {
        $this->put('/autores/9999', ['nome' => 'Qualquer'])->assertNotFound();
    }
}
