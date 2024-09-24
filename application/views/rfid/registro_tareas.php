<?php $this->load->view('templates/header'); ?>
<style>
    .pagination {
        display: flex;
        justify-content: center;
        margin: 20px 0;
    }
</style>
<div class="col py-3">
    <h2 class="text-center">Panel de tareas con RFID</h2>
    <hr>

    <div class="row">
        <div class="col-md-6">
            <!-- Botón para abrir el modal de nueva solicitud -->
            <button type="button" class="btn mb-3 bg-primary text-white" data-bs-toggle="modal" data-bs-target="#nuevaSolicitudModal">
                <i class="bi bi-clipboard2-plus"></i> Nueva Solicitud
            </button>
        </div>
    </div>

    <!-- Formularios en línea -->
    <div class="row">
        <!-- Formulario de búsqueda -->
        <form id="formBuscarSolicitudes" class="col-lg-6 col-md-12 d-flex align-items-end mb-4">
            <div class="input-group w-100">
                <input type="text" class="form-control" id="buscarTexto" placeholder="ID de tarea, Ubicacion o Operario">
                <button class="btn btn-primary" type="submit">Buscar</button>
            </div>
        </form>
    </div>

    <!-- Tabla para mostrar las solicitudes -->
    <div class="table-responsive">
        <table class="table table-striped table-responsive">
            <thead class="bg-primary text-white">
                <tr>
                    <th>ID</th>
                    <th>Descripción</th>
                    <th>Tipo</th>
                    <th>Total Equipos</th>
                    <th>Fecha de Solicitud</th>
                    <th>Área de Solicitud</th>
                    <th>Tec. Asignado</th>
                    <th>Estado</th>
                    <th>Accion</th>

                </tr>
            </thead>
            <tbody id="tareasCreadas">

            </tbody>
        </table>
    </div>

    <!-- Controles de paginación -->
    <div class="pagination-container">
        <ul class="pagination" id="paginacion"></ul>
    </div>
</div>




<!-- Modal para agregar nueva solicitud -->
<div class="modal fade" id="nuevaSolicitudModal" tabindex="-1" aria-labelledby="nuevaSolicitudModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="nuevaSolicitudModalLabel">Nueva Tarea</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formNuevaTareaLectura">
                    <div class="mb-3">
                        <label for="ubicacion" class="form-label">Unidad</label>
                        <select id="ubicacion" name="unidad" class="form-control" required>
                            <option value="" disabled selected>Seleccionar ubicación</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tipo_tarea" class="form-label">Tipo de Tarea</label>
                        <select id="tipo_tarea" name="tipo_tarea" class="form-control" required>
                            <option value="Control">Control</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción del Problema</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Enviar Solicitud</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal para asignar técnico -->

<div class="modal fade" id="nuevaAsignacion" tabindex="-1" aria-labelledby="nuevaAsignacionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="nuevaAsignacionLabel">Asignar Operario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formNuevaAsignacion">
                    <input type="hidden" id="solicitudId" name="solicitud_id">
                    <div class="mb-3">
                        <label for="operarios" class="form-label">Operario</label>
                        <select id="operarios" name="operarios" class="form-control" required>
                            <option value="" disabled selected>Seleccionar Operario</option>
                            <!-- Opciones del técnico se llenarán aquí -->
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Asignar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Definir paginaActual en el alcance global
    let paginaActual = 1;

    // Manejar envío del formulario con AJAX
    $('#formNuevaTareaLectura').on('submit', function(e) {
        e.preventDefault(); // Prevenir el envío normal del formulario

        $.ajax({
            url: "<?php echo site_url('crear_tarea_lectura'); ?>",
            type: "POST",
            data: $(this).serialize(), // Serializar los datos del formulario
            dataType: "json",
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        title: 'Éxito',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    }).then(function() {
                        $('#nuevaSolicitudModal').modal('hide');
                        // Recargar la tabla de solicitudes
                        cargarSolicitudes(paginaActual);
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: response.message,
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    title: 'Error',
                    text: 'Hubo un problema al procesar la solicitud.',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            }
        });
    });

    // Función para cargar la lista de técnicos desde el servidor
    function cargaroperarios() {
        $.ajax({
            url: "<?php echo site_url('obtener_operarios'); ?>", // URL del endpoint para obtener técnicos
            type: "GET", // Método HTTP GET
            dataType: "json", // Tipo de datos esperado en la respuesta
            success: function(response) {
                var opciones = '<option value="" disabled selected>Seleccionar Operario</option>';
                // Crear las opciones del select a partir de la respuesta
                $.each(response.operarios, function(key, tecnico) {
                    opciones += '<option value="' + tecnico.id + '">' + tecnico.nombre + '</option>';
                });
                $('#operarios').html(opciones); // Insertar las opciones en el select
            },
            error: function(xhr, status, error) {
                console.error("Error al cargar los técnicos:", status, error);
                $("#operarios").html('<option value="">Error al cargar Operario</option>'); // Mostrar mensaje de error
            }
        });
    }

    // Función para cargar ubicaciones
    function cargarUbicaciones() {
        $.ajax({
            url: "<?php echo site_url('obtener_ubicaciones_select'); ?>",
            type: "GET",
            dataType: "json",

            success: function(response) {
                var opciones = '<option value="" disabled selected>Seleccionar ubicación</option>';
                $.each(response.ubicaciones, function(key, ubicacion) {
                    opciones += '<option value="' + ubicacion.id + '">' + ubicacion.nombre + '</option>';
                });
                $('#ubicacion').html(opciones);
            },
            error: function(xhr, status, error) {
                console.error("Error al cargar las ubicaciones:", status, error);
                $("#ubicacion").html('<option value="">Error al cargar ubicaciones</option>');
            }
        });
    }

    // Definir la función cargarSolicitudes fuera del $(document).ready para acceso global
    function cargarSolicitudes(pagina = 1, cantidad = 5, filtro = '') {
        var cantidadPorPagina = 5; // Definir una variable para la cantidad de registros por página

        $.ajax({
            url: "<?php echo site_url('tareas_asignadas_obtener'); ?>",
            type: "POST",
            data: {
                pagina: pagina,
                cantidad: cantidadPorPagina, // Usar la variable definida aquí
                filtro: filtro // Parámetro de búsqueda
            },
            dataType: "json",
            success: function(response) {
                var hayPendientes = false; // Variable para detectar si hay algún registro con estado 3
                var filas = "";

                // Crear las filas dinámicamente con los datos de la tarea de lectura
                $.each(response.tareas, function(index, item) {
                    // Si el estado es 3, marcamos que hay registros pendientes
                    if (item.estado == 3 && !hayPendientes) {
                        hayPendientes = true;
                    }

                    var estiloFila = (item.estado == 3) ? 'style="background-color: #E7EDFF; font-weight: bold;"' : '';

                    filas += '<tr ' + estiloFila + '>' +
                        '<td>' + item.id + '</td>' +
                        '<td>' + item.descripcion_tarea + '</td>' +
                        '<td>' + item.tipo_tarea + '</td>' +
                        '<td>' + item.total_equipos + '</td>' +
                        '<td>' + item.feccre + '</td>' +
                        '<td>' + item.nombre_ubicacion + '</td>' +
                        '<td style="background-color: ' + (item.nombre_operario ? '' : '#DF5562') + '; color: ' + (item.nombre_operario ? '' : '#721c24') + ';">' +
                        (item.nombre_operario ? item.nombre_operario : '') +
                        '</td>' +
                        '<td><center><i class="bi ' +
                        (item.estado == 1 ? 'fs-4 bi-send-fill' :
                            item.estado == 2 ? 'fs-4 bi-pc-display-horizontal' :
                            item.estado == 3 ? 'fs-4 bi-check-circle' :
                            item.estado == 4 ? 'fs-4 bi-check-circle-fill' :
                            'fs-4 bi-question-circle') +
                        '" style="color: ' +
                        (item.estado == 1 ? '#1F3BB6' :
                            item.estado == 2 ? '#0a1039' :
                            item.estado == 3 ? '#2aa932' :
                            item.estado == 4 ? '#05AD30' :
                            '#000') + '; cursor: ' +
                        (item.estado == 1 || item.estado == 2 || item.estado == 3 ? 'pointer' : 'default') + ';" ' +
                        'title="' + (item.estado == 1 ? 'Solicitado' :
                            item.estado == 2 ? 'En proceso' :
                            item.estado == 3 ? 'Por confirmar' :
                            item.estado == 4 ? 'Completado' :
                            'Desconocido') + '"></i></center></td>' +
                        '<td><center>' +
                        '<div style="display: flex; gap: 5px; justify-content: center;">' +
                        '<button class="btn btn-success btn-sm nuevaAsignacion" ' +
                        'data-bs-toggle="modal" data-bs-target="#nuevaAsignacion" ' +
                        'data-id="' + item.id + '" ' +
                        'data-nombre="' + item.descripcion_tarea + '" ' +
                        (item.estado == 3 || item.estado == 4 ? 'disabled' : '') + '>Asignar</button>' +
                        '<button class="btn btn-danger btn-sm reportePDF ' +
                        (item.estado == 3 || item.estado == 4 ? '' : 'disabled') + '" ' +
                        'data-id="' + item.id + '">' +
                        '<i class="bi bi-file-earmark-pdf"></i>' +
                        '</button>' +
                        '<button class="btn btn-primary btn-sm confirmar ' +
                        (item.estado == 3 ? '' : 'disabled') + '" ' +
                        'title="Confirmar" data-id="' + item.id + '" data-ubicacion_tarea="' + item.nombre_ubicacion + '" data-operario_id="' + item.operario + '">' +
                        '<i class="bi bi-check-circle"></i>' +
                        '</button>' +
                        '</div>' +
                        '</center></td>' +
                        '</tr>';
                });

                // Mostrar la alerta si hay registros pendientes, pero solo una vez
                if (hayPendientes) {
                    Swal.fire({
                        title: 'Pendientes de Confirmación',
                        text: 'Existen registros a la espera de ser confirmados.',
                        icon: 'warning',
                        confirmButtonText: 'Entendido'
                    });
                }


                $("#tareasCreadas").html(filas);

                // Paginación
                var totalregistros = response.totalregistros;
                var numerolinks = Math.ceil(totalregistros / cantidadPorPagina); // Usar la misma variable aquí
                var paginador = "<ul class='pagination'>";

                // Botón "Primera" y "Anterior"
                if (pagina > 1) {
                    paginador += "<li class='page-item'><a class='page-link' href='#' data-pagina='1'>Primera</a></li>";
                    paginador += "<li class='page-item'><a class='page-link' href='#' data-pagina='" + (pagina - 1) + "'>Anterior</a></li>";
                } else {
                    paginador += "<li class='page-item disabled'><a class='page-link'>Primera</a></li>";
                    paginador += "<li class='page-item disabled'><a class='page-link'>Anterior</a></li>";
                }

                // Rango de páginas a mostrar
                var maxLinksVisible = 5;
                var startPage = Math.max(1, pagina - Math.floor(maxLinksVisible / 2));
                var endPage = Math.min(numerolinks, pagina + Math.floor(maxLinksVisible / 2));

                // Añadir "..." si es necesario antes del primer enlace visible
                if (startPage > 1) {
                    paginador += "<li class='page-item'><a class='page-link' href='#' data-pagina='1'>1</a></li>";
                    if (startPage > 2) {
                        paginador += "<li class='page-item disabled'><a class='page-link'>...</a></li>";
                    }
                }

                // Enlaces de páginas visibles
                for (var i = startPage; i <= endPage; i++) {
                    if (i == pagina) {
                        paginador += "<li class='page-item active'><a class='page-link' href='#'>" + i + "</a></li>";
                    } else {
                        paginador += "<li class='page-item'><a class='page-link' href='#' data-pagina='" + i + "'>" + i + "</a></li>";
                    }
                }

                // Añadir "..." si es necesario después del último enlace visible
                if (endPage < numerolinks) {
                    if (endPage < numerolinks - 1) {
                        paginador += "<li class='page-item disabled'><a class='page-link'>...</a></li>";
                    }
                    paginador += "<li class='page-item'><a class='page-link' href='#' data-pagina='" + numerolinks + "'>" + numerolinks + "</a></li>";
                }

                // Botón "Siguiente" y "Última"
                if (pagina < numerolinks) {
                    paginador += "<li class='page-item'><a class='page-link' href='#' data-pagina='" + (pagina + 1) + "'>Siguiente</a></li>";
                    paginador += "<li class='page-item'><a class='page-link' href='#' data-pagina='" + numerolinks + "'>Última</a></li>";
                } else {
                    paginador += "<li class='page-item disabled'><a class='page-link'>Siguiente</a></li>";
                    paginador += "<li class='page-item disabled'><a class='page-link'>Última</a></li>";
                }

                paginador += "</ul>";
                $("#paginacion").html(paginador);

                // Delegar eventos para los enlaces de paginación
                $("#paginacion").off("click", "a.page-link").on("click", "a.page-link", function(e) {
                    e.preventDefault();
                    var newPage = $(this).data("pagina");
                    if (newPage) {
                        cargarSolicitudes(newPage); // Cargar la nueva página
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error("Error en la solicitud:", status, error);
            }
        });
    }



    $(document).ready(function() {
        /*

        Swal.fire({
            title: "Bienvenido al modulo de tareas RFID.",
            width: 300,
            padding: "3em",
            color: "#716add",
            background: "#fff url(/images/trees.png)",
            backdrop: `
                    rgba(0,0,123,0.4)
                    url("assets/imagenes/gving-crypto.gif")
                    left top
                    no-repeat
                `
        });

*/
        // Cargar ubicaciones cuando se abre el modal
        $('#nuevaSolicitudModal').on('show.bs.modal', function() {
            cargarUbicaciones();
        });


        // Manejar eventos de clic en la paginación
        $(document).on('click', '.page-link', function(e) {
            e.preventDefault();
            var pagina = $(this).data('pagina');
            if (pagina) {
                paginaActual = pagina;
                cargarSolicitudes(paginaActual);
            }
        });

        // Manejar inserción de nuevo registro y recargar la tabla
        $('#insertarSolicitud').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: "<?php echo site_url('tareas_lectura_insertar'); ?>",
                type: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    Swal.fire({
                        title: 'Éxito',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    }).then(function() {
                        $('#nuevaSolicitudModal').modal('hide');
                        cargarSolicitudes(paginaActual);
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error en la inserción:", status, error);
                }
            });
        });

        // Manejar clic en el botón "Asignar" en la tabla de solicitudes
        $(document).on('click', '.nuevaAsignacion', function() {
            var solicitudId = $(this).data('id');
            var equipoNombre = $(this).data('nombre');

            // Rellenar los campos ocultos del formulario en el modal
            $('#solicitudId').val(solicitudId);

            // Opcional: Si quieres mostrar el nombre del equipo en algún lugar del modal
            $('#nuevaAsignacionLabel').text('Asignar Técnico para: ' + equipoNombre);

            // Cargar la lista de técnicos en el select del modal
            cargaroperarios();
        });

        // Manejar el envío del formulario para asignar el técnico
        $('#formNuevaAsignacion').on('submit', function(e) {
            e.preventDefault(); // Evitar el comportamiento predeterminado del formulario

            var solicitudId = $('#solicitudId').val(); // Obtener el ID de la solicitud
            var operarioId = $('#operarios').val(); // Obtener el ID del técnico seleccionado

            // Verificar que se haya seleccionado un técnico
            if (!operarioId) {
                alert('Por favor, seleccione un técnico.');
                return;
            }

            // Enviar la solicitud de asignación de técnico
            $.ajax({
                url: "<?php echo site_url('asignar_operario'); ?>", // URL del endpoint para asignar el técnico
                type: "POST", // Método HTTP POST
                data: {
                    solicitud_id: solicitudId,
                    operario_id: operarioId
                },
                dataType: "json", // Tipo de datos esperado en la respuesta
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: response.message,
                            confirmButtonText: 'Aceptar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $('#nuevaAsignacion').modal('hide'); // Cerrar el modal
                                cargarSolicitudes(paginaActual); // Recargar la lista de solicitudes
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                            confirmButtonText: 'Aceptar'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error al asignar técnico:", status, error);
                    alert('Error al asignar el técnico. Inténtalo de nuevo.'); // Mostrar mensaje de error
                }
            });
        });


        //confirmar lectura
        $(document).on('click', '.confirmar', function() {
            var solicitudId = $(this).data('id');
            var ubicacion_tarea = $(this).data('ubicacion_tarea');
            var operario_id = $(this).data('operario_id');
            console.log(" ", solicitudId);
            console.log(" ", operario_id);

            // Llamada AJAX para confirmar la solicitud
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Estás a punto de confirmar esta tarea.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, confirmar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "<?php echo site_url('confirmar_tarea_lectura_admin'); ?>", // Endpoint para confirmar
                        type: "POST",
                        data: {
                            solicitud_id: solicitudId,
                            ubicacion: ubicacion_tarea,
                            operario_id: operario_id
                        },
                        dataType: "json",
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire(
                                    '¡Confirmado!',
                                    response.message,
                                    'success'
                                );
                                // Recargar la tabla o actualizar la página si es necesario
                                cargarSolicitudes(paginaActual); // Si tienes una función para recargar la tabla
                            } else {
                                Swal.fire(
                                    'Error',
                                    response.message,
                                    'error'
                                );
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire(
                                'Error',
                                'Hubo un problema al confirmar la solicitud.',
                                'error'
                            );
                        }
                    });
                }
            });
        });

        $('#formBuscarSolicitudes').on('submit', function(e) {
            e.preventDefault(); // Prevenir el comportamiento por defecto del formulario
            var filtro = $('#buscarTexto').val(); // Obtener el valor del campo de búsqueda
            cargarSolicitudes(1, 5, filtro); // Cargar las solicitudes con el filtro
        });


        // Cargar solicitudes iniciales cuando se carga la página
        cargarSolicitudes(paginaActual);

        // Manejar eventos de clic en la paginación
        $(document).on('click', '.page-link', function(e) {
            e.preventDefault(); // Evitar el comportamiento predeterminado del enlace
            var pagina = $(this).data('pagina');
            if (pagina) {
                paginaActual = pagina; // Actualizar la página actual
                cargarSolicitudes(paginaActual); // Recargar las solicitudes para la nueva página
            }
        });

    });
</script>

<script>
    $(document).on('click', '.reportePDF', function() {
        var tareaId = $(this).data('id'); // Capturar el ID de la tarea

        // SweetAlert de confirmación
        Swal.fire({
            title: 'Confirmar Reporte',
            text: "¿Deseas generar el reporte PDF para esta tarea?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, generar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Construir la URL solo con el ID de la tarea
                var url = '<?php echo site_url('generar_reporte_tarea_lectura'); ?>' + '?tareaId=' + tareaId;

                // Abrir el reporte en una nueva pestaña
                window.open(url, '_blank');
            }
        });
    });
</script>

<?php $this->load->view('templates/footer'); ?>