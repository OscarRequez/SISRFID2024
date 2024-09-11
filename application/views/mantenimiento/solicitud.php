<?php $this->load->view('templates/header'); ?>

<style>
    .pagination {
        display: flex;
        justify-content: center;
        margin: 20px 0;
    }
</style>

<div class="col py-3">
    <h2>Solicitudes de Mantenimiento</h2>
    <div class="row">
        <div class="col-md-6">
            <!-- Botón para abrir el modal de nueva solicitud -->
            <button type="button" class="btn mb-3 bg-primary text-white" data-bs-toggle="modal" data-bs-target="#nuevaSolicitudModal">
                <i class="bi bi-clipboard2-plus"></i> Nueva Solicitud
            </button>
        </div>
    </div>

    <!-- Tabla para mostrar las solicitudes -->
    <div class="table-responsive">
        <table class="table table-striped table-responsive">
            <thead class="bg-primary text-white">
                <tr>
                    <th>ID</th>
                    <th>ID Equipo Eq | Codigo</th>
                    <th>Descripción</th>
                    <th>Prioridad</th>
                    <th>Tipo</th>
                    <th>Fecha de Solicitud</th>
                    <th>Área de Solicitud</th>
                    <th>Estado</th>

                </tr>
            </thead>
            <tbody id="solicitudesTableBody">
                <!-- Las filas de la tabla se llenarán dinámicamente aquí -->
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
            <div class="modal-header">
                <h5 class="modal-title" id="nuevaSolicitudModalLabel">Nueva Solicitud</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formNuevaSolicitud">
                    <div class="mb-3">
                        <label for="ubicacion" class="form-label">Unidad</label>
                        <select id="ubicacion" name="unidad" class="form-control" required>
                            <option value="" disabled selected>Seleccionar ubicación</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tipo_mantenimiento" class="form-label">Tipo de Mantenimiento</label>
                        <select id="tipo_mantenimiento" name="tipo_mantenimiento" class="form-control" required>
                            <option value="Preventivo">Preventivo</option>
                            <option value="Correctivo">Correctivo</option>
                            <option value="Operativo">Operativo</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="numero_serie_eq" class="form-label">Código del Equipo</label>
                        <input type="text" autocomplete="off" class="form-control" id="numero_serie_eq" name="numero_serie_eq" placeholder="Ingrese el Código del equipo" required>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción del Problema</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="prioridad" class="form-label">Prioridad</label>
                        <select id="prioridad" name="prioridad" class="form-control" required>
                            <option value="Baja">Baja</option>
                            <option value="Media">Media</option>
                            <option value="Alta">Alta</option>
                        </select>
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

<script>
    // Definir paginaActual en el alcance global
    let paginaActual = 1;

    // Manejar envío del formulario con AJAX
    $('#formNuevaSolicitud').on('submit', function(e) {
        e.preventDefault(); // Prevenir el envío normal del formulario

        $.ajax({
            url: "<?php echo site_url('crear_solicitud'); ?>",
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
    function cargarSolicitudes(pagina) {
        $.ajax({
            url: "<?php echo site_url('solicitudes_obtener'); ?>",
            type: "POST",
            data: {
                pagina: pagina,
                cantidad: 5 // Número de registros por página
            },
            dataType: "json",
            success: function(response) {
                var filas = "";
                $.each(response.solicitudes, function(key, item) {
                    filas += '<tr>' +
                        '<td>' + item.id + '</td>' +
                        '<td>' + item.equipo_id + ' | ' + item.numero_serie_eq + '</td>' +
                        '<td>' + item.descripcion + '</td>' +
                        '<td>' + item.prioridad + '</td>' +
                        '<td>' + item.tipo_mantenimiento + '</td>' +
                        '<td>' + item.fecha_solicitud + '</td>' +
                        '<td>' + item.ubicacion_nombre + '</td>' +
                        '<td><center><i class="bi ' +
                        (item.estado == 1 ? 'fs-4 bi bi-send-check-fill ' :
                            item.estado == 2 ? 'fs-4 bi bi-wrench-adjustable' :
                            item.estado == 3 ? 'fs-4 bi bi-check-circle-fill' :
                            item.estado == 4 ? 'fs-4 bi-check-circle' : // Nuevo ícono para estado 4
                            '') +
                        '" style="color: ' +
                        (item.estado == 1 ? '#1F3BB6' :
                            item.estado == 2 ? '#D93C54' :
                            item.estado == 3 ? 'green' :
                            item.estado == 4 ? 'red' : // Nuevo color para estado 4
                            'black') + '; cursor: ' +
                        (item.estado == 1 || item.estado == 2 || item.estado == 4 ? 'pointer' : 'default') + ';" ' +
                        'title="' + (item.estado == 1 ? 'Solicitado' :
                            item.estado == 2 ? 'En Mantenimiento' :
                            item.estado == 3 ? 'Completado' :
                            item.estado == 4 ? 'Por Confirmar' : // Nuevo título para estado 4
                            'Desconocido') +
                        '"></i></center></td>' +
                        '</tr>';
                });

                $("#solicitudesTableBody").html(filas);

                // Paginación
                var totalregistros = response.totalregistros;
                var numerolinks = Math.ceil(totalregistros / 5);
                var paginador = "<ul class='pagination'>";

                if (pagina > 1) {
                    paginador += "<li class='page-item'><a class='page-link' href='#' data-pagina='1'>Primera</a></li>";
                    paginador += "<li class='page-item'><a class='page-link' href='#' data-pagina='" + (pagina - 1) + "'>Anterior</a></li>";
                } else {
                    paginador += "<li class='page-item disabled'><a class='page-link'>Primera</a></li>";
                    paginador += "<li class='page-item disabled'><a class='page-link'>Anterior</a></li>";
                }

                for (var i = 1; i <= numerolinks; i++) {
                    if (i == pagina) {
                        paginador += "<li class='page-item active'><a class='page-link' href='#'>" + i + "</a></li>";
                    } else {
                        paginador += "<li class='page-item'><a class='page-link' href='#' data-pagina='" + i + "'>" + i + "</a></li>";
                    }
                }

                if (pagina < numerolinks) {
                    paginador += "<li class='page-item'><a class='page-link' href='#' data-pagina='" + (pagina + 1) + "'>Siguiente</a></li>";
                    paginador += "<li class='page-item'><a class='page-link' href='#' data-pagina='" + numerolinks + "'>Última</a></li>";
                } else {
                    paginador += "<li class='page-item disabled'><a class='page-link'>Siguiente</a></li>";
                    paginador += "<li class='page-item disabled'><a class='page-link'>Última</a></li>";
                }

                paginador += "</ul>";
                $("#paginacion").html(paginador);
            },
            error: function(xhr, status, error) {
                console.error("Error en la solicitud:", status, error);
            }
        });
    }

    $(document).ready(function() {
        // Cargar ubicaciones cuando se abre el modal
        $('#nuevaSolicitudModal').on('show.bs.modal', function() {
            cargarUbicaciones();
        });

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

        // Manejar inserción de nuevo registro y recargar la tabla
        $('#insertarSolicitud').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: "<?php echo site_url('solicitudes_insertar'); ?>",
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
    });
</script>

<?php $this->load->view('templates/footer'); ?>