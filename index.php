<?php

define('BASE_URL', '/Proyecto-Restaurante-Italiano/');
ini_set('display_errors', 1);

error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once 'Models/Conexion.php';

$controller = $_GET['controller'] ?? null;
$action     = $_GET['action'] ?? null;



switch ($controller) {

  case 'auth':
    require_once 'Controllers/AuthController.php';
    $auth = new AuthController($conexion);
    $auth->$action();
    exit;

  case 'reserva':
    require_once 'Controllers/ReservaController.php';
    $reserva = new ReservaController();
    $reserva->$action();
    exit;
}


?>



<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Ristorante Italini</title>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <!-- Nueva combinación de fuentes -->
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Mulish:wght@300;400;600&display=swap"
    rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&display=swap" rel="stylesheet">


  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="css/normalize.css">

  <script src="/Proyecto-Restaurante-Italiano/js/app.js" defer></script>



</head>

<body>
  <header class="encabezado">
    <div class="contenedor-navegacion">
      <div class="contenido-navegacion contenedor">
        <div class="logo">
          <h2>
            Ristorante <span class="verde">Ita</span><span>li</span><span class="rojo">ni</span>
          </h2>
        </div>
        <nav class="navegacion ocultar">
          <a href="#">Inicio</a>
          <a href="#nosotros">Sobre Nosotros</a>

          <a href="#menu">Menú</a>

          <a href="#chefs">Chef</a>
          <a href="#contacto">Contacto</a>
          <a href="index.php?controller=auth&action=login">Login</a>

        </nav>
        <div class="icono-menu"><span></span><span></span><span></span></div>
      </div>
    </div>
    <div class="contenido-header">
      <div class="contenedor-encabezado">
        <div class="texto-encabezado">
          <h2 class="titulo-hero">
            <span>¡</span><span>A</span><span>C</span><span>C</span><span>O</span><span>G</span>
            <span>L</span><span>I</span><span>E</span><span>N</span><span>Z</span><span>A</span><span>!</span>
          </h2>
          <a href="#menu" class="btn bordes">Il nostro menú</a>
        </div>
        <video autoplay loop muted>
          <source src="assets/bg_video.mp4">
        </video>
      </div>
    </div>
    </div>
  </header>
  <section id="nosotros" class="contenedor-nosotros contenedor">
    <div class="texto-nosotros">
      <p class="bienvenido">Bienvenido a!</p>
      <h1>Ristorante Italini</h1>
      <p>
        Donde Italia cobra vida en cada plato.
        En el corazón de la ciudad, Ristorante Italini no es solo un restaurante: es un viaje directo a las calles
        empedradas de Roma, a las colinas de la Toscana y a las costas de Amalfi. Aquí, la tradición italiana más
        auténtica se encuentra con la pasión contemporánea por la excelencia gastronómica.
        Cada mañana, nuestro chef y su brigada seleccionan los mejores ingredientes: tomates San Marzano recién
        llegados, mozzarella di bufala DOP que se derrite en la boca, prosciutto di Parma cortado a cuchillo, aceite de
        oliva virgen extra prensado en frío y pasta fresca elaborada en casa, segundo a segundo, como lo harían las
        nonnas en sus cocinas.
      </p>
      <a href="#contacto" class="btn btn-rojo">Contactar</a>
    </div>

    <div class="imagenes-nosotros">
      <div class="imagen1">
        <img src="assets/nosotros1.webp" alt="mujer comiendo pizza">
      </div>
      <div class="imagenes2">
        <img src="assets/nosotros2.webp" alt="mujer comiendo pizza">
        <img src="assets/nosotros3.webp" alt="plato con pasta">
      </div>
    </div>
  </section>


  </div>
  <section id="menu" class="menu contenedor">

    <h2 class="texto-platillos">Platillos populares</h2>

    <div class="filtro-categorias">
      <button class="btn btn-verde filtro-toggle">
        Categorías
      </button>

      <div class="botones-platillos ocultar">
        <button class="todos btn btn-verde">Todos</button>
        <button class="ensaladas btn btn-verde">Ensaladas</button>
        <button class="pasta btn btn-verde">Pasta</button>
        <button class="pizza btn btn-verde">Pizza</button>
        <button class="postres btn btn-verde">Postres</button>
      </div>
    </div>
    <div class="platillos">
      <div class="platillo" data-platillo="ensalada">
        <img src="assets/ensalada.webp" alt="Ensalada de Quinoa">
        <h2>Ensalada de Quinoa</h2>
        <p>Quinoa con tomates cherry, espinacas, aceitunas y vinagreta de limón.</p>
        <div class="precio">
          <p>$12.00</p>
          <button>Comprar</button>
        </div>
      </div>
      <div class="platillo" data-platillo="ensalada">
        <img src="assets/ensalada2.webp" alt="Ensalada Caprese">
        <h2>Ensalada Caprese</h2>
        <p>Tomate, mozzarella fresca y albahaca con reducción de balsámico.</p>
        <div class="precio">
          <p>$12.50</p>
          <button>Comprar</button>
        </div>
      </div>
      <div class="platillo" data-platillo="pasta">
        <img src="assets/pasta1.webp" alt="Penne al Pesto">
        <h2>Penne al Pesto</h2>
        <p>Pasta penne con salsa de pesto casera, piñones y queso parmesano.</p>
        <div class="precio">
          <p>$7.00</p>
          <button>Comprar</button>
        </div>
      </div>
      <div class="platillo" data-platillo="pasta">
        <img src="assets/pasta2.webp" alt="Spaghetti Carbonara">
        <h2>Spaghetti Carbonara</h2>
        <p>Spaghetti con panceta crujiente, huevo y queso pecorino.</p>
        <div class="precio">
          <p>$8.50</p>
          <button>Comprar</button>
        </div>
      </div>
      <div class="platillo" data-platillo="pizza">
        <img src="assets/pizza1.webp" alt="Pizza Prosciutto">
        <h2>Pizza Prosciutto</h2>
        <p>Masa fina con tomate San Marzano, mozzarella y prosciutto.</p>
        <div class="precio">
          <p>$18.00</p>
          <button>Comprar</button>
        </div>
      </div>
      <div class="platillo" data-platillo="pizza">
        <img src="assets/pizza2.webp" alt="Pizza Margherita">
        <h2>Pizza Margherita</h2>
        <p>Tomate, mozzarella fior di latte y albahaca fresca.</p>
        <div class="precio">
          <p>$19.50</p>
          <button>Comprar</button>
        </div>
      </div>
      <div class="platillo" data-platillo="postre">
        <img src="assets/postres1.webp" alt="Tiramisú">
        <h2>Tiramisú</h2>
        <p>Clásico tiramisú con capas de bizcocho, café y crema de mascarpone.</p>
        <div class="precio">
          <p>$5.00</p>
          <button>Comprar</button>
        </div>
      </div>
      <div class="platillo" data-platillo="postre">
        <img src="assets/postres2.webp" alt="Panna Cotta">
        <h2>Panna Cotta</h2>
        <p>Panna cotta de vainilla servida con coulis de frutos rojos.</p>
        <div class="precio">
          <p>$4.00</p>
          <button>Comprar</button>
        </div>
      </div>
    </div>
  </section>

  <section id="contacto" class="reservas contenedor">

    <div class="contenido-reservas">
      <div class="info-reservas">
        <h2>¡Prenota il tuo tavolo!</h2>
        <p class="descripcion-reservas">Reserva tu mesa y disfruta de la auténtica experiencia italiana. Nuestro equipo
          te espera con los mejores platillos de la casa.</p>
        <div class="detalles-contacto">
          <div class="detalle">
            <span class="icono">📍</span>
            <div>
              <h3>Ubicación</h3>
              <p>Via Roma 123, Ciudad</p>
            </div>
          </div>
          <div class="detalle">
            <span class="icono">📞</span>
            <div>
              <h3>Teléfono</h3>
              <p>+34 123 456 789</p>
            </div>
          </div>
          <div class="detalle">
            <span class="icono">🕒</span>
            <div>
              <h3>Horario</h3>
              <p>Lun-Dom: 12:00 - 23:00</p>
            </div>
          </div>
        </div>
      </div>
      <form class="formulario-reservas" id="formReservas">
        <h3>Formulario de Reserva</h3>
        <div class="campo">
          <label for="nombre">Nombre Completo</label>
          <input type="text" id="nombre" name="nombre" placeholder="Tu nombre" required>
        </div>
        <div class="campo">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" placeholder="tu@email.com" required>
        </div>
        <div class="campo">
          <label for="telefono">Teléfono</label>
          <input type="tel" id="telefono" name="telefono" placeholder="+34 123 456 789" required>
        </div>
        <div class="campo-grupo">
          <div class="campo">
            <label for="fecha">Fecha</label>
            <input type="date" id="fecha" name="fecha" required>
          </div>
          <div class="campo">
            <label for="hora">Hora</label>
            <input type="time" id="hora" name="hora" required>
          </div>
        </div>
        <div class="campo-grupo">
          <div class="campo">
            <label for="personas">Personas</label>
            <input type="number" id="personas" name="personas" min="1" max="20" placeholder="Ej: 4" required>
          </div>
          <div class="campo">
            <label for="ocasion">Ocasión</label>
            <select id="ocasion" name="ocasion">
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
          <label for="comentarios">Comentarios Especiales</label>
          <textarea id="comentarios" name="comentarios" rows="4"
            placeholder="Alergias, preferencias, peticiones especiales..."></textarea>
        </div>
        <button type="submit" class="btn btn-rojo btn-full">Confirmar Reserva</button>
      </form>
    </div>
  </section>

  <section id="chefs" class="chefs contenedor">
    <h2 class="titulo-chefs">I Nostri Chefcini</h2>
    <p class="descripcion-chefs">
      La passione, l’arte e la tradizione italiana nelle mani dei nostri maestri della cucina.
    </p>

    <div class="contenedor-chefs">

      <div class="card-chef">
        <img src="assets/chef1.jpg" alt="Chef Italiano 1">
        <h3>Chef Adam Garcia</h3>
        <p>Especialista en pasta fresca y recetas tradicionales romanas.</p>
      </div>

      <div class="card-chef">
        <img src="assets/chef2.jpg" alt="Chef Italiano 2">
        <h3>Chef Miguel Izquierdo</h3>
        <p>Maestro pizzaiolo experto en masa napolitana de fermentación lenta.</p>
      </div>

      <div class="card-chef">
        <img src="assets/chef3.jpg" alt="Chef Italiano 3">
        <h3>Chef Bryan Machado</h3>
        <p>Creador de los mejores postres italianos: tiramisú y panna cotta.</p>
      </div>

      <div class="card-chef">
        <img src="assets/chef4.jpg" alt="Chef Italiano 4">
        <h3>Chef Oscar Magallanes</h3>
        <p>Apasionado por los mariscos y la cocina mediterránea auténtica.</p>
      </div>

      <div class="card-chef">
        <img src="assets/chef5.jpg" alt="Chef Italiano 5">
        <h3>Chef Ariel Solorzano</h3>
        <p>Especialista en carnes, salsas y platos al estilo toscano.</p>
      </div>

    </div>
  </section>


  <footer class="footer">
    <div class="contenedor contenido-footer">
      <div class="footer-info">
        <h2>Ristorante <span class="verde">Ita</span><span>li</span><span class="rojo">ni</span></h2>
        <p>Sabores auténticos de Italia en cada bocado</p>
      </div>
      <div class="footer-links">
        <h3>Enlaces</h3>
        <a href="#nosotros">Sobre Nosotros</a>

        <a href="#menu">Menú</a>

        <a href="#">Reservas</a>
        <a href="#contacto">Contacto</a>

      </div>
      <div class="footer-contacto">
        <h3>Contacto</h3>
        <p>📍 Via Roma 123, Ciudad</p>
        <p>📞 +34 123 456 789</p>
        <p>✉️ info@ristoranteitalini.com</p>
      </div>
    </div>
    <div class="footer-copyright">
      <p>&copy; 2025 Ristorante Italini. Tutti i diritti riservati.</p>
    </div>
  </footer>


</body>

</html>