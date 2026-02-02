<?php
/**
 * Clase Reserva - Entidad/Modelo de datos
 * Representa la estructura de una reserva del restaurante
 */
class Reserva {
    // Propiedades públicas
    public $id;
    public $codigo;
    public $nombre;
    public $email;
    public $telefono;
    public $fecha;
    public $hora;
    public $personas;
    public $ocasion;
    public $comentarios;
    public $estado;
    public $created_at;
    
    /**
     * Constructor
     * @param array $datos - Array asociativo con los datos de la reserva
     */
    public function __construct($datos = []) {
        $this->id = isset($datos['id']) ? $datos['id'] : null;
        $this->codigo = isset($datos['codigo']) ? $datos['codigo'] : null;
        $this->nombre = isset($datos['nombre']) ? $datos['nombre'] : null;
        $this->email = isset($datos['email']) ? $datos['email'] : null;
        $this->telefono = isset($datos['telefono']) ? $datos['telefono'] : null;
        $this->fecha = isset($datos['fecha']) ? $datos['fecha'] : null;
        $this->hora = isset($datos['hora']) ? $datos['hora'] : null;
        $this->personas = isset($datos['personas']) ? $datos['personas'] : null;
        $this->ocasion = isset($datos['ocasion']) ? $datos['ocasion'] : '';
        $this->comentarios = isset($datos['comentarios']) ? $datos['comentarios'] : '';
        $this->estado = isset($datos['estado']) ? $datos['estado'] : 'pendiente';
        $this->created_at = isset($datos['created_at']) ? $datos['created_at'] : null;
    }
    
    /**
     * Convertir el objeto a array
     * @return array
     */
    public function toArray() {
        return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'nombre' => $this->nombre,
            'email' => $this->email,
            'telefono' => $this->telefono,
            'fecha' => $this->fecha,
            'hora' => $this->hora,
            'personas' => $this->personas,
            'ocasion' => $this->ocasion,
            'comentarios' => $this->comentarios,
            'estado' => $this->estado,
            'created_at' => $this->created_at
        ];
    }
    
    /**
     * Validar los datos de la reserva
     * @return array - ['valid' => bool, 'errors' => array]
     */
    public function validar() {
        $errores = [];
        
        if (empty($this->nombre) || strlen($this->nombre) < 3) {
            $errores[] = 'El nombre debe tener al menos 3 caracteres';
        }
        
        if (empty($this->email) || !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El email no es válido';
        }
        
        if (empty($this->telefono) || !preg_match('/^[0-9]{9,15}$/', $this->telefono)) {
            $errores[] = 'El teléfono debe tener entre 9 y 15 dígitos';
        }
        
        if (empty($this->fecha)) {
            $errores[] = 'La fecha es requerida';
        } else {
            $fechaReserva = strtotime($this->fecha);
            $fechaHoy = strtotime(date('Y-m-d'));
            if ($fechaReserva < $fechaHoy) {
                $errores[] = 'La fecha no puede ser anterior a hoy';
            }
        }
        
        if (empty($this->hora)) {
            $errores[] = 'La hora es requerida';
        }
        
        if (empty($this->personas) || $this->personas < 1) {
            $errores[] = 'Debe seleccionar al menos 1 persona';
        }
        
        $estadosValidos = ['pendiente', 'confirmada', 'cancelada'];
        if (!in_array($this->estado, $estadosValidos)) {
            $errores[] = 'Estado no válido';
        }
        
        return [
            'valid' => empty($errores),
            'errors' => $errores
        ];
    }
    
    /**
     * Obtener representación en string
     * @return string
     */
    public function __toString() {
        return sprintf(
            "Reserva #%s - %s (%s) - %s personas el %s a las %s - Estado: %s",
            $this->codigo ?? 'N/A',
            $this->nombre,
            $this->email,
            $this->personas,
            $this->fecha,
            $this->hora,
            $this->estado
        );
    }
}
?>
