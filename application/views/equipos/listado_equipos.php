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
        <h3 class="mb-3">Listado de Equipos Médicos</h3>
        <hr>
        <div class="col-md-12">

            <!-- Input de búsqueda -->
            <div class="form-group">
                <input type="text" id="buscar" class="form-control" placeholder="Buscar por Nombre, RFID, Codigo o Ubicación">
            </div>
            <br>
            <!-- Tabla de equipos -->
            <div class="table table-responsive">
                <table class="table table-hover table-bordered table-responsive">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Nombre</th>
                            <th>Codigo</th>
                            <th>Tag RFID</th>
                            <th>Ubicación</th>
                            <th>
                                <center>Estado</center>
                            </th> <!-- Columna para el estado del equipo -->
                            <th>
                                <center>Acciones</center>
                            </th> <!-- Columna para el botón de ver imagen -->
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

                <p id="modalDescripcion"> </p>
            </div>
        </div>
    </div>
</div>

<!-- Script para manejar búsqueda, paginación y visualización de imágenes -->
<script>
    $(document).ready(function() {
        // Variables de control
        var paginaActual = 1;
        var cantidadRegistros = 6; // Número de registros por página

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
                            (item.estado == 1 ? 'fs-4 bi-check-circle-fill' :
                                item.estado == 3 ? 'fs-4 bi-wrench-adjustable-circle-fill' :
                                'fs-4 bi-x-circle-fill') +
                            '" data-id="' + item.id + '" data-estado="' + item.estado + '" ' +
                            'style="color: ' +
                            (item.estado == 1 ? 'green' :
                                item.estado == 3 ? 'blue' :
                                'red') + ';" ' +
                            'title="' + (item.estado == 1 ? 'Activo' :
                                item.estado == 3 ? 'Mantenimiento' :
                                'Inactivo') +
                            '"></i></center></td>' +
                            '<td><center>' +
                            (item.Imagen ?
                                '<button class="btn btn-success btn-sm verImagen" data-imagen="' + item.Imagen + '" data-descripcion="' + item.descripcion + '" ><i class="bi bi-eye"></i></button>' :
                                '<button class="btn btn-secondary btn-sm disabled"><i class="bi bi-eye"></i></button>') +
                            '</center></td>' +
                            '</tr>';

                    });
                    $("#tbequipos").html(filas);

                    // Manejo de la paginación
                    var totalRegistros = response.totalregistros;
                    var numeroLinks = Math.ceil(totalRegistros / cantidad);
                    var paginador = "<ul class='pagination'>";

                    // Botón "Primera" y "Anterior"
                    if (pagina > 1) {
                        paginador += "<li class='page-item'><a class='page-link' href='#' data-pagina='1'>Primera</a></li>";
                        paginador += "<li class='page-item'><a class='page-link' href='#' data-pagina='" + (pagina - 1) + "'>Anterior</a></li>";
                    } else {
                        paginador += "<li class='page-item disabled'><a class='page-link'>Primera</a></li>";
                        paginador += "<li class='page-item disabled'><a class='page-link'>Anterior</a></li>";
                    }

                    // Botones de números de página
                    var startPage = Math.max(1, pagina - 2);
                    var endPage = Math.min(numeroLinks, pagina + 2);

                    if (startPage > 1) {
                        paginador += "<li class='page-item'><a class='page-link' href='#' data-pagina='1'>1</a></li>";
                        if (startPage > 2) paginador += "<li class='page-item disabled'><a class='page-link'>...</a></li>";
                    }

                    for (var i = startPage; i <= endPage; i++) {
                        if (i == pagina) {
                            paginador += "<li class='page-item active'><a class='page-link' href='#'>" + i + "</a></li>";
                        } else {
                            paginador += "<li class='page-item'><a class='page-link' href='#' data-pagina='" + i + "'>" + i + "</a></li>";
                        }
                    }

                    if (endPage < numeroLinks) {
                        if (endPage < numeroLinks - 1) paginador += "<li class='page-item disabled'><a class='page-link'>...</a></li>";
                        paginador += "<li class='page-item'><a class='page-link' href='#' data-pagina='" + numeroLinks + "'>" + numeroLinks + "</a></li>";
                    }

                    // Botón "Siguiente" y "Última"
                    if (pagina < numeroLinks) {
                        paginador += "<li class='page-item'><a class='page-link' href='#' data-pagina='" + (pagina + 1) + "'>Siguiente</a></li>";
                        paginador += "<li class='page-item'><a class='page-link' href='#' data-pagina='" + numeroLinks + "'>Última</a></li>";
                    } else {
                        paginador += "<li class='page-item disabled'><a class='page-link'>Siguiente</a></li>";
                        paginador += "<li class='page-item disabled'><a class='page-link'>Última</a></li>";
                    }

                    paginador += "</ul>";
                    $(".paginacion").html(paginador);
                },
                error: function(xhr, status, error) {
                    console.error("Error al cargar los datos: " + error);
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
            var nuevaPagina = $(this).data("pagina");
            if (nuevaPagina) {
                paginaActual = nuevaPagina;
                var valorBuscar = $("#buscar").val();
                mostrarDatosEquipos(valorBuscar, paginaActual, cantidadRegistros);
            }
        });

        // Manejo del botón de ver imagen
        $("#tbequipos").on("click", ".verImagen", function() {

            var imagen = $(this).data("imagen");
            var descripcion = $(this).data("descripcion");
            $("#modalImagen").attr("src", "<?php echo base_url('assets/imagenes/equipos/') ?>" + imagen);
            // Actualizar el texto de la descripción en el modal
            $("#modalDescripcion").text(descripcion);
            $("#imagenModal").modal('show');
        });
    });
</script>



<?php $this->load->view('templates/footer'); ?>