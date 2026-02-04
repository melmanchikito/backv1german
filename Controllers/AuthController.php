<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


require_once __DIR__ . '/../Models/Conexion.php';

class AuthController
{
    private $conexion;

    public function __construct()
    {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function login()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        require_once __DIR__ . '/../Views/login.php';
    }

    public function autenticar()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $usuario  = $_POST['usuario'] ?? '';
        $password = $_POST['password'] ?? '';

        $sql = "SELECT * FROM usuarios WHERE usuario = ? AND estado = 1 LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado && $resultado->num_rows === 1) {
            $user = $resultado->fetch_assoc();

            if ($password === $user['password']) {

                $_SESSION['usuario'] = $user['usuario'];
                $_SESSION['rol']     = $user['rol'];

                // ✅ REDIRECCIÓN CORRECTA
                header("Location: /Proyecto-Restaurante-Italiano/Views/admin-reservas.php");
                exit;
            }
        }

        // ❌ login incorrecto
        $_SESSION['error'] = "Usuario o contraseña incorrectos";
        header("Location: index.php?controller=auth&action=login");
        exit;
    }


    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_destroy();
        header("Location: index.php");
        exit;
    }
}
