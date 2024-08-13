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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>


</head>

<body>
    <style>
        * {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;

            .status-indicator {
                width: 10px;
                height: 10px;
                background-color: #3AEF2E;
                border-radius: 50%;
                display: inline-block;
                margin-right: 5px;
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
        }

        #menu a.nav-link {
            color: #ffffff;
            padding: 10px;
            border-radius: 5px;
            text-decoration: none;
        }

        /* Efecto hover para los enlaces del menú */
        #menu a.nav-link:hover {
            color: #26DE17;
            /* Color de texto al hacer hover */
            transition: background-color 0.3s ease, color 0.3s ease;
        }
    </style>

    <div class="container-fluid">
        <div class="row">
            <nav class="navbar" style="background-color: #3587F1;">
                <div class="container-fluid">
                    <span class="navbar-text text-white ms-auto">
                        <span id="currentDateTime"></span>
                    </span>
                </div>

            </nav>
            <div class="container">
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
                                        <li class="w-100">
                                            <a href="#" class="nav-link px-0"> <i class="fs-4 bi bi-pencil-square"></i> <span class="d-none d-sm-inline">Registro</span></a>
                                        </li>
                                        <li>
                                            <a href="#" class="nav-link px-0"> <i class="fs-4 bi bi-list-check"></i> <span class="d-none d-sm-inline">Listado</span></a>
                                        </li>
                                        <li>
                                            <a href="#" class="nav-link px-0"> <i class="fs-4 bi bi-journal-text"></i> <span class="d-none d-sm-inline">Reportes</span></a>
                                        </li>
                                    </ul>
                                    <?php endif; ?>
                                </li>
                            

                              
                                <li>
                                <?php if ($this->M_permisos->tiene_permiso($this->session->userdata('role'), 'usuarios')): ?>
                                    <a href="#submenu2" data-bs-toggle="collapse" class="nav-link px-0 align-middle">
                                        <i class="fs-4 bi-people"></i> <span class="ms-1 d-none d-sm-inline">Usuarios</span>
                                    </a>
                                    <ul class="collapse nav flex-column ms-1" id="submenu2" data-bs-parent="#menu">
                                        <li class="w-100">
                                            <a href="<?php echo site_url('usuarios'); ?>" class="nav-link px-0"> <i class=" fs-4 bi bi-person-add"></i> <span class="d-none d-sm-inline">Nuevo Usuario</span></a>
                                        </li>
                                        <li>
                                            <a href="<?php echo site_url('roles'); ?>" class="nav-link px-0"> <i class="fs-4 bi bi-person-lines-fill"></i> <span class="d-none d-sm-inline">Roles</span></a>
                                        </li>
                                        <li>
                                            <a href="#" class="nav-link px-0"> <i class="fs-4 bi bi-person-fill-lock"></i> <span class="d-none d-sm-inline">Permisos</span></a>
                                        </li>
                                    </ul>
                                    <?php endif; ?>
                                </li>
                            


                                <li>
                                <?php if ($this->M_permisos->tiene_permiso($this->session->userdata('role'), 'rfid')): ?>
                                    <a href="#submenu3" data-bs-toggle="collapse" class="nav-link px-0 align-middle">
                                        <i class="fs-4 bi bi-radar"></i> <span class="ms-1 d-none d-sm-inline">RFID</span>
                                    </a>
                                    <ul class="collapse nav flex-column ms-1" id="submenu3" data-bs-parent="#menu">
                                        <li class="w-100">
                                            <a href="#" class="nav-link px-0"> <i class="fs-4 bi bi-check2-square"></i> <span class="d-none d-sm-inline">Lectura</span></a>
                                        </li>
                                        <li>
                                            <a href="#" class="nav-link px-0"> <i class="fs-4 bi bi-clipboard2-plus"></i> <span class="d-none d-sm-inline">Registro</span></a>
                                        </li>
                                        <li>
                                            <a href="#" class="nav-link px-0"> <i class="fs-4 bi bi-journal-text"></i> <span class="d-none d-sm-inline">Reportes</span></a>
                                        </li>
                                    </ul>
                                    <?php endif; ?>
                                </li>

                                <li>
                                <?php if ($this->M_permisos->tiene_permiso($this->session->userdata('role'), 'documentacion')): ?>
                                <a href="#submenu4" data-bs-toggle="collapse" class="nav-link px-0 align-middle">
                                        <i class="fs-4 bi bi-file-earmark-pdf"></i> <span class="ms-1 d-none d-sm-inline">Documentacion</span>
                                    </a>
                                    <ul class="collapse nav flex-column ms-1" id="submenu4" data-bs-parent="#menu">
                                        <li class="w-100">
                                            <a href="#" class="nav-link px-0"> <i class="fs-4 bi bi-files"></i> <span class="d-none d-sm-inline">Manuales</span></a>
                                        </li>
                                        <li>
                                            <a href="#" class="nav-link px-0"> <i class="fs-4 bi bi-clipboard2-plus"> </i> <span class="d-none d-sm-inline">Registro</span></a>
                                        </li>
                                    </ul>
                                    <?php endif; ?>
                                </li>

                                <li>
                                <?php if ($this->M_permisos->tiene_permiso($this->session->userdata('role'), 'mantenimiento')): ?>
                                    <a href="#submenu5" data-bs-toggle="collapse" class="nav-link px-0 align-middle">
                                        <i class="fs-4 bi bi-wrench-adjustable-circle"></i> <span class="ms-1 d-none d-sm-inline">Mantenimiento</span>
                                    </a>
                                    <ul class="collapse nav flex-column ms-1" id="submenu5" data-bs-parent="#menu">
                                        <li class="w-100">
                                            <a href="#" class="nav-link px-0"> <i class="fs-4 bi bi-files"></i> <span class="d-none d-sm-inline">Manuales</span></a>
                                        </li>
                                        <li>
                                            <a href="#" class="nav-link px-0"> <i class="fs-4 bi bi-clipboard2-plus"> </i> <span class="d-none d-sm-inline">Solicitud</span></a>
                                        </li>
                                    </ul>
                                </li>
                                <?php endif; ?>
                            </ul>

                            <hr>
                            <div class="dropdown pb-4">
                                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="assets/imagenes/usuarios/oscar.png" alt="Usuario" width="40" height="40" class="rounded-circle">
                                    <span class="d-none d-sm-inline mx-1"><?php echo $this->session->userdata('username'); ?> <br> <?php echo $this->session->userdata('rol_name');  ?></span>
                                </a>
                                <Center>
                                    <p>
                                        <?php if ($this->session->userdata('user_id')): ?>
                                            <span class="status-indicator"></span>
                                            <span class="status-text">En línea</span>
                                        <?php else: ?>
                                            <span class="status-indicator"></span>
                                            <span class="status-text">Desconectado</span>
                                        <?php endif; ?>
                                    </p>
                                </Center>
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