<?php $this->load->view('templates/header'); ?>
<style>
    .pagination {
        display: flex;
        justify-content: center;
        margin: 20px 0;
    }
</style>
<div class="col py-3">
    <h2 class="text-center">Lectura RFID - Control de Equipos Institucionales</h2>
    <hr>
    <!-- Formularios en línea -->
    <div class="row">
        <!-- Formulario de búsqueda -->
        <form id="formBuscarSolicitudes" class="col-lg-6 col-md-12 d-flex align-items-end mb-4">
            <div class="input-group w-100">
                <input type="text" class="form-control" id="buscarTexto" placeholder="Buscar ID de tarea o Ubicacion">
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
                    <th>Leídos</th>
                    <th>Por leer</th>
                    <th>Fuera de Área</th>
                    <th>Fecha de Solicitud</th>
                    <th>Área de Solicitud</th>
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

<!-- Modal de Lectura -->
<div class="modal fade" id="modalLectura" tabindex="-1" aria-labelledby="modalLecturaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLecturaLabel">Lectura de Tarea</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formLectura">
                    <div class="mb-3">
                        <label for="lecturaDatos" class="form-label">Ingrese los datos de la lectura:</label>
                        <textarea class="form-control" id="lecturaDatos" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Ingresar Datos</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación de Tarea -->
<div class="modal fade" id="modalConfirmarTarea" tabindex="-1" aria-labelledby="modalConfirmarTareaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmarTareaLabel">Confirmar Tarea</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formConfirmarTarea">
                    <div class="mb-3">
                        <label for="observacionTarea" class="form-label">Observación:</label>
                        <textarea class="form-control" id="observacionTarea" rows="3" placeholder="Ingrese las observaciones de la tarea"></textarea>
                    </div>
                    <input type="hidden" id="idTareaConfirmar"> <!-- Campo oculto para el ID de la tarea -->
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnEnviarConfirmacion">Confirmar</button>
            </div>
        </div>
    </div>
</div>


<script>
    // Definir paginaActual en el alcance global
    let paginaActual = 1;


    // Definir la función cargarSolicitudes fuera del $(document).ready para acceso global
    function cargarSolicitudes(pagina = 1, cantidad = 5, filtro = '') {
        var cantidadPorPagina = 5; // Definir una variable para la cantidad de registros por página

        $.ajax({
            url: "<?php echo site_url('tareas_lectura_obtener'); ?>",
            type: "POST",
            data: {
                pagina: pagina,
                cantidad: cantidadPorPagina, // Usar la variable definida aquí
                filtro: filtro // Parámetro de búsqueda
            },
            dataType: "json",
            success: function(response) {
                console.log(response);
                var filas = "";

                // Verificar si hay tareas
                if (response.tareas.length === 0) {
                    filas = '<tr><td colspan="11" class="text-center">Sin tareas asignadas</td></tr>'; // Ajusta el colspan según el número de columnas
                } else {
                    // Crear las filas dinámicamente con los datos de la tarea de lectura
                    $.each(response.tareas, function(index, item) {
                        let estiloFila = ''; // Puedes agregar condiciones para cambiar el estilo de la fila si es necesario

                        filas += '<tr ' + estiloFila + '>' +
                            '<td>' + item.id + '</td>' + // ID de la tarea
                            '<td>' + item.descripcion_tarea + '</td>' + // Descripción de la tarea
                            '<td>' + item.tipo_tarea + '</td>' + // Tipo de tarea
                            '<td style="text-align: center;">' + item.total_equipos + '</td>' + // Total de equipos
                            '<td style="text-align: center;">' + item.total_leidos + '</td>' + // Total leídos
                            '<td style="text-align: center;">' + item.total_no_leidos + '</td>' + // Total no leídos
                            '<td style="text-align: center;">' + item.total_otros + '</td>' + // Total otros
                            '<td>' + item.feccre + '</td>' + // Fecha de creación de la tarea
                            '<td>' + item.nombre_ubicacion + '</td>' + // Ubicación de la tarea
                            '<td><center><i class="bi ' +
                            (item.estado == 1 ? 'fs-4 bi-send-fill ' : // Estado 1: Solicitud enviada
                                item.estado == 2 ? 'fs-4 bi bi-pc-display-horizontal' : // Estado 2: En Proceso"
                                item.estado == 3 ? 'fs-4 bi-check-circle' : // Estado 3: Completado
                                item.estado == 4 ? 'fs-4 bi-check-circle-fill' : // Estado 4: Por Confirmar
                                'fs-4 bi-question-circle') + // Estado desconocido
                            '" style="color: ' +
                            (item.estado == 1 ? '#1F3BB6' : // Color para estado "Solicitado"
                                item.estado == 2 ? '#0a1039' : // Color para estado "En Proceso"
                                item.estado == 3 ? '#05AD30 ' : // Color para estado "Por Confirmar"
                                item.estado == 4 ? '#2aa932' : // Color para estado "Completado"
                                '#000') + '; cursor: ' +
                            (item.estado == 1 || item.estado == 2 || item.estado == 3 ? 'pointer' : 'default') + ';" ' +
                            'title="' + (item.estado == 1 ? 'Solicitado' :
                                item.estado == 2 ? 'En Proceso' :
                                item.estado == 3 ? 'Por Confirmar' :
                                item.estado == 4 ? 'Completado' :
                                'Desconocido') +
                            '"></i></center></td>' +

                            '<td><center>' +
                            '<div style="display: flex; gap: 5px; justify-content: center;">' +
                            // Botón Lectura
                            '<button title="Lectura de RFID" class="btn btn-success btn-sm nuevaLectura" ' +
                            'data-bs-toggle="modal" data-bs-target="#modalLectura" ' + // Abre el modal de lectura
                            'data-id="' + item.id + '" ' +
                            'data-ubicacion_nombre="' + item.nombre_ubicacion + '" ' +
                            'data-ubicacion="' + item.ubicacion_tarea + '" ' +
                            (item.estado == 3 || item.estado == 4 ? 'disabled' : '') + '>' +
                            '<i title="Lectura de RFID" class="bi bi-upc-scan"></i></button>' +
                            // Botón Reporte PDF
                            '<button class="btn btn-danger btn-sm reportePDF ' +
                            (item.estado == 3 || item.estado == 4 ? '' : 'disabled') + '" ' +
                            'data-id="' + item.id + '">' +
                            '<i class="bi bi-file-earmark-pdf" title="Generar Reporte PDF"></i></button>' +
                            // Botón Confirmar
                            '<button class="btn btn-primary btn-sm confirmar ' +
                            (item.estado == 1 || item.estado == 2 ? '' : 'disabled') + '" ' +
                            'data-bs-toggle="modal" data-bs-target="#modalConfirmarTarea" ' +
                            'data-id="' + item.id + '">' +
                            '<i class="bi bi-check-circle"></i></button>' +
                            '</div>' +
                            '</center></td>' +
                            '</tr>';

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
        // Cargar solicitudes iniciales
        cargarSolicitudes(paginaActual);

        // Manejar eventos de clic en la paginación
        $(document).on('click', '.page-link', function(e) {
            e.preventDefault();
            var pagina = $(this).data('pagina');
            if (pagina) {
                paginaActual = pagina;
                cargarSolicitudes(paginaActual);
            }
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

        // MODAL DE NUEVA LECTURA
        $(document).on('click', '.nuevaLectura', function() {
            var tareaId = $(this).data('id');
            var ubicacionTarea = $(this).data('ubicacion_nombre');

            // Actualiza el título del modal con el nombre de la tarea
            $('#modalLecturaLabel').text('LECTURA RFID ' + ubicacionTarea);

            // Limpiar el campo de textarea
            $('#lecturaDatos').val('');

            // Manejar la acción del botón de "Ingresar Datos"
            $('#formLectura').off('submit').on('submit', function(e) {
                e.preventDefault();

                var datosLectura = $('#lecturaDatos').val();

                // Enviar los datos por AJAX
                $.ajax({
                    url: "<?php echo site_url('procesar_lectura_tags'); ?>",
                    type: "POST",
                    data: {
                        rfid_tags: datosLectura,
                        tarea_id: tareaId
                    },
                    dataType: 'json',
                    success: function(response) {
                        // Mostrar SweetAlert según el estado de la respuesta
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Éxito',
                                text: response.message,
                                confirmButtonText: 'Ok'
                            });
                            // Cerrar el modal al éxito
                            $('#modalLectura').modal('hide');
                            // Recargar o actualizar la tabla si es necesario
                            cargarSolicitudes();
                        } else if (response.status === 'error') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,
                                confirmButtonText: 'Ok'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        // Manejo de errores generales de la solicitud
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Hubo un problema al procesar la solicitud.',
                            confirmButtonText: 'Ok'
                        });
                        console.error("Error al ingresar los datos de lectura:", error);
                    }
                });
            });
        });

        //EVENTO DE CONFIRMAR LA TAREA
        // Evento para cuando se hace clic en el botón de confirmar tarea
        $(document).on('click', '.confirmar', function() {
            var idTarea = $(this).data('id'); // Obtener el ID de la tarea
            $('#idTareaConfirmar').val(idTarea); // Establecer el ID en el campo oculto del modal
            $('#modalConfirmarTarea').modal('show'); // Mostrar el modal
        });

        // Evento para enviar la confirmación al servidor
        $('#btnEnviarConfirmacion').on('click', function() {
            var idTarea = $('#idTareaConfirmar').val(); // Obtener el ID de la tarea desde el campo oculto
            var observacion = $('#observacionTarea').val(); // Obtener la observación

            // Validar que se haya ingresado una observación
            if (!observacion.trim()) {
                Swal.fire('Error', 'Debe ingresar una observación o como mínimo ingrese la frase "sin observaciones"', 'error');
                return;
            }

            // SweetAlert de confirmación
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Estás a punto de confirmar esta solicitud.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, confirmar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Enviar la solicitud AJAX si se confirma
                    $.ajax({
                        url: "<?php echo site_url('confirmar_tarea_lectura'); ?>",
                        type: 'POST',
                        data: {
                            id: idTarea,
                            observacion: observacion
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Éxito', 'La tarea fue confirmada correctamente', 'success');
                                $('#modalConfirmarTarea').modal('hide'); // Cerrar el modal
                                // Recargar la tabla
                                cargarSolicitudes(paginaActual);

                            } else {
                                Swal.fire('Error', 'No se pudo confirmar la tarea', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Hubo un problema al confirmar la tarea', 'error');
                        }
                    });
                }
            });
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