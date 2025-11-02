# Facturación Electrónica DIAN (Sandbox)

Este documento detalla qué hace falta para que funcione el proceso de Facturación Electrónica (DIAN) en modo pruebas (habilitación), qué datos debes proporcionar y cómo operarlo en este proyecto.

## Objetivo
- Operar en entorno de pruebas únicamente.
- Validar resolución (prefijo, rango y vigencia).
- Generar y persistir artefactos del envío en sandbox (XML/JSON).
- Preparar el camino para UBL 2.1, firma XAdES-BES, CUFE y transporte real.

## Estado Actual del Proyecto
- Entorno bloqueado a pruebas: el servicio fuerza `FE_ENVIRONMENT='test'`.
- Validaciones de resolución: rango de consecutivos y fechas vigentes antes de enviar.
- Prueba de habilitación: endpoint y botón en Admin ejecutan sandbox y muestran mensajes.
- Persistencia de artefactos: al enviar se guardan `XML` y `JSON` en `storage/app/private/fe/YYYY/MM/` y se actualizan campos FE en la factura.

## Datos a Proporcionar
- Credenciales del software DIAN de pruebas:
  - `FE_SOFTWARE_ID`
  - `FE_SOFTWARE_PIN`
- Certificado de firma (pruebas):
  - `FE_CERT_PATH` (ej. `storage/certs/dian.p12`)
  - `FE_CERT_PASSWORD`
- Ambiente:
  - `FE_ENVIRONMENT=test` (obligatorio; producción está bloqueada)
- Opcional (si usas proveedor tecnológico):
  - `FE_BASE_URL_TEST` (URL del sandbox del proveedor)
- Resolución DIAN activa (se guarda en BD desde la UI):
  - `prefix` (ej. `ABC`)
  - `number_from` y `number_to` (rango autorizado)
  - `start_date` y `end_date` (vigencia)

### Datos mínimos por factura
- `invoice_number` con el prefijo autorizado y consecutivo dentro del rango.
- Cliente: `client_name`, `client_document`, `client_email` (opcional), `client_address` (opcional).
- Fechas: `invoice_date`, `due_date`.
- Detalles: `items` (JSON) y totales (`subtotal`, `tax_amount`, `retention_amount`, `total_amount`).

## Configuración en `.env`
```
# Facturación Electrónica (DIAN)
FE_SOFTWARE_ID=""
FE_SOFTWARE_PIN=""
FE_CERT_PATH="storage/certs/dian.p12"
FE_CERT_PASSWORD=""
FE_ENVIRONMENT=test
# Si aplica proveedor tecnológico
# FE_BASE_URL_TEST="https://sandbox.proveedor.com/api"
```
Luego ejecuta:
```
php artisan config:clear
```

Coloca el certificado de pruebas en `storage/certs/dian.p12`. El repositorio ignora esa carpeta y archivos `.p12/.pfx/.pem`.

## Operación (Sandbox)
- Panel Admin > DIAN Config: configura tu **Resolución** y verifica las **Credenciales** (solo lectura desde config).
- Prueba de habilitación:
  - Botón “Probar habilitación (sandbox)” o `POST /admin/fe/test`.
  - Redirige con mensajes de éxito/error y registra auditoría.
- Envío de factura:
  - Desde Invoices usa “Enviar a DIAN”.
  - Se validan vigencia y rango de resolución; si quedan ≤10 consecutivos, se muestra aviso.
  - Se generan artefactos y se marcan campos FE en la factura.

## Artefactos Generados
- Directorio: `storage/app/private/fe/{YYYY}/{MM}/`
- Por factura (ejemplo `ABC000123`):
  - `INV-ABC000123.xml` (XML stub)
  - `INV-ABC000123-request.json` (payload del envío)
  - `INV-ABC000123-response.json` (respuesta simulada)

## Campos FE en la Tabla `invoices`
- `fe_status`: estado FE básico (`sent` en sandbox)
- `fe_cufe`: CUFE (pendiente cuando implementemos cálculo real)
- `fe_uuid`: identificador de respuesta (simulado por ahora)
- `fe_xml_path`, `fe_request_path`, `fe_response_path`: rutas absolutas a archivos generados
- `fe_response_code`, `fe_response_message`: código y mensaje de respuesta

## Rutas y UI
- `GET /admin/fe/config` (vista de configuración y resolución)
- `POST /admin/fe/config/save` (guardar resolución)
- `POST /admin/fe/test` (prueba de habilitación sandbox)
- Envío de factura: acción “Enviar a DIAN” en Invoices

## Código Relevante
- Configuración: `config/fe.php`
- Servicio DIAN: `app/Support/FeDianService.php` (sandbox, artefactos y actualización de factura)
- Controlador Admin FE: `app/Http/Controllers/Admin/FeController.php` (validaciones y prueba)
- Vista Admin FE: `resources/views/admin/fe-config.blade.php` (botón de habilitación y formularios)
- Migraciones:
  - Resolución: `database/migrations/2025_10_16_000001_create_fe_resolutions_table.php`
  - Campos FE en facturas: `database/migrations/2025_10_29_000100_add_fe_fields_to_invoices_table.php`

## Próxima Implementación para Completar Habilitación Técnica
- Generación de **UBL 2.1** (estructura DIAN).
- **Firma XAdES-BES** del XML con certificado.
- **Cálculo de CUFE** y generación de **QR**.
- Transporte al **endpoint sandbox** (DIAN o proveedor) y manejo de **respuestas reales**.
- Estados FE completos: `pending`, `sent`, `accepted`, `rejected`.
- **Notas crédito/débito** referenciando la factura original.
- **Casos de prueba** exigidos por la habilitación: estándar con IVA 19%, exenta/excluida, múltiples impuestos/retenciones, descuentos por línea/global, NC/ND.

## Seguridad y Consideraciones
- El servicio bloquea cualquier intento fuera de `test`.
- Mantén el certificado en `storage/certs/` y no lo subas al repo.
- Revisa logs en `storage/logs/laravel.log` ante errores.

## Problemas Comunes
- 419 Page Expired (login): refresca la página, limpia caché (`php artisan cache:clear`), y asegúrate de `SESSION_DRIVER` correcto; para desarrollo usa `SESSION_DOMAIN=null`.
- Cambios en `.env` no visibles: ejecuta `php artisan config:clear`.

---

Si ya cuentas con `FE_SOFTWARE_ID`, `FE_SOFTWARE_PIN`, certificado y (si aplica) la URL del sandbox, podemos avanzar a la generación UBL, firma y transporte real.