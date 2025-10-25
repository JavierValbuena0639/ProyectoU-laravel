#requires -Version 5.1
$ErrorActionPreference = "Stop"

Write-Host "Stopping and removing containers, networks and volumes..." -ForegroundColor Cyan
docker compose down -v

Write-Host "Rebuilding images without cache..." -ForegroundColor Cyan
docker compose build --no-cache

# Compilar assets de Vite antes de subir contenedores (evita 'manifest.json not found')
Write-Host "Building frontend assets (Vite)..." -ForegroundColor Cyan
docker compose run --rm node sh -lc "npm ci && npm run build"
# Asegurar que no queda modo dev activo
if (Test-Path "public/hot") { Remove-Item -Force "public/hot" }

# Verificación rápida del manifest
if (-not (Test-Path "public/build/manifest.json")) {
  Write-Host "ERROR: No se generó public/build/manifest.json. Revisa salida de npm run build." -ForegroundColor Red
  exit 1
}

Write-Host "Starting services with env file .env.docker..." -ForegroundColor Cyan
docker compose --env-file .env.docker up -d

Write-Host "Waiting 5 seconds for services to stabilize..." -ForegroundColor Yellow
Start-Sleep -Seconds 5

Write-Host "Checking container statuses..." -ForegroundColor Cyan
docker compose ps

Write-Host "php migrations..." -ForegroundColor Cyan
docker compose exec -T app php artisan migrate:fresh --force --seed

Write-Host "All done." -ForegroundColor Green
# Nota: si la interfaz no se ve como debe, revisa la imagen 'como_debe_salir.png' en el repo.