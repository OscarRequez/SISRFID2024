<?php $this->load->view('templates/header'); ?>


<style>
    /* Estilo para gráficos */
    .chart-container {
        position: relative;
        height: 400px;
        width: 60%;
        border: 1px solid #122EF9;
    }

    .table-responsive {
        border: 1px solid #122EF9;
    }
</style>
</head>
<!-- welcome.php -->
<div class="col py-3">
    <div class="container mt-4">
        <div class="row">
            <!-- Estadísticas Generales -->
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Equipos Registrados</h5>
                        <p class="card-text"><strong>150</strong></p>
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
                        <canvas id="equipmentDistributionChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Últimas Solicitudes de Mantenimiento</h5>
                        <table class="table table-striped">
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
<br>

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
    const ctx2 = document.getElementById('equipmentDistributionChart').getContext('2d');
    const equipmentDistributionChart = new Chart(ctx2, {
        type: 'pie', // Tipo de gráfico: 'pie' para gráfico circular
        data: {
            labels: ['Operativos', 'En Mantenimiento', 'Dados de Baja'], // Etiquetas de cada sección
            datasets: [{
                label: 'Distribución de Equipos',
                data: [50, 30, 20], // Datos para cada sección
                backgroundColor: [
                    'rgba(75, 192, 192, 0.5)',
                    'rgba(255, 159, 64, 0.5)',
                    'rgba(255, 99, 132, 0.5)'
                ], // Colores para cada sección
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(255, 99, 132, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true, // El gráfico se ajusta al tamaño del contenedor
        }
    });
</script>


