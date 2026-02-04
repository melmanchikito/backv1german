<?php
require_once __DIR__ . '/../Models/Reserva.php';

class ReservaService
{
    private $conexion;

    public function __construct($db)
    {
        $this->conexion = $db;
    }

    // Generar código único de reserva
    private function generarCodigo()
    {
        do {
            $codigo = 'RES-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
            $existe = $this->verificarCodigoExistente($codigo);
        } while ($existe);

        return $codigo;
    }

    // Verificar si un código ya existe
    private function verificarCodigoExistente($codigo)
    {
        $query = "SELECT COUNT(*) as total FROM reservas WHERE codigo = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("s", $codigo);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $fila = $resultado->fetch_assoc();
        return $fila['total'] > 0;
    }

    // Crear nueva reserva
    public function crear(Reserva $reserva)
    {
        try {
            // LOG SERVICE 1: Inicio del método
            error_log("[SERVICE] Iniciando crear() con datos:");
            error_log("[SERVICE] nombre: " . $reserva->nombre);
            error_log("[SERVICE] email: " . $reserva->email);
            error_log("[SERVICE] telefono: " . $reserva->telefono);
            error_log("[SERVICE] fecha: " . $reserva->fecha);
            error_log("[SERVICE] hora: " . $reserva->hora);
            error_log("[SERVICE] personas: " . $reserva->personas);
            error_log("[SERVICE] ocasion: " . $reserva->ocasion);
            error_log("[SERVICE] estado: " . $reserva->estado);

            // El código se genera automáticamente por el trigger de la BD
            $query = "INSERT INTO reservas (nombre, email, telefono, fecha, hora, personas, ocasion, comentarios) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            // LOG SERVICE 2: Query preparado
            error_log("[SERVICE] Query: " . $query);

            $stmt = $this->conexion->prepare($query);

            if (!$stmt) {
                error_log("[SERVICE] ERROR al preparar statement: " . $this->conexion->error);
                throw new Exception("Error al preparar la consulta: " . $this->conexion->error);
            }

            error_log("[SERVICE] Statement preparado correctamente");

            // LOG SERVICE 3: Bind params - CORREGIDO: 8 parámetros (sin estado)
            error_log("[SERVICE] Haciendo bind_param con tipos: ssssssss");

            $stmt->bind_param(
                "sssssiss",
                $reserva->nombre,
                $reserva->email,
                $reserva->telefono,
                $reserva->fecha,
                $reserva->hora,
                $reserva->personas,
                $reserva->ocasion,
                $reserva->comentarios
            );

            error_log("[SERVICE] bind_param ejecutado");

            // LOG SERVICE 4: Ejecutando INSERT
            error_log("[SERVICE] Ejecutando INSERT...");

            if ($stmt->execute()) {
                error_log("[SERVICE] INSERT exitoso!");

                $insertId = $this->conexion->insert_id;
                error_log("[SERVICE] ID insertado: " . $insertId);

                // Obtener el código generado por el trigger
                $querySelect = "SELECT codigo FROM reservas WHERE id = ?";
                error_log("[SERVICE] Obteniendo código generado por trigger...");

                $stmtSelect = $this->conexion->prepare($querySelect);
                $stmtSelect->bind_param("i", $insertId);
                $stmtSelect->execute();
                $resultado = $stmtSelect->get_result();
                $fila = $resultado->fetch_assoc();

                error_log("[SERVICE] Código obtenido: " . $fila['codigo']);

                return [
                    'success' => true,
                    'message' => '¡Reserva creada exitosamente!',
                    'codigo' => $fila['codigo'],
                    'id' => $insertId
                ];
            } else {
                error_log("[SERVICE] ERROR en execute(): " . $stmt->error);
                error_log("[SERVICE] MySQL errno: " . $this->conexion->errno);
                error_log("[SERVICE] MySQL error: " . $this->conexion->error);
                throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
            }
        } catch (Exception $e) {
            error_log("[SERVICE] EXCEPCIÓN capturada: " . $e->getMessage());
            error_log("[SERVICE] Stack trace: " . $e->getTraceAsString());
            return [
                'success' => false,
                'message' => 'Error al crear la reserva: ' . $e->getMessage()
            ];
        }
    }

    // Obtener todas las reservas
    public function obtenerTodas()
    {
        try {
            $query = "SELECT id, codigo, nombre, email, telefono, 
                     DATE_FORMAT(fecha, '%d/%m/%Y') as fecha_formato,
                     DATE_FORMAT(hora, '%H:%i') as hora_formato,
                     CONCAT(DATE_FORMAT(fecha, '%d/%m/%Y'), ' a las ', DATE_FORMAT(hora, '%H:%i')) as fecha_hora,
                     personas, ocasion, comentarios, estado,
                     DATE_FORMAT(created_at, '%d/%m/%Y %H:%i') as fecha_registro
                     FROM reservas 
                     ORDER BY fecha DESC, hora DESC";

            $resultado = $this->conexion->query($query);

            if (!$resultado) {
                throw new Exception("Error al obtener las reservas: " . $this->conexion->error);
            }

            $reservas = [];
            while ($fila = $resultado->fetch_assoc()) {
                $reservas[] = $fila;
            }

            return [
                'success' => true,
                'data' => $reservas,
                'total' => count($reservas)
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al obtener las reservas: ' . $e->getMessage()
            ];
        }
    }

    // Obtener reserva por ID
    public function obtenerPorId($id)
    {
        try {
            $query = "SELECT * FROM reservas WHERE id = ?";
            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $resultado = $stmt->get_result();

            if ($fila = $resultado->fetch_assoc()) {
                return [
                    'success' => true,
                    'data' => $fila
                ];
            }

            return [
                'success' => false,
                'message' => 'Reserva no encontrada'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    // Obtener reservas por estado
    public function obtenerPorEstado($estado)
    {
        try {
            $query = "SELECT id, codigo, nombre, email, telefono, 
                     DATE_FORMAT(fecha, '%d/%m/%Y') as fecha_formato,
                     DATE_FORMAT(hora, '%H:%i') as hora_formato,
                     CONCAT(DATE_FORMAT(fecha, '%d/%m/%Y'), ' a las ', DATE_FORMAT(hora, '%H:%i')) as fecha_hora,
                     personas, ocasion, comentarios, estado,
                     DATE_FORMAT(created_at, '%d/%m/%Y %H:%i') as fecha_registro
                     FROM reservas 
                     WHERE estado = ?
                     ORDER BY fecha DESC, hora DESC";

            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param("s", $estado);
            $stmt->execute();
            $resultado = $stmt->get_result();

            $reservas = [];
            while ($fila = $resultado->fetch_assoc()) {
                $reservas[] = $fila;
            }

            return [
                'success' => true,
                'data' => $reservas,
                'total' => count($reservas)
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    // Actualizar estado de reserva
    public function actualizarEstado($id, $estado)
    {
        try {
            // Validar que el estado sea válido
            $estadosValidos = ['pendiente', 'confirmada', 'cancelada'];
            if (!in_array($estado, $estadosValidos)) {
                throw new Exception("Estado no válido");
            }

            $query = "UPDATE reservas SET estado = ? WHERE id = ?";
            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param("si", $estado, $id);

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    return [
                        'success' => true,
                        'message' => 'Estado actualizado correctamente'
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'No se encontró la reserva o el estado ya era el mismo'
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Error al actualizar el estado'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    // Actualizar reserva completa
    public function actualizar($id, Reserva $reserva)
    {
        try {
            $query = "UPDATE reservas 
                     SET nombre = ?, email = ?, telefono = ?, fecha = ?, 
                         hora = ?, personas = ?, ocasion = ?, comentarios = ?
                     WHERE id = ?";

            $stmt = $this->conexion->prepare($query);

            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . $this->conexion->error);
            }

            $stmt->bind_param(
                "sssssissi",
                $reserva->nombre,
                $reserva->email,
                $reserva->telefono,
                $reserva->fecha,
                $reserva->hora,
                $reserva->personas,
                $reserva->ocasion,
                $reserva->comentarios,
                $id
            );

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Reserva actualizada exitosamente'
                ];
            } else {
                throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al actualizar la reserva: ' . $e->getMessage()
            ];
        }
    }

    // Eliminar reserva
    public function eliminar($id)
    {
        try {
            $query = "DELETE FROM reservas WHERE id = ?";
            $stmt = $this->conexion->prepare($query);
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    return [
                        'success' => true,
                        'message' => 'Reserva eliminada correctamente'
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'No se encontró la reserva'
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Error al eliminar la reserva'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    // Contar reservas por estado
    public function contarPorEstado()
    {
        try {
            $query = "SELECT estado, COUNT(*) as total 
                     FROM reservas 
                     GROUP BY estado";

            $resultado = $this->conexion->query($query);

            $estadisticas = [
                'pendiente' => 0,
                'confirmada' => 0,
                'cancelada' => 0,
                'total' => 0
            ];

            while ($fila = $resultado->fetch_assoc()) {
                $estadisticas[$fila['estado']] = (int)$fila['total'];
                $estadisticas['total'] += (int)$fila['total'];
            }

            return [
                'success' => true,
                'data' => $estadisticas
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
}
