
# Bioimplant — Sistema de Gestión y Trazabilidad de Cajas Quirúrgicas

Sistema web para administrar el ciclo de vida completo de cajas de instrumental quirúrgico en un centro médico. Desarrollado en **Laravel 12** con **Bootstrap 5** y **MySQL**.

---

## Arquitectura

**Stack:** PHP 8.4 / Laravel 12 / MySQL 8 + PDO-MySQL / Vite + Bootstrap 5 + Bootstrap Icons

```
Sistema-Cajas-2026/
├── app/
│   ├── Http/
│   │   ├── Controllers/     → 9 controladores (CRUD, procesos)
│   │   ├── Middleware/       → RoleMiddleware (control de acceso por rol)
│   │   └── Requests/        → Validaciones de formularios
│   ├── Models/               → 5 modelos Eloquent
│   └── Services/
│       └── BoxStateService   → Máquina de estados de cajas
├── database/
│   └── migrations/           → 6 migraciones + 2 de Laravel base
├── resources/
│   ├── css/app.css           → Estilos Bootstrap personalizados
│   ├── js/app.js             → Bootstrap JS
│   └── views/                → 9 vistas Blade con layout x2
├── routes/
│   └── web.php               → ~25 rutas agrupadas por middleware
├── public/
│   ├── build/                → Assets compilados por Vite
│   └── storage/              → PDFs e imágenes (link simbólico)
└── storage/app/public/cajas/
    ├── pdfs/                 → Notas de consignación (PDF)
    └── imagenes/             → Fotos de instrumental
```

---

## Base de Datos — 6 Tablas

### `users` (usuarios)
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | PK | |
| name | string | Nombre completo |
| email | string (unique) | Login |
| password | hashed | |
| role | enum('admin','deposito','tecnico','consumo','acondicionador') | Control de acceso |

### `cajas` (cajas de instrumental)
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | PK | |
| nombre | string | Nombre descriptivo (ej: "Caja Traumatología") |
| codigo_interno | string (unique) | Código único (ej: "CX-001") |
| estado | enum | DISPONIBLE / EN ESTERILIZADORA / EN CX / EN TRANSITO / PENDIENTE / ACONDICIONAMIENTO / EN REPARACION / BAJA |
| pdf_path | string?null | Ruta al PDF de nota de consignación |
| created_at / updated_at | timestamps | |

### `cirugias` (cirugías)
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | PK | |
| bioimplant_id | string?null | ID externo del paciente |
| paciente | string | Nombre del paciente |
| medico | string | Nombre del médico |
| fecha_cx | date | Fecha programada |
| start_time | timestamp?null | Inicio real de cirugía |
| end_time | timestamp?null | Fin real de cirugía |
| tecnico_id | FK→users | Técnico asignado |
| access_token | string(32) | Token único para acceso público a vista de cirugía |
| status | enum | PENDIENTE / EN_CURSO / COMPLETADA / CANCELADA |

### `caja_cirugia` (PIVOT — relación muchos a muchos)
| Campo | Tipo |
|-------|------|
| caja_id | FK→cajas |
| cirugia_id | FK→cirugias |

### `evento_cajas` (historial de estados)
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | PK | |
| caja_id | FK→cajas | |
| user_id | FK→users | Quién hizo el cambio |
| estado_anterior | string | Estado antes del cambio |
| estado_nuevo | string | Estado después del cambio |
| observaciones | text?null | Nota del operador |
| fotos | json?null | (reservado para futuro) |

### `caja_imagenes` (imágenes de cajas)
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | PK | |
| caja_id | FK→cajas | |
| ruta | string | Ruta relativa en storage/app/public/ |
| descripcion | string?null | Texto descriptivo |
| created_at | timestamp | Fecha de carga |

### `consumos` (consumo de insumos por cirugía)
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | PK | |
| cirugia_id | FK→cirugias | |
| caja_id | FK→cajas | |
| items | json | Array de insumos consumidos (estructura libre) |
| observaciones | text?null | Notas adicionales |

---

## Máquina de Estados de Cajas

El núcleo del sistema es `BoxStateService` que define transiciones válidas y registra cada cambio en `evento_cajas`.

```
                    ┌─────────────────────────────────────────────────────┐
                    │                                                     │
                    ▼                                                     │
              ┌──────────┐     ┌──────────────────┐     ┌──────────────┐ │
  ┌──────┐    │          │     │                  │     │              │ │
  │      │    │ EN ESTE- │────>│      EN CX       │────>│ EN TRANSITO  │ │
  │      │    │ RILIZAD. │     │                  │     │              │ │
  │      │    └────┬─────┘     └────┬─────────────┘     └──────┬───────┘ │
  │      │         │               │                         │          │
  │ DIS- │         │   ┌───────────┘                         │          │
  │ PONI-│         │   │  (falla)                            │          │
  │ BLE  │         ▼   ▼                                     ▼          │
  │      │    ┌──────────────────┐     ┌──────────────┐     ┌──────────┐│
  │      │    │                  │     │              │     │          ││
  │      │    │  EN REPARACION   │<────│  PENDIENTE   │<────│PENDIENTE ││
  │      │    │                  │     │  (Auditoría) │     │(llegó)   ││
  │      │    └────────┬─────────┘     └──────────────┘     └──────────┘│
  │      │             │                                              │
  │      │             ▼                                              │
  │      │    ┌──────────────────┐     ┌──────────────────┐          │
  │      │    │                  │────>│                  │──────────┘
  │      │    │      BAJA        │     │ ACONDICIONAMIENTO│
  └──────┘    └──────────────────┘     └──────────────────┘
```

**Transiciones válidas por estado:**

| Estado actual | Puede ir a |
|---------------|------------|
| DISPONIBLE | EN ESTERILIZADORA, EN REPARACION, BAJA |
| EN ESTERILIZADORA | EN CX, DISPONIBLE, EN REPARACION |
| EN CX | EN TRANSITO, EN REPARACION |
| EN TRANSITO | PENDIENTE |
| PENDIENTE | ACONDICIONAMIENTO, EN REPARACION |
| ACONDICIONAMIENTO | DISPONIBLE |
| EN REPARACION | DISPONIBLE, BAJA |
| BAJA | (ninguno — estado terminal) |

---

## Roles y Permisos

Middleware `role:admin,rol2,rol3...` en cada grupo de rutas. `RoleMiddleware` verifica que `auth()->user()->role` esté en la lista.

| Rol | Descripción | Acceso |
|-----|-------------|--------|
| **admin** | Super-administrador | TODAS las rutas (dashboard depósito, recepción, lavado, cirugías, admin técnicos, admin cajas) |
| **deposito** | Encargado de depósito | Dashboard depósito, listado cajas, detalle caja, egreso/reparación/baja, admin técnicos (solo lectura en teoría) |
| **tecnico** | Técnico quirúrgico | Dashboard cirugías asignadas |
| **consumo** | Auditor de recepción | Dashboard recepción (control de tránsito y auditoría) |
| **acondicionador** | Encargado de lavado | Dashboard lavado (disponibilizar cajas) |

---

## Rutas

| Método | URL | Controlador@método | Middleware | Nombre |
|--------|-----|-------------------|------------|--------|
| GET | / | vista auth.login | — | — |
| GET | /dashboard | redirect según role | auth, verified | dashboard |
| **DEPÓSITO** (admin + depósito) | | | | |
| GET | /admin, /deposito | DepositoController@index | auth, role:admin,deposito | admin.dashboard / deposito.dashboard |
| GET | /cajas | CajaController@index | auth, role:admin,deposito | cajas.index |
| GET | /cajas/{caja} | CajaController@show | auth, role:admin,deposito | cajas.show |
| POST | /cajas/{caja}/egreso | DepositoController@egreso | auth, role:admin,deposito | cajas.egreso |
| POST | /cajas/{caja}/reparacion | DepositoController@reparacion | auth, role:admin,deposito | cajas.reparacion |
| POST | /cajas/{caja}/baja | DepositoController@baja | auth, role:admin,deposito | cajas.baja |
| POST | /cajas/{caja}/pdf | CajaController@uploadPdf | auth, role:admin,deposito | cajas.pdf |
| DELETE | /cajas/{caja}/pdf | CajaController@deletePdf | auth, role:admin,deposito | cajas.deletePdf |
| POST | /cajas/{caja}/imagen | CajaController@uploadImagen | auth, role:admin,deposito | cajas.imagen |
| DELETE | /cajas/imagen/{imagen} | CajaController@deleteImagen | auth, role:admin,deposito | cajas.deleteImagen |
| GET | /admin/tecnicos | AdminTecnicoController@index | auth, role:admin,deposito | admin.tecnicos |
| POST | /admin/tecnicos | AdminTecnicoController@store | auth, role:admin,deposito | admin.tecnicos.store |
| PUT | /admin/tecnicos/{tecnico} | AdminTecnicoController@update | auth, role:admin,deposito | admin.tecnicos.update |
| DELETE | /admin/tecnicos/{tecnico} | AdminTecnicoController@destroy | auth, role:admin,deposito | admin.tecnicos.destroy |
| **ADMIN CAJAS** (solo admin) | | | | |
| GET | /admin/cajas | AdminCajaController@index | auth, role:admin | admin.cajas.index |
| POST | /admin/cajas | AdminCajaController@store | auth, role:admin | admin.cajas.store |
| PUT | /admin/cajas/{caja} | AdminCajaController@update | auth, role:admin | admin.cajas.update |
| DELETE | /admin/cajas/{caja} | AdminCajaController@destroy | auth, role:admin | admin.cajas.destroy |
| POST | /admin/cajas/{caja}/pdf | AdminCajaController@uploadPdf | auth, role:admin | admin.cajas.pdf |
| DELETE | /admin/cajas/{caja}/pdf | AdminCajaController@deletePdf | auth, role:admin | admin.cajas.deletePdf |
| POST | /admin/cajas/{caja}/imagen | AdminCajaController@uploadImagen | auth, role:admin | admin.cajas.imagen |
| DELETE | /admin/cajas/imagen/{imagen} | AdminCajaController@deleteImagen | auth, role:admin | admin.cajas.deleteImagen |
| **TÉCNICO** | | | | |
| GET | /tecnico | TecnicoController@index | auth, role:admin,tecnico | tecnico.dashboard |
| GET | /cx/{cirugia}/{token} | TecnicoController@viewSurgery | — (token público) | tecnico.surgery.view |
| POST | /cx/{cirugia}/llegado | TecnicoController@llegado | — (token público) | tecnico.surgery.llegado |
| POST | /cx/{cirugia}/finalizar | TecnicoController@finalizar | — (token público) | tecnico.surgery.finalizar |
| **RECEPCIÓN** (admin + consumo) | | | | |
| GET | /consumo | ConsumoController@index | auth, role:admin,consumo | consumo.dashboard |
| POST | /consumo/{caja}/controlar | ConsumoController@controlar | auth, role:admin,consumo | consumo.controlar |
| POST | /consumo/{caja}/finalizar | ConsumoController@finalizar | auth, role:admin,consumo | consumo.finalizar |
| **LAVADO** (admin + acondicionador) | | | | |
| GET | /acondicionamiento | AcondicionadorController@index | auth, role:admin,acondicionador | acondicionador.dashboard |
| POST | /acondicionamiento/{caja}/disponibilizar | AcondicionadorController@disponibilizar | auth, role:admin,acondicionador | acondicionador.disponibilizar |
| **PERFIL** (cualquier auth) | | | | |
| GET | /profile | ProfileController@edit | auth | profile.edit |
| PATCH | /profile | ProfileController@update | auth | profile.update |
| DELETE | /profile | ProfileController@destroy | auth | profile.destroy |

---

## Vistas (Blade)

| Ruta de vista | Layout | Propósito |
|---------------|--------|-----------|
| `layouts/app.blade.php` | — | Layout principal: navbar (logo 200×150, dropdowns por rol), page-header, alerts, footer |
| `layouts/guest.blade.php` | — | Layout público (login card) |
| `auth/login.blade.php` | guest | Login con logo y gradiente |
| `deposito/dashboard.blade.php` | app | Dashboard depósito: 8 stat cards (disponibles, en CX, etc.), tabla filtrable de cajas, modal egreso/asignar/reparar/baja |
| `consumo/dashboard.blade.php` | app | Dashboard recepción: columna "En Tránsito" con botón Recibir, columna "Auditoría" con select ok/falla |
| `tecnico/dashboard.blade.php` | app | Dashboard técnico: tabla de cirugías asignadas con estado y enlace |
| `tecnico/surgery_view.blade.php` | app | Vista de cirugía en curso: detalle, boxes con PDF, imágenes con zoom modal, botón Llegado/Finalizar |
| `acondicionador/dashboard.blade.php` | app | Dashboard lavado: lista de cajas en ACONDICIONAMIENTO con botón Disponibilizar |
| `admin/tecnicos.blade.php` | app | CRUD técnicos: stats, lista con buscar en vivo, modales crear/editar |
| `admin/cajas.blade.php` | app | CRUD cajas: stats, tabla con estados y PDF, modal crear |
| `cajas/index.blade.php` | app | Listado completo de cajas con filtros |
| `cajas/show.blade.php` | app | Detalle de caja: historial de eventos, galería de imágenes con delete, PDF upload/view/delete |
| `profile/edit.blade.php` | app | Perfil de usuario: información, cambio contraseña, zona de peligro |

---

## Flujo de Trabajo Completo (Ciclo de Vida de una Caja)

### 1. Creación de caja
**Rol:** admin → `/admin/cajas` (AdminCajaController@store)\
Se crea una caja con nombre y código interno. Estado inicial: `DISPONIBLE`.

### 2. Asignación a cirugía (Egreso)
**Rol:** depósito o admin → `/deposito` (modal "Asignar")\
Se selecciona técnico, paciente, médico. Opcionalmente se adjunta PDF (nota de consignación) y una o más imágenes.\
**Transición:** DISPONIBLE → EN ESTERILIZADORA\
Se crea automáticamente una `Cirugia` con status `PENDIENTE` y se asocia la caja vía tabla pivote.

### 3. Inicio de cirugía
**Rol:** técnico → `/tecnico` (link a cirugía) o por token público `/cx/{id}/{token}`\
El técnico confirma que las cajas llegaron en condiciones y marca inicio.\
**Transición caja:** EN ESTERILIZADORA → EN CX\
**Transición cirugía:** PENDIENTE → EN_CURSO (se registra start_time)

### 4. Finalización de cirugía
**Rol:** técnico\
El técnico registra consumos de insumos por caja y finaliza.\
**Transición caja:** EN CX → EN TRANSITO\
**Transición cirugía:** EN_CURSO → COMPLETADA (se registra end_time)\
Se guarda registro en tabla `consumos` con items (JSON) y observaciones.

### 5. Recepción y control (Consumo / Auditoría)
**Rol:** consumo o admin → `/consumo`
- **Paso 5a (Recibir):** La caja llega al área de consumo. Botón "Recibir" → caja pasa a `PENDIENTE`.
- **Paso 5b (Auditar):** Se evalúa la caja:
  - **OK:** Caja íntegra → `ACONDICIONAMIENTO` (pasa a lavado)
  - **FALLA:** Se detectan problemas → `EN REPARACION`

### 6. Lavado y acondicionamiento
**Rol:** acondicionador o admin → `/acondicionamiento`\
Se limpia, desinfecta y seca el instrumental.\
Botón "Disponibilizar" → caja vuelve a `DISPONIBLE`, lista para nuevo ciclo.

### 7. Reparación
**Rol:** depósito o admin → `/deposito` (modal "Enviar a Reparación")\
Si una caja falla en cualquier etapa, se envía a `EN REPARACION`.\
Desde reparación puede volver a `DISPONIBLE` o darse de `BAJA`.

### 8. Baja
**Rol:** depósito o admin → `/deposito` (modal "Dar de Baja")\
Estado terminal. La caja ya no puede reingresar al ciclo.

---

## Gestión de Archivos

**Almacenamiento:** Disco `public` de Laravel (`storage/app/public/cajas/`)

| Tipo | Ruta | Validación | Tamaño máx |
|------|------|-----------|------------|
| PDF (Nota de Consignación) | `cajas/pdfs/` | mimes:pdf | 10 MB |
| Imágenes | `cajas/imagenes/` | jpg, jpeg, png, webp | 5 MB c/u |

**Acceso en vistas:** `asset('storage/' . $imagen->ruta)` (no `Storage::url()` para evitar importar facade en Blade).

**Vinculación:** Debe existir link simbólico: `php artisan storage:link`

**Eliminación:** Al borrar imagen/PDF, se elimina el archivo físico con `Storage::disk('public')->delete()`.

---

## Vistas — Estructura y Estilo

### Layouts
- **`app.blade.php`**: NavBar con logo a la izquierda (200×150px), dropdowns por rol, menú usuario (avatar con inicial, dropdown perfil/cerrar sesión). Footer con logo gris y copyright.
- **`guest.blade.php`**: Fondo gradiente navy con glow decorativo, card login centrada.

### Componentes de diseño (CSS personalizado en `resources/css/app.css`)
- **`.navbar-bio`**: Fondo gradiente navy (#1e3a5f → #0f172a), altura 70px, nav-links con hover semitransparente, dropdowns con sombra.
- **`.stat-card`**: Tarjeta con padding 2rem, sombra, hover lift (translateY -5px), icono colorido arriba, número grande (2.5rem) + label uppercase.
- **`.card-elegante`**: Card con backdrop-filter blur, header gradiente gris claro, border-bottom.
- **`.btn-bio`**: Botón con border-radius 12px, hover lift + sombra, variantes sm/lg.
- **`.badge-estado`**: Badge redondeado (50rem), texto uppercase pequeño.
- **`.form-control-bio`**: Input con border 2px, focus ring primary.
- **`.table-bio`**: Tabla con headers uppercase small, filas hover.

### Paleta de colores
- Navy: `#0f172a` / `#1e3a5f`
- Primary: `#3b82f6` → `#2563eb`
- Success: `#10b981` → `#059669`
- Danger: `#ef4444` → `#dc2626`
- Warning: `#f59e0b`
- Info: `#06b6d4`

**Responsive deshabilitado:** Las columnas Bootstrap se comportan como desktop siempre (media query que fuerza `min-width: 100%` en mobile).

---

## Instalación y Configuración

```bash
# Requisitos: PHP 8.4+, MySQL 8+, Composer, Node 20+
git clone <repo>
cd Sistema-Cajas-2026

# Backend
composer install
cp .env.example .env   # Configurar DB_CONNECTION=mysql, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
php artisan key:generate
php artisan migrate

# Frontend
npm install
npm run build

# Storage link
php artisan storage:link

# Iniciar servidor
php artisan serve
# Visitar http://localhost:8000
```

### Seeders recomendados (crear primer admin)
```php
php artisan tinker
>>> \App\Models\User::create(['name'=>'Admin','email'=>'admin@bioimplant.com','password'=>bcrypt('admin123'),'role'=>'admin']);
```

---

## Convenciones de Código

- **Sin comentarios** en código fuente (excepto migraciones).
- **Blade:** Variables con `$camelCase`, helpers como `asset()`.
- **Rutas:** Nombres con `recurso.accion` (ej: `cajas.egreso`, `consumo.controlar`).
- **Tablas:** Nombres en español, plural (`caja_imagenes`, `evento_cajas`). Modelo `CajaImagen` requiere `protected $table = 'caja_imagenes'` explícito.
- **Controladores:** Un controller por área funcional. Métodos retornan `back()` con mensaje `success`/`error`/`warning`.
- **Estado de cajas:** Constantes en español mayúsculas (ej: `'DISPONIBLE'`, `'EN ESTERILIZADORA'`).
- **Vite:** CSS y JS compilados con Vite, referenciados con `@vite()`.
