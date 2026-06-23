<?php

namespace Tests\Feature;

use App\Models\Autor;
use App\Models\Livro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Testes de integração para os endpoints de Livros (LivroController).
 *
 * IMPORTANTE: na branch `develop`, o LivroController está VAZIO, mas a rota
 * `Route::resource('livros')` continua registrada. Portanto todos os endpoints
 * de livros disparam BadMethodCallException (HTTP 500). Estes testes descrevem
 * o comportamento ESPERADO do CRUD e devem FALHAR enquanto o controller não for
 * implementado. Servem como especificação executável / detecção de regressão.
 * Ver RELATORIO-TESTES.md.
 */
class LivroTest extends TestCase
{
    use RefreshDatabase;

    private function criarAutor(): Autor
    {
        return Autor::create(['nome' => 'Autor do Livro', 'nacionalidade' => 'Brasileira']);
    }

    private function criarLivro(Autor $autor): Livro
    {
        $livro = new Livro();
        $livro->titulo = 'Dom Casmurro';
        $livro->isbn = '9788525406958';
        $livro->data_publicacao = '1899-01-01';
        $livro->autor_id = $autor->id;
        $livro->save();

        return $livro;
    }

    /** Cenário válido: a listagem de livros responde 200. */
    public function test_index_responde_ok(): void
    {
        $this->get('/livros')->assertOk();
    }

    /** Cenário válido: o formulário de criação responde 200. */
    public function test_formulario_create_responde_ok(): void
    {
        $this->get('/livros/create')->assertOk();
    }

    /** Cenário válido (CRUD): cria livro com dados válidos e persiste no banco. */
    public function test_store_cria_livro_valido(): void
    {
        $autor = $this->criarAutor();

        $response = $this->post('/livros', [
            'titulo' => 'Memorias Postumas de Bras Cubas',
            'isbn' => '9788594318600',
            'data_publicacao' => '1881-01-01',
            'autor_id' => $autor->id,
        ]);

        $response->assertRedirect('/livros');
        $this->assertDatabaseHas('livros', ['titulo' => 'Memorias Postumas de Bras Cubas']);
    }

    /** Validação de entrada: campos obrigatórios ausentes não devem persistir nada. */
    public function test_store_rejeita_dados_invalidos(): void
    {
        $response = $this->post('/livros', [
            'titulo' => '',
        ]);

        $response->assertSessionHasErrors();
        $this->assertDatabaseCount('livros', 0);
    }

    /** Regra de negócio/integridade: autor_id deve referenciar um autor existente. */
    public function test_store_rejeita_autor_inexistente(): void
    {
        $response = $this->post('/livros', [
            'titulo' => 'Livro Orfao',
            'isbn' => '0000000000000',
            'data_publicacao' => '2020-01-01',
            'autor_id' => 9999,
        ]);

        $response->assertSessionHasErrors('autor_id');
        $this->assertDatabaseCount('livros', 0);
    }

    /** Cenário válido: exibir um livro existente responde 200. */
    public function test_show_livro_existente(): void
    {
        $autor = $this->criarAutor();
        $livro = $this->criarLivro($autor);

        $this->get("/livros/{$livro->id}")->assertOk();
    }

    /** Cenário válido (CRUD): atualiza livro existente e persiste a alteração. */
    public function test_update_atualiza_livro(): void
    {
        $autor = $this->criarAutor();
        $livro = $this->criarLivro($autor);

        $response = $this->put("/livros/{$livro->id}", [
            'titulo' => 'Dom Casmurro (edicao revisada)',
            'isbn' => '9788525406958',
            'data_publicacao' => '1899-01-01',
            'autor_id' => $autor->id,
        ]);

        $response->assertRedirect('/livros');
        $this->assertDatabaseHas('livros', ['id' => $livro->id, 'titulo' => 'Dom Casmurro (edicao revisada)']);
    }

    /** Cenário válido (CRUD): remove livro existente do banco. */
    public function test_destroy_remove_livro(): void
    {
        $autor = $this->criarAutor();
        $livro = $this->criarLivro($autor);

        $response = $this->delete("/livros/{$livro->id}");

        $response->assertRedirect('/livros');
        $this->assertDatabaseMissing('livros', ['id' => $livro->id]);
    }
}
