# Bioimplant — Sistema de Gestión y Trazabilidad de Cajas Quirúrgicas

Sistema web para administrar el ciclo de vida completo de cajas de instrumental quirúrgico consignado: desde que la caja se consigna (con su remito en PDF), se asigna a una cirugía (CX), se esteriliza, se usa, se retira, se audita y se acondiciona, hasta que vuelve a estar disponible. Cada cambio de estado queda registrado en un historial auditable.

Desarrollado con **Laravel 12**, **Bootstrap 5** y **MySQL**. Expone además una **API REST (Sanctum)** para integrarse con el módulo externo de Consignaciones.

---

## Arquitectura

**Stack:** PHP 8.4 (mínimo 8.2) / Laravel 12 / MySQL 8 / Laravel Sanctum / smalot/pdfparser / Vite + Bootstrap 5 + Bootstrap Icons

```
Sistema-Cajas-2026/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── (web)  Deposito, Caja, AdminCaja, AdminTecnico, Grupo, Consignacion,
│   │   │   │          Tecnico, Consumo, Logistica, Acondicionador, Profile
│   │   │   ├── Api/   Auth, Caja, Cirugia, Consignacion, Consumo, Evento, Grupo
│   │   │   └── Auth/  Controladores de autenticación (Breeze)
│   │   ├── Middleware/RoleMiddleware   → control de acceso por rol
│   │   ├── Requests/                   → Validaciones (Api/, Auth/, Profile)
│   │   └── Resources/                  → Transformadores JSON de la API
│   ├── Models/                         → 10 modelos Eloquent
│   └── Services/
│       ├── BoxStateService                  → Máquina de estados + historial
│       ├── ConsignacionesPollService        → Polling del módulo externo de Consignaciones
│       ├── InstrumentalPdfExtractorService  → Extrae instrumental desde el PDF del remito
│       └── ProcedureApiService              → Búsqueda de procedimientos en API externa
├── database/migrations/                → 25 migraciones
├── resources/views/                    → Vistas Blade (ver sección Vistas)
├── routes/
│   ├── web.php                         → Rutas web (sesión + roles)
│   ├── api.php                         → API REST (/api, Sanctum + abilities)
│   └── console.php                     → Scheduler (poll de consignaciones cada 2 min)
├── docs/
│   ├── API_CONSIGNACIONES.md           → Documentación detallada de la API
│   ├── apache/ y nginx/                → Configuración del reverse proxy HTTPS
└── .github/workflows/                  → CI/CD (deploy.yml, laravel.yml)
```

---

## Base de Datos

### `users`
| Campo | Descripción |
|-------|-------------|
| name, email (unique), password | Datos de acceso |
| role | enum: `admin`, `deposito`, `tecnico`, `consumo`, `acondicionador`, `logistica` (default `deposito`) |

### `cajas`
| Campo | Descripción |
|-------|-------------|
| nombre | Nombre descriptivo |
| codigo_interno (unique) | Código único. El poll de consignaciones busca `CAJA-{NcoCac}` |
| estado | enum de 11 estados (ver Máquina de estados). Default `DISPONIBLE` |
| consignatario_nombre, fecha_consignacion | Datos de la consignación vigente |
| pdf_path | PDF del remito (storage público) |
| imagen_salida_path | Imagen de salida (usada por la API) |

### `caja_consignaciones` y `caja_consignacion_items`
Historial de consignaciones de cada caja y el instrumental detallado de cada una.

| Tabla | Campos |
|-------|--------|
| caja_consignaciones | caja_id, user_id, consignatario_nombre, fecha_consignacion, observaciones, pdf_path |
| caja_consignacion_items | caja_consignacion_id, seccion, cantidad, descripcion, incluido (bool) |

### `cirugias`
| Campo | Descripción |
|-------|-------------|
| plc_cod | Código de planilla de CX (PlcCod) del sistema externo |
| external_nco_cod | Código de nota de consignación externa (NcoCod) |
| external_implantes | JSON con implantes informados por el sistema externo |
| paciente, medico, fecha_cx | Datos de la CX |
| observaciones | Texto libre |
| start_time, end_time | Inicio y fin reales |
| tecnico_id | FK → users (nullable) |
| tecnico_nombre | Nombre de técnico **externo** (cuando no es usuario del sistema) |
| tecnico_original_id | Técnico original si fue reasignado |
| access_token | Token único (32 chars) para el acceso público a la vista de CX |
| status | `PENDIENTE`, `EN_CURSO`, `COMPLETADA`, `CANCELADA`, `POSTPUESTA` |

### `caja_cirugia` (pivote)
`caja_id`, `cirugia_id` — relación muchos a muchos.

### `evento_cajas` (historial de estados)
`caja_id`, `user_id` (nullable), `responsable_nombre` (cuando actúa una persona externa sin usuario), `estado_anterior`, `estado_nuevo`, `observaciones`, `fotos` (JSON, reservado).

### `caja_imagenes`
`caja_id`, `ruta`, `descripcion`. Modelo `CajaImagen` con `$table = 'caja_imagenes'`.

### `consumos`
`cirugia_id`, `caja_id`, `items` (JSON), `observaciones`. Se crea al finalizar la CX; también lo puede crear el poll de consignaciones con los implantes externos.

### `grupos` y `grupo_caja`
Agrupación de cajas (p. ej. un set completo para un tipo de cirugía). `grupo_caja` tiene unique (`grupo_id`, `caja_id`).

### `tokens_accion`
Links públicos de un solo uso (24 h) para que una persona externa ejecute una acción de recepción. Campos: `token`, `accion`, `caja_id`, `responsable_nombre`, `params` (JSON), `expires_at`, `used_at`.

### Tablas de Laravel
`personal_access_tokens` (Sanctum), `sessions`, `cache`, `jobs`, etc.

---

## Máquina de Estados de Cajas

`BoxStateService::canTransition()` define las transiciones válidas y `BoxStateService::transition()` aplica el cambio y registra el evento en `evento_cajas`, todo dentro de una transacción.

```
DISPONIBLE ──► CONSIGNADA ──► PENDIENTE_DESPACHO ──► EN ESTERILIZADORA ──► EN CX
 (Sin Consignar)  (remito)      (vinculada a CX)                              │
     ▲                                                                        ▼
     │                                                                  CX FINALIZADA
     │                                                                        │
     │                                                                        ▼
 ACONDICIONAMIENTO ◄── PENDIENTE ◄───────────── EN TRANSITO VUELTA ◄──────────┘
     │            (auditoría)    (recibida)       (retirada por logística)
     └──► DISPONIBLE

 Desde varios estados: ──► EN REPARACION ──► DISPONIBLE | BAJA
```

| Estado actual | Puede ir a |
|---------------|------------|
| DISPONIBLE (Sin Consignar) | CONSIGNADA, EN REPARACION, BAJA |
| CONSIGNADA | PENDIENTE_DESPACHO, DISPONIBLE, EN REPARACION, BAJA |
| PENDIENTE_DESPACHO | EN ESTERILIZADORA, DISPONIBLE, EN REPARACION |
| EN ESTERILIZADORA | EN CX, DISPONIBLE, EN REPARACION |
| EN CX | CX FINALIZADA, EN REPARACION, DISPONIBLE |
| CX FINALIZADA | EN TRANSITO VUELTA, EN REPARACION, DISPONIBLE |
| EN TRANSITO VUELTA | PENDIENTE |
| PENDIENTE | ACONDICIONAMIENTO, EN REPARACION |
| ACONDICIONAMIENTO | DISPONIBLE |
| EN REPARACION | DISPONIBLE, BAJA |
| BAJA | (terminal) |

Etiquetas en la UI (`Caja::estadoLabels()`): `DISPONIBLE` se muestra como **"Sin Consignar"** y `PENDIENTE_DESPACHO` como "Pend. Despacho".

**Regla de negocio:** solo las cajas `CONSIGNADA` pueden vincularse a una CX.

---

## Roles y Permisos

Se controla con el middleware `role:rol1,rol2,...` (`RoleMiddleware`).

| Rol | Descripción | Acceso web |
|-----|-------------|------------|
| **admin** | Super-administrador | Todas las áreas, incluido ABM de cajas |
| **deposito** | Encargado de depósito | Inventario, consignaciones, grupos, técnicos, egresos, reparación/baja, CX |
| **tecnico** | Técnico quirúrgico | Sus cirugías asignadas (`/tecnico`) |
| **consumo** | Auditor de recepción | Recepción y auditoría (`/consumo`) |
| **logistica** | Retiro de cajas | Retiro desde el servicio (`/logistica`) |
| **acondicionador** | Lavado | Acondicionamiento (`/acondicionamiento`) |

> Nota: el menú muestra "Recepción" a `deposito`, pero las rutas `/consumo*` solo permiten `admin` y `consumo`.

---

## Rutas Web

### Depósito (`admin`, `deposito`)
| Método | URL | Acción |
|--------|-----|--------|
| GET | `/admin`, `/deposito` | Dashboard de inventario |
| GET | `/cajas`, `/cajas/{caja}` | Listado y detalle (eventos, imágenes, PDF, consignaciones, grupos) |
| POST | `/cajas/{caja}/egreso` | Asignar caja a una CX nueva |
| POST | `/cajas/{caja}/vincular-cx` | Vincular caja CONSIGNADA a una CX existente → PENDIENTE_DESPACHO |
| POST | `/cajas/{caja}/a-esterilizadora` | PENDIENTE_DESPACHO → EN ESTERILIZADORA |
| POST | `/cajas/{caja}/reparacion`, `/baja`, `/disponibilizar` | Reparación, baja y vuelta a Disponible |
| POST | `/cajas/{caja}/delegar-recepcion` | Genera link externo de recepción (24 h) |
| POST/DELETE | `/cajas/{caja}/pdf`, `/cajas/{caja}/imagen`, `/cajas/imagen/{imagen}` | Archivos de la caja |
| GET | `/consignaciones` | Cajas Sin Consignar / Consignadas |
| POST | `/cajas/{caja}/consignar/extraer` | Paso 1: sube PDF y extrae instrumental |
| POST | `/cajas/{caja}/consignar` | Paso 2: confirma y pasa a CONSIGNADA |
| POST | `/cajas/{caja}/devolver` | CONSIGNADA → DISPONIBLE |
| GET | `/cajas/{caja}/consignaciones` | Historial JSON de consignaciones |
| POST | `/cirugias/{cirugia}/reasignar` | Reasignar técnico |
| POST | `/cirugias/{cirugia}/cancelar` | Cancelar o postergar CX (devuelve cajas a DISPONIBLE) |
| GET | `/procedimientos/buscar` | Búsqueda en API externa de procedimientos (JSON) |
| GET/POST/PUT/DELETE | `/admin/tecnicos` | ABM de técnicos |
| GET/POST/PUT/DELETE | `/admin/grupos` | ABM de grupos |
| POST | `/grupos/{grupo}/egreso` | Egreso grupal a una CX |

### Solo `admin`
ABM de cajas (`/admin/cajas`), carga/borrado de PDF e imágenes (`/admin/cajas/...`).

### Técnico
| Método | URL | Acceso |
|--------|-----|--------|
| GET | `/tecnico` | `admin`, `tecnico` |
| GET | `/cx/{cirugia}/{token}` | Público (valida token) |
| POST | `/cx/{cirugia}/llegado` | Público |
| POST | `/cx/{cirugia}/finalizar` | Público |

### Recepción, Logística, Acondicionamiento
| Método | URL | Acceso |
|--------|-----|--------|
| GET | `/consumo` | `admin`, `consumo` |
| POST | `/consumo/{caja}/controlar`, `/consumo/{caja}/finalizar` | `admin`, `consumo` |
| GET/POST | `/recepcion/{token}`, `/recepcion/{token}/confirmar` | Público (token de un solo uso, 24 h) |
| GET | `/logistica` | `admin`, `logistica` |
| POST | `/logistica/{caja}/retirar` | `admin`, `logistica` |
| GET | `/acondicionamiento` | `admin`, `acondicionador` |
| POST | `/acondicionamiento/{caja}/disponibilizar` | `admin`, `acondicionador` |

### Perfil y autenticación
`/profile` (cualquier usuario autenticado) y las rutas de Breeze (`login`, `register`, recuperar contraseña, etc.).

---

## API REST (Sanctum)

Prefijo `/api`. Requiere header `Accept: application/json` y `Authorization: Bearer <token>`. Documentación completa en [docs/API_CONSIGNACIONES.md](docs/API_CONSIGNACIONES.md).

- `POST /api/login` (público) emite un token con *abilities* según el rol del usuario; también `GET /api/me`, `POST /api/logout`, `GET/DELETE /api/tokens`.
- **Recursos** (cada grupo exige su ability `*:read` o `*:write`): `cajas` (incluye `PATCH /cajas/{caja}/estado`), `consignaciones` (listar, stats, crear, devolver), `cirugias` (incluye `POST /cirugias/vincular-cx` y asociar/desasociar cajas), `consumos`, `grupos` (con cajas) y `eventos` (solo lectura).
- Abilities por rol (ver `AuthController::abilitiesForRole()`):

| Rol | Escritura | Solo lectura |
|-----|-----------|--------------|
| admin | todo (`*`) | — |
| deposito | cajas, cirugías, grupos, consignaciones | consumos, eventos |
| tecnico | cirugías, consumos | cajas, eventos, consignaciones |
| consumo | consumos | cajas, cirugías, eventos, consignaciones |
| logistica | — | cajas, cirugías, eventos, consignaciones |
| acondicionador | cajas | cirugías, eventos |

---

## Integración con el módulo de Consignaciones

1. **Push (API):** el módulo externo consigna cajas y crea/vincula cirugías llamando a `/api/consignaciones`, `/api/cirugias/vincular-cx`, etc.
2. **Poll (scheduler):** `routes/console.php` ejecuta cada 2 minutos `ConsignacionesPollService::poll()`, que consulta `CONSIGNACIONES_POLL_URL` con el parámetro `desde`. Por cada nota con `NcoPlcCod`, busca la caja `CAJA-{NcoCac}` en estado `CONSIGNADA`, crea la `Cirugia` (PENDIENTE), la vincula, registra el `Consumo` con los implantes y pasa la caja a `PENDIENTE_DESPACHO`. **Está deshabilitado por defecto** (`CONSIGNACIONES_POLL_ENABLED=false`) y requiere ejecutar `php artisan schedule:work` (o un cron con `schedule:run`).
3. **Procedimientos externos:** `ProcedureApiService` consulta `API_PROCEDURE_URL` con `API_PROCEDURE_TOKEN` (planilla de CX) para autocompletar el egreso.

---

## Flujo de Trabajo (Ciclo de Vida de una Caja)

### 1. Alta de caja
**admin** → `/admin/cajas`. Se crea con nombre y código interno, estado `DISPONIBLE` (mostrado como "Sin Consignar").

### 2. Consignación
**deposito/admin** → `/consignaciones`. Se indica consignatario y fecha y se adjunta el PDF del remito. `InstrumentalPdfExtractorService` lee el PDF (con `smalot/pdfparser`), detecta ítems "cantidad + descripción" agrupados por sección y muestra una grilla editable. Al confirmar, la caja pasa a `CONSIGNADA` y se guarda la consignación con sus ítems. Se puede revertir con "Devolver" (→ `DISPONIBLE`).

### 3. Vinculación a una cirugía
- **Egreso manual** (`/deposito`): se indica paciente, médico, PlcCod, técnico (usuario interno o nombre externo) y opcionalmente PDF e imágenes. La caja pasa por `PENDIENTE_DESPACHO` y luego a `EN ESTERILIZADORA`. Si el técnico es externo, se muestra un link público a la CX.
- **Vincular a CX existente**: `CONSIGNADA` → `PENDIENTE_DESPACHO` sin avanzar.
- **Egreso grupal**: lo mismo para todas las cajas de un grupo (las no consignadas se omiten e informan).
- **Automático**: vía API o poll del módulo de Consignaciones.
- La CX puede **reasignarse** a otro técnico, **cancelarse** o **postergarse** (las cajas vuelven a `DISPONIBLE`).

### 4. Cirugía
**tecnico** (`/tecnico` o link público `/cx/{id}/{token}`):
- **Llegado:** cajas → `EN CX`; cirugía `PENDIENTE` → `EN_CURSO` (guarda `start_time`).
- **Finalizar:** cajas → `CX FINALIZADA`; se crea un `Consumo` por caja; cirugía → `COMPLETADA` (guarda `end_time`).

### 5. Retiro
**logistica** → `/logistica`. Retira las cajas `CX FINALIZADA` del hospital/sanatorio → `EN TRANSITO VUELTA`.

### 6. Recepción y auditoría
**consumo/admin** → `/consumo` (o link externo delegado):
- **Recibir:** `EN TRANSITO VUELTA` → `PENDIENTE`.
- **Auditar:** OK → `ACONDICIONAMIENTO`; falla → `EN REPARACION`.

### 7. Acondicionamiento
**acondicionador/admin** → `/acondicionamiento`. "Disponibilizar" → `DISPONIBLE`.

### 8. Reparación y baja
**deposito/admin** → modales de "Enviar a Reparación" y "Dar de Baja". Desde reparación se vuelve a `DISPONIBLE` o se da de `BAJA` (terminal).

---

## Vistas (Blade)

| Vista | Propósito |
|-------|-----------|
| `layouts/app`, `guest`, `navigation` | Layout principal (navbar por rol), layout público de login |
| `auth/*` | Login, registro, recuperación de contraseña (Breeze) |
| `deposito/dashboard` | Inventario: estadísticas por estado, tabla filtrable/paginada, modales de egreso, vincular CX, grupos, reparación, baja, delegar recepción |
| `consignaciones/index`, `confirmar` | Listado Sin Consignar/Consignadas y grilla editable del instrumental extraído del PDF |
| `cajas/index`, `show` | Listado y detalle (eventos, imágenes, PDF, consignaciones) |
| `admin/cajas`, `tecnicos`, `grupos` | ABM de cajas, técnicos y grupos |
| `tecnico/dashboard`, `surgery_view` | CX asignadas y vista de CX (llegado/finalizar) |
| `consumo/dashboard` | Recepción y auditoría |
| `logistica/dashboard` | Retiro de cajas |
| `acondicionador/dashboard` | Lavado y disponibilización |
| `recepcion/token`, `hecho` | Confirmación externa por token |
| `profile/edit` | Perfil del usuario |

El estilo propio (navbar `.navbar-bio`, `.stat-card`, `.card-elegante`, `.btn-bio`, `.badge-estado`, `.table-bio`) está en `resources/css/app.css`.

---

## Gestión de Archivos

Disco `public` (`storage/app/public/cajas/`). Requiere `php artisan storage:link`.

| Tipo | Ruta | Validación |
|------|------|-----------|
| PDF (remito / nota de consignación) | `cajas/pdfs/` | `mimes:pdf`, 10 MB |
| Imágenes | `cajas/imagenes/` | jpg, jpeg, png, webp, 5 MB c/u |

En vistas se usa `asset('storage/' . $ruta)`. Al borrar una imagen o PDF se elimina también el archivo físico.

---

## Instalación y Configuración

```bash
# Requisitos: PHP 8.2+ (8.4 recomendado), MySQL 8+, Composer, Node 20+
git clone <repo>
cd Sistema-Cajas-2026

composer install
cp .env.example .env     # Configurar DB_CONNECTION=mysql y credenciales
php artisan key:generate
php artisan migrate

npm install
npm run build

php artisan storage:link
php artisan serve        # http://localhost:8000
```

`composer run dev` levanta servidor, cola, logs y Vite a la vez.

### Variables de entorno relevantes
| Variable | Uso |
|----------|-----|
| `DB_*` | Conexión MySQL (el `.env.example` trae SQLite por defecto) |
| `API_PROCEDURE_URL`, `API_PROCEDURE_TOKEN` | API externa de procedimientos |
| `CONSIGNACIONES_POLL_ENABLED`, `CONSIGNACIONES_POLL_URL` | Poll del módulo de Consignaciones |
| `QUEUE_CONNECTION`, `SESSION_DRIVER`, `CACHE_STORE` | `database` por defecto |

### Primer usuario administrador
```php
php artisan tinker
>>> \App\Models\User::create(['name'=>'Admin','email'=>'admin@ejemplo.com','password'=>'cambiar-esta-clave','role'=>'admin']);
```
(El modelo castea `password` a `hashed`, no hace falta `bcrypt()`.)

### Poll de consignaciones (opcional)
Poner `CONSIGNACIONES_POLL_ENABLED=true` y mantener corriendo `php artisan schedule:work` (o un cron `* * * * * php artisan schedule:run`).

---

## Despliegue

- `.github/workflows/laravel.yml` — al hacer push a `master`, entra por SSH al servidor Ubuntu, hace `git pull`, instala dependencias, compila assets, migra, cachea config/rutas/vistas, configura Apache y reinicia `php artisan serve` en una sesión `tmux`.
- `.github/workflows/deploy.yml` — pipeline CI/CD alternativo que se dispara con push a `main`.
- **Apache** (`docs/apache/`) termina HTTPS en 443 y hace reverse proxy a `artisan serve` en el puerto 8000. Hay una alternativa en `docs/nginx/`.
- `bootstrap/app.php` confía en todos los proxies (`trustProxies(at: '*')`).

> ⚠️ Los secretos (contraseña de base de datos, token de la API de procedimientos) **no deben estar escritos en los workflows**. Usar GitHub Secrets / `.env` del servidor.

---

## Convenciones de Código

- Tablas en español y plural (`caja_imagenes`, `evento_cajas`, `caja_consignaciones`); los modelos con plural irregular definen `$table` explícito.
- Rutas con nombre `recurso.accion` (`cajas.egreso`, `consumo.controlar`).
- Controladores por área funcional; devuelven `back()` con mensaje `success` / `error` / `warning`.
- Estados de cajas en mayúsculas, con constantes de texto (`'DISPONIBLE'`, `'EN ESTERILIZADORA'`, etc.). Todo cambio de estado debe pasar por `BoxStateService::transition()` para quedar en el historial.
- Assets con Vite (`@vite()`); CSS y JS en `resources/`.

---

## Puntos pendientes / a tener en cuenta

- Las rutas públicas `/cx/{cirugia}/llegado` y `/finalizar` no validan el token de acceso (solo `viewSurgery` lo hace).
- Varios controladores (`Acondicionador`, `Consumo`, `Tecnico`) no comprueban `canTransition()` antes de transicionar (`Tecnico` solo registra un warning).
- Las rutas de registro de Breeze están habilitadas para invitados.
- La cobertura de tests es mínima (solo pruebas de Breeze y de ejemplo).
- Los workflows `laravel.yml` (`master`) y `deploy.yml` (`main`) son redundantes.
