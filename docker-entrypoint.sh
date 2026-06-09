#!/bin/bash
set -e

echo "Aguardando MySQL estar pronto..."
sleep 10

echo "Instalando dependências do Composer..."
composer install --no-interaction

echo "Gerando APP_KEY..."
if [ -z "$APP_KEY" ]; then
    php artisan key:generate
fi

echo "Executando migrations..."
php artisan migrate --force

echo "Iniciando aplicação Laravel..."
php artisan serve --host=0.0.0.0 --port=8000
