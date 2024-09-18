<?php $this->load->view('templates/header'); ?>
<style>
    .pagination {
        display: flex;
        justify-content: center;
        margin: 20px 0;
    }
</style>
<div class="container py-3">
    <div class="row">
        <h3 class="mb-3">Reportes de Equipos</h3>

        <!-- Filtros de Búsqueda -->
        <div class="mb-4">
            <h5>Filtros de Búsqueda</h5>
            <form id="reportFiltersForm">
                <div class="row">
                    <!-- Filtro por Ubicación -->
                    <div class="col">
                        <label for="ubicacion">Ubicación</label>
                        <select id="ubicacion" class="form-select">
                            <option value="">Todas</option>
                            <?php foreach ($ubicaciones as $ubicacion): ?>
                                <option value="<?php echo $ubicacion['id']; ?>"><?php echo $ubicacion['nombre']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <!-- Filtro por Estado -->
                    <div class="col">
                        <label for="estado">Estado</label>
                        <select id="estado" class="form-select">
                            <option value="">Todos</option>
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                            <option value="3">Mantenimiento</option>
                        </select>
                    </div>
                    <!-- Filtro por Nombre/Descripción -->
                    <div class="col">
                        <label for="search">Buscar</label>
                        <input type="text" id="search" class="form-control" placeholder="Nombre o Descripción">
                    </div>
                    <!-- Filtro por Número de Serie/Tag RFID -->
                    <div class="col">
                        <label for="numero_serie">Código / Tag RFID</label>
                        <input type="text" id="numero_serie" class="form-control" placeholder="Número de Serie / Tag RFID">
                    </div>
                </div>
                <button type="button" id="applyFilters" class="btn btn-primary mt-2">Aplicar Filtros <i class="bi bi-patch-check"></i></button>
                <button type="button" id="generateReport" class="btn btn-success mt-2">Generar Reporte <i class="bi bi-filetype-pdf"> </i></button>
            </form>
        </div>

        <!-- Resultados del Reporte -->
        <div class="mb-4">
            <h3>Resultados del Reporte</h3>
            <div class="table-responsive" id="reportTableContainer">
                <!-- Aquí se cargará la tabla de resultados por AJAX -->
                <table class="table table-striped table-responsive">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Código de Equipo</th>
                            <th>Tag RFID</th>
                            <th>Ubicación</th>
                            <th>Estado</th>
                            <th>Historial</th>
                        </tr>
                    </thead>
                    <tbody id="tbequipos">
                        <tr>
                            <td colspan="6" class="text-center"><strong>Genere una consulta</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Controles de Paginación -->
        <div id="paginationControls" class="mt-4">
            <!-- Aquí se cargarán los controles de paginación por AJAX -->
        </div>
    </div>
</div>
<script>
    //eliminar campos en blanco
    document.addEventListener('DOMContentLoaded', function() {
        // Obtener todos los campos de texto en el formulario
        var inputs = document.querySelectorAll('#reportFiltersForm input[type="text"]');

        // Añadir un evento de "paste" para cada campo de texto
        inputs.forEach(function(input) {
            input.addEventListener('paste', function(event) {
                // Obtener el texto pegado
                var pastedText = (event.clipboardData || window.clipboardData).getData('text');

                // Eliminar los espacios en blanco del texto pegado
                var cleanedText = pastedText.trim();

                // Establecer el valor limpio en el campo de texto
                setTimeout(function() {
                    input.value = cleanedText;
                }, 0);
            });

            // También podemos limpiar el texto cuando se pierde el foco del campo (opcional)
            input.addEventListener('blur', function() {
                input.value = input.value.trim();
            });
        });
    });
</script>
<script>
    $(document).ready(function() {

        // Función para cargar los datos de equipos con filtros
        function cargarDatosEquipos(pagina = 1) {
            var filtros = {
                ubicacion: $('#ubicacion').val(),
                estado: $('#estado').val(),
                search: $('#search').val(),
                numero_serie: $('#numero_serie').val()
            };

            $.ajax({
                url: "<?php echo site_url('cargar_datos'); ?>",
                type: "POST",
                data: {
                    pagina: pagina,
                    filtros: filtros
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
                            '<td>' + (item.estado == 1 ? 'Activo' : item.estado == 0 ? 'Inactivo' : 'Mantenimiento') + '</td>' +
                            '<td><a href="#" class="btn btn-danger" data-id="' + item.id + '" onclick="reporte_historial_equipo(' + item.id + '); return false;"><i class="bi bi-file-pdf-fill"></i></a></td>' +
                        '</tr>';
                    });
                    $("#tbequipos").html(filas);

                    // Manejo de la paginación
                    var totalRegistros = response.totalregistros;
                    var cantidadRegistros = response.cantidad;
                    var numeroLinks = Math.ceil(totalRegistros / cantidadRegistros);
                    var paginador = "<ul class='pagination'>";

                    // Botón "Primera" y "Anterior"
                    if (pagina > 1) {
                        paginador += "<li class='page-item'><a class='page-link' href='#' data-pagina='1'>Primera</a></li>";
                        paginador += "<li class='page-item'><a class='page-link' href='#' data-pagina='" + (pagina - 1) + "'>Anterior</a></li>";
                    } else {
                        paginador += "<li class='page-item disabled'><a class='page-link'>Primera</a></li>";
                        paginador += "<li class='page-item disabled'><a class='page-link'>Anterior</a></li>";
                    }

                    // Botones de números de página con puntos suspensivos
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
                    $("#paginationControls").html(paginador);
                },
                error: function(xhr, status, error) {
                    console.error("Error al cargar los datos: " + error);
                }
            });
        }

        // Aplicar filtros
        $('#applyFilters').click(function() {
            cargarDatosEquipos();
        });

        // Manejo de la paginación
        $(document).on('click', '.page-link', function(e) {
            e.preventDefault();
            var pagina = $(this).data('pagina');
            if (pagina) {
                cargarDatosEquipos(pagina);
            }
        });

        // Generar reporte en PDF o Excel
        $('#generateReport').click(function() {
            var filtros = {
                ubicacion: $('#ubicacion').val(),
                estado: $('#estado').val(),
                search: $('#search').val(),
                numero_serie: $('#numero_serie').val()
            };
            var queryString = $.param(filtros);
            var url = '<?php echo site_url('generar_reporte'); ?>' + '?' + queryString;
            // Abrir el reporte en una nueva pestaña
            window.open(url, '_blank');
        });

    });
</script>

<script>
    function reporte_historial_equipo(id) {
        console.log("ID del equipo:", id); // Imprime el ID en la consola

        var Id = (id); // Capturar el ID de la tarea

        // Confirmar la acción con SweetAlert
        Swal.fire({
            title: 'Confirmar Reporte',
            text: "¿Deseas generar el reporte para este equipo?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, generar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {

            if (result.isConfirmed) {
                // Construir la URL solo con el ID de la tarea
                var url = '<?php echo site_url('reporte_historial_equipo'); ?>' + '?Id=' + Id;
                // Abrir el reporte en una nueva pestaña
                window.open(url, '_blank');
            }


        });
    }
</script>



<?php $this->load->view('templates/footer'); ?>