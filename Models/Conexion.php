<?php

class Conexion
{

    public static function conectar()
    {
        $host = "aws-1-us-east-1.pooler.supabase.com";
        $port = "5432";
        $dbname = "postgres";

        // 👇 ESTE usuario es especial de supabase
        $user = "postgres.rkdsbimpukupujmptugp";

        // 👇 tu password real
        $password = "BGmelmanMM1978";

        try {
            $conexion = new PDO(
                "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require",
                $user,
                $password
            );

            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conexion;
        } catch (PDOException $e) {
            die("Error conexión: " . $e->getMessage());
        }
    }
}
