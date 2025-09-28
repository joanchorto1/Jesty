# Jesty SaaS

Jesty es una aplicación de facturación basada en el modelo SaaS (Software as a Service). Permite gestionar presupuestos, facturas, productos y clientes de manera eficiente y sencilla. La aplicación está diseñada para ser utilizada por empresas para gestionar sus presupuestos, crear facturas, y llevar un control de sus operaciones de ventas.

## Tecnologías utilizadas

- **Frontend:**
    - Vue 3
    - Inertia.js
    - Tailwind CSS
    - Chart.js (para gráficos dinámicos)

- **Backend:**
    - Laravel
    - Eloquent ORM
    - Laravel Sanctum (para autenticación)

- **Base de datos:**
    - MySQL (o el sistema de base de datos preferido en tu entorno)

## Funcionalidades principales

1. **Gestión de presupuestos y facturas:**
    - Crear, editar y eliminar presupuestos y facturas.
    - Visualizar el detalle de presupuestos y facturas, incluyendo los ítems asociados.
    - Crear facturas a partir de presupuestos existentes.
    - Imprimir los presupuestos y las facturas.

2. **Gestión de productos:**
    - Añadir, editar y eliminar productos.
    - Asignar productos a presupuestos y facturas con cálculos automáticos de total.

3. **Clientes:**
    - Crear y administrar los clientes asociados a presupuestos y facturas.

4. **Dashboard:**
    - Visualizar gráficos dinámicos sobre el estado de las facturas y presupuestos.
    - Información clave en el panel de control para una gestión rápida y eficiente.

5. **Autenticación y Roles:**
    - Autenticación de usuarios con Laravel Sanctum.
    - Roles de usuario (administrador) para gestionar las empresas, usuarios, y datos del sistema.

## Instalación

### Requisitos previos

- PHP >= 8.0
- Composer
- Node.js (preferiblemente la versión LTS)
- MySQL o cualquier base de datos relacional compatible con Laravel

### Paso 1: Clonar el repositorio

```bash
git clone https://github.com/joanchorto1/Jesty_Saas.git
cd Jesty_Saas
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run dev
php artisan serve

```

## Estructura del proyecto

### Backend (Laravel)

- **app/**: Contiene la lógica de la aplicación, controladores, modelos y servicios.
- **routes/**: Las rutas del proyecto, definidas principalmente en `web.php` y `api.php`.
- **database/migrations/**: Contiene los archivos de migración para la base de datos.
- **resources/views/**: Las vistas generadas por Inertia.js, incluyendo los componentes Vue.
- **app/Http/Controllers**: Controladores principales como `BudgetController`, `InvoiceController`, etc.

### Frontend (Vue 3)

- **resources/js/**: Los archivos principales de Vue, que incluyen la lógica para cada componente y vista.
- **resources/js/Pages/**: Las vistas basadas en componentes de Vue que se renderizan usando Inertia.js.
- **resources/js/Components/**: Componentes reutilizables en diferentes partes de la aplicación.
- **resources/js/Layouts/**: El layout principal de la aplicación, que incluye el menú de navegación, el dashboard y otros componentes comunes.

## Módulo de RRHH deshabilitado

El módulo de recursos humanos (RRHH) està deshabilitat per defecte. Aquesta decisió garanteix que la navegació i les rutes públiques no exposin funcionalitats relacionades amb RRHH mentre roman inactiu.

### Com reactivar-lo

1. Habilita la característica al fitxer d'entorn establint `FEATURE_RRHH_ENABLED=true`.
2. Marca la característica com a activa a la base de dades executant un script d'actualització, per exemple:

   ```bash
   php artisan tinker
   >>> \App\Models\Feature::updateOrCreate(
   ...     ['name' => 'RRHH'],
   ...     ['description' => 'Gestió de recursos humans', 'is_active' => true]
   ... )
   ```

3. Torna a assignar la característica RRHH als plans o rols que la necessitin:

   ```php
   >>> $featureId = \App\Models\Feature::where('name', 'RRHH')->value('id');
   >>> \App\Models\Plan::whereIn('name', ['Premium', 'FirstMonthFree'])->each(fn($plan) => $plan->features()->syncWithoutDetaching([$featureId]));
   >>> \App\Models\Role::where('name', 'Administrador')->each(fn($role) => $role->features()->syncWithoutDetaching([$featureId]));
   ```

4. Torna a iniciar la sessió dels usuaris que necessitin accedir al mòdul per actualitzar la navegació compartida per Inertia.

Per invertir els canvis i tornar a deshabilitar el mòdul, estableix `FEATURE_RRHH_ENABLED=false` i assigna `is_active` a `false` per a la característica `RRHH`.

## Contribución

Si deseas contribuir a este proyecto, por favor sigue estos pasos:

1. Haz un fork del repositorio.
2. Crea una rama para tu nueva funcionalidad (`git checkout -b feature/nueva-funcionalidad`).
3. Realiza tus cambios y haz commit (`git commit -am 'Añadir nueva funcionalidad'`).
4. Sube tus cambios a tu fork (`git push origin feature/nueva-funcionalidad`).
5. Abre un Pull Request con una descripción clara de lo que hace tu contribución.

## Licencia

Este proyecto está bajo la licencia MIT. Consulta el archivo [LICENSE](LICENSE) para más detalles.
 
