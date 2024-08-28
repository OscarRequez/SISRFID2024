<?php $this->load->view('templates/header'); ?>

<style>
    .pagination {
        display: flex;
        justify-content: center;
        margin: 20px 0;
    }
</style>

<div class="col py-3">
    <div class="row">
        <h4 class="mb-3">Gestión de Equipos Médicos</h4>

        <!-- Botón para abrir el modal -->
        <div class="col py-3">
            <div class="row">
                <div class="col-md-6">
                    <button type="button" id="btnAgregarEquipo" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalEquipo">
                        Agregar Equipo
                    </button>
                </div>
            </div>
        </div>

        <!-- Formulario de búsqueda -->
        <div class="col py-3">
            <form id="formBusqueda">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="valorBuscar" name="valorBuscar" placeholder="Buscar por Código, RFID, Unidad.">
                    <button class="btn btn-primary" type="submit">Buscar</button>
                </div>
            </form>
        </div>

        <div class="table table-responsive">
            <table class="table table-hover table-bordered table-responsive">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Código</th>
                        <th>C.RFID</th>
                        <th>Ubicación</th>
                        <th>Registrado</th>
                        <th>F.Creación</th>
                        <th>Estado</th>
                        <th>Imagen</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbequipos">
                    <!-- Las filas se llenarán con JavaScript -->
                </tbody>
            </table>
        </div>
        <div class="paginacion">
            <!-- La paginación se llenará con JavaScript -->
        </div>
    </div>
</div>

<!-- Modal para agregar y editar equipos -->

<div class="modal fade" id="modalEquipo" tabindex="-1" role="dialog" aria-labelledby="modalEquipoLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalEquipoLabel">Agregar Equipo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEquipo" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="equipoId" name="id">
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="Descripcion">Descripción</label>
                        <input type="text" class="form-control" id="Descripcion" name="Descripcion" autocomplete="off" required>
                    </div>
                    <div class="form-group">
                        <label for="Codigo">Código</label>
                        <input type="text" class="form-control" id="Codigo" name="Codigo" autocomplete="off" required>
                    </div>
                    <div class="form-group">
                        <label for="RFID">RFID</label>
                        <input type="text" class="form-control" id="RFID" name="RFID" autocomplete="off" required>
                    </div>
                    <div class="form-group">
                        <label for="ubicacion">Ubicación</label>
                        <select class="form-control" id="ubicacion" name="ubicacion" required>
                            <!-- Las opciones se llenarán aquí -->
                        </select>
                    </div>
                    <!-- Mostrar la imagen actual -->
                    <div class="mb-3" id="imagenActualContainer" style="display: none;">
                        <label for="imagenActual" class="form-label">Imagen Actual</label>
                        <br>
                        <img id="imagenActual" src="" alt="Imagen del equipo" class="img-thumbnail" style="max-width: 100px;">
                    </div>
                    <div class="form-group">
                        <label for="imagen">Actualizar Imagen</label>
                        <input type="file" class="form-control" id="imagen" name="imagen">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardar">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>




<script>
    $(document).ready(function() {
        // Variable para indicar el modo del modal (agregar o editar)
        var modoModal = 'agregar'; // Puede ser 'agregar' o 'editar'

        // Función para mostrar datos de equipos
        function mostrarDatosEquipos(valorBuscar, pagina, cantidad) {
            $.ajax({
                url: "<?php echo site_url('equipos_mostrar'); ?>",
                type: "POST",
                data: {
                    buscar: valorBuscar,
                    nropagina: pagina,
                    cantidad: cantidad
                },
                dataType: "json",
                success: function(response) {
                    var filas = "";
                    $.each(response.equipos, function(key, item) {
                        filas += '<tr>' +
                            '<td>' + item.id + '</td>' +
                            '<td>' + item.nombre + '</td>' +
                            '<td>' + item.descripcion + '</td>' +
                            '<td>' + item.numero_serie + '</td>' +
                            '<td>' + item.tag_rfid_id + '</td>' +
                            '<td>' + item.ubicacion_nombre + '</td>' +
                            '<td>' + item.usucre + '</td>' +
                            '<td>' + item.feccre + '</td>' +
                            '<td><center><i class="bi ' +
                            (item.estado == 1 ? 'fs-4 bi bi-check-circle-fill' :
                                item.estado == 3 ? 'fs-4 bi bi-wrench-adjustable-circle-fill' :
                                'fs-4 bi bi-x-circle-fill') +
                            '" data-id="' + item.id + '" data-estado="' + item.estado + '" ' +
                            'style="color: ' +
                            (item.estado == 1 ? 'green' :
                                item.estado == 3 ? 'blue' :
                                'red') + '; cursor: ' +
                            (item.estado == 0 || item.estado == 3 ? 'pointer' : 'default') + ';" ' +
                            (item.estado == 0 || item.estado == 3 ? 'data-toggle="modal" data-target="#motivoModal"' : '') +
                            'title="' + (item.estado == 1 ? 'Activo' :
                                item.estado == 3 ? 'Mantenimiento' :
                                'Inactivo') +
                            '"></i></center></td>' +
                            '<td><img width="50" height="50" src="<?php echo base_url('assets/imagenes/equipos/') ?>' + item.Imagen + '" alt=""></td>' +
                            '<td>' +
                            '<div class="d-flex justify-content-center">' +
                            '<button class="btn btn-sm editModal btn-warning me-2" ' +
                            'data-id="' + item.id + '" data-nombre="' + item.nombre + '" ' +
                            'data-descripcion="' + item.descripcion + '" data-numero-serie="' + item.numero_serie + '" ' +
                            'data-tag-rfid-id="' + item.tag_rfid_id + '" data-ubicacion-id="' + item.ubicacion_id + '" ' +
                            'data-estado="' + item.estado + '" data-imagen="' + item.Imagen + '">' +
                            '<i class="bi bi-pencil-square"></i></button>' +
                            '<button class="btn btn-sm btn-danger ' +
                            (item.estado == 0 ? 'disabled' : '') +
                            '" onclick="' + (item.estado == 0 ? 'return false;' : 'eliminarEquipo(' + item.id + ')') + '">' +
                            '<i class="bi bi-trash-fill"></i></button>' +
                            '</div>' +
                            '</td>' +
                            '</tr>';


                    });
                    $("#tbequipos").html(filas);
                    // Paginación
                    var totalregistros = response.totalregistros;
                    var cantidadregistros = response.cantidad;
                    var numerolinks = Math.ceil(totalregistros / cantidadregistros);
                    var paginador = "<ul class='pagination'>";

                    if (pagina > 1) {
                        paginador += "<li class='page-item'><a class='page-link first' href='#' data-pagina='1'>Primera</a></li>";
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
                        paginador += "<li class='page-item'><a class='page-link last' href='#' data-pagina='" + numerolinks + "'>Última</a></li>";
                    } else {
                        paginador += "<li class='page-item disabled'><a class='page-link'>Siguiente</a></li>";
                        paginador += "<li class='page-item disabled'><a class='page-link'>Última</a></li>";
                    }

                    paginador += "</ul>";
                    $(".paginacion").html(paginador);
                }
            });
        }

        // Cargar ubicaciones en el select
        function cargarUbicaciones() {
            $.ajax({
                url: "<?php echo site_url('obtener_ubicaciones_select'); ?>",
                type: "GET",
                dataType: "json",
                success: function(response) {
                    var select = $('#ubicacion');
                    select.empty(); // Limpiar opciones existentes
                    select.append('<option value="" disabled selected>Seleccionar ubicación</option>');
                    $.each(response.ubicaciones, function(index, ubicacion) {
                        select.append('<option value="' + ubicacion.id + '">' + ubicacion.nombre + '</option>');
                    });
                }
            });
        }


        $(document).ready(function() {
            mostrarDatosEquipos('', 1,7 );
            cargarUbicaciones(); // Cargar ubicaciones al inicio

            $(document).on('submit', '#formBusqueda', function(e) {
                e.preventDefault();
                var valorBuscar = $('#valorBuscar').val();
                mostrarDatosEquipos(valorBuscar, 1, 5);
            });

            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                var pagina = $(this).data('pagina');
                var valorBuscar = $('#valorBuscar').val();
                mostrarDatosEquipos(valorBuscar, pagina, 5);
            });

            // Mostrar modal de agregar
            $('#btnAgregarEquipo').click(function() {
                modoModal = 'agregar';
                $('#modalEquipoLabel').text('Agregar Equipo');
                $('#btnGuardar').text('Guardar');
                limpiarFormulario();
                $('#modalEquipo').modal('show');
            });

            // Limpiar formulario
            function limpiarFormulario() {
                $('#equipoId').val('');
                $('#nombre').val('');
                $('#Descripcion').val('');
                $('#Codigo').val('');
                $('#RFID').val('');
                $('#ubicacion').val('');
                $('#imagenActual').attr('src', '');
                $('#imagenActualContainer').hide();
            }

            // Mostrar modal de edición
            $(document).on('click', '.editModal', function() {
                modoModal = 'editar';
                $('#modalEquipoLabel').text('Editar Equipo');
                $('#btnGuardar').text('Actualizar');

                // Cargar datos del equipo en el formulario
                $('#equipoId').val($(this).data('id'));
                $('#nombre').val($(this).data('nombre'));
                $('#Descripcion').val($(this).data('descripcion'));
                $('#Codigo').val($(this).data('numero-serie'));
                $('#RFID').val($(this).data('tag-rfid-id'));
                $('#ubicacion').val($(this).data('ubicacion-id'));


                var imagenActual = $(this).data('imagen');
                console.log('aqui la imagenes', imagenActual);
                if (imagenActual) {
                    $('#imagenActual').attr('src', '<?php echo base_url("assets/imagenes/equipos/"); ?>' + imagenActual);
                    $('#imagenActualContainer').show();
                } else {
                    $('#imagenActual').attr('src', '');
                    $('#imagenActualContainer').hide();
                }

                $('#modalEquipo').modal('show');
            });


            // FUNCION PARA AGREGAR O EDITAR EQUIPO NUEVO
            $('#formEquipo').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                var url = (modoModal === 'agregar') ? "<?php echo site_url('agregar_equipo'); ?>" : "<?php echo site_url('editar_equipo'); ?>";

                $.ajax({
                    url: url,
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        $('#modalEquipo').modal('hide');
                        mostrarDatosEquipos('', 1, 5);

                        // Mostrar SweetAlert basado en la respuesta
                        if (response.status === 'success') {
                            Swal.fire('¡Éxito!', response.message, 'success');
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    }
                });
            });
        });
    });
</script>
<script>
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
    });

    function eliminarEquipo(id) {
        swalWithBootstrapButtons.fire({
            title: "¿Estás seguro?",
            text: "No podrás revertir esta acción!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Sí, eliminarlo!",
            cancelButtonText: "No, cancelar!",
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Ingrese el motivo de la eliminación',
                    input: 'textarea',
                    inputLabel: 'Motivo',
                    inputPlaceholder: 'Ingrese el motivo aquí...',
                    inputAttributes: {
                        'aria-label': 'Motivo de la eliminación'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Eliminar',
                    cancelButtonText: 'Cancelar',
                    showLoaderOnConfirm: true,
                    preConfirm: (motivo) => {
                        if (!motivo || motivo.trim() === '') {
                            Swal.showValidationMessage('Por favor, ingresa el motivo de la eliminación.');
                            return false;
                        }
                        return $.ajax({
                            url: "<?php echo site_url('eliminar_equipo'); ?>",
                            type: "POST",
                            data: {
                                id: id,
                                motivo: motivo
                            },
                            dataType: "json",
                            success: function(response) {
                                if (response.status === 'success') {
                                    // Actualizar el icono del estado en la vista
                                    $('i[data-id="' + id + '"]').removeClass('bi-exclamation-circle').addClass('bi-check-circle');
                                    Swal.fire({
                                        title: 'Eliminado!',
                                        text: 'El equipo ha sido eliminado.',
                                        icon: 'success'
                                    }).then(() => {
                                        // Actualiza el ícono de la fila en lugar de eliminarla
                                        $('i[data-id="' + id + '"]').removeClass('bi-check-circle').addClass('bi-x-circle').css('color', 'red');
                                        $('i[data-id="' + id + '"]').closest('tr').find('td').eq(8).html('<center><i class="bi fs-4 bi-x-circle" style="color: red;"></i></center>');

                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Error',
                                        text: response.message,
                                        icon: 'error'
                                    });
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    title: 'Error',
                                    text: 'Hubo un problema al eliminar el equipo.',
                                    icon: 'error'
                                });
                            }
                        });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire({
                    title: 'Cancelado',
                    text: 'La eliminación ha sido cancelada.',
                    icon: 'error'
                });
            }
        });
    }
</script>


<script>
    $(document).ready(function() {
        // Manejar clic en el ícono del estado
        $('body').on('click', 'i[data-toggle="modal"][data-target="#motivoModal"]', function() {
            var equipoId = $(this).data('id');

            // Hacer una solicitud AJAX para obtener la información del equipo eliminado
            $.ajax({
                url: "<?php echo site_url('obtener_motivo_eliminacion'); ?>", // Cambia esto según tu controlador
                type: "POST",
                data: {
                    id: equipoId
                },
                dataType: 'json', // Asegúrate de que jQuery maneje la respuesta como JSON
                success: function(response) {
                    console.log(response); // Verifica el formato de la respuesta

                    if (response.status === 'success') {
                        // Accede a los datos correctamente
                        $('#motivoTexto').text(response.data.motivo);
                        $('#usuarioEliminador').text(response.data.usr_inactivador);
                        $('#fechaEliminacion').text(response.data.fecha_inactivacion); // Añadir este campo si es necesario
                        $('#numeroSerie').text(response.data.numero_serie); // Añadir este campo si es necesario
                        $('#motivoModal').modal('show');
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: response.message,
                            icon: 'error'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    // Puedes obtener información adicional del error aquí
                    console.error(xhr.responseText); // Verifica el error en la consola
                    Swal.fire({
                        title: 'Error',
                        text: 'Hubo un problema al obtener la información del equipo.',
                        icon: 'error'
                    });
                }
            });
        });
    });
</script>


<!-- Modal -->
<!-- Modal -->
<div class="modal fade" id="motivoModal" tabindex="-1" aria-labelledby="motivoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="motivoModalLabel">Motivo de Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Motivo:</strong> <span id="motivoTexto"></span></p>
                <p><strong>Usuario que inactivó:</strong> <span id="usuarioEliminador"></span></p>
                <p><strong>Fecha de Inactivación:</strong> <span id="fechaEliminacion"></span></p>
                <p><strong>Número de Serie:</strong> <span id="numeroSerie"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>



<?php $this->load->view('templates/footer'); ?>