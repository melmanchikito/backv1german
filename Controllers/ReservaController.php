<?php
require_once __DIR__ . '/../Models/Conexion.php';
require_once __DIR__ . '/../Models/Reserva.php';
require_once __DIR__ . '/../Services/ReservaService.php';

class ReservaController
{
    private $reservaService;

    public function __construct()
    {
        global $conexion;

        if (!$conexion) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Error de conexión a base de datos"
            ]);
            exit;
        }

        $this->reservaService = new ReservaService($conexion);
    }

    private function jsonHeaders()
    {
        header('Content-Type: application/json');
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type");
    }

    // ============================
    // CREAR RESERVA
    // ============================
    public function crear()
    {
        $this->jsonHeaders();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        // echo json_encode($_POST);
        //exit;

        $reserva = new Reserva([
            'nombre' => trim($_POST['nombre'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'fecha' => $_POST['fecha'] ?? '',
            'hora' => $_POST['hora'] ?? '',
            'personas' => trim($_POST['personas'] ?? ''),
            'ocasion' => trim($_POST['ocasion'] ?? ''),
            'comentarios' => trim($_POST['comentarios'] ?? ''),
            'estado' => 'pendiente'
        ]);

        $validacion = $reserva->validar();

        if (!$validacion['valid']) {
            echo json_encode([
                'success' => false,
                'errors' => $validacion['errors']
            ]);
            return;
        }

        echo json_encode($this->reservaService->crear($reserva));
    }

    // ============================
    public function listar()
    {
        $this->jsonHeaders();
        echo json_encode($this->reservaService->obtenerTodas());
    }

    // ============================
    public function obtenerPorId()
    {
        $this->jsonHeaders();

        if (empty($_GET['id'])) {
            echo json_encode(['success' => false, 'message' => 'ID requerido']);
            return;
        }

        echo json_encode(
            $this->reservaService->obtenerPorId($_GET['id'])
        );
    }

    // ============================
    public function listarPorEstado()
    {
        $this->jsonHeaders();

        $estado = $_GET['estado'] ?? '';

        if (!$estado) {
            echo json_encode(['success' => false, 'message' => 'Estado requerido']);
            return;
        }

        echo json_encode(
            $this->reservaService->obtenerPorEstado($estado)
        );
    }

    // ============================
    public function actualizarEstado()
    {
        $this->jsonHeaders();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'POST requerido']);
            return;
        }

        echo json_encode(
            $this->reservaService->actualizarEstado(
                $_POST['id'],
                $_POST['estado']
            )
        );
    }

    // ============================
    public function actualizar()
    {
        $this->jsonHeaders();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'POST requerido']);
            return;
        }

        $reserva = new Reserva($_POST);
        $validacion = $reserva->validar();

        if (!$validacion['valid']) {
            echo json_encode([
                'success' => false,
                'errors' => $validacion['errors']
            ]);
            return;
        }

        echo json_encode(
            $this->reservaService->actualizar($_POST['id'], $reserva)
        );
    }

    // ============================
    public function eliminar()
    {
        $this->jsonHeaders();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'POST requerido']);
            return;
        }

        echo json_encode(
            $this->reservaService->eliminar($_POST['id'])
        );
    }

    // ============================
    public function estadisticas()
    {
        $this->jsonHeaders();
        echo json_encode($this->reservaService->contarPorEstado());
    }
}
