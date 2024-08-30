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

</style>
</head>
<!-- welcome.php -->

<div class="col py-3">
    <div class="row">
        <!-- Estadísticas Generales -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Equipos Registrados</h5>
                  <center> <h3 class="card-text" id="cantidad-equipos"><strong></strong><h3/></center> 
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
                <div class="card-body">
                    <h5 class="card-title">Notificaciones</h5>
                    <ul class="list-group">
                        <li class="list-group-item">Equipo X necesita mantenimiento</li>
                        <li class="list-group-item">Solicitud Y completada</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos adicionales y accesos rápidos -->
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
                    <h5 class="card-title">Últimas Solicitudes de Mantenimiento</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-responsive">
                            <thead>
                                <tr>
                                    <th>Equipo</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Equipo A</td>
                                    <td>01/08/2024</td>
                                    <td>En proceso</td>
                                </tr>
                                <tr>
                                    <td>Equipo B</td>
                                    <td>02/08/2024</td>
                                    <td>Completado</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const ctx = document.getElementById('maintenanceChart').getContext('2d');
    const maintenanceChart = new Chart(ctx, {
        type: 'bar', // Puedes cambiar a 'line', 'pie', etc.
        data: {
            labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio'], // Etiquetas del eje X
            datasets: [{
                label: 'Solicitudes de Mantenimiento',
                data: [12, 19, 3, 5, 2, 3, 7], // Datos para cada mes
                backgroundColor: 'rgba(106, 222, 90, 0.5)', // Color de las barras
                borderColor: 'rgba(123, 225, 217 , 1)', // Color del borde de las barras
                borderWidth: 2
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true // El eje Y comienza en 0
                }
            }
        }
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
                                'rgba(60, 69, 255 , 0.8)', // Color para Operativos
                                'rgba(248, 189, 33, 0.8)', // Color para En Mantenimiento
                                'rgba(255, 28, 90, 0.8)'  // Color para Dados de Baja
                            ],
                            borderColor: [
                                'rgba(60, 69, 255, 1)',
                                'rgba(248, 189, 33, 1)',
                                'rgba(255, 28, 90, 1)'
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