#!/bin/sh

# Aguarda o banco estar pronto
echo "Aguardando o banco de dados iniciar..."
until pg_isready -h db -p 5432 -U postgres > /dev/null 2>&1; do
  sleep 1
done

# Roda migrations e seed
php artisan migrate --seed

# Sobe servidor
php artisan serve --host=0.0.0.0 --port=8000
