// Selectores globales
const menu = document.querySelector(".icono-menu");
const navegacion = document.querySelector(".navegacion");
let errores = [];

// Inicialización única
document.addEventListener("DOMContentLoaded", () => {
  validaciones();
  eventos();
  eventosMenu();
  toggleFiltrosCategorias();
  eventoFormulario();
});

const validaciones = () => {
  const form = document.getElementById("formReservas");
  form.addEventListener("submit", function (e) {
    e.preventDefault();

    const nombre = document.getElementById("nombre").value.trim();
    if (nombre.length < 3 || !/^[a-zA-ZÁÉÍÓÚÑáéíóúñ ]+$/.test(nombre)) {
      errores.push(
        "Escribe un nombre válido (solo letras y mínimo 3 caracteres)."
      );
    }

    const email = document.getElementById("email").value.trim();
    const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!regexEmail.test(email)) {
      errores.push("Ingresa un correo electrónico válido.");
    }

    const telefono = document.getElementById("telefono").value.trim();
    if (!/^[0-9]{9,15}$/.test(telefono)) {
      errores.push(
        "Ingresa un teléfono válido (solo números, mínimo 9 dígitos)."
      );
    }

    const fechaInput = document.getElementById("fecha").value;
    const fechaSeleccionada = new Date(fechaInput);
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0); // evitar errores por hora

    if (!fechaInput || fechaSeleccionada < hoy) {
      errores.push("La fecha debe ser desde hoy en adelante.");
    }

    const hora = document.getElementById("hora").value;
    if (hora < "12:00" || hora > "23:00") {
      errores.push("Selecciona una hora entre 12:00 y 23:00.");
    }

    const personas = document.getElementById("personas").value;
    const personasNum = parseInt(personas);
    if (personas === "" || isNaN(personasNum) || personasNum < 1 || personasNum > 20) {
      errores.push("El número de personas debe ser entre 1 y 20.");
    }
  });
};

const eventos = () => {
  menu.addEventListener("click", abrirMenu);
};

const abrirMenu = () => {
  navegacion.classList.remove("ocultar");
  botonCerrar();
};

// Toggle de filtros de categorías
const toggleFiltrosCategorias = () => {
  const btnToggle = document.querySelector(".filtro-toggle");
  const contenedor = document.querySelector(".botones-platillos");

  if (!btnToggle || !contenedor) return;

  btnToggle.addEventListener("click", () => {
    contenedor.classList.toggle("ocultar");
  });
};

const botonCerrar = () => {
  const botonCerrar = document.createElement("p");
  const overlay = document.createElement("div");
  overlay.classList.add("filtro");
  const body = document.querySelector("body");

  if (document.querySelectorAll(".filtro").length > 0) return;

  body.appendChild(overlay);
  botonCerrar.textContent = "X";
  botonCerrar.classList.add("btn-cerrar");
  navegacion.appendChild(botonCerrar);
  cerrarMenu(botonCerrar, overlay);
};

const cerrarMenu = (boton, overlay) => {
  boton.addEventListener("click", () => {
    navegacion.classList.add("ocultar");
    overlay.remove();
    boton.remove();
  });
  overlay.onclick = () => {
    overlay.remove();
    navegacion.classList.add("ocultar");
    boton.remove();
  };
};

// Funcionalidad de filtrado de platillos
const eventosMenu = () => {
  const botones = document.querySelectorAll(".botones-platillos button");
  const platillos = document.querySelectorAll(".platillo");

  // Mapa de categorías para simplificar la lógica
  const categoriaMap = {
    todos: () => true,
    ensaladas: (tipo) => tipo === "ensalada",
    pasta: (tipo) => tipo === "pasta",
    pizza: (tipo) => tipo === "pizza",
    postres: (tipo) => tipo === "postre",
  };

  botones.forEach((boton) => {
    boton.addEventListener("click", () => {
      // Remover clase activo de todos los botones
      botones.forEach((btn) => btn.classList.remove("activo"));

      // Agregar clase activo al botón clickeado
      boton.classList.add("activo");

      const categoria = boton.classList[0];
      const filtro = categoriaMap[categoria];

      if (filtro) {
        platillos.forEach((platillo) => {
          const tipoPlatillo = platillo.dataset.platillo;

          if (filtro(tipoPlatillo)) {
            platillo.classList.remove("ocultar");
          } else {
            platillo.classList.add("ocultar");
          }
        });
      }
    });
  });

  // Activar el botón "Todos" por defecto
  const botonTodos = document.querySelector(".todos");
  if (botonTodos) {
    botonTodos.classList.add("activo");
  }
};

// Funcionalidad del formulario de reservas con envío a PHP
const eventoFormulario = () => {
  const formulario = document.querySelector("#formReservas");

  if (formulario) {
    formulario.addEventListener("submit", async (e) => {
      e.preventDefault();

      // Si hay errores de validación, detener
      if (errores.length > 0) {
        alert("Corrige los siguientes errores:\n\n" + errores.join("\n"));
        errores = [];
        return;
      }

      // Recopilar datos del formulario
      const formData = new FormData(formulario);
      
      // Deshabilitar botón de envío
      const btnSubmit = formulario.querySelector('button[type="submit"]');
      const textoOriginal = btnSubmit.textContent;
      btnSubmit.disabled = true;
      btnSubmit.textContent = 'Enviando...';

      try {
        // Enviar datos al servidor
        const response = await fetch('reservas.php?accion=crear', {
          method: 'POST',
          body: formData
        });

        const resultado = await response.json();

        if (resultado.success) {
          alert(
            `¡Gracias por tu reserva!\n\n` +
            `Tu código de reserva es: ${resultado.codigo}\n\n` +
            `Te contactaremos pronto para confirmar tu reserva.`
          );
          formulario.reset();
        } else {
          alert('Error al crear la reserva:\n' + resultado.message);
        }
      } catch (error) {
        alert('Error al procesar la reserva:\n' + error.message);
        console.error('Error:', error);
      } finally {
        // Rehabilitar botón
        btnSubmit.disabled = false;
        btnSubmit.textContent = textoOriginal;
      }
    });
  }
};
