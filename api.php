<?php

/**
 * API RESTful — Ristorante Italini
 * Punto único de entrada backend
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

/* =========================
   CORS CONFIG — NETLIFY
========================= */

$allowedOrigins = [
    "https://ristoranteitalgm.netlify.app"
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: $origin");
}

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=utf-8");

/* =========================
   PREFLIGHT
========================= */

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

/* =========================
   SESIÓN (cross-domain)
========================= */

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'None'
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* =========================
   CARGA DE CLASES
========================= */

require_once __DIR__ . '/Models/Conexion.php';
require_once __DIR__ . '/Models/Reserva.php';
require_once __DIR__ . '/Services/ReservaService.php';
require_once __DIR__ . '/Controllers/ReservaController.php';
require_once __DIR__ . '/Controllers/AuthController.php';

/* =========================
   PARAMS
========================= */

$controller = $_GET['controller'] ?? null;
$accion     = $_GET['accion'] ?? null;
$action     = $_GET['action'] ?? null;

/* =========================
   ROUTER
========================= */

try {

    /* ===== AUTH ===== */

    if ($controller === 'auth') {

        $auth = new AuthController();

        switch ($action) {

            case 'autenticar':
            case 'login':
                $auth->autenticar();
                break;

            case 'logout':
                $auth->logout();
                break;

            default:
                echo json_encode([
                    'success' => false,
                    'message' => 'Acción auth inválida'
                ]);
        }

        exit;
    }

    /* ===== RESERVAS ===== */

    if ($accion) {

        $reserva = new ReservaController();

        switch ($accion) {

            case 'crear':
                $reserva->crear();
                break;

            case 'listar':
                $reserva->listar();
                break;

            case 'listar-por-estado':
                $reserva->listarPorEstado();
                break;

            case 'obtener':
                $reserva->obtenerPorId();
                break;

            case 'actualizar-estado':
                $reserva->actualizarEstado();
                break;

            case 'actualizar':
                $reserva->actualizar();
                break;

            case 'eliminar':
                $reserva->eliminar();
                break;

            case 'estadisticas':
                $reserva->estadisticas();
                break;

            default:
                echo json_encode([
                    'success' => false,
                    'message' => 'Acción no válida',
                    'acciones' => [
                        'crear',
                        'listar',
                        'listar-por-estado',
                        'obtener',
                        'actualizar-estado',
                        'actualizar',
                        'eliminar',
                        'estadisticas'
                    ]
                ]);
        }

        exit;
    }

    /* ===== INFO API ===== */

    echo json_encode([
        'success' => true,
        'api' => 'Ristorante Italini API',
        'status' => 'running',
        'version' => '1.0',
        'base_url' => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'],
        'endpoints' => [
            'auth_login' => 'POST ?controller=auth&action=autenticar',
            'auth_logout' => 'GET ?controller=auth&action=logout',
            'reservas_listar' => 'GET ?accion=listar',
            'reservas_crear' => 'POST ?accion=crear',
            'reservas_actualizar' => 'POST ?accion=actualizar',
            'reservas_eliminar' => 'POST ?accion=eliminar'
        ]
    ], JSON_PRETTY_PRINT);
} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Error interno',
        'error' => $e->getMessage()
    ]);
}
