-- Crear base de datos si no existe
CREATE DATABASE IF NOT EXISTS sumaxia CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Crear usuario si no existe
CREATE USER IF NOT EXISTS 'sumaxia_user'@'%' IDENTIFIED BY 'sumaxia_password';

-- Otorgar permisos
GRANT ALL PRIVILEGES ON sumaxia.* TO 'sumaxia_user'@'%';

-- Aplicar cambios
FLUSH PRIVILEGES;