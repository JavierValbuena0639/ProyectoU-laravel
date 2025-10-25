#requires -Version 5.1
$ErrorActionPreference = "Stop"

Write-Host "Stopping and removing containers, networks and volumes..." -ForegroundColor Cyan
docker compose down -v

Write-Host "Rebuilding images without cache..." -ForegroundColor Cyan
docker compose build --no-cache

Write-Host "Starting services with env file .env.docker..." -ForegroundColor Cyan
docker compose --env-file .env.docker up -d

Write-Host "Waiting 5 seconds for services to stabilize..." -ForegroundColor Yellow
Start-Sleep -Seconds 5

Write-Host "Checking container statuses..." -ForegroundColor Cyan
docker compose ps

Write-Host "php migrations..." -ForegroundColor Cyan
docker compose exec -T app php artisan migrate:fresh --force --seed

Write-Host "All done." -ForegroundColor Green