<?php $this->load->view('templates/header'); ?>
<style>
    /* Ajusta el tamaño de los gráficos para que se ajusten al contenedor */
    .chart-container {
        width: 60%;
        height: 100%;
        /* Asegúrate de que los gráficos no excedan el contenedor */
        max-width: 100%;
        max-height: 80%;
        position: relative;

    }


    .pagination {
        display: flex;
        justify-content: center;
        margin: 20px 0;
    }

    /*notificacione*/
    @keyframes pulse {
        0% {
            background-color: #f8d7da;
        }

        50% {
            background-color: #f5c6cb;
        }

        100% {
            background-color: #f8d7da;
        }
    }

    /* Aplica el borde y el efecto de pulso solo a la tarjeta de notificaciones */
    .card.notificaciones-alert {
        border: 2px solid red;
        box-shadow: 0 0 10px rgba(255, 0, 0, 0.5);
        animation: pulse 1s infinite;
    }

    .notificaciones-wrapper {
        max-height: 210px;
        /* Ajusta la altura según lo necesites */
        overflow-y: auto;
        /* Añade una barra de desplazamiento si hay demasiadas notificaciones */
    }

    .list-group-item {
        cursor: pointer;
    }

    .notificaciones-wrapper .badge {
        position: absolute;
        top: 0;
        right: 0;
        background-color: red;
        color: white;
    }

</style>
</head>
<!-- welcome.php -->

<div class="col py-3">
    
    <div class="row">
        <!-- Notificaciones -->
        <div class="col-md-3">
            <div class="card notificaciones-card">
                <div class="card-body bg-primary text-white">
                    <h5 class="card-title">
                        Notificaciones
                        <span id="notificacionesCount" class="badge rounded-pill bg-danger" style="display: none;"></span>
                    </h5>
                    <ul class="list-group notificaciones-wrapper" id="notificacionesList">
                        <!-- Aquí se llenarán las notificaciones -->
                    </ul>
                    <button id="showMoreBtn" class="btn mt-2 bg-dark text-white">Mostrar más</button>
                </div>
            </div>
        </div>


        <!-- Gráfico de Solicitudes de Mantenimiento -->
        <div class="col-md-6">
            <div class="card border border-primary">
                <div class="card-body">
                    <canvas class="chart-container-canvas" id="maintenanceChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Estadísticas Generales -->
        <div class="col-md-3">
            <div class="card border border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title">Equipos Registrados</h5>
                </div>
                <div class="card-body">
                    <center>
                        <h3 class="card-text" id="cantidad-equipos"><strong></strong>
                        </h3>
                    </center>
                </div>
            </div>
        </div>


    </div>

    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card border border-primary">
                <div class="card-body">
                    <h5 class="card-title">Distribución de Equipos</h5>
                    <canvas class="chart-container" id="equipmentDistributionChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border border-primary">
                <div class="card-body">
                    <h5 class="card-title ">Últimas Solicitudes de Mantenimiento</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-responsive">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>Equipo</th>
                                    <th>Area</th>
                                    <th>Fecha Solicitud</th>
                                    <th>
                                        <center>Estado</center>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="solicitudesMantenimiento">
                                <!-- Datos se llenarán por AJAX -->
                            </tbody>
                        </table>
                    </div>
                    <div class="paginacion"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalNotificacion" tabindex="-1" aria-labelledby="modalNotificacionLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNotificacionLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="modalNotificacionMensaje"></p>
                    <p id="modalNotificacionFecha" class="text-muted"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>



    <script>
        function cargarNotificaciones() {
            $.ajax({
                url: "<?php echo site_url('obtener_notificaciones'); ?>",
                type: "GET",
                dataType: "json",
                success: function(response) {
                    let notificacionesHtml = '';
                    let tieneNotificacionesSinLeer = false;
                    let conteoNotificacionesSinLeer = 0;

                    // Muestra solo las primeras 5 notificaciones
                    const cantidadVisible = 2;

                    $.each(response, function(key, notificacion) {
                        let itemClass = notificacion.leida == 0 ? 'list-group-item list-group-item-action list-group-item-danger' : 'list-group-item';
                        notificacionesHtml += `
                    <li class="${itemClass}" data-id="${notificacion.id}" data-mensaje="${notificacion.mensaje}" data-fecha="${notificacion.feccre}">
                        <strong>${notificacion.titulo}</strong>
                    </li>
                `;
                        if (notificacion.leida == 0) {
                            tieneNotificacionesSinLeer = true;
                            conteoNotificacionesSinLeer++;
                        }
                    });

                    $('#notificacionesList').html(notificacionesHtml);

                    // Muestra solo un número limitado de notificaciones
                    if ($('#notificacionesList .list-group-item').length > cantidadVisible) {
                        $('#notificacionesList .list-group-item').slice(cantidadVisible).hide();
                        $('#showMoreBtn').show();
                    } else {
                        $('#showMoreBtn').hide();
                    }

                    if (tieneNotificacionesSinLeer) {
                        $('.notificaciones-card').addClass('notificaciones-alert');
                        $('#notificacionesCount').text(conteoNotificacionesSinLeer).show();
                    } else {
                        $('.notificaciones-card').removeClass('notificaciones-alert');
                        $('#notificacionesCount').hide();
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error al cargar notificaciones:", status, error);
                }
            });
        }

        // Muestra más notificaciones al hacer clic en el botón
        $(document).on('click', '#showMoreBtn', function() {
            $('#notificacionesList .list-group-item').slice(2).show(); // Muestra las notificaciones ocultas
            $(this).hide(); // Oculta el botón "Mostrar más" cuando ya no haya más notificaciones ocultas
        });

        $(document).ready(function() {
            cargarNotificaciones();
        });

        // Manejar el clic en una notificación
        $(document).on('click', '.list-group-item', function() {
            var id = $(this).data('id');
            var mensaje = $(this).data('mensaje');
            var titulo = $(this).find('strong').text();
            var fecha = $(this).data('fecha');

            $('#modalNotificacion .modal-title').text(titulo);
            $('#modalNotificacionMensaje').text(mensaje);
            $('#modalNotificacionFecha').text('Fecha: ' + fecha);

            // Marcar la notificación como leída
            $.ajax({
                url: "<?php echo site_url('marcar_notificacion_leida'); ?>",
                type: "POST",
                data: {
                    id: id
                },
                success: function(response) {
                    cargarNotificaciones(); // Volver a cargar las notificaciones para actualizar la vista
                },
                error: function(xhr, status, error) {
                    console.error("Error al marcar notificación como leída:", status, error);
                }
            });

            $('#modalNotificacion').modal('show');
        });
    </script>

    <script>
        function cargarSolicitudesMantenimiento(pagina, cantidad) {
            $.ajax({
                url: "<?php echo site_url('mantenimiento_mostrarSolicitudes'); ?>", // Corrige la URL si es necesario
                type: "POST",
                data: {
                    pagina: pagina,
                    cantidad: cantidad
                },
                dataType: "json",
                success: function(response) {
                    var filas = "";
                    $.each(response.solicitudes, function(key, item) {
                        filas += '<tr>' +
                            '<td>' + item.numero_serie_eq + '</td>' +
                            '<td>' + item.ubicacion_nombre + '</td>' +
                            '<td>' + item.fecha_solicitud + '</td>' +
                            '<td><center><i class="bi ' +
                            (item.estado == 1 ? 'fs-4 bi bi-send-check-fill ' :
                                item.estado == 2 ? 'fs-4 bi bi-wrench-adjustable' :
                                item.estado == 4 ? 'fs-4 bi-check-circle' : // Icono para estado 4
                                'fs-4 bi-check-circle-fill') +
                            '" style="color: ' +
                            (item.estado == 1 ? '#1F3BB6' :
                                item.estado == 2 ? '#4691F7' :
                                item.estado == 4 ? '#05A01A' :
                                'green') + '; cursor: ' +
                            (item.estado == 1 || item.estado == 2 || item.estado == 4 ? 'pointer' : 'default') + ';" ' +
                            'title="' + (item.estado == 1 ? 'Solicitado' :
                                item.estado == 2 ? 'En Proceso' :
                                item.estado == 4 ? 'Por Confirmar' : // Título para estado 4
                                'Completado') +
                            '"></i></center></td>' +
                            '</tr>';
                    });

                    $("#solicitudesMantenimiento").html(filas);

                    // Paginación
                    var totalregistros = response.totalregistros;
                    var numerolinks = Math.ceil(totalregistros / cantidad);
                    var paginador = "<ul class='pagination'>";

                    // Primera y Anterior
                    if (pagina > 1) {
                        paginador += "<li><a class='page-link' href='javascript:void(0)' onclick='cargarSolicitudesMantenimiento(1, " + cantidad + ")'>Primera</a></li>";
                        paginador += "<li><a class='page-link' href='javascript:void(0)' onclick='cargarSolicitudesMantenimiento(" + (pagina - 1) + ", " + cantidad + ")'>Anterior</a></li>";
                    } else {
                        paginador += "<li class='disabled'><a class='page-link' href='#'>Primera</a></li>";
                        paginador += "<li class='disabled'><a class='page-link' href='#'>Anterior</a></li>";
                    }

                    // Páginas Centrales
                    var rango = 2; // Cantidad de páginas a mostrar antes y después de la página actual
                    var startPage = Math.max(1, pagina - rango);
                    var endPage = Math.min(numerolinks, pagina + rango);

                    if (startPage > 1) {
                        paginador += "<li><a class='page-link' href='javascript:void(0)' onclick='cargarSolicitudesMantenimiento(1, " + cantidad + ")'>1</a></li>";
                        if (startPage > 2) {
                            paginador += "<li class='disabled'><a class='page-link' href='#'>...</a></li>";
                        }
                    }

                    for (var i = startPage; i <= endPage; i++) {
                        paginador += "<li" + (i === pagina ? " class='active'" : "") + "><a class='page-link' href='javascript:void(0)' onclick='cargarSolicitudesMantenimiento(" + i + ", " + cantidad + ")'>" + i + "</a></li>";
                    }

                    if (endPage < numerolinks) {
                        if (endPage < numerolinks - 1) {
                            paginador += "<li class='disabled'><a class='page-link' href='#'>...</a></li>";
                        }
                        paginador += "<li><a class='page-link' href='javascript:void(0)' onclick='cargarSolicitudesMantenimiento(" + numerolinks + ", " + cantidad + ")'>" + numerolinks + "</a></li>";
                    }

                    // Siguiente y Última
                    if (pagina < numerolinks) {
                        paginador += "<li><a class='page-link' href='javascript:void(0)' onclick='cargarSolicitudesMantenimiento(" + (pagina + 1) + ", " + cantidad + ")'>Siguiente</a></li>";
                        paginador += "<li><a class='page-link' href='javascript:void(0)' onclick='cargarSolicitudesMantenimiento(" + numerolinks + ", " + cantidad + ")'>Última</a></li>";
                    } else {
                        paginador += "<li class='disabled'><a class='page-link' href='#'>Siguiente</a></li>";
                        paginador += "<li class='disabled'><a class='page-link' href='#'>Última</a></li>";
                    }

                    paginador += "</ul>";
                    $(".paginacion").html(paginador);
                },
                error: function(xhr, status, error) {
                    console.error("Error al cargar los datos: " + error);
                }
            });
        }

        $(document).ready(function() {
            cargarSolicitudesMantenimiento(1, 3); // Cargar la primera página con 3 registros
        });
    </script>

    <script>
        function cargarDatosGrafico() {
            $.ajax({
                url: "<?php echo site_url('obtenerDatosGraficoSolicitudes'); ?>",
                type: "GET",
                dataType: "json",
                success: function(response) {
                    // Transformar el objeto en un array y asegurarse de que los valores sean numéricos
                    const data = Object.keys(response).map(key => Number(response[key]));

                    const ctx = document.getElementById('maintenanceChart').getContext('2d');
                    const maintenanceChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                            datasets: [{
                                label: 'Solicitudes de Mantenimiento',
                                data: data, // Datos transformados desde el controlador
                                backgroundColor: 'rgba(106, 222, 90, 0.5)',
                                borderColor: 'rgba(123, 225, 217 , 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        // Configura los ticks para que sean enteros
                                        stepSize: 1,
                                        callback: function(value) {
                                            return Number.isInteger(value) ? value : '';
                                        }
                                    }
                                }
                            }
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar los datos del gráfico: ' + error);
                }
            });
        }

        // Cargar los datos del gráfico al cargar la página
        $(document).ready(function() {
            cargarDatosGrafico();
        });
    </script>

    <script>
        $(document).ready(function() {
            // Realiza la solicitud AJAX para obtener los datos
            $.ajax({
                url: '<?php echo site_url('welcome/distribucion_equipos'); ?>',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Organiza los datos
                    let cantidad = [0, 0, 0]; // Inicializa un array para almacenar las cantidades

                    data.forEach(function(item) {
                        if (item.estado == 1) {
                            cantidad[0] = item.cantidad; // Operativos
                        } else if (item.estado == 3) {
                            cantidad[1] = item.cantidad; // En Mantenimiento
                        } else if (item.estado == 0) {
                            cantidad[2] = item.cantidad; // Dados de Baja
                        }
                    });

                    // Crear el gráfico con el estilo preferido
                    const ctx2 = document.getElementById('equipmentDistributionChart').getContext('2d');
                    const equipmentDistributionChart = new Chart(ctx2, {
                        type: 'pie', // Tipo de gráfico: 'pie' para gráfico circular
                        data: {
                            labels: ['Operativos', 'En Mantenimiento', 'Dados de Baja'], // Etiquetas de cada sección
                            datasets: [{
                                label: 'Distribución de Equipos',
                                data: cantidad, // Datos dinámicos obtenidos por AJAX
                                backgroundColor: [
                                    'rgba(71, 193, 186 , 0.8)', // Color para Operativos
                                    'rgba(112, 207, 95, 0.8)', // Color para En Mantenimiento
                                    'rgba(79, 89, 99, 0.8)' // Color para Dados de Baja
                                ],
                                borderColor: [
                                    'rgba(71, 193, 186 , 0.8)', // Color para Operativos
                                    'rgba(112, 207, 95, 0.8)', // Color para En Mantenimiento
                                    'rgba(79, 89, 99, 0.8)' // Color para Dados de Baja
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true, // El gráfico se ajusta al tamaño del contenedor
                        }
                    });
                },
                error: function() {
                    console.error('Error al cargar los datos para el gráfico.');
                }
            });
        });
    </script>

    <script>
        //CANTIDAD DE EQUIPÒS TOTALES EN KLA BASE DE DATOS
        $(document).ready(function() {
            // Realiza la solicitud AJAX al cargar la página
            $.ajax({
                url: '<?php echo site_url('cantidad_equipos'); ?>', // URL del controlador/método
                type: 'GET', // Tipo de solicitud
                dataType: 'json', // Tipo de dato esperado (JSON)
                success: function(data) {
                    // Actualiza el contenido del elemento con id="cantidad-equipos"
                    $('#cantidad-equipos').text(data.cantidad);
                },
                error: function() {
                    // Muestra un mensaje de error en caso de falla
                    $('#cantidad-equipos').text('Error al cargar');
                }
            });
        });
    </script>

    <script>
        $(document).on('click', '#notificationsList .list-group-item', function() {
            var notificacionId = $(this).data('id');

            // Obtener los detalles de la notificación
            $.ajax({
                url: "<?php echo site_url('obtener_detalle_notificacion'); ?>",
                type: "POST",
                data: {
                    id: notificacionId
                },
                dataType: "json",
                success: function(response) {
                    $('#modalTitle').text(response.notificacion.titulo);
                    $('#modalMessage').text(response.notificacion.mensaje);

                    // Mostrar el modal
                    $('#notificationModal').modal('show');

                    // Marcar la notificación como leída
                    if (!response.notificacion.leida) {
                        $.ajax({
                            url: "<?php echo site_url('marcar_notificacion_leida'); ?>",
                            type: "POST",
                            data: {
                                id: notificacionId
                            },
                            success: function() {
                                // Actualizar la vista para marcar la notificación como leída
                                $(`#notificationsList .list-group-item[data-id="${notificacionId}"]`).removeClass('bg-warning');
                            },
                            error: function(xhr, status, error) {
                                console.error("Error al marcar notificación como leída:", status, error);
                            }
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error al obtener detalle de la notificación:", status, error);
                }
            });
        });
    </script>

    <?php $this->load->view('templates/footer'); ?>