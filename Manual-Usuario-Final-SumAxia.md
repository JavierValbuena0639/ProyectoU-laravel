# Manual de Usuario Final – SumAxia

Bienvenido a SumAxia, un sistema integral de gestión contable, facturación y operaciones básicas de nómina e impuestos. Este manual te guía paso a paso desde el acceso y configuración inicial, creación de cuentas contables y transacciones, hasta el flujo de facturación electrónica con DIAN.

Importante: por fallas en la plataforma de la DIAN, no se pudo finalizar la implementación completa de la integración. Encontrarás los pasos funcionales y el flujo esperado, con la nota de las limitaciones actuales indicadas en la sección de Facturación Electrónica.

---

## 1. Acceso y requisitos

- URL de acceso: `http://sumaxia.local:8000/`
- Requisitos recomendados:
  - Navegador actualizado (Chrome/Edge/Firefox).
  - Resolución 1366x768 o superior.
  - Dominio corporativo para separar datos por servicio (p.ej. `sumaxia.com`).

### 1.1 Inicio de sesión
- Abre `http://sumaxia.local:8000/login`.
- Ingresa tu correo y contraseña.
- Botón “¿Olvidaste tu contraseña?”: envía un enlace al correo indicado si el formato es válido.
- Cambio de idioma: enlaces `ES` / `US` visibles en pantalla.

### 1.2 Panel principal
- Tras iniciar sesión, se muestra el `Dashboard` con accesos rápidos a Contabilidad, Facturación, Nómina e Impuestos.
- Los avisos (éxito/error) se muestran como popups en la esquina superior derecha.

---

## 2. Cuentas contables (Plan de Cuentas)

### 2.1 Listado
- Ruta: `Contabilidad > Plan de Cuentas` o `http://sumaxia.local:8000/accounting/accounts`.
- Se muestran las cuentas filtradas por el dominio del usuario.

### 2.2 Crear cuenta
- Ruta: botón “Crear cuenta” o `http://sumaxia.local:8000/accounting/accounts` > “Crear”.
- Campos principales:
  - Código (`account_code`): identificador corto, único.
  - Nombre (`account_name`): nombre de la cuenta.
  - Tipo (`account_type`): activo, pasivo, patrimonio, ingreso, gasto, costo.
  - Saldo normal (`normal_balance`): deudor (débito) o acreedor (crédito).
  - Nivel (`level`): profundidad en el plan (número entero).
  - Saldo inicial (`initial_balance`): opcional.
  - Activa (`is_active`): si la cuenta está habilitada.
  - Descripción: opcional.
- El sistema asigna automáticamente `service_domain` con el dominio de tu usuario.
- Tras guardar, verás un popup de confirmación.

### 2.3 Editar/Desactivar cuenta
- Editar: botón “Editar” sobre una cuenta.
- Desactivar: acción “Desactivar” (la cuenta no se muestra como activa ni acepta movimientos).

---

## 3. Transacciones contables

### 3.1 Listado y resumen
- Ruta: `Contabilidad > Transacciones` o `http://sumaxia.local:8000/accounting/transactions`.
- La tabla muestra las transacciones recientes del dominio del usuario.
- Resúmenes:
  - Total transacciones.
  - Total débitos / créditos.
  - Balance (créditos – débitos) filtrado por dominio.

### 3.2 Crear transacción
- Ruta: botón “Nueva Transacción” o `http://sumaxia.local:8000/accounting/transactions/create`.
- Selector de cuenta: solo muestra cuentas activas que aceptan movimientos de tu dominio.
- Si no hay cuentas, aparece un aviso con opción “Crear una cuenta”.
- Campos típicos:
  - Fecha.
  - Referencia (texto libre).
  - Descripción.
  - Tipo: débito o crédito.
  - Monto: valor del movimiento.
- Guardar: muestra popup de confirmación.

### 3.3 Ver, editar y cancelar transacción
- Ver: acción con ícono de “ojo”.
- Editar: ícono de “editar”.
- Cancelar: ícono de “papelera”. Solo es posible si la transacción pertenece al dominio del usuario.

---

## 4. Facturación

### 4.1 Facturas
- Listado: `http://sumaxia.local:8000/invoicing/invoices`.
- Crear: botón “Nueva factura”.
- Editar/Ver/Cancelar: acciones disponibles por factura.

### 4.2 Cotizaciones
- Listado: `http://sumaxia.local:8000/invoicing/quotes`.
- Crear: “Nueva cotización”.
- Historial/Versionado: disponibles en el módulo de cotizaciones.
- Convertir a factura: acción “Convertir” desde una cotización.
 - Envío de formularios: el registro de cotizaciones se realiza sin recargar la página y muestra un popup de confirmación.

### 4.3 Facturación electrónica (DIAN)
- Configuración: `Admin > DIAN (FE)` o `http://sumaxia.local:8000/admin/fe/config`.
- Envío: acción “Enviar a DIAN” sobre una factura.
- Estado actual: Por fallas en la plataforma de la DIAN, la implementación completa del flujo de envío no pudo finalizarse. El sistema mantiene la configuración y los puntos de entrada esperados, a la espera de estabilidad en el servicio de la entidad.

---

## 5. Reportes

- Exportar CSV (Contabilidad):
  - Transacciones: botón “Exportar CSV” en `Contabilidad > Transacciones`.
  - Cuentas: “Exportar CSV” en `Contabilidad > Plan de Cuentas`.
- Exportar CSV (Facturación):
  - Facturas: disponible en `Facturación > Facturas`.

---

## 6. Nómina

- Inicio: `http://sumaxia.local:8000/payroll`.
- Empleados: crear desde `Empleados > Crear`.
- Procesar nómina: sección `Procesar`, con confirmación y popup de resultado.
 - Envío de formularios: las acciones de “Crear empleado” y “Procesar nómina” envían sin recargar la página y muestran un popup.

---

## 7. Impuestos

- Módulo básico en `http://sumaxia.local:8000/taxes`.
- Sección en evolución con visualización y futura gestión detallada.

---

## 8. Idioma y sesión

- Cambiar idioma: enlaces en la barra superior o pie de página (`ES`, `US`).
- Cerrar sesión: menú principal o `Logout`.

---

## 9. Seguridad y dominios

- Los datos se filtran por el dominio del correo del usuario (p.ej., `@sumaxia.com`).
- Acciones sensibles requieren autenticación. Algunas funciones de verificación/2FA están disponibles de manera básica.

---

## 10. Resolución de problemas

- Selector de cuenta vacío al crear transacción:
  - Verifica que existan cuentas activas que acepten movimientos en tu dominio.
  - Usa el enlace “Crear una cuenta” del aviso.
- Balance/Resumen no coincide:
  - Confirmar que las cuentas asociadas a las transacciones pertenecen al mismo dominio.
- DIAN no responde o errores al enviar:
  - Ver nota de estado en la sección 4.3: hay fallas en la plataforma de la DIAN que impiden finalizar la implementación.

---

## 11. Glosario rápido

- Cuenta contable: elemento del plan de cuentas para clasificar transacciones.
- Débito/Crédito: naturaleza del movimiento contable.
- Dominio de servicio: separa datos por empresa/servicio según correo del usuario.
- Popup (alerta): avisos de éxito, error o validación que aparecen en pantalla.
 - Envío AJAX: envío de formulario sin recargar la página; verás un spinner breve en el botón y un popup con el resultado.

---

## 12. Soporte

- Si necesitas asistencia, contacta con el administrador de tu dominio o soporte de SumAxia.