<?php
require_once __DIR__ . '/../Models/Conexion.php';
require_once __DIR__ . '/../Models/Reserva.php';
require_once __DIR__ . '/../Services/ReservaService.php';

class ReservaController {
    private $reservaService;
    
    public function __construct() {
        global $conexion;
        $this->reservaService = new ReservaService($conexion);
    }
    
    // Procesar creación de reserva
    public function crear() {
        header('Content-Type: application/json');
        
        // LOG 1: Verificar método HTTP
        error_log("=== INICIO CREAR RESERVA ===");
        error_log("Método HTTP: " . $_SERVER['REQUEST_METHOD']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log("ERROR: Método no permitido");
            echo json_encode([
                'success' => false,
                'message' => 'Método no permitido'
            ]);
            return;
        }
        
        // LOG 2: Datos recibidos en POST
        error_log("Datos POST recibidos: " . print_r($_POST, true));
        
        // Crear objeto Reserva con los datos del POST
        $reserva = new Reserva([
            'nombre' => isset($_POST['nombre']) ? htmlspecialchars(trim($_POST['nombre'])) : '',
            'email' => isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '',
            'telefono' => isset($_POST['telefono']) ? htmlspecialchars(trim($_POST['telefono'])) : '',
            'fecha' => isset($_POST['fecha']) ? $_POST['fecha'] : '',
            'hora' => isset($_POST['hora']) ? $_POST['hora'] : '',
            'personas' => isset($_POST['personas']) ? htmlspecialchars(trim($_POST['personas'])) : '',
            'ocasion' => isset($_POST['ocasion']) ? htmlspecialchars(trim($_POST['ocasion'])) : '',
            'comentarios' => isset($_POST['comentarios']) ? htmlspecialchars(trim($_POST['comentarios'])) : '',
            'estado' => 'pendiente'
        ]);
        
        // LOG 3: Objeto Reserva creado
        error_log("Objeto Reserva creado: " . print_r($reserva->toArray(), true));
        
        // Validar los datos
        $validacion = $reserva->validar();
        
        // LOG 4: Resultado de validación
        error_log("Validación: " . ($validacion['valid'] ? 'OK' : 'ERRORES'));
        
        if (!$validacion['valid']) {
            error_log("Errores de validación: " . print_r($validacion['errors'], true));
            echo json_encode([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validacion['errors']
            ]);
            return;
        }
        
        // LOG 5: Llamando al servicio
        error_log("Llamando a ReservaService->crear()");
        
        // Llamar al servicio para crear la reserva
        $resultado = $this->reservaService->crear($reserva);
        
        // LOG 6: Resultado del servicio
        error_log("Resultado del servicio: " . print_r($resultado, true));
        error_log("=== FIN CREAR RESERVA ===");
        
        echo json_encode($resultado);
    }
    
    // Obtener todas las reservas
    public function listar() {
        header('Content-Type: application/json');
        $resultado = $this->reservaService->obtenerTodas();
        echo json_encode($resultado);
    }
    
    // Obtener reserva por ID
    public function obtenerPorId() {
        header('Content-Type: application/json');
        
        if (empty($_GET['id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'ID es requerido'
            ]);
            return;
        }
        
        $resultado = $this->reservaService->obtenerPorId($_GET['id']);
        echo json_encode($resultado);
    }
    
    // Obtener reservas por estado
    public function listarPorEstado() {
        header('Content-Type: application/json');
        
        if (empty($_GET['estado'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Estado es requerido'
            ]);
            return;
        }
        
        $estadosValidos = ['pendiente', 'confirmada', 'cancelada'];
        if (!in_array($_GET['estado'], $estadosValidos)) {
            echo json_encode([
                'success' => false,
                'message' => 'Estado no válido'
            ]);
            return;
        }
        
        $resultado = $this->reservaService->obtenerPorEstado($_GET['estado']);
        echo json_encode($resultado);
    }
    
    // Actualizar estado
    public function actualizarEstado() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Método no permitido'
            ]);
            return;
        }
        
        if (empty($_POST['id']) || empty($_POST['estado'])) {
            echo json_encode([
                'success' => false,
                'message' => 'ID y estado son requeridos'
            ]);
            return;
        }
        
        $estadosValidos = ['pendiente', 'confirmada', 'cancelada'];
        if (!in_array($_POST['estado'], $estadosValidos)) {
            echo json_encode([
                'success' => false,
                'message' => 'Estado no válido'
            ]);
            return;
        }
        
        $resultado = $this->reservaService->actualizarEstado($_POST['id'], $_POST['estado']);
        echo json_encode($resultado);
    }
    
    // Actualizar reserva completa
    public function actualizar() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Método no permitido'
            ]);
            return;
        }
        
        if (empty($_POST['id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'ID es requerido'
            ]);
            return;
        }
        
        // Crear objeto Reserva con los datos del POST
        $reserva = new Reserva([
            'nombre' => isset($_POST['nombre']) ? htmlspecialchars(trim($_POST['nombre'])) : '',
            'email' => isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '',
            'telefono' => isset($_POST['telefono']) ? htmlspecialchars(trim($_POST['telefono'])) : '',
            'fecha' => isset($_POST['fecha']) ? $_POST['fecha'] : '',
            'hora' => isset($_POST['hora']) ? $_POST['hora'] : '',
            'personas' => isset($_POST['personas']) ? htmlspecialchars(trim($_POST['personas'])) : '',
            'ocasion' => isset($_POST['ocasion']) ? htmlspecialchars(trim($_POST['ocasion'])) : '',
            'comentarios' => isset($_POST['comentarios']) ? htmlspecialchars(trim($_POST['comentarios'])) : ''
        ]);
        
        // Validar los datos
        $validacion = $reserva->validar();
        
        if (!$validacion['valid']) {
            echo json_encode([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validacion['errors']
            ]);
            return;
        }
        
        $resultado = $this->reservaService->actualizar($_POST['id'], $reserva);
        echo json_encode($resultado);
    }
    
    // Eliminar reserva
    public function eliminar() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Método no permitido'
            ]);
            return;
        }
        
        if (empty($_POST['id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'ID es requerido'
            ]);
            return;
        }
        
        $resultado = $this->reservaService->eliminar($_POST['id']);
        echo json_encode($resultado);
    }
    
    // Obtener estadísticas
    public function estadisticas() {
        header('Content-Type: application/json');
        $resultado = $this->reservaService->contarPorEstado();
        echo json_encode($resultado);
    }
}
?>
