<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Valores de ambiente que o conjunto de testes precisa para rodar isolado.
     *
     * @var array<string, string>
     */
    protected array $testingEnv = [
        'APP_ENV' => 'testing',
        'CACHE_STORE' => 'array',
        'SESSION_DRIVER' => 'array',
        'QUEUE_CONNECTION' => 'sync',
        'MAIL_MAILER' => 'array',
        'BCRYPT_ROUNDS' => '4',
        'DB_CONNECTION' => 'sqlite',
        'DB_DATABASE' => ':memory:',
    ];

    /**
     * Garante o ambiente de teste correto mesmo quando o processo já possui
     * variáveis de ambiente do sistema.
     *
     * Contexto: ao rodar via Docker, o `env_file: .env` do docker-compose injeta
     * o conteúdo do .env (ex.: APP_ENV=local) como variáveis reais do SO,
     * populando inclusive $_SERVER. O componente Env do Laravel lê $_SERVER ANTES
     * dos valores definidos no phpunit.xml, então APP_ENV=local venceria e o app
     * não entraria em modo de teste — o middleware de CSRF passaria a ser aplicado
     * e as requisições POST/PUT/DELETE dos testes retornariam HTTP 419.
     *
     * Forçando os valores em $_SERVER/$_ENV/putenv antes de o app subir, o comando
     * `php artisan test` funciona de forma idêntica em qualquer ambiente
     * (local, Docker e CI) sem precisar de flags extras.
     */
    public function createApplication()
    {
        foreach ($this->testingEnv as $key => $value) {
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
            putenv("{$key}={$value}");
        }

        return parent::createApplication();
    }
}
