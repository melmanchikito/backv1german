<?php

/**
 * API RESTful para Ristorante Italini
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

// ===== CORS CORRECTO =====

$allowedOrigins = [
    "https://ristoranteitalgm.netlify.app",
    "http://localhost:5500",
    "http://127.0.0.1:5500"
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: $origin");
}

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=utf-8");

// Preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}



// Iniciar sesión
session_start();

// Error reporting para desarrollo
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Autoload de clases
require_once __DIR__ . '/Models/Conexion.php';
require_once __DIR__ . '/Models/Reserva.php';
require_once __DIR__ . '/Services/ReservaService.php';
require_once __DIR__ . '/Controllers/ReservaController.php';
require_once __DIR__ . '/Controllers/AuthController.php';

// Obtener parámetros de la URL
$controller = $_GET['controller'] ?? null;
$accion = $_GET['accion'] ?? null;
$action = $_GET['action'] ?? null;

try {
    // Rutas de autenticación
    if ($controller === 'auth') {
        $authController = new AuthController();

        switch ($action) {
            case 'autenticar':
            case 'login':
                $authController->autenticar();
                break;

            case 'logout':
                $authController->logout();
                break;

            default:
                echo json_encode([
                    'success' => false,
                    'message' => 'Acción de autenticación no válida'
                ]);
        }
        exit;
    }

    // Rutas de reservas
    if ($accion) {
        $reservaController = new ReservaController();

        switch ($accion) {
            case 'crear':
                $reservaController->crear();
                break;

            case 'listar':
                $reservaController->listar();
                break;

            case 'listar-por-estado':
                $reservaController->listarPorEstado();
                break;

            case 'obtener':
                $reservaController->obtenerPorId();
                break;

            case 'actualizar-estado':
                $reservaController->actualizarEstado();
                break;

            case 'actualizar':
                $reservaController->actualizar();
                break;

            case 'eliminar':
                $reservaController->eliminar();
                break;

            case 'estadisticas':
                $reservaController->estadisticas();
                break;

            default:
                echo json_encode([
                    'success' => false,
                    'message' => 'Acción no válida',
                    'acciones_disponibles' => [
                        'crear' => 'POST - Crear nueva reserva',
                        'listar' => 'GET - Listar todas las reservas',
                        'listar-por-estado' => 'GET - Listar reservas por estado (requiere parámetro estado)',
                        'obtener' => 'GET - Obtener una reserva por ID (requiere parámetro id)',
                        'actualizar-estado' => 'POST - Actualizar estado de reserva',
                        'actualizar' => 'POST - Actualizar reserva completa',
                        'eliminar' => 'POST - Eliminar una reserva',
                        'estadisticas' => 'GET - Obtener estadísticas de reservas'
                    ]
                ]);
        }
        exit;
    }

    // Si no hay parámetros, mostrar información de la API
    echo json_encode([
        'success' => true,
        'message' => 'API de Ristorante Italini',
        'version' => '1.0',
        'endpoints' => [
            'auth' => [
                'login' => 'POST ?controller=auth&action=autenticar',
                'logout' => 'GET ?controller=auth&action=logout'
            ],
            'reservas' => [
                'crear' => 'POST ?accion=crear',
                'listar' => 'GET ?accion=listar',
                'listar-por-estado' => 'GET ?accion=listar-por-estado&estado={estado}',
                'obtener' => 'GET ?accion=obtener&id={id}',
                'actualizar-estado' => 'POST ?accion=actualizar-estado',
                'actualizar' => 'POST ?accion=actualizar',
                'eliminar' => 'POST ?accion=eliminar',
                'estadisticas' => 'GET ?accion=estadisticas'
            ]
        ]
    ], JSON_PRETTY_PRINT);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor',
        'error' => $e->getMessage()
    ]);
}
