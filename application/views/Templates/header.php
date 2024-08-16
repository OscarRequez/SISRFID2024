<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SISAMED</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Incluir SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Incluir SweetAlert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

    <!-- Incluye jQuery y SweetAlert en tu vista -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>
    <style>
        * {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .status-indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            background-color: green;
            border-radius: 50%;
            vertical-align: middle;
        }

        .status-text {
            display: inline-block;
            color: #fff;
            /* Color blanco para el texto */
        }

        /* Estilo para la imagen de usuario */
        .user-img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }

        @media (max-width: 600px) {

            /* Ajusta el valor según el punto de quiebre que prefieras */
            .user-img {
                width: 0px;
                height: 0px;
            }
        }

        #menu a.nav-link {
            color: #ffffff;
            padding: 10px;
            border-radius: 5px;
            text-decoration: none;
        }

    
        /* Efecto hover para los enlaces del menú */
        #menu a.nav-link:hover {
            color: #565FDC;
            /* Color de texto al hacer hover */
            transition: background-color 0.3s ease, color 0.3s ease;
            transform: scale(1.1);
        }
    </style>

    <div class="container-fluid">
        <div class="row">
            <nav class="navbar" style="background-color: #7887C7;">
                <div class="container-fluid">
                    <span class="navbar-text text-white ms-auto">
                        <span id="currentDateTime"></span>
                    </span>
                </div>
            </nav>
            <div class="container-fluid">
                <div class="row flex-nowrap">
                    <div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 bg-dark">
                        <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 text-white min-vh-100">

                            <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start" id="menu">
                                <li>
                                    <a href="<?php echo site_url('welcome'); ?>" class="nav-link px-0 align-middle">
                                        <i class="fs-4 bi bi-house"></i> <span class="ms-1 d-none d-sm-inline">Inicio</span>
                                    </a>
                                </li>

                                <li>
                                    <?php if ($this->M_permisos->tiene_permiso($this->session->userdata('role'), 'equipos')): ?>
                                        <a href="#submenu1" data-bs-toggle="collapse" class="nav-link px-0 align-middle">
                                            <i class="fs-4 bi bi bi-heart-pulse"></i> <span class="ms-1 d-none d-sm-inline">Equipos Médicos</span>
                                        </a>
                                        <ul class="collapse nav flex-column ms-1" id="submenu1" data-bs-parent="#menu">
                                            <?php if ($this->M_permisos->tiene_permiso($this->session->userdata('role'), 'equipos', 'registro')): ?>
                                                <li class="w-100">
                                                    <a href="#" class="nav-link px-0">
                                                        <i class="fs-4 bi bi-pencil-square"></i>
                                                        <span class="d-none d-sm-inline">Registro</span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>

                                            <?php if ($this->M_permisos->tiene_permiso($this->session->userdata('role'), 'equipos', 'listado')): ?>
                                                <li>
                                                    <a href="#" class="nav-link px-0">
                                                        <i class="fs-4 bi bi-list-check"></i>
                                                        <span class="d-none d-sm-inline">Listado</span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>

                                            <?php if ($this->M_permisos->tiene_permiso($this->session->userdata('role'), 'equipos', 'reportes')): ?>
                                                <li>
                                                    <a href="#" class="nav-link px-0">
                                                        <i class="fs-4 bi bi-journal-text"></i>
                                                        <span class="d-none d-sm-inline">Reportes</span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    <?php endif; ?>
                                </li>

                                <li>
                                    <?php if ($this->M_permisos->tiene_permiso($this->session->userdata('role'), 'usuarios')): ?>
                                        <a href="#submenu2" data-bs-toggle="collapse" class="nav-link px-0 align-middle">
                                            <i class="fs-4 bi-people"></i>
                                            <span class="ms-1 d-none d-sm-inline">Usuarios</span>
                                        </a>
                                        <ul class="collapse nav flex-column ms-1" id="submenu2" data-bs-parent="#menu">
                                            <?php if ($this->M_permisos->tiene_permiso($this->session->userdata('role'), 'usuarios', 'usuarios')): ?>
                                                <li class="w-100">
                                                    <a href="<?php echo site_url('usuarios'); ?>" class="nav-link px-0">
                                                        <i class="fs-4 bi bi-people-fill"></i>
                                                        <span class="d-none d-sm-inline">Usuarios</span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>

                                            <?php if ($this->M_permisos->tiene_permiso($this->session->userdata('role'), 'usuarios', 'roles')): ?>
                                                <li>
                                                    <a href="<?php echo site_url('roles'); ?>" class="nav-link px-0">
                                                        <i class="fs-4 bi bi-person-lines-fill"></i>
                                                        <span class="d-none d-sm-inline">Roles</span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>

                                            <?php if ($this->M_permisos->tiene_permiso($this->session->userdata('role'), 'usuarios', 'permisos')): ?>
                                                <li>
                                                    <a href="#" class="nav-link px-0">
                                                        <i class="fs-4 bi bi-emoji-sunglasses-fill"></i>
                                                        <span class="d-none d-sm-inline">Avatars</span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    <?php endif; ?>
                                </li>

                                <li>
                                    <?php if ($this->M_permisos->tiene_permiso($this->session->userdata('role'), 'rfid')): ?>
                                        <a href="#submenu3" data-bs-toggle="collapse" class="nav-link px-0 align-middle">
                                            <i class="fs-4 bi bi-radar"></i>
                                            <span class="ms-1 d-none d-sm-inline">RFID</span>
                                        </a>
                                        <ul class="collapse nav flex-column ms-1" id="submenu3" data-bs-parent="#menu">
                                            <?php if ($this->M_permisos->tiene_permiso($this->session->userdata('role'), 'rfid', 'lectura')): ?>
                                                <li class="w-100">
                                                    <a href="#" class="nav-link px-0">
                                                        <i class="fs-4 bi bi-check2-square"></i>
                                                        <span class="d-none d-sm-inline">Lectura</span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>

                                            <?php if ($this->M_permisos->tiene_permiso($this->session->userdata('role'), 'rfid', 'registro')): ?>
                                                <li>
                                                    <a href="#" class="nav-link px-0">
                                                        <i class="fs-4 bi bi-clipboard2-plus"></i>
                                                        <span class="d-none d-sm-inline">Registro</span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>

                                            <?php if ($this->M_permisos->tiene_permiso($this->session->userdata('role'), 'rfid', 'reportes')): ?>
                                                <li>
                                                    <a href="#" class="nav-link px-0">
                                                        <i class="fs-4 bi bi-journal-text"></i>
                                                        <span class="d-none d-sm-inline">Reportes</span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    <?php endif; ?>
                                </li>

                                <li>
                                    <?php if ($this->M_permisos->tiene_permiso($this->session->userdata('role'), 'documentacion')): ?>
                                        <a href="#submenu4" data-bs-toggle="collapse" class="nav-link px-0 align-middle">
                                            <i class="fs-4 bi bi-file-earmark-pdf"></i>
                                            <span class="ms-1 d-none d-sm-inline">Documentación</span>
                                        </a>
                                        <ul class="collapse nav flex-column ms-1" id="submenu4" data-bs-parent="#menu">
                                            <?php if ($this->M_permisos->tiene_permiso($this->session->userdata('role'), 'documentacion', 'manuales')): ?>
                                                <li class="w-100">
                                                    <a href="#" class="nav-link px-0">
                                                        <i class="fs-4 bi bi-files"></i>
                                                        <span class="d-none d-sm-inline">Manuales</span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>

                                            <?php if ($this->M_permisos->tiene_permiso($this->session->userdata('role'), 'documentacion', 'registro')): ?>
                                                <li>
                                                    <a href="#" class="nav-link px-0">
                                                        <i class="fs-4 bi bi-clipboard2-plus"></i>
                                                        <span class="d-none d-sm-inline">Registro</span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    <?php endif; ?>
                                </li>

                                <li>
                                    <?php if ($this->M_permisos->tiene_permiso($this->session->userdata('role'), 'mantenimiento')): ?>
                                        <a href="#submenu5" data-bs-toggle="collapse" class="nav-link px-0 align-middle">
                                            <i class="fs-4 bi bi-info-circle"></i>
                                            <span class="ms-1 d-none d-sm-inline">Mantenimiento</span>
                                        </a>
                                        <ul class="collapse nav flex-column ms-1" id="submenu5" data-bs-parent="#menu">
                                            <?php if ($this->M_permisos->tiene_permiso($this->session->userdata('role'), 'mantenimiento', 'manuales')): ?>
                                                <li class="w-100">
                                                    <a href="#" class="nav-link px-0">
                                                        <i class="fs-4 bi bi-phone-vibrate"></i>
                                                        <span class="d-none d-sm-inline">Contacto</span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>

                                            <?php if ($this->M_permisos->tiene_permiso($this->session->userdata('role'), 'mantenimiento', 'solicitud')): ?>
                                                <li>
                                                    <a href="#" class="nav-link px-0">
                                                        <i class="fs-4 bi bi-journal-text"></i>
                                                        <span class="d-none d-sm-inline">Reportes</span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    <?php endif; ?>
                                </li>
                            </ul>

                            <hr>
                            <div class="dropdown pb-4">
                                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                                   <img src="assets/imagenes/usuarios/oscar.png" alt="Usuario" width="50" height="50" class="rounded-circle">
                                    <span class="d-none d-sm-inline mx-1"><?php echo $this->session->userdata('username'); ?> <br> <?php echo $this->session->userdata('rol_name');  ?></span>
                                </a>
                                <div class="text-center">
                                    <?php if ($this->session->userdata('user_id')): ?>
                                        <span class="status-indicator"></span>
                                        <span class="status-text d-none d-md-inline">En línea</span>
                                    <?php else: ?>
                                        <span class="status-indicator"></span>
                                        <span class="status-text d-none d-md-inline">Desconectado</span>
                                    <?php endif; ?>
                                </div>
                                <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
                                    <li><a class="dropdown-item" href="#">Perfil</a></li>
                                    <li><a class="dropdown-item" href="#">Configuración</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="<?php echo site_url('logout'); ?> ">Cerrar sesión</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <script>
                        function updateDateTime() {
                            const options = {
                                timeZone: 'America/La_Paz',
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit',
                                second: '2-digit'
                            };
                            const now = new Date().toLocaleString('es-ES', options);
                            document.getElementById('currentDateTime').textContent = now;
                        }

                        // Actualizar la fecha y hora al cargar la página
                        updateDateTime();
                        // Actualizar la fecha y hora cada segundo
                        setInterval(updateDateTime, 1000);
                    </script>