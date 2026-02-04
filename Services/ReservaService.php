<?php
require_once __DIR__ . '/../Models/Reserva.php';

class ReservaService
{
    private $conexion;

    public function __construct($db)
    {
        $this->conexion = $db;
    }

    /* =========================
       CREAR
    ========================= */

    public function crear(Reserva $reserva)
    {
        try {

            $sql = "INSERT INTO reservas
            (nombre,email,telefono,fecha,hora,personas,ocasion,comentarios,estado)
            VALUES (?,?,?,?,?,?,?,?,?)";

            $stmt = $this->conexion->prepare($sql);

            $stmt->execute([
                $reserva->nombre,
                $reserva->email,
                $reserva->telefono,
                $reserva->fecha,
                $reserva->hora,
                $reserva->personas,
                $reserva->ocasion,
                $reserva->comentarios,
                $reserva->estado
            ]);

            // PostgreSQL last insert id
            $id = $this->conexion->lastInsertId();

            return [
                "success" => true,
                "message" => "Reserva creada",
                "id" => $id
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    /* =========================
       LISTAR TODAS
    ========================= */

    public function obtenerTodas()
    {
        try {

            $sql = "SELECT * FROM reservas
                    ORDER BY fecha DESC, hora DESC";

            $stmt = $this->conexion->query($sql);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                "success" => true,
                "data" => $data,
                "total" => count($data)
            ];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    /* =========================
       POR ID
    ========================= */

    public function obtenerPorId($id)
    {
        $stmt = $this->conexion->prepare(
            "SELECT * FROM reservas WHERE id=?"
        );

        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            return ["success" => true, "data" => $data];
        }

        return ["success" => false, "message" => "No encontrada"];
    }

    /* =========================
       POR ESTADO
    ========================= */

    public function obtenerPorEstado($estado)
    {
        $stmt = $this->conexion->prepare(
            "SELECT * FROM reservas WHERE estado=? ORDER BY fecha DESC"
        );

        $stmt->execute([$estado]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            "success" => true,
            "data" => $data,
            "total" => count($data)
        ];
    }

    /* =========================
       ACTUALIZAR ESTADO
    ========================= */

    public function actualizarEstado($id, $estado)
    {
        $stmt = $this->conexion->prepare(
            "UPDATE reservas SET estado=? WHERE id=?"
        );

        $stmt->execute([$estado, $id]);

        return [
            "success" => true,
            "rows" => $stmt->rowCount()
        ];
    }

    /* =========================
       ACTUALIZAR COMPLETA
    ========================= */

    public function actualizar($id, Reserva $r)
    {
        $sql = "UPDATE reservas SET
            nombre=?,
            email=?,
            telefono=?,
            fecha=?,
            hora=?,
            personas=?,
            ocasion=?,
            comentarios=?
            WHERE id=?";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([
            $r->nombre,
            $r->email,
            $r->telefono,
            $r->fecha,
            $r->hora,
            $r->personas,
            $r->ocasion,
            $r->comentarios,
            $id
        ]);

        return ["success" => true];
    }

    /* =========================
       ELIMINAR
    ========================= */

    public function eliminar($id)
    {
        $stmt = $this->conexion->prepare(
            "DELETE FROM reservas WHERE id=?"
        );

        $stmt->execute([$id]);

        return [
            "success" => true,
            "rows" => $stmt->rowCount()
        ];
    }

    /* =========================
       ESTADISTICAS
    ========================= */

    public function contarPorEstado()
    {
        $sql = "SELECT estado, COUNT(*) total
                FROM reservas
                GROUP BY estado";

        $stmt = $this->conexion->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $out = [
            "pendiente" => 0,
            "confirmada" => 0,
            "cancelada" => 0,
            "total" => 0
        ];

        foreach ($rows as $r) {
            $out[$r["estado"]] = (int)$r["total"];
            $out["total"] += (int)$r["total"];
        }

        return ["success" => true, "data" => $out];
    }
}
