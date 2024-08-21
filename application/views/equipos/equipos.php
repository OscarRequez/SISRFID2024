<?php $this->load->view('templates/header'); ?>

<style>


    .chart-container {
        width: 60%;
        height: 100%;
        max-width: 100%;
        max-height: 80%;
        position: relative;
    }

    .col-md-3,
    .col-md-6,
    .col-md-4,
    .col-md-8 {
        padding: 0.5rem;
        box-sizing: border-box;
        max-width: 80%;
        max-height: 80%;
    }

    .pagination {
        display: flex;
        justify-content: center;
        margin: 20px 0;
    }

    .pagination a {
        color: #404BDF;
        padding: 8px 16px;
        margin: 0 4px;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        text-decoration: none;
        font-size: 16px;
    }

    .pagination .active a {
        background-color: #7A7CE5;
        color: #fff;
        border-color: #7A84E5;
    }

    .pagination .disabled a {
        color: #6c757d;
        border-color: #dee2e6;
        cursor: not-allowed;
    }

    .pagination .disabled a,
    .pagination .active a {
        cursor: default;
    }

    .pagination .first,
    .pagination .last {
        font-weight: bold;
    }

    /* Asegúrate de que las imágenes se ajusten al contenedor */
    .table img {
        max-width: 100px; /* Ajusta el tamaño según sea necesario */
        height: auto;
    }
</style>

<div class="col py-3">
    <div class="row">
        <h4 class="mb-3">Gestión de Equipos Médicos</h4>

        <!-- Botón para abrir el modal -->
        <div class="col py-3">
            <div class="row">
                <div class="col-md-6">
                    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalEquipo">
                        Agregar Nuevo Equipo
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
                        <th>Creado por</th>
                        <th>Fecha creación</th>
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
            <div class="modal-header" style="background-color:#7A7CE5; color:#fff">
                <h5 class="modal-title" id="modalEquipoLabel">Agregar Equipo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEquipo" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="equipoId" name="id">
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="Descripcion">Descripción</label>
                        <input type="text" class="form-control" id="Descripcion" name="Descripcion" required>
                    </div>
                    <div class="form-group">
                        <label for="Codigo">Código</label>
                        <input type="text" class="form-control" id="Codigo" name="Codigo" required>
                    </div>
                    <div class="form-group">
                        <label for="RFID">RFID</label>
                        <input type="text" class="form-control" id="RFID" name="RFID" required>
                    </div>
                    <div class="form-group">
                        <label for="ubicacion">Ubicación</label>
                        <select class="form-control" id="ubicacion" name="ubicacion" required>
                            <!-- Las opciones se llenarán aquí -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="imagen">Imagen</label>
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
    function mostrarDatosEquipos(valorBuscar, pagina, cantidad) {
        console.log("Valor de búsqueda:", valorBuscar); // Verifica el valor en la consola
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
                        '<td>' + item.ubicacion_id + '</td>' +
                        '<td>' + item.usucre + '</td>' +
                        '<td>' + item.feccre + '</td>' +
                        '<td><center><i class="bi ' +
                        (item.estado == 1 ? 'fs-4 bi-check-circle' : 'fs-4 bi-x-circle') +
                        ' icon-toggle" data-id="' + item.id + '" data-estado="' + item.estado + '" style="color: ' +
                        (item.estado == 1 ? 'green' : 'red') + '; cursor: pointer;"></i></center></td>' +
                        '<td><img src="<?php echo base_url('uploads/') ?>' + item.Imagen + '" alt="Imagen del equipo"></td>' +
                        '<td><center><button class="btn btn-sm editModal" style="background-color:#527E99; color:#DBE4DB" ' +
                        'data-id="' + item.id + '" data-nombre="' + item.nombre + '" ' +
                        'data-descripcion="' + item.descripcion + '" data-numero-serie="' + item.numero_serie + '" ' +
                        'data-tag-rfid-id="' + item.tag_rfid_id + '" data-ubicacion-id="' + item.ubicacion_id + '" ' +
                        'data-estado="' + item.estado + '">Editar</button>' +
                        '' +
                        '<a href="<?php echo site_url('eliminar_equipo/'); ?>' + item.id + '" class="btn btn-sm" style="background-color:#F82A46; color:#DBE4DB" onclick="return confirm(\'¿Estás seguro de que deseas eliminar este equipo?\');">Eliminar</a></center></td>' +
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

    $(document).ready(function() {
        mostrarDatosEquipos('', 1, 5);

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

        $(document).on('click', '.editModal', function() {
            var id = $(this).data('id');
            var nombre = $(this).data('nombre');
            var descripcion = $(this).data('descripcion');
            var numeroSerie = $(this).data('numero-serie');
            var tagRfidId = $(this).data('tag-rfid-id');
            var ubicacionId = $(this).data('ubicacion-id');
            var estado = $(this).data('estado');

            $('#modalEquipoLabel').text('Editar Equipo');
            $('#equipoId').val(id);
            $('#nombre').val(nombre);
            $('#Descripcion').val(descripcion);
            $('#Codigo').val(numeroSerie);
            $('#RFID').val(tagRfidId);
            $('#ubicacion').val(ubicacionId);
            $('#modalEquipo').modal('show');
        });

        $('#formEquipo').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                url: "<?php echo site_url('guardar_equipo'); ?>",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.success) {
                        $('#modalEquipo').modal('hide');
                        mostrarDatosEquipos($('#valorBuscar').val(), 1, 5);
                    } else {
                        alert('Error al guardar el equipo.');
                    }
                }
            });
        });
    });
</script>

<?php $this->load->view('templates/footer'); ?>
