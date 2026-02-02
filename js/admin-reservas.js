// Administración de Reservas
document.addEventListener('DOMContentLoaded', function() {
    let reservas = [];
    let filtroActual = 'todas';
    let busquedaActual = '';

    // Elementos del DOM
    const cuerpoTabla = document.getElementById('cuerpoTabla');
    const mensajeLoading = document.getElementById('mensajeLoading');
    const mensajeError = document.getElementById('mensajeError');
    const mensajeVacio = document.getElementById('mensajeVacio');
    const tablaReservas = document.getElementById('tablaReservas');
    const buscador = document.getElementById('buscarReserva');
    const botonesFiltro = document.querySelectorAll('.btn-filtro');
    
    // Estadísticas
    const totalReservas = document.getElementById('totalReservas');
    const totalPendientes = document.getElementById('totalPendientes');
    const totalConfirmadas = document.getElementById('totalConfirmadas');
    const totalCanceladas = document.getElementById('totalCanceladas');

    // Modal de comentarios
    const modal = document.getElementById('modalComentarios');
    const textoComentarios = document.getElementById('textoComentarios');

    // Modal de edición
    const modalEditar = document.getElementById('modalEditar');
    const formEditar = document.getElementById('formEditar');

    // Cargar reservas al iniciar
    cargarReservas();

    // Event Listeners
    botonesFiltro.forEach(btn => {
        btn.addEventListener('click', function() {
            botonesFiltro.forEach(b => b.classList.remove('activo'));
            this.classList.add('activo');
            filtroActual = this.dataset.filtro;
            filtrarReservas();
        });
    });

    buscador.addEventListener('input', function(e) {
        busquedaActual = e.target.value.toLowerCase();
        filtrarReservas();
    });

    // Event listener para el formulario de edición
    formEditar.addEventListener('submit', function(e) {
        e.preventDefault();
        actualizarReserva();
    });

    // También cerrar con tecla ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (!modal.classList.contains('ocultar')) {
                cerrarModalComentarios();
            }
            if (!modalEditar.classList.contains('ocultar')) {
                cerrarModalEditar();
            }
        }
    });

    // Función para cargar reservas
    async function cargarReservas() {
        try {
            const response = await fetch('../reservas.php?accion=listar');
            const resultado = await response.json();

            mensajeLoading.classList.add('ocultar');

            if (resultado.success) {
                reservas = resultado.data;
                actualizarEstadisticas();
                filtrarReservas();
            } else {
                mostrarError(resultado.message);
            }
        } catch (error) {
            mensajeLoading.classList.add('ocultar');
            mostrarError('Error al cargar las reservas: ' + error.message);
        }
    }

    // Función para filtrar reservas
    function filtrarReservas() {
        let reservasFiltradas = reservas;

        // Filtrar por estado
        if (filtroActual !== 'todas') {
            reservasFiltradas = reservasFiltradas.filter(r => r.estado === filtroActual);
        }

        // Filtrar por búsqueda
        if (busquedaActual) {
            reservasFiltradas = reservasFiltradas.filter(r => 
                r.nombre.toLowerCase().includes(busquedaActual) ||
                r.email.toLowerCase().includes(busquedaActual) ||
                r.codigo.toLowerCase().includes(busquedaActual)
            );
        }

        renderizarTabla(reservasFiltradas);
    }

    // Función para renderizar la tabla
    function renderizarTabla(datos) {
        cuerpoTabla.innerHTML = '';

        if (datos.length === 0) {
            tablaReservas.classList.add('ocultar');
            mensajeVacio.classList.remove('ocultar');
            return;
        }

        tablaReservas.classList.remove('ocultar');
        mensajeVacio.classList.add('ocultar');

        datos.forEach(reserva => {
            const fila = document.createElement('tr');
            
            const ocasionTexto = reserva.ocasion || 'No especificada';
            const comentariosTexto = reserva.comentarios || 'Sin comentarios';
            const comentariosCorto = comentariosTexto.length > 50 
                ? comentariosTexto.substring(0, 50) + '...' 
                : comentariosTexto;

            fila.innerHTML = `
                <td><strong>${reserva.codigo}</strong></td>
                <td>${reserva.nombre}</td>
                <td>${reserva.email}</td>
                <td>${reserva.telefono}</td>
                <td><strong>${reserva.fecha_hora}</strong></td>
                <td><strong>${reserva.personas}</strong></td>
                <td>${ocasionTexto}</td>
                <td>
                    ${comentariosTexto.length > 50 
                        ? `<span class="comentarios-corto">${comentariosCorto}</span>
                           <button class="btn-accion btn-ver" onclick="verComentarios('${reserva.id}')">Ver más</button>`
                        : comentariosTexto
                    }
                </td>
                <td>
                    <span class="badge-estado badge-${reserva.estado}">
                        ${reserva.estado.charAt(0).toUpperCase() + reserva.estado.slice(1)}
                    </span>
                </td>
                <td>
                    <div class="acciones-reserva">
                        <button class="btn-accion btn-ver" onclick="abrirModalEditar(${reserva.id})" title="Editar">
                            ✏️ Editar
                        </button>
                        ${reserva.estado !== 'confirmada' 
                            ? `<button class="btn-accion btn-confirmar" onclick="cambiarEstado(${reserva.id}, 'confirmada')">
                                Confirmar
                               </button>`
                            : ''
                        }
                        ${reserva.estado !== 'cancelada' 
                            ? `<button class="btn-accion btn-cancelar" onclick="cambiarEstado(${reserva.id}, 'cancelada')">
                                Cancelar
                               </button>`
                            : ''
                        }
                    </div>
                </td>
                <td style="text-align: center;">
                    <button class="btn-eliminar" onclick="eliminarReserva(${reserva.id})" title="Eliminar reserva">
                        ✖
                    </button>
                </td>
            `;

            cuerpoTabla.appendChild(fila);
        });
    }

    // Función para actualizar estadísticas
    function actualizarEstadisticas() {
        const pendientes = reservas.filter(r => r.estado === 'pendiente').length;
        const confirmadas = reservas.filter(r => r.estado === 'confirmada').length;
        const canceladas = reservas.filter(r => r.estado === 'cancelada').length;

        totalReservas.textContent = reservas.length;
        totalPendientes.textContent = pendientes;
        totalConfirmadas.textContent = confirmadas;
        totalCanceladas.textContent = canceladas;
    }

    // Función para cambiar estado (global para que sea accesible desde el HTML)
    window.cambiarEstado = async function(id, nuevoEstado) {
        if (!confirm(`¿Está seguro de cambiar el estado a "${nuevoEstado}"?`)) {
            return;
        }

        try {
            const formData = new FormData();
            formData.append('id', id);
            formData.append('estado', nuevoEstado);

            const response = await fetch('../reservas.php?accion=actualizar-estado', {
                method: 'POST',
                body: formData
            });

            const resultado = await response.json();

            if (resultado.success) {
                alert('Estado actualizado correctamente');
                cargarReservas();
            } else {
                alert('Error: ' + resultado.message);
            }
        } catch (error) {
            alert('Error al actualizar el estado: ' + error.message);
        }
    };

    // Función para ver comentarios completos
    window.verComentarios = function(id) {
        const reserva = reservas.find(r => r.id == id);
        if (reserva) {
            textoComentarios.textContent = reserva.comentarios || 'Sin comentarios';
            if (modal) {
                modal.classList.remove('ocultar');
                modal.style.display = 'flex';
            }
        }
    };

    // Función para cerrar modal de comentarios
    window.cerrarModalComentarios = function() {
        if (modal) {
            modal.classList.add('ocultar');
            modal.style.display = 'none';
        }
    };

    // Función para abrir modal de edición
    window.abrirModalEditar = async function(id) {
        const reserva = reservas.find(r => r.id == id);
        if (!reserva) {
            alert('Reserva no encontrada');
            return;
        }

        // Obtener datos completos de la reserva
        try {
            const response = await fetch(`../reservas.php?accion=obtener&id=${id}`);
            const resultado = await response.json();

            if (resultado.success) {
                const datos = resultado.data;
                
                // Llenar el formulario
                document.getElementById('edit-id').value = datos.id;
                document.getElementById('edit-codigo').value = datos.codigo;
                document.getElementById('edit-nombre').value = datos.nombre;
                document.getElementById('edit-email').value = datos.email;
                document.getElementById('edit-telefono').value = datos.telefono;
                document.getElementById('edit-fecha').value = datos.fecha;
                document.getElementById('edit-hora').value = datos.hora;
                document.getElementById('edit-personas').value = datos.personas;
                document.getElementById('edit-ocasion').value = datos.ocasion || '';
                document.getElementById('edit-comentarios').value = datos.comentarios || '';

                // Mostrar modal
                modalEditar.classList.remove('ocultar');
                modalEditar.style.display = 'flex';
            } else {
                alert('Error al obtener los datos: ' + resultado.message);
            }
        } catch (error) {
            alert('Error al cargar la reserva: ' + error.message);
        }
    };

    // Función para cerrar modal de edición
    window.cerrarModalEditar = function() {
        if (modalEditar) {
            modalEditar.classList.add('ocultar');
            modalEditar.style.display = 'none';
            formEditar.reset();
        }
    };

    // Función para actualizar reserva
    async function actualizarReserva() {
        try {
            const formData = new FormData(formEditar);

            const response = await fetch('../reservas.php?accion=actualizar', {
                method: 'POST',
                body: formData
            });

            const resultado = await response.json();

            if (resultado.success) {
                alert('Reserva actualizada correctamente');
                cerrarModalEditar();
                cargarReservas();
            } else {
                alert('Error al actualizar: ' + resultado.message);
            }
        } catch (error) {
            alert('Error al actualizar la reserva: ' + error.message);
        }
    }

    // Función para eliminar reserva
    window.eliminarReserva = async function(id) {
        const reserva = reservas.find(r => r.id == id);
        if (!reserva) {
            alert('Reserva no encontrada');
            return;
        }

        const confirmacion = confirm(
            `¿Está seguro de eliminar la reserva?\n\n` +
            `Código: ${reserva.codigo}\n` +
            `Cliente: ${reserva.nombre}\n` +
            `Fecha: ${reserva.fecha_hora}\n\n` +
            `Esta acción no se puede deshacer.`
        );

        if (!confirmacion) {
            return;
        }

        try {
            const formData = new FormData();
            formData.append('id', id);

            const response = await fetch('../reservas.php?accion=eliminar', {
                method: 'POST',
                body: formData
            });

            const resultado = await response.json();

            if (resultado.success) {
                alert('Reserva eliminada correctamente');
                cargarReservas();
            } else {
                alert('Error al eliminar: ' + resultado.message);
            }
        } catch (error) {
            alert('Error al eliminar la reserva: ' + error.message);
        }
    };

    // Función para mostrar errores
    function mostrarError(mensaje) {
        mensajeError.querySelector('p').textContent = mensaje;
        mensajeError.classList.remove('ocultar');
        tablaReservas.classList.add('ocultar');
    }
});
