<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// =======================
// CORS CORRECTO
// =======================

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

// Preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  exit;
}

// =======================
// SESIÓN
// =======================

session_start();

// =======================
// CONEXIÓN
// =======================

require_once __DIR__ . '/Models/Conexion.php';
$conexion = Conexion::conectar();

// =======================
// ROUTER
// =======================

$controller = $_GET['controller'] ?? null;
$action     = $_GET['action'] ?? null;

try {

  if ($controller && $action) {

    switch ($controller) {

      case 'auth':
        require_once __DIR__ . '/Controllers/AuthController.php';
        $c = new AuthController($conexion);
        break;

      case 'reserva':
        require_once __DIR__ . '/Controllers/ReservaController.php';
        $c = new ReservaController($conexion);
        break;

      default:
        throw new Exception("Controller no válido");
    }

    if (!method_exists($c, $action)) {
      throw new Exception("Acción no existe");
    }

    $c->$action();
    exit;
  }

  // ======================
  // DOCUMENTACIÓN API
  // ======================

  echo json_encode([
    "api" => "Ristorante Italini API",
    "status" => "running",
    "endpoints" => [

      "auth_login" => [
        "method" => "POST",
        "url" => "?controller=auth&action=autenticar"
      ],

      "reservas_listar" => [
        "method" => "GET",
        "url" => "?controller=reserva&action=listar"
      ],

      "reservas_crear" => [
        "method" => "POST",
        "url" => "?controller=reserva&action=crear"
      ],

      "reservas_actualizar" => [
        "method" => "POST",
        "url" => "?controller=reserva&action=actualizar"
      ],

      "reservas_eliminar" => [
        "method" => "POST",
        "url" => "?controller=reserva&action=eliminar"
      ]
    ]
  ], JSON_PRETTY_PRINT);
} catch (Throwable $e) {

  http_response_code(500);

  echo json_encode([
    "error" => true,
    "message" => $e->getMessage()
  ]);
}
