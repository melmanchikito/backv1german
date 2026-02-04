<?php

require_once __DIR__ . '/../Models/Conexion.php';

class AuthController
{
    private $conexion;

    public function __construct()
    {
        global $conexion;

        if (!$conexion) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "No hay conexión a la base de datos"
            ]);
            exit;
        }

        $this->conexion = $conexion;
    }

    # =========================
    # LOGIN API
    # =========================
    public function autenticar()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                "success" => false,
                "message" => "Método no permitido — usar POST"
            ]);
            exit;
        }

        $usuario  = $_POST['usuario'] ?? '';
        $password = $_POST['password'] ?? '';

        if (!$usuario || !$password) {
            echo json_encode([
                "success" => false,
                "message" => "Faltan credenciales"
            ]);
            exit;
        }

        try {

            $sql = "SELECT id, usuario, password, rol
                    FROM usuarios
                    WHERE usuario = :usuario
                    AND estado = true
                    LIMIT 1";

            $stmt = $this->conexion->prepare($sql);

            $stmt->execute([
                ":usuario" => $usuario
            ]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                echo json_encode([
                    "success" => false,
                    "message" => "Usuario no encontrado"
                ]);
                exit;
            }

            # ⚠️ si luego usas password_hash cambia a password_verify
            if ($password !== $user['password']) {
                echo json_encode([
                    "success" => false,
                    "message" => "Contraseña incorrecta"
                ]);
                exit;
            }

            # crear sesión
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $_SESSION['usuario'] = $user['usuario'];
            $_SESSION['rol']     = $user['rol'];
            $_SESSION['user_id'] = $user['id'];

            echo json_encode([
                "success" => true,
                "message" => "Login correcto",
                "usuario" => $user['usuario'],
                "rol" => $user['rol']
            ]);

            exit;
        } catch (Exception $e) {

            echo json_encode([
                "success" => false,
                "message" => "Error DB",
                "error" => $e->getMessage()
            ]);
            exit;
        }
    }

    # =========================
    # LOGOUT API
    # =========================
    public function logout()
    {
        header('Content-Type: application/json');

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_destroy();

        echo json_encode([
            "success" => true,
            "message" => "Sesión cerrada"
        ]);
        exit;
    }

    # =========================
    # CHECK SESSION (útil para admin)
    # =========================
    public function check()
    {
        header('Content-Type: application/json');

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['usuario'])) {
            echo json_encode([
                "logged" => false
            ]);
            exit;
        }

        echo json_encode([
            "logged" => true,
            "usuario" => $_SESSION['usuario'],
            "rol" => $_SESSION['rol']
        ]);
        exit;
    }
}
