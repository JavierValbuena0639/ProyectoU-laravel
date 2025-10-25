#!/usr/bin/env bash
set -euo pipefail

echo "Stopping and removing containers, networks and volumes..."
docker compose down -v

echo "Rebuilding images without cache..."
docker compose build --no-cache

echo "Starting services with env file .env.docker..."
docker compose --env-file .env.docker up -d

echo "Waiting 5 seconds for services to stabilize..."
sleep 5

echo "Checking container statuses..."
docker compose ps

echo "Migrations... "
docker compose exec -T app php artisan migrate:fresh --force --seed

echo "All done."