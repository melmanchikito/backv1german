# 🍝 Ristorante Italini - Backend API

## 📋 Descripción
Backend RESTful API para el sitio web de **Ristorante Italini**. Sistema desarrollado en **PHP puro** siguiendo la arquitectura **MVC (Modelo-Vista-Controlador)**, diseñado para gestionar reservas de restaurante y autenticación de usuarios.

## ✨ Características

- **API RESTful** completa con endpoints JSON
- **Arquitectura MVC** limpia y organizada
- **CRUD completo** de reservas
- **Sistema de autenticación** con sesiones PHP
- **Validación de datos** en servidor
- **Generación automática** de códigos de reserva únicos
- **Gestión de estados** de reservas (Pendiente, Confirmada, Cancelada)
- **CORS configurado** para comunicación con frontend
- **Base de datos MySQL** con triggers y procedimientos

## 🛠️ Tecnologías Utilizadas

- **PHP 7.4+** - Lenguaje del backend
- **MySQL 5.7+** - Base de datos relacional
- **PDO/MySQLi** - Conexión a base de datos
- **Apache** - Servidor web
- **htaccess** - Configuración de rutas y CORS

## 📁 Estructura del Proyecto (MVC)

```
Proyecto-Restaurante-Italiano/
├── api.php                    # Punto de entrada único de la API
├── .htaccess                  # Configuración Apache y CORS
├── database.sql               # Script de creación de BD
├── Controllers/               # Capa de Controladores
│   ├── AuthController.php     # Autenticación de usuarios
│   └── ReservaController.php  # Gestión de reservas
├── Models/                    # Capa de Modelos
│   ├── Conexion.php           # Conexión a base de datos
│   └── Reserva.php            # Modelo de entidad Reserva
├── Services/                  # Capa de Servicios
│   └── ReservaService.php     # Lógica de negocio de reservas
└── README.md                  # Este archivo
```

## 🚀 Instalación y Configuración

### Requisitos Previos

- **XAMPP** o **WAMP** (Apache + PHP + MySQL)
  - PHP >= 7.4
  - MySQL >= 5.7
  - Apache 2.4
- **Composer** (opcional, para futuras dependencias)

### Pasos de Instalación

#### 1. Instalar XAMPP

Descarga e instala XAMPP desde: https://www.apachefriends.org/

#### 2. Configurar el Proyecto

```bash
# Copia el proyecto a la carpeta htdocs de XAMPP
# Ruta típica: C:\xampp\htdocs\

# En Windows
xcopy /E /I "Proyecto-Restaurante-Italiano" "C:\xampp\htdocs\Proyecto-Restaurante-Italiano"
```

#### 3. Configurar la Base de Datos

1. **Iniciar servicios de XAMPP:**
   - Abre el panel de control de XAMPP
   - Inicia **Apache** y **MySQL**

2. **Acceder a phpMyAdmin:**
   - Navega a: `http://localhost/phpmyadmin`

3. **Crear la base de datos:**
   - Click en "Nueva" en el menú izquierdo
   - O ejecuta el script SQL incluido:

   ```bash
   # Opción A: Importar desde phpMyAdmin
   # - Click en "Importar"
   # - Selecciona el archivo database.sql
   # - Click en "Continuar"

   # Opción B: Desde línea de comandos
   mysql -u root -p < database.sql
   ```

4. **Verificar la creación:**
   ```sql
   USE ristorante;
   SHOW TABLES;
   -- Debe mostrar: reservas, usuarios
   ```

#### 4. Configurar Conexión a Base de Datos

Edita el archivo `Models/Conexion.php`:

```php
<?php
$host = "localhost";
$bd = "ristorante";
$user = "root";          // Tu usuario de MySQL
$pass = "";              // Tu contraseña de MySQL (vacía por defecto en XAMPP)

$conexion = new mysqli($host, $user, $pass, $bd);

if ($conexion->connect_error) {
    die("Connection failed: " . $conexion->connect_error);
}

$conexion->set_charset("utf8");
```

#### 5. Crear Usuario Administrador

Ejecuta en phpMyAdmin o MySQL:

```sql
USE ristorante;

INSERT INTO usuarios (usuario, password, rol, estado)
VALUES ('admin', 'admin123', 'administrador', 1);
```

#### 6. Verificar Instalación

Accede a:
```
http://localhost/Proyecto-Restaurante-Italiano/api.php
```

Deberías ver una respuesta JSON con información de la API:
```json
{
  "success": true,
  "message": "API de Ristorante Italini",
  "version": "1.0",
  "endpoints": { ... }
}
```

## 📡 Endpoints de la API

### Base URL
```
http://localhost/Proyecto-Restaurante-Italiano/api.php
```

### Autenticación

#### Login
```http
POST /api.php?controller=auth&action=autenticar

Content-Type: application/x-www-form-urlencoded

usuario=admin&password=admin123

Response:
{
  "success": true,
  "message": "Autenticación exitosa",
  "usuario": "admin",
  "rol": "administrador"
}
```

#### Logout
```http
GET /api.php?controller=auth&action=logout

Response:
{
  "success": true,
  "message": "Sesión cerrada exitosamente"
}
```

### Reservas

#### Crear Reserva
```http
POST /api.php?accion=crear

Content-Type: application/x-www-form-urlencoded

nombre=Juan Pérez
email=juan@example.com
telefono=0991234567
fecha=2025-03-15
hora=19:30
personas=4
ocasion=cumpleaños
comentarios=Mesa cerca de la ventana

Response:
{
  "success": true,
  "message": "¡Reserva creada exitosamente!",
  "codigo": "RES-20250204-000123",
  "id": 123
}
```

#### Listar Todas las Reservas
```http
GET /api.php?accion=listar

Response:
{
  "success": true,
  "data": [
    {
      "id": 1,
      "codigo": "RES-20250204-000001",
      "nombre": "Juan Pérez",
      "email": "juan@example.com",
      "telefono": "0991234567",
      "fecha_hora": "15/03/2025 a las 19:30",
      "personas": 4,
      "ocasion": "cumpleaños",
      "comentarios": "Mesa cerca de la ventana",
      "estado": "pendiente"
    }
  ],
  "total": 1
}
```

#### Obtener Reserva por ID
```http
GET /api.php?accion=obtener&id=1

Response:
{
  "success": true,
  "data": { ... }
}
```

#### Listar por Estado
```http
GET /api.php?accion=listar-por-estado&estado=pendiente

Estados válidos: pendiente, confirmada, cancelada
```

#### Actualizar Estado
```http
POST /api.php?accion=actualizar-estado

id=1&estado=confirmada

Response:
{
  "success": true,
  "message": "Estado actualizado correctamente"
}
```

#### Actualizar Reserva Completa
```http
POST /api.php?accion=actualizar

id=1&nombre=Juan Pérez&email=juan@example.com&...

Response:
{
  "success": true,
  "message": "Reserva actualizada correctamente"
}
```

#### Eliminar Reserva
```http
POST /api.php?accion=eliminar

id=1

Response:
{
  "success": true,
  "message": "Reserva eliminada correctamente"
}
```

#### Estadísticas
```http
GET /api.php?accion=estadisticas

Response:
{
  "success": true,
  "data": {
    "total": 10,
    "pendientes": 5,
    "confirmadas": 3,
    "canceladas": 2
  }
}
```

## 🗄️ Esquema de Base de Datos

### Tabla: reservas
```sql
CREATE TABLE reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    personas INT NOT NULL,
    ocasion VARCHAR(50),
    comentarios TEXT,
    estado ENUM('pendiente','confirmada','cancelada') DEFAULT 'pendiente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_fecha (fecha),
    INDEX idx_estado (estado),
    INDEX idx_codigo (codigo)
);
```

### Tabla: usuarios
```sql
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol VARCHAR(50) NOT NULL,
    estado TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Trigger: generar_codigo_reserva
Genera automáticamente códigos únicos para cada reserva:
```
Formato: RES-YYYYMMDD-NNNNNN
Ejemplo: RES-20250204-000123
```

## 🔒 Seguridad

### Implementado:
- ✅ Validación de datos en servidor
- ✅ Sanitización de inputs (htmlspecialchars)
- ✅ Prepared statements (prevención SQL Injection)
- ✅ Gestión de sesiones PHP
- ✅ CORS configurado para frontend específico
- ✅ Validación de tipos de datos

### Recomendaciones de Producción:
- 🔐 Implementar **password_hash()** para contraseñas
- 🔐 Usar **JWT** o tokens de autenticación
- 🔐 Implementar **rate limiting**
- 🔐 Configurar **HTTPS**
- 🔐 Validar y sanitizar **TODOS** los inputs
- 🔐 Implementar logs de auditoría

## 🧪 Pruebas

### Con Postman o Thunder Client:

1. **Probar creación de reserva:**
```
POST http://localhost/Proyecto-Restaurante-Italiano/api.php?accion=crear
Body (x-www-form-urlencoded):
  nombre: Test User
  email: test@test.com
  telefono: 0991234567
  fecha: 2025-03-20
  hora: 20:00
  personas: 2
```

2. **Probar listado:**
```
GET http://localhost/Proyecto-Restaurante-Italiano/api.php?accion=listar
```

### Con cURL:
```bash
# Crear reserva
curl -X POST "http://localhost/Proyecto-Restaurante-Italiano/api.php?accion=crear" \
  -d "nombre=Test&email=test@test.com&telefono=0991234567&fecha=2025-03-20&hora=20:00&personas=2"

# Listar reservas
curl "http://localhost/Proyecto-Restaurante-Italiano/api.php?accion=listar"
```

## 📊 Logs y Debugging

Los logs están habilitados en `api.php`:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

**Para producción**, desactiva los errores visibles:
```php
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/error.log');
```

## 🔧 Configuración de CORS

El archivo `.htaccess` incluye configuración CORS:
```apache
Header set Access-Control-Allow-Origin "*"
Header set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"
Header set Access-Control-Allow-Headers "Content-Type, Authorization"
Header set Access-Control-Allow-Credentials "true"
```

Para producción, restringe el origen:
```apache
Header set Access-Control-Allow-Origin "https://tu-dominio.com"
```

## 🐛 Solución de Problemas

### Error: "Access denied for user"
- Verifica credenciales en `Models/Conexion.php`
- Asegúrate de que MySQL esté iniciado

### Error: "Table doesn't exist"
- Ejecuta el script `database.sql`
- Verifica el nombre de la base de datos

### Error: 404 en endpoints
- Verifica que `.htaccess` esté activo
- Asegura que mod_rewrite esté habilitado en Apache

### CORS Error desde frontend
- Verifica la configuración en `.htaccess`
- Asegura que Apache tenga mod_headers activo

## 📞 Información de Contacto

Para soporte o consultas sobre el proyecto:
- **Proyecto:** Ristorante Italini Backend API
- **Curso:** MATERIA_DESARROLLO-WEB

## 📄 Licencia

Este proyecto es parte de un trabajo académico de **Desarrollo Web**.

## 👨‍💻 Autor

Desarrollado siguiendo arquitectura MVC y buenas prácticas de desarrollo PHP.

---

**¡Mangia bene! 🇮🇹**
