<?php $this->load->library('session'); ?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>SISAMED</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Estilo para la barra lateral */
        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #3B4045;
            padding-top: 20px;
        }

        .sidebar a {
            padding: 10px 15px;
            text-decoration: none;
            font-size: 15px;
            color: #BBC8E1;
            display: block;
        }

        .sidebar a:hover {
            background-color: #2A65D5;
        }

        .content {
            margin-left: 250px;
            padding: 20px;
        }

        /* Estilo para el punto verde y el texto */
        .status-indicator {
            width: 10px;
            height: 10px;
            background-color: #14F605;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }

        .status-text {
            display: inline-block;
            color: #fff;
            /* Color verde para el texto */
        }
    </style>
</head>

<body>

    <!-- Contenido principal -->
    <div class="content">

        <!-- Barra lateral -->
        <div class="sidebar">
            <div class="card-header bg-primary text-white d-flex align-items-center">
                <!-- Contenido textual a la izquierda -->
                <div class="mr-auto">
                    <h5> <?php echo $this->session->userdata('username'); ?> </h5>
                    <p>
                        <?php echo $this->session->userdata('rol_name'); ?> <br>

                        <?php if ($this->session->userdata('user_id')): ?>
                            <span class="status-indicator"></span>
                            <span class="status-text">En línea</span>
                        <?php else: ?>
                            <span class="status-indicator"></span>
                            <span class="status-text">Desconectado</span>
                        <?php endif; ?>
                    </p>

                </div>

                           <!-- Imagen a la derecha -->
            <div class="ml-3 bg-dark">
                <img src="<?php echo base_url('assets/imagenes/usuarios/oscar.png'); ?>" class="img-fluid rounded" style="width: 50px; height: 50px;">
            </div>
            </div>

 

            <a href="<?php echo site_url('welcome'); ?>">Inicio</a>
            <a href="<?php echo site_url('Equipos'); ?>">Equipos Medicos</a>
            <a href="<?php echo site_url('usuarios'); ?>">Usuarios</a>
            <a href="<?php echo site_url('welcome'); ?>">RFID</a>
            <a href="<?php echo site_url('usuarios'); ?>">Reportes</a>
            <a href="<?php echo site_url('otros'); ?>">Mantenimiento</a>
            <a href="<?php echo site_url('otros'); ?>">Manuales y documentación</a>
            <a href="<?php echo site_url('logout'); ?>">Cerrar sesión</a>
        </div>

        <!-- Contenido -->
        <div class="main-content">
            <!-- Aquí puedes agregar el contenido principal de la página -->
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>