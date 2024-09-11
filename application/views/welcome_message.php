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
</style>
</head>
<!-- welcome.php -->

<div class="col py-3">
    <div class="row">
        <!-- Estadísticas Generales -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body ">
                    <h5 class="card-title">Equipos Registrados</h5>
                    <center>
                        <h3 class="card-text" id="cantidad-equipos"><strong></strong>
                            <h3 />
                    </center>
                </div>
            </div>
        </div>

        <!-- Gráfico de Solicitudes de Mantenimiento -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <canvas id="maintenanceChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Notificaciones -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body bg-primary text-white">
                    <h5 class="card-title">Notificaciones</h5>
                    <ul class="list-group">
                        <li class="list-group-item">Equipo X necesita mantenimiento</li>
                        <li class="list-group-item">Solicitud Y completada</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Distribución de Equipos</h5>
                    <canvas class="chart-container" id="equipmentDistributionChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
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
                                item.estado == 4 ? 'fs-4 bi-check-circle' :// Icono para estado 4
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

                    if (pagina > 1) {
                        paginador += "<li><a class='page-link' href='javascript:void(0)' onclick='cargarSolicitudesMantenimiento(1, " + cantidad + ")'>Primera</a></li>";
                        paginador += "<li><a class='page-link' href='javascript:void(0)' onclick='cargarSolicitudesMantenimiento(" + (pagina - 1) + ", " + cantidad + ")'>Anterior</a></li>";
                    } else {
                        paginador += "<li class='disabled'><a class='page-link' href='#'>Primera</a></li>";
                        paginador += "<li class='disabled'><a class='page-link' href='#'>Anterior</a></li>";
                    }

                    for (var i = 1; i <= numerolinks; i++) {
                        paginador += "<li" + (i === pagina ? " class='active'" : "") + "><a class='page-link' href='javascript:void(0)' onclick='cargarSolicitudesMantenimiento(" + i + ", " + cantidad + ")'>" + i + "</a></li>";
                    }

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
            cargarSolicitudesMantenimiento(1, 3); // Cargar la primera página con 5 registros
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

    <?php $this->load->view('templates/footer'); ?>