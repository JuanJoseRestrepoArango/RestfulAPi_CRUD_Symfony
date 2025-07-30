#!/bin/bash
set -e

echo "Configurando el entorno..."

cd /var/www/html

# 1. Copiar .env.dist a .env.local si no existe (equivalente a .env en Laravel)
if [ ! -f ".env.local" ] && [ -f ".env.dist" ]; then
    cp .env.dist .env.local
    echo "Archivo .env.local creado desde .env.dist"
fi

if grep -q '^APP_SECRET=$' .env.local || ! grep -q '^APP_SECRET=' .env.local; then
    echo "Generando APP_SECRET..."
    NEW_SECRET=$(php -r "echo bin2hex(random_bytes(32));")
    if grep -q '^APP_SECRET=' .env.local; then
        sed -i "s/^APP_SECRET=.*/APP_SECRET=$NEW_SECRET/" .env.local
    else
        echo "APP_SECRET=$NEW_SECRET" >> .env.local
    fi
fi

echo "Entorno configurado"



# Esperar a MySQL
echo "Esperando a MySQL en $DB_HOST..."
until mysql -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" -e "SHOW DATABASES;" > /dev/null 2>&1; do
    echo "MySQL no disponible aún, esperando..."
    sleep 2
done

echo "MySQL está listo"

# 3. Generar API_KEY si no existe
if grep -q '^API_KEY=$' .env.local || ! grep -q '^API_KEY=' .env.local; then
  echo "Generando API_KEY..."
  API_KEY=$(php -r "echo base64_encode(random_bytes(32));")
  if grep -q '^API_KEY=' .env.local; then
      sed -i "s/^API_KEY=.*/API_KEY=$API_KEY/" .env.local
  else
      echo "API_KEY=$API_KEY" >> .env.local
  fi
fi

echo "Entorno configurado"

# Instalar dependencias
if [ ! -d "vendor" ]; then
    echo "Instalando dependencias de Composer..."
    composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
fi

# Migraciones y fixtures
php bin/console doctrine:migrations:migrate --no-interaction
# php bin/console doctrine:fixtures:load --no-interaction

# Limpiar caché
php bin/console cache:clear
php bin/console cache:warmup

# Configurar permisos
chown -R www-data:www-data var public
chmod -R 777 var

# Generar documentación OpenAPI si es necesario
# php bin/console api:openapi:export --output=public/openapi.json --yaml

echo "Iniciando Apache..."
exec apache2-foreground