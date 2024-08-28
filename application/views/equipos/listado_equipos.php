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
        <div class="col-md-12">
            <h1 class="mt-4">Listado de Equipos Médicos</h1>

            <!-- Input de búsqueda -->
            <div class="form-group">
                <input type="text" id="buscar" class="form-control" placeholder="Buscar por RFID, serie o ubicación">
            </div>
            <br>
            <!-- Tabla de equipos -->
            <div class="table table-responsive">
                <table class="table table-hover table-bordered table-responsive">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Nombre</th>
                            <th>Número de Serie</th>
                            <th>Tag RFID</th>
                            <th>Ubicación</th>
                            <th><center>Estado</center></th> <!-- Columna para el estado del equipo -->
                            <th><center>Acciones</center></th> <!-- Columna para el botón de ver imagen -->
                        </tr>
                    </thead>
                    <tbody id="tbequipos">
                        <!-- Aquí se insertarán las filas de la tabla -->
                    </tbody>
                </table>
            </div>
            <!-- Paginación -->
            <div class="paginacion mt-3"></div>
        </div>
    </div>
</div>

<!-- Modal para ver la imagen -->
<div class="modal fade" id="imagenModal" tabindex="-1" aria-labelledby="imagenModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imagenModalLabel">Imagen del Equipo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImagen" src="" alt="Imagen del equipo" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<!-- Script para manejar búsqueda, paginación y visualización de imágenes -->
<script>
    $(document).ready(function() {
        // Variables de control
        var paginaActual = 1;
        var cantidadRegistros = 7; // Número de registros por página

        // Función para cargar los datos de equipos
        function mostrarDatosEquipos(valorBuscar, pagina, cantidad) {
            $.ajax({
                url: "<?php echo site_url('mostrar_equipos'); ?>",
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
                            '<td>' + item.nombre + '</td>' +
                            '<td>' + item.numero_serie + '</td>' +
                            '<td>' + item.tag_rfid_id + '</td>' +
                            '<td>' + item.ubicacion_nombre + '</td>' +
                            '<td><center><i class="bi ' +
                            (item.estado == 1 ? 'fs-4 bi bi-check-circle-fill"' :
                                item.estado == 3 ? 'fs-4 bi bi-wrench-adjustable-circle-fill' :
                                'fs-4 bi bi-x-circle-fill') +
                            '" data-id="' + item.id + '" data-estado="' + item.estado + '" ' +
                            'style="color: ' +
                            (item.estado == 1 ? 'green' :
                                item.estado == 3 ? 'blue' :
                                'red') + '; cursor: ' +
                            'title="' + (item.estado == 1 ? 'Activo' :
                                item.estado == 3 ? 'Mantenimiento' :
                                'Inactivo') +
                            '"></i></center></td>' +
                            '<td><center><button class="btn btn-success btn-sm verImagen" data-imagen="' + item.Imagen + '"><i class="bi bi-eye"></i></button><center></td>' + // Botón para ver imagen
                            '</tr>';
                    });
                    $("#tbequipos").html(filas);

                    // Manejo de la paginación
                    var totalRegistros = response.totalregistros;
                    var cantidadRegistros = response.cantidad;
                    var numeroLinks = Math.ceil(totalRegistros / cantidadRegistros);
                    var paginador = "<ul class='pagination'>";

                    if (pagina > 1) {
                        paginador += "<li class='page-item'><a class='page-link' href='#' data-pagina='1'>Primera</a></li>";
                        paginador += "<li class='page-item'><a class='page-link' href='#' data-pagina='" + (pagina - 1) + "'>Anterior</a></li>";
                    } else {
                        paginador += "<li class='page-item disabled'><a class='page-link'>Primera</a></li>";
                        paginador += "<li class='page-item disabled'><a class='page-link'>Anterior</a></li>";
                    }

                    for (var i = 1; i <= numeroLinks; i++) {
                        if (i == pagina) {
                            paginador += "<li class='page-item active'><a class='page-link' href='#'>" + i + "</a></li>";
                        } else {
                            paginador += "<li class='page-item'><a class='page-link' href='#' data-pagina='" + i + "'>" + i + "</a></li>";
                        }
                    }

                    if (pagina < numeroLinks) {
                        paginador += "<li class='page-item'><a class='page-link' href='#' data-pagina='" + (pagina + 1) + "'>Siguiente</a></li>";
                        paginador += "<li class='page-item'><a class='page-link' href='#' data-pagina='" + numeroLinks + "'>Última</a></li>";
                    } else {
                        paginador += "<li class='page-item disabled'><a class='page-link'>Siguiente</a></li>";
                        paginador += "<li class='page-item disabled'><a class='page-link'>Última</a></li>";
                    }

                    paginador += "</ul>";
                    $(".paginacion").html(paginador);
                }
            });
        }

        // Cargar datos inicialmente
        mostrarDatosEquipos("", paginaActual, cantidadRegistros);

        // Búsqueda dinámica
        $("#buscar").keyup(function() {
            var valorBuscar = $(this).val();
            mostrarDatosEquipos(valorBuscar, 1, cantidadRegistros);
        });

        // Manejo de la paginación
        $(".paginacion").on("click", "a", function(e) {
            e.preventDefault();
            paginaActual = $(this).data("pagina");
            var valorBuscar = $("#buscar").val();
            mostrarDatosEquipos(valorBuscar, paginaActual, cantidadRegistros);
        });

        // Manejo del botón de ver imagen
        $("#tbequipos").on("click", ".verImagen", function() {
            var imagen = $(this).data("imagen");
            $("#modalImagen").attr("src", "<?php echo base_url('assets/imagenes/equipos/') ?>" + imagen);
            $("#imagenModal").modal('show');
        });
    });
</script>


<?php $this->load->view('templates/footer'); ?>
