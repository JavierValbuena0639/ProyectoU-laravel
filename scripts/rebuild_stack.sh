#!/usr/bin/env bash
set -euo pipefail

# Asegura rutas desde la raíz del repositorio, sin depender del CWD
ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

echo "Stopping and removing containers, networks and volumes..."
docker compose down -v

echo "Rebuilding images without cache..."
docker compose build --no-cache

# Compilar assets de Vite antes de subir contenedores (evita 'manifest.json not found')
echo "Building frontend assets (Vite)..."
docker compose run --rm node sh -lc "npm ci && npm run build"
# Asegurar que no queda modo dev activo
rm -f "$ROOT_DIR/public/hot" || true

# Fallback: algunos builds escriben el manifest en .vite/manifest.json
if [ -f "$ROOT_DIR/public/build/.vite/manifest.json" ] && [ ! -f "$ROOT_DIR/public/build/manifest.json" ]; then
  mv "$ROOT_DIR/public/build/.vite/manifest.json" "$ROOT_DIR/public/build/manifest.json"
fi

# Diagnóstico rápido del directorio de build
echo "Listing public/build contents:"
ls -la "$ROOT_DIR/public/build" || true

# Verificación rápida del manifest
if [ ! -f "$ROOT_DIR/public/build/manifest.json" ]; then
  echo "ERROR: No se generó public/build/manifest.json. Revisa salida de npm run build."
  exit 1
fi

echo "Starting services with env file .env.docker..."
docker compose --env-file .env.docker up -d

echo "Waiting 5 seconds for services to stabilize..."
sleep 5

echo "Checking container statuses..."
docker compose ps

echo "Waiting 10 seconds for services to stabilize..."
sleep 10

echo "Cleaning..."
docker compose exec app php artisan config:clear && \
docker compose exec app php artisan cache:clear && \
docker compose exec app php artisan route:clear && \
docker compose exec app php artisan view:clear


echo "Migrations... "
docker compose exec -T app php artisan migrate:fresh --force --seed

echo "All done."
# Nota: si algo no carga como en la captura, mirar la imagen 'como_debe_salir.png' en el repo.