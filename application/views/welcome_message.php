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
    <h1>Bienvenido a SISAMED</h1>

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <!-- Tabla de Datos -->

            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Ventas</th>
                    <th>Gastos</th>
                    <th>Beneficio</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>2024-08-01</td>
                    <td>$5000</td>
                    <td>$2000</td>
                    <td>$3000</td>
                </tr>
                <tr>
                    <td>2024-08-02</td>
                    <td>$6000</td>
                    <td>$2500</td>
                    <td>$3500</td>
                </tr>
                <!-- Más filas según sea necesario -->
            </tbody>
        </table>
    </div>


    <!-- Gráfico de Datos -->
    <div class="chart-container">
        <canvas id="myChart"></canvas>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Configuración del gráfico
    const ctx = document.getElementById('myChart').getContext('2d');
    const myChart = new Chart(ctx, {
        type: 'bar', // Tipo de gráfico (puede ser 'bar', 'line', 'pie', etc.)
        data: {
            labels: ['2024-08-01', '2024-08-02'], // Etiquetas para el eje X
            datasets: [{
                label: 'Ventas',
                data: [5000, 6000], // Datos para el gráfico
                backgroundColor: 'rgba(39, 198, 26, 0.8)', // Color de fondo de las barras
                borderColor: 'rgba(75, 192, 192, 1)', // Color del borde de las barras
                borderWidth: 1
            }, {
                label: 'Gastos',
                data: [2000, 2500], // Datos para el gráfico
                backgroundColor: 'rgba(31, 104, 199, 0.8)', // Color de fondo de las barras
                borderColor: 'rgba(16, 15, 18, 1)', // Color del borde de las barras
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>


<?php $this->load->view('templates/footer'); ?>