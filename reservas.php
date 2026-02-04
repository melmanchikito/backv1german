<?php
// Punto de entrada para manejar las peticiones de reservas
require_once __DIR__ . '/Controllers/ReservaController.php';

$controller = new ReservaController();

// Determinar la acción a ejecutar
$accion = isset($_GET['accion']) ? $_GET['accion'] : '';

switch ($accion) {
    case 'crear':
        $controller->crear();
        break;

    case 'listar':
        $controller->listar();
        break;

    case 'listar-por-estado':
        $controller->listarPorEstado();
        break;

    case 'obtener':
        $controller->obtenerPorId();
        break;

    case 'actualizar-estado':
        $controller->actualizarEstado();
        break;

    case 'actualizar':
        $controller->actualizar();
        break;

    case 'eliminar':
        $controller->eliminar();
        break;

    case 'estadisticas':
        $controller->estadisticas();
        break;

    default:
        header('Content-Type: application/json');
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
        break;
}
