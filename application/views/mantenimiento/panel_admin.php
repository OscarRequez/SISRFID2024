<?php $this->load->view('templates/header'); ?>

<style>
    .pagination {
        display: flex;
        justify-content: center;
        margin: 20px 0;
    }
</style>

<script>
    $(document).ready(function() {
        // Establecer la fecha actual como valor predeterminado en el campo de fecha fin
        let today = new Date().toISOString().split('T')[0];
        $('#fechaFin').attr('max', today); // Limitar la fecha máxima a hoy
        $('#fechaFin').val(today); // Establecer la fecha fin predeterminada como hoy
    });
</script>
<div class="col py-3">
    <h2>Panel Administrativo de Mantenimiento</h2>
<hr>
    <!-- Formularios en línea -->
    <div class="row">
        <!-- Formulario de filtro por fecha -->
        <form id="filtroFechasMantenimiento" class="col-lg-6 col-md-12 d-flex align-items-end mb-4">
            <div class="row w-100">
                <div class="col-md-4">
                    <label for="fechaInicio" class="form-label">Fecha Inicio</label>
                    <input type="date" class="form-control" id="fechaInicio" name="fecha_inicio" required>
                </div>
                <div class="col-md-4">
                    <label for="fechaFin" class="form-label">Fecha Fin</label>
                    <input type="date" class="form-control" id="fechaFin" name="fecha_fin" required>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-file-earmark-pdf"></i> Reporte</button>
                </div>
            </div>
        </form>

        <!-- Formulario de búsqueda -->
        <form id="formBuscarSolicitudes" class="col-lg-6 col-md-12 d-flex align-items-end mb-4">
            <div class="input-group w-100">
                <input type="text" class="form-control" id="buscarTexto" placeholder="ID de Mantenimiento o Código de Equipo">
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
                    <th>ID Equipo | Codigo</th>
                    <th>Descripción</th>
                    <th>Prioridad</th>
                    <th>Tipo</th>
                    <th>Fecha de Solicitud</th>
                    <th>Área de Solicitud</th>
                    <th>Tec. Asignado</th>
                    <th>Estado</th>
                    <th>Imagen</th>
                    <th>Accion</th>

                </tr>
            </thead>
            <tbody id="solicitudesTableBody">

            </tbody>
        </table>
    </div>

    <!-- Controles de paginación -->
    <div class="pagination-container">
        <ul class="pagination" id="paginacion"></ul>
    </div>
</div>

<!-- Modal para asignar técnico -->

<div class="modal fade" id="nuevaAsignacion" tabindex="-1" aria-labelledby="nuevaAsignacionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="nuevaAsignacionLabel">Asignar Técnico</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formNuevaAsignacion">
                    <input type="hidden" id="solicitudId" name="solicitud_id">
                    <input type="hidden" id="equipoNombre" name="equipo_nombre">
                    <div class="mb-3">
                        <label for="tecnicos" class="form-label">Técnico</label>
                        <select id="tecnicos" name="tecnicos" class="form-control" required>
                            <option value="" disabled selected>Seleccionar Técnico</option>
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

<!-- Modal para ver la imagen -->
<div class="modal fade" id="imagenModal" tabindex="-1" aria-labelledby="imagenModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="imagenModalLabel">Imagen del Equipo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImagen" src="" alt="Imagen del equipo" width="300px" class="img-fluid">
            </div>

        </div>
    </div>
</div>

<script>
    // Definir la variable global para la página actual
    let paginaActual = 1;

    // Función para cargar la lista de técnicos desde el servidor
    function cargarTecnicos() {
        $.ajax({
            url: "<?php echo site_url('obtener_tecnicos'); ?>", // URL del endpoint para obtener técnicos
            type: "GET", // Método HTTP GET
            dataType: "json", // Tipo de datos esperado en la respuesta
            success: function(response) {
                var opciones = '<option value="" disabled selected>Seleccionar Técnico</option>';
                // Crear las opciones del select a partir de la respuesta
                $.each(response.tecnicos, function(key, tecnico) {
                    opciones += '<option value="' + tecnico.id + '">' + tecnico.nombre + '</option>';
                });
                $('#tecnicos').html(opciones); // Insertar las opciones en el select
            },
            error: function(xhr, status, error) {
                console.error("Error al cargar los técnicos:", status, error);
                $("#tecnicos").html('<option value="">Error al cargar técnicos</option>'); // Mostrar mensaje de error
            }
        });
    }

    function cargarSolicitudes(pagina = 1, cantidad = 5, filtro = '') {
        $.ajax({
            url: "<?php echo site_url('solicitudes_obtener'); ?>", // URL del endpoint para obtener solicitudes
            type: "POST", // Método HTTP POST
            data: {
                pagina: pagina,
                cantidad: cantidad, // Número de registros por página
                filtro: filtro // Parámetro de búsqueda
            },
            dataType: "json", // Tipo de datos esperado en la respuesta
            success: function(response) {
                var hayPendientes = false; // Variable para detectar si hay algún registro con estado 4
                var filas = "";

                $.each(response.solicitudes, function(key, item) {
                    // Si el estado es 4, marcamos que hay registros pendientes
                    if (item.estado == 4) {
                        hayPendientes = true;
                    }

                    // Determinar si la fila debe tener un fondo verde suave
                    var estiloFila = (item.estado == 4) ? 'style="background-color: #E7EDFF; font-weight: bold; "' : '';

                    filas += '<tr ' + estiloFila + '>' +
                        '<td>' + item.id + '</td>' +
                        '<td>' + item.equipo_id + ' | ' + item.numero_serie_eq + '</td>' +
                        '<td>' + item.descripcion + '</td>' +
                        '<td>' + item.prioridad + '</td>' +
                        '<td>' + item.tipo_mantenimiento + '</td>' +
                        '<td>' + item.fecha_solicitud + '</td>' +
                        '<td>' + item.ubicacion_nombre + '</td>' +
                        '<td style="background-color: ' + (item.usu_tecnico ? '' : '#DF5562') + '; color: ' + (item.usu_tecnico ? '' : '#721c24') + ';">' +
                        (item.usu_tecnico ? item.usu_tecnico : '') +
                        '</td>' +
                        '<td><center><i class="bi ' +
                        (item.estado == 1 ? 'fs-4 bi-send-fill ' :
                            item.estado == 2 ? 'fs-4 bi-wrench-adjustable' :
                            item.estado == 3 ? 'fs-4 bi-check-circle-fill' :
                            item.estado == 4 ? 'fs-4 bi-question-circle' :
                            'fs-4 bi-question-circle') +
                        '" style="color: ' +
                        (item.estado == 1 ? '#1F3BB6' :
                            item.estado == 2 ? '#D93C54' :
                            item.estado == 3 ? '#05AD30' :
                            item.estado == 4 ? '#FFC107' :
                            '#000') + '; cursor: ' +
                        (item.estado == 1 || item.estado == 2 || item.estado == 4 ? 'pointer' : 'default') + ';" ' +
                        'title="' + (item.estado == 1 ? 'Solicitado' :
                            item.estado == 2 ? 'En Mantenimiento' :
                            item.estado == 3 ? 'Completado' :
                            item.estado == 4 ? 'Por Confirmar' :
                            'Desconocido') +
                        '"></i></center></td>' +
                        '<td><center>' +
                        (item.Imagen ?
                            '<button class="btn btn-primary btn-sm verImagen" data-imagen="' + item.Imagen + '" data-descripcion="' + item.descripcion + '"><i class="bi bi-eye"></i></button>' :
                            '<button class="btn btn-secondary btn-sm disabled"><i class="bi bi-eye"></i></button>') +
                        '</center></td>' +
                        '<td><center>' +
                        '<div style="display: flex; gap: 5px; justify-content: center;">' +
                        '<button class="btn btn-success btn-sm nuevaAsignacion" ' +
                        'data-bs-toggle="modal" data-bs-target="#nuevaAsignacion" ' +
                        'data-id="' + item.id + '" ' +
                        'data-nombre="' + item.numero_serie_eq + '" ' +
                        (item.estado == 3 || item.estado == 4 ? 'disabled' : '') + '>' +
                        'Asignar</button>' +
                        '<button class="btn btn-danger btn-sm reportePDF ' +
                        (item.estado == 3 || item.estado == 4 ? '' : 'disabled') + '" ' +
                        'data-id="' + item.id + '">' +
                        '<i class="bi bi-file-earmark-pdf"></i>' +
                        '</button>' +
                        '<button class="btn btn-primary btn-sm confirmar ' +
                        (item.estado == 4 ? '' : 'disabled') + '" ' +
                        'title="Confirmar" data-id="' + item.id + '" data-equipo-id="' + item.equipo_id + '">' +
                        '<i class="bi bi-check-circle"></i>' +
                        '</button>' +
                        '</div>' +
                        '</center></td>' +
                        '</tr>';
                });

                // Mostrar la alerta si hay registros pendientes
                if (hayPendientes) {
                    Swal.fire({
                        title: 'Pendientes de Confirmación',
                        text: 'Existen registros a la espera de ser confirmados.',
                        icon: 'warning',
                        confirmButtonText: 'Entendido'
                    });
                }

                $("#solicitudesTableBody").html(filas); // Insertar las filas en el cuerpo de la tabla

                // Manejar la paginación
                var totalregistros = response.totalregistros;
                var numerolinks = Math.ceil(totalregistros / cantidad);
                var paginador = "<ul class='pagination'>";

                // Botones "Primera" y "Anterior"
                if (pagina > 1) {
                    paginador += "<li class='page-item'><a class='page-link' href='javascript:void(0)' onclick='cargarSolicitudes(1, " + cantidad + ")'>Primera</a></li>";
                    paginador += "<li class='page-item'><a class='page-link' href='javascript:void(0)' onclick='cargarSolicitudes(" + (pagina - 1) + ", " + cantidad + ")'>Anterior</a></li>";
                } else {
                    paginador += "<li class='page-item disabled'><a class='page-link' href='#'>Primera</a></li>";
                    paginador += "<li class='page-item disabled'><a class='page-link' href='#'>Anterior</a></li>";
                }

                // Botones de números de página
                var startPage = Math.max(1, pagina - 2); // Mostramos hasta 2 páginas antes de la actual
                var endPage = Math.min(numerolinks, pagina + 2); // Mostramos hasta 2 páginas después de la actual

                if (startPage > 1) {
                    paginador += "<li class='page-item'><a class='page-link' href='javascript:void(0)' onclick='cargarSolicitudes(1, " + cantidad + ")'>1</a></li>";
                    if (startPage > 2) paginador += "<li class='page-item disabled'><a class='page-link'>...</a></li>";
                }

                for (var i = startPage; i <= endPage; i++) {
                    if (i == pagina) {
                        paginador += "<li class='page-item active'><a class='page-link' href='javascript:void(0)'>" + i + "</a></li>";
                    } else {
                        paginador += "<li class='page-item'><a class='page-link' href='javascript:void(0)' onclick='cargarSolicitudes(" + i + ", " + cantidad + ")'>" + i + "</a></li>";
                    }
                }

                if (endPage < numerolinks) {
                    if (endPage < numerolinks - 1) paginador += "<li class='page-item disabled'><a class='page-link'>...</a></li>";
                    paginador += "<li class='page-item'><a class='page-link' href='javascript:void(0)' onclick='cargarSolicitudes(" + numerolinks + ", " + cantidad + ")'>" + numerolinks + "</a></li>";
                }

                // Botones "Siguiente" y "Última"
                if (pagina < numerolinks) {
                    paginador += "<li class='page-item'><a class='page-link' href='javascript:void(0)' onclick='cargarSolicitudes(" + (pagina + 1) + ", " + cantidad + ")'>Siguiente</a></li>";
                    paginador += "<li class='page-item'><a class='page-link' href='javascript:void(0)' onclick='cargarSolicitudes(" + numerolinks + ", " + cantidad + ")'>Última</a></li>";
                } else {
                    paginador += "<li class='page-item disabled'><a class='page-link' href='#'>Siguiente</a></li>";
                    paginador += "<li class='page-item disabled'><a class='page-link' href='#'>Última</a></li>";
                }

                paginador += "</ul>";
                $("#paginacion").html(paginador); // Insertar la paginación en el contenedor
            },
            error: function(xhr, status, error) {
                console.error("Error en la solicitud:", status, error);
            }
        });
    }

    $(document).ready(function() {
        // Manejar clic en el botón "Asignar" en la tabla de solicitudes
        $(document).on('click', '.nuevaAsignacion', function() {
            var solicitudId = $(this).data('id');
            var equipoNombre = $(this).data('nombre');

            // Rellenar los campos ocultos del formulario en el modal
            $('#solicitudId').val(solicitudId);
            $('#equipoNombre').val(equipoNombre);

            // Opcional: Si quieres mostrar el nombre del equipo en algún lugar del modal
            $('#nuevaAsignacionLabel').text('Asignar Técnico para: ' + equipoNombre);

            // Cargar la lista de técnicos en el select del modal
            cargarTecnicos();
        });

        // Manejar el envío del formulario para asignar el técnico
        $('#formNuevaAsignacion').on('submit', function(e) {
            e.preventDefault(); // Evitar el comportamiento predeterminado del formulario

            var solicitudId = $('#solicitudId').val(); // Obtener el ID de la solicitud
            var tecnicoId = $('#tecnicos').val(); // Obtener el ID del técnico seleccionado

            // Verificar que se haya seleccionado un técnico
            if (!tecnicoId) {
                alert('Por favor, seleccione un técnico.');
                return;
            }

            // Enviar la solicitud de asignación de técnico
            $.ajax({
                url: "<?php echo site_url('asignar_tecnico'); ?>", // URL del endpoint para asignar el técnico
                type: "POST", // Método HTTP POST
                data: {
                    solicitud_id: solicitudId,
                    tecnico_id: tecnicoId
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


        $(document).on('click', '.confirmar', function() {
            var solicitudId = $(this).data('id');
            var equipoId = $(this).data('equipo-id');

            // Llamada AJAX para confirmar la solicitud
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Estás a punto de confirmar esta solicitud.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, confirmar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "<?php echo site_url('confirmar_solicitud'); ?>", // Endpoint para confirmar
                        type: "POST",
                        data: {
                            solicitud_id: solicitudId,
                            equipo_id: equipoId
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

        // Manejo del botón de ver imagen
        $("#solicitudesTableBody").on("click", ".verImagen", function() {
            var imagen = $(this).data("imagen");
            var descripcion = $(this).data("descripcion");
            $("#modalImagen").attr("src", "<?php echo base_url('assets/imagenes/equipos/') ?>" + imagen);
            // Asignar la descripción al elemento <p> en el modal
            $("#modalDescripcion").text('Descricion: ' + descripcion);
            $("#imagenModal").modal('show');
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
                var url = '<?php echo site_url('generar_reporte_tareas'); ?>' + '?tareaId=' + tareaId;

                // Abrir el reporte en una nueva pestaña
                window.open(url, '_blank');
            }
        });
    });
</script>
<script>
    $('#filtroFechasMantenimiento').on('submit', function(event) {
        event.preventDefault(); // Previene el envío del formulario por defecto

        // SweetAlert de confirmación
        Swal.fire({
            title: 'Confirmar Reporte',
            text: "¿Deseas generar el reporte PDF?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, generar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Captura las fechas (capturarlas aquí, después de la confirmación)
                var fechaInicio = $('#fechaInicio').val();
                var fechaFin = $('#fechaFin').val();

                // Verificar que las fechas no estén vacías
                if (fechaInicio === '' || fechaFin === '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Por favor, selecciona ambas fechas para generar el reporte.'
                    });
                    return;
                }

                // Validar que la fecha de fin no sea anterior a la fecha de inicio
                if (fechaFin < fechaInicio) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'La fecha de fin no puede ser anterior a la fecha de inicio.'
                    });
                    return;
                }

                // Realizar petición AJAX para verificar si hay datos antes de abrir el PDF
                $.ajax({
                    url: '<?php echo site_url('mantenimiento_reporte_fechas'); ?>',
                    method: 'GET',
                    data: {
                        fechaInicio: fechaInicio,
                        fechaFin: fechaFin,
                        check: true // Agregar bandera para verificar que es una verificacion de si hay datos en esas fechas
                    },
                    success: function(response) {
                        try {
                            var jsonResponse = JSON.parse(response);
                            if (jsonResponse.error) {
                                // Mostrar SweetAlert si no hay datos
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: jsonResponse.error
                                });
                            } else {
                                // Abrir el reporte en una nueva pestaña si no hay errores
                                var url = '<?php echo site_url('mantenimiento_reporte_fechas'); ?>?fechaInicio=' + encodeURIComponent(fechaInicio) + '&fechaFin=' + encodeURIComponent(fechaFin);
                                window.open(url, '_blank');
                            }
                        } catch (e) {
                            console.error("Error en la respuesta del servidor: ", e);
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Hubo un problema al generar el reporte. Inténtalo nuevamente.'
                        });
                    }
                });
            }
        });
    });
</script>
<?php $this->load->view('templates/footer'); ?>