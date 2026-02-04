<?php
session_start();

if (!isset($_SESSION['usuario'])) {
  header("Location: index.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Administración de Reservas - Ristorante Italini</title>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Mulish:wght@300;400;600&display=swap"
    rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="../css/normalize.css" />
  <link rel="stylesheet" href="../css/styles.css" />
  <link rel="stylesheet" href="../css/admin-reservas.css" />
</head>

<body>
  <header class="admin-header">
    <div class="contenedor">
      <div class="admin-nav">
        <div class="logo">
          <h2>
            Ristorante <span class="verde">Ita</span><span>li</span><span class="rojo">ni</span>
          </h2>
        </div>
        <nav class="admin-links">
          <a href="/Proyecto-Restaurante-Italiano/index.php">Volver al sitio</a>

        </nav>
      </div>
    </div>
  </header>

  <main class="admin-main">
    <div class="contenedor">
      <div class="admin-titulo">
        <h1>Gestione delle Prenotazioni</h1>
        <p class="subtitulo">Administración de reservas del restaurante</p>
      </div>

      <div class="filtros-reservas">
        <div class="filtro-grupo">
          <button class="btn btn-filtro activo" data-filtro="todas">Todas</button>
          <button class="btn btn-filtro" data-filtro="pendiente">Pendientes</button>
          <button class="btn btn-filtro" data-filtro="confirmada">Confirmadas</button>
          <button class="btn btn-filtro" data-filtro="cancelada">Canceladas</button>
        </div>
        <div class="busqueda-reservas">
          <input type="text" id="buscarReserva" placeholder="Buscar por nombre, email o código...">
        </div>
      </div>

      <div class="tabla-container">
        <div id="mensajeLoading" class="mensaje-loading">
          <p>Cargando reservas...</p>
        </div>

        <div id="mensajeError" class="mensaje-error ocultar">
          <p></p>
        </div>

        <table class="tabla-reservas" id="tablaReservas">
          <thead>
            <tr>
              <th>Código</th>
              <th>Nombre</th>
              <th>Email</th>
              <th>Teléfono</th>
              <th>Fecha y Hora</th>
              <th>Personas</th>
              <th>Ocasión</th>
              <th>Comentarios</th>
              <th>Estado</th>
              <th>Acciones</th>
              <th>Eliminar</th>
            </tr>
          </thead>
          <tbody id="cuerpoTabla">
            <!-- Contenido dinámico -->
          </tbody>
        </table>

        <div id="mensajeVacio" class="mensaje-vacio ocultar">
          <p>No hay reservas para mostrar</p>
        </div>
      </div>

      <div class="estadisticas-resumen">
        <div class="stat-card">
          <span class="stat-numero" id="totalReservas">0</span>
          <span class="stat-label">Total Reservas</span>
        </div>
        <div class="stat-card verde">
          <span class="stat-numero" id="totalPendientes">0</span>
          <span class="stat-label">Pendientes</span>
        </div>
        <div class="stat-card confirmada">
          <span class="stat-numero" id="totalConfirmadas">0</span>
          <span class="stat-label">Confirmadas</span>
        </div>
        <div class="stat-card cancelada">
          <span class="stat-numero" id="totalCanceladas">0</span>
          <span class="stat-label">Canceladas</span>
        </div>
      </div>
    </div>
  </main>

  <footer class="admin-footer">
    <div class="contenedor">
      <p>&copy; 2025 Ristorante Italini. Panel de Administración.</p>
    </div>
  </footer>

  <!-- Modal para ver comentarios completos -->
  <div id="modalComentarios" class="modal ocultar">
    <div class="modal-contenido">
      <span class="btn-cerrar-modal" onclick="cerrarModalComentarios()">&times;</span>
      <h3>Comentarios de la Reserva</h3>
      <p id="textoComentarios"></p>
    </div>
  </div>

  <!-- Modal para editar reserva -->
  <div id="modalEditar" class="modal ocultar">
    <div class="modal-contenido-grande">
      <span class="btn-cerrar-modal" onclick="cerrarModalEditar()">&times;</span>
      <h3>Editar Reserva</h3>
      <form id="formEditar" class="form-editar">
        <input type="hidden" id="edit-id" name="id">

        <div class="campo-readonly">
          <label>Código de Reserva</label>
          <input type="text" id="edit-codigo" readonly>
        </div>

        <div class="campo">
          <label for="edit-nombre">Nombre Completo *</label>
          <input type="text" id="edit-nombre" name="nombre" required>
        </div>

        <div class="campo">
          <label for="edit-email">Email *</label>
          <input type="email" id="edit-email" name="email" required>
        </div>

        <div class="campo">
          <label for="edit-telefono">Teléfono *</label>
          <input type="tel" id="edit-telefono" name="telefono" required>
        </div>

        <div class="campo-grupo">
          <div class="campo">
            <label for="edit-fecha">Fecha *</label>
            <input type="date" id="edit-fecha" name="fecha" required>
          </div>
          <div class="campo">
            <label for="edit-hora">Hora *</label>
            <input type="time" id="edit-hora" name="hora" required>
          </div>
        </div>

        <div class="campo-grupo">
          <div class="campo">
            <label for="edit-personas">Personas *</label>
            <input type="number" id="edit-personas" name="personas" min="1" max="20" required>
          </div>
          <div class="campo">
            <label for="edit-ocasion">Ocasión</label>
            <select id="edit-ocasion" name="ocasion">
              <option value="">Seleccionar</option>
              <option value="casual">Casual</option>
              <option value="cumpleaños">Cumpleaños</option>
              <option value="aniversario">Aniversario</option>
              <option value="negocios">Negocios</option>
              <option value="otro">Otro</option>
            </select>
          </div>
        </div>

        <div class="campo">
          <label for="edit-comentarios">Comentarios Especiales</label>
          <textarea id="edit-comentarios" name="comentarios" rows="4"></textarea>
        </div>

        <div class="botones-modal">
          <button type="button" class="btn btn-cancelar-modal" onclick="cerrarModalEditar()">Cancelar</button>
          <button type="submit" class="btn btn-guardar-modal">Guardar Cambios</button>
        </div>
      </form>
    </div>
  </div>

  <script src="../js/admin-reservas.js"></script>
</body>

</html>