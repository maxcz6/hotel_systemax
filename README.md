# 🏨 Hotel Systemax

Sistema integral de gestión hotelera desarrollado con Laravel 12. Diseñado para optimizar la administración de reservas, habitaciones, clientes y reportes financieros.

## 🚀 Características Principales

### 👥 Gestión de Usuarios y Roles
*   **Gerente**: Acceso total al sistema, incluyendo gestión de habitaciones, tipos de habitación, servicios y reportes avanzados.
*   **Recepción**: Acceso enfocado en la operación diaria: gestión de clientes, reservas, check-in/check-out y pagos.

### 🛏️ Gestión de Habitaciones
*   Control de tipos de habitación (Simple, Doble, Suite, etc.) con capacidad y precios configurables.
*   Estados de habitación en tiempo real: Disponible, Ocupada, Limpieza, Mantenimiento.
*   Validaciones de precios y capacidad.

### 📅 Gestión de Reservas
*   Flujo completo de reserva: Pendiente -> Confirmada -> Check-in -> Check-out.
*   Validación de disponibilidad por fechas.
*   Cálculo automático de costos y descuentos.
*   Registro de notas y observaciones.

### 👤 Gestión de Clientes
*   Registro detallado con validación de documentos (DNI, RUC para Perú).
*   Historial de estancias y preferencias.

### 📊 Reportes y Estadísticas
*   **Reporte General**: Resumen de reservas, ingresos y ocupación.
*   **Reporte de Ingresos**: Detalle financiero por fechas y métodos de pago.
*   **Reporte de Ocupación**: Análisis de ocupación diaria y habitaciones más solicitadas.
*   **Reporte de Servicios**: Métricas de consumo de servicios adicionales.

## 🛠️ Tecnologías Utilizadas

*   **Backend**: Laravel 12, PHP 8.2+
*   **Base de Datos**: MySQL / MariaDB
*   **Frontend**: Blade Templates, CSS3 (Diseño personalizado y responsivo)
*   **Servidor**: Apache/Nginx (XAMPP compatible)

## ⚙️ Instalación y Configuración

Sigue estos pasos para desplegar el proyecto en tu entorno local:

1.  **Clonar el repositorio**
    ```bash
    git clone <url-del-repositorio>
    cd hotel_systemax
    ```

2.  **Instalar dependencias de PHP**
    ```bash
    composer install
    ```

3.  **Configurar entorno**
    *   Duplica el archivo `.env.example` y renómbralo a `.env`.
    *   Configura tus credenciales de base de datos en el archivo `.env`:
        ```env
        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=hotel_systemax
        DB_USERNAME=root
        DB_PASSWORD=
        ```

4.  **Generar clave de aplicación**
    ```bash
    php artisan key:generate
    ```

5.  **Ejecutar migraciones y seeders**
    Esto creará las tablas y los usuarios por defecto.
    ```bash
    php artisan migrate --seed
    ```
    *Nota: Si necesitas reiniciar la base de datos, usa `php artisan migrate:fresh --seed`.*

6.  **Iniciar el servidor local**
    ```bash
    php artisan serve
    ```
    Accede a: `http://127.0.0.1:8000`

## 🔑 Credenciales de Acceso (Demo)

El sistema viene con usuarios preconfigurados para pruebas:

| Rol | Email | Contraseña |
| :--- | :--- | :--- |
| **Gerente** | `gerente@hotel.com` | `password` |
| **Recepción** | `recepcion@hotel.com` | `password` |

## 📂 Estructura de Módulos

*   **App\Models**: Modelos Eloquent (Reserva, Habitacion, Cliente, Pago, etc.).
*   **App\Http\Controllers**: Lógica de negocio (ReservaController, ReportesController, etc.).
*   **App\Http\Requests**: Validaciones de formularios (StoreReservaRequest, etc.).
*   **resources/views**: Vistas Blade organizadas por módulo.
*   **database/migrations**: Estructura de la base de datos.

## 📝 Notas Adicionales

*   El sistema incluye validaciones específicas para documentos de identidad peruanos (DNI 8 dígitos, RUC 11 dígitos).
*   Los reportes utilizan gráficos y tablas para una mejor visualización de datos.
*   El sistema maneja estados de sesión y protección CSRF para seguridad.

---
Desarrollado para Hotel Systemax.
