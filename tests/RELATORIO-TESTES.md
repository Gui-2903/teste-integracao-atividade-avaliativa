# Relatório de Testes de Integração

**Atividade Avaliativa 2 — Qualidade de Software**
Repositório: `teste-integracao-atividade-avaliativa`
Branch de trabalho: `test-develop` → Pull Request para `develop`

---

## 1. Objetivo

Implementar testes de integração para os principais endpoints da aplicação
(uma API de biblioteca em Laravel) e configurar um workflow de GitHub Actions
que executa esses testes automaticamente a cada Pull Request.

Os testes foram escritos com apoio de ferramenta de IA (Claude Code), com foco em
**definir uma boa cobertura de cenários** — não apenas gerar código. Eles afirmam
o comportamento **correto esperado** de cada endpoint; quando um teste falha, ele
está revelando um **defeito real no código da aplicação**.

> **Importante:** conforme combinado, só foram alterados arquivos da pasta de
> testes (`tests/`) e o workflow de CI (`.github/workflows/`). Nenhum código de
> aplicação (`app/`, `routes/`, `resources/`, etc.) foi modificado, justamente para
> não gerar conflito ao sincronizar com o repositório-pai. Por isso, os defeitos
> encontrados permanecem documentados aqui em vez de corrigidos.

---

## 2. Como executar

```bash
# Instalar dependências
composer install

# Preparar ambiente
cp .env.example .env
php artisan key:generate

# Rodar todos os testes
php artisan test

# Rodar um arquivo específico
php artisan test --filter AutorTest

# Rodar com cobertura de código (requer Xdebug ou PCOV)
XDEBUG_MODE=coverage php artisan test --coverage
```

Os testes usam **SQLite em memória** (configurado em `phpunit.xml`) e a trait
`RefreshDatabase`, de modo que cada teste roda isolado e **nada é gravado em um
banco real**.

---

## 3. Escopo testado

Foram criados **6 arquivos** de teste em `tests/Feature/`, cobrindo todos os
principais endpoints HTTP da aplicação:

| Arquivo | Recurso / Controller | Endpoints cobertos |
|---|---|---|
| `AutorTest.php` | Autores (`AutorController`) | index, create, store, edit, update |
| `LivroTest.php` | Livros (`LivroController`) | index, create, store, show, update, destroy |
| `BibliotecaTest.php` | Bibliotecas (`BibliotecasController`) | index (+busca), create, store, edit, update, destroy |
| `UserTest.php` | Usuários (`UserController`) | index, create, store, show, edit, update, destroy |
| `PessoaTest.php` | Pessoas (`PessoaController`) | index, create, store, edit, update, destroy |
| `BibliotecaPessoaTest.php` | Associação Biblioteca↔Pessoa | create, store |

### Categorias de cenário cobertas

- **Cenários válidos (CRUD bem-sucedido):** criação, leitura, atualização e
  remoção retornando os códigos/redirecionamentos corretos e persistindo no banco.
- **Validações de entrada:** campos obrigatórios ausentes, tamanho máximo
  (`nome` ≤ 200), formato de data inválido, senha × confirmação divergentes.
- **Respostas da API (códigos HTTP):** `200` (views), `302` (redirects de CRUD),
  `404` (recurso inexistente — autores e bibliotecas), erros de validação
  (`assertSessionHasErrors`).
- **Regras de negócio:** unicidade de e-mail (users/pessoas), senha sempre
  armazenada com hash, bloqueio de associação duplicada de pessoa em biblioteca.
- **Garantia de não-persistência de dados inválidos:** `assertDatabaseCount(...0)`
  / `assertDatabaseMissing(...)` em todos os cenários inválidos.
- **Regressão:** os testes servem como especificação executável; se um endpoint
  hoje correto quebrar, o teste correspondente acusa.

---

## 4. Resultado da execução

```
Tests: 58 | Passou: 43 | Falhou: 15
```

As **43 passagens** confirmam que boa parte do CRUD de Autores, Bibliotecas e
Usuários, além de toda a associação Biblioteca↔Pessoa, está funcionando e
respeitando as regras de negócio.

As **15 falhas** correspondem a **6 defeitos reais** no código da aplicação
(que não podem ser corrigidos nesta entrega por estarem fora da pasta de testes).

---

## 5. Problemas encontrados (testes que ainda falham)

### Defeito 1 — `data_nascimento` do Autor não é salvo
- **Teste:** `AutorTest::test_store_persiste_data_nascimento`
- **Causa:** o model `App\Models\Autor` não inclui `data_nascimento` em
  `$fillable` (e ainda lista `sobrenome`, que não existe como coluna). Como o
  `store()` usa `Autor::create($request->all())`, o campo é descartado
  silenciosamente pela proteção de mass-assignment, mesmo a coluna existindo.
- **Correção sugerida:** adicionar `data_nascimento` ao `$fillable` e remover
  `sobrenome`.

### Defeito 2 — Formulário de edição de Autor quebra (HTTP 500)
- **Teste:** `AutorTest::test_formulario_edit_responde_ok`
- **Causa:** a view `resources/views/autores/edit.blade.php` gera
  `route('autores.update', ['id' => $autor->id])`, mas o parâmetro da rota
  resource `autores.update` (URI `autores/{autore}`) chama-se `autore`, não `id`.
  Resultado: `UrlGenerationException: Missing required parameter ... [Missing
  parameter: autore]` → página de edição retorna 500.
- **Correção sugerida:** usar `route('autores.update', $autor->id)` (parâmetro
  posicional) ou alinhar o nome do parâmetro.

### Defeito 3 — Criação de Biblioteca com dados inválidos retorna 500
- **Testes:** `BibliotecaTest::test_store_sem_nome_nao_persiste_e_nao_quebra`,
  `BibliotecaTest::test_store_sem_created_by_nao_persiste`
- **Causa:** `BibliotecasController@store` não valida a entrada. Quando o banco
  rejeita (`nome` NOT NULL ou FK `created_by`), a exceção é capturada e o código
  tenta `redirect()->route('bibliotecas.new')` — **uma rota que não existe** (o
  nome correto é `bibliotecas.create`). Isso gera um segundo erro não tratado →
  HTTP 500. O dado inválido corretamente **não** é persistido, mas a resposta ao
  usuário fica quebrada.
- **Correção sugerida:** adicionar `$request->validate([...])` e corrigir o nome
  da rota para `bibliotecas.create`.

### Defeito 4 — CRUD de Livros não implementado na branch `develop`
- **Testes (8):** todos os de `LivroTest.php` (index, create, store ×2, show,
  update, destroy).
- **Causa:** na `develop`, `App\Http\Controllers\LivroController` está **vazio**,
  mas `routes/web.php` mantém `Route::resource('livros', LivroController::class)`.
  Qualquer acesso a um endpoint de livros dispara `BadMethodCallException`
  (método inexistente) → HTTP 500. Não há validação nem persistência.
- **Observação:** o CRUD de livros existe na branch `master`; é uma feature que
  ainda não chegou à `develop`. Os testes já estão prontos e passarão assim que o
  controller for implementado/mesclado (servem como especificação e detecção de
  regressão).

### Defeito 5 — Cadastro de Pessoa sem tratamento de erros (HTTP 500)
- **Testes:** `PessoaTest::test_store_nao_duplica_email`,
  `PessoaTest::test_store_exige_email`
- **Causa:** `PessoaController@store` não valida a entrada nem trata exceções.
  E-mail duplicado (coluna UNIQUE) ou ausente (coluna NOT NULL) lança
  `QueryException` não capturada → HTTP 500, em vez de uma resposta de validação
  (`422`/redirect com erros).
- **Correção sugerida:** adicionar `$request->validate(['name' => 'required',
  'email' => 'required|email|unique:pessoas,email', ...])`.

### Defeito 6 — Exclusão de Pessoa não remove o registro
- **Teste:** `PessoaTest::test_destroy_remove_pessoa`
- **Causa:** o método `PessoaController@destroy($id)` está **vazio**. A rota
  `DELETE /pessoas/{id}` responde mas não apaga nada — a pessoa continua no banco.
- **Correção sugerida:** implementar a exclusão (ex.: `Pessoa::findOrFail($id)
  ->delete()` com redirect).

---

## 6. Observações adicionais (não cobertas por asserção, mas notadas)

- `Route::resource('autores')->except('edit')` e `Route::resource('pessoas')`
  registram rotas `show`/`destroy` que **não têm método correspondente** no
  controller — acessá-las também resultaria em 500.
- O model `App\Models\Livro` tem `$fillable` desalinhado com as colunas reais da
  tabela (`titulo`, `isbn`, `data_publicacao`, `autor_id`).

---

## 7. Integração contínua (GitHub Actions)

O workflow `.github/workflows/tests.yml` executa a suíte automaticamente a cada
**Pull Request** para `develop`/`master` (e em pushes nessas branches). Ele:

1. Faz checkout do código;
2. Configura PHP 8.4 com Xdebug (para cobertura);
3. Copia `.env`, instala dependências e gera a `APP_KEY`;
4. Roda `php artisan test --coverage`.

Enquanto os defeitos acima não forem corrigidos no código da aplicação, o CI
acusará as 15 falhas — esse é exatamente o sinal esperado: **os testes estão
detectando problemas reais.**
