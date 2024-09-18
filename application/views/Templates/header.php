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
            font-size: 14px;

        }

        td,
        th {
            font-size: 13px !important;
        }

        /* PARA EL TAMAÑO DEL CONTENIDO CENTRAL TOMANDO EL ASIDE*/
        .py-3 {
            max-width: 80%;
            overflow-x: hidden;
        }

        /* PARA EL TAMAÑO DEL CONTENIDO CENTRAL TOMANDO EL ASIDE*/

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
        /* COLORES TEMAS */
        /* Tema Azul - por defecto */
        .default-theme .bg-primary,
        .default-theme .btn-primary {
            background-color: #007bff !important;
            border-color: #007bff !important;
        }

        .default-theme .text-primary {
            color: #007bff !important;
        }

        .default-theme #menu a.nav-link:hover {
            color: #0056b3 !important;
            /* Color del texto al hacer hover en el tema azul */
            transition: background-color 0.3s ease, color 0.3s ease;
            transform: scale(1.1);
        }

        /* Tema Verde */
        .green-theme .bg-primary,
        .green-theme .btn-primary {
            background-color: #28a745 !important;
            border-color: #28a745 !important;
        }

        .green-theme .text-primary {
            color: #28a745 !important;
        }

        .green-theme #menu a.nav-link:hover {
            color: #1e7e34 !important;
            /* Color del texto al hacer hover en el tema verde */
            /* Color de fondo al hacer hover en el tema verde */
            transition: background-color 0.3s ease, color 0.3s ease;
            transform: scale(1.1);
        }

        /* Tema Naranja */
        .orange-theme .bg-primary,
        .orange-theme .btn-primary {
            background-color: #fd7e14 !important;
            border-color: #fd7e14 !important;
        }

        .orange-theme .text-primary {
            color: #fd7e14 !important;
        }

        .orange-theme #menu a.nav-link:hover {
            color: #e56b00 !important;
            /* Color del texto al hacer hover en el tema naranja */
            /* Color de fondo al hacer hover en el tema naranja */
            transition: background-color 0.3s ease, color 0.3s ease;
            transform: scale(1.1);
        }

        /* Tema Morado */
        .red-theme .bg-primary,
        .red-theme .btn-primary {
            background-color: #8747E7 !important;
            border-color: #8747E7 !important;
        }

        .red-theme .text-primary {
            color: #8747E7 !important;
        }

        .red-theme #menu a.nav-link:hover {
            color: #8747E7 !important;
            /* Color de fondo al hacer hover en el tema rojo */
            transition: background-color 0.3s ease, color 0.3s ease;
            transform: scale(1.1);
        }

        /* COLORES TEMAS PAGINACIÓN */
        /* Tema Azul - por defecto */
        .default-theme .pagination a {
            color: #ffffff;
            /* Texto blanco sobre fondo azul */
            background-color: #007bff;
            /* Fondo azul */
            border: none;
            /* Sin bordes */
        }

        .default-theme .pagination .active a {
            background-color: #0056b3;
            /* Fondo azul oscuro */
            color: #ffffff;
            /* Texto blanco */
        }

        .default-theme .pagination .disabled a {
            color: #6c757d;
            /* Texto gris para deshabilitados */
            background-color: #e9ecef;
            /* Fondo gris claro */
        }

        /* Tema Verde */
        .green-theme .pagination a {
            color: #ffffff;
            /* Texto blanco sobre fondo verde */
            background-color: #28a745;
            /* Fondo verde */
            border: none;
            /* Sin bordes */
        }

        .green-theme .pagination .active a {
            background-color: #1e7e34;
            /* Fondo verde oscuro */
            color: #ffffff;
            /* Texto blanco */
        }

        .green-theme .pagination .disabled a {
            color: #6c757d;
            /* Texto gris para deshabilitados */
            background-color: #e9ecef;
            /* Fondo gris claro */
        }

        /* Tema Naranja */
        .orange-theme .pagination a {
            color: #ffffff;
            /* Texto blanco sobre fondo naranja */
            background-color: #fd7e14;
            /* Fondo naranja */
            border: none;
            /* Sin bordes */
        }

        .orange-theme .pagination .active a {
            background-color: #e56b00;
            /* Fondo naranja oscuro */
            color: #ffffff;
            /* Texto blanco */
        }

        .orange-theme .pagination .disabled a {
            color: #6c757d;
            /* Texto gris para deshabilitados */
            background-color: #e9ecef;
            /* Fondo gris claro */
        }

        /* Tema Rojo */
        .red-theme .pagination a {
            color: #ffffff;
            /* Texto blanco sobre fondo rojo */
            background-color: #8747E7;
            /* Fondo rojo */
            border: none;
            /* Sin bordes */
        }

        .red-theme .pagination .active a {
            background-color: #8747E7;
            /* Fondo rojo oscuro */
            color: #ffffff;
            /* Texto blanco */
        }

        .red-theme .pagination .disabled a {
            color: #6c757d;
            /* Texto gris para deshabilitados */
            background-color: #AFA7BA;
            /* Fondo gris claro */
        }
    </style>

    

    <div class="container-fluid">
        <div class="row">
            <div class="container-fluid ">
                <div class="row flex-nowrap">
                    <!--AQUI ESTA EL ASIDE-->
                    <div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 bg-dark ">
                          <!--aside fijo sticky top-->
                        <div class="d-flex sticky-top flex-column align-items-center align-items-sm-start px-3 pt-2 text-white min-vh-100">
                            <a href="" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                                <span class="fs-5 d-none d-sm-inline">SISAMED <?php echo date('Y'); ?></span>
                            </a>
                           
                            <!--  <a href="" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                                <span class="fs-5 d-none d-sm-inline"id="currentDateTime"></span>
                            </a>-->

                            <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start collapse-horizontal" id="menu">
                                <li>
                                    <a href="<?php echo site_url('welcome'); ?>" class="nav-link px-0 align-middle">
                                        <i class="fs-4 bi bi-house"></i> <span class="ms-1 d-none d-sm-inline">Inicio</span>
                                    </a>
                                </li>

                                <li>
                                    <?php if ($this->M_permisos->tiene_permiso($this->session->userdata('role'), 'equipos')): ?>
                                        <a href="#submenu1" data-bs-toggle="collapse" class="nav-link px-0 align-middle">
                                            <i class="fs-4 bi bi-pc-display-horizontal"></i> <span class="ms-1 d-none d-sm-inline">Equipos Médicos</span>
                                        </a>
                                        <ul class="collapse nav flex-column ms-1" id="submenu1" data-bs-parent="#menu">
                                            <?php if ($this->M_permisos->tiene_permiso($this->session->userdata('role'), 'equipos', 'registro')): ?>
                                                <li class="w-100">
                                                    <a href="<?php echo site_url('equipos'); ?>" class="nav-link px-0">
                                                        <i class="fs-4 bi bi-pencil-square"></i>
                                                        <span class="d-none d-sm-inline">Registro</span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>

                                            <?php if ($this->M_permisos->tiene_permiso($this->session->userdata('role'), 'equipos', 'listado')): ?>
                                                <li>
                                                    <a href="<?php echo site_url('listado'); ?>" class="nav-link px-0">
                                                        <i class="fs-4 bi bi-list-check"></i>
                                                        <span class="d-none d-sm-inline">Listado</span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>

                                            <?php if ($this->M_permisos->tiene_permiso($this->session->userdata('role'), 'equipos', 'reportes')): ?>
                                                <li>
                                                    <a href="<?php echo site_url('reportes'); ?>" class="nav-link px-0">
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
                                                        <span class="d-none d-sm-inline">Registros</span>
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
                                                    <a href="<?php echo site_url('avatars'); ?>" class="nav-link px-0">
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
                                            <i class="fs-4 bi bi-router"></i>
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
                                            <i class="fs-4 bi bi-wrench-adjustable-circle-fill"></i>
                                            <span class="ms-1 d-none d-sm-inline">Mantenimiento</span>
                                        </a>
                                        <ul class="collapse nav flex-column ms-1" id="submenu5" data-bs-parent="#menu">
                                            <?php if ($this->M_permisos->tiene_permiso($this->session->userdata('role'), 'mantenimiento', 'panel')): ?>
                                                <li class="w-100">
                                                    <a href="<?php echo site_url('panel'); ?>" class="nav-link px-0">
                                                        <i class="fs-4 bi bi-person-workspace"></i>
                                                        <span class="d-none d-sm-inline">Panel Admin</span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>

                                            <?php if ($this->M_permisos->tiene_permiso($this->session->userdata('role'), 'mantenimiento', 'solicitud')): ?>
                                                <li>
                                                    <a href="<?php echo site_url('solicitud'); ?>" class="nav-link px-0">
                                                        <i class="fs-4 bi bi-headset"></i>
                                                        <span class="d-none d-sm-inline">Solicitudes</span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>

                                            <?php if ($this->M_permisos->tiene_permiso($this->session->userdata('role'), 'mantenimiento', 'soporte')): ?>
                                                <li class="w-100">
                                                    <a href="<?php echo site_url('tecnicos'); ?>" class="nav-link px-0">
                                                        <i class="fs-4 bi bi-ui-checks "></i>
                                                        <span class="d-none d-sm-inline">Tecnicos</span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    <?php endif; ?>
                                </li>
                            </ul>

                            <div class="dropdown pb-4">
                                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img id="userAvatar" src="<?php echo base_url($this->session->userdata('avatar_path')); ?>" alt="Usuario" width="50" height="50" class="rounded-circle">
                                    <span class="d-none d-sm-inline mx-1"><?php echo $this->session->userdata('username'); ?> <br> <?php echo $this->session->userdata('rol_name'); ?></span>
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
                                    <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#avatarModal">Avatars</a></li>
                                    <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#themeModal">Cambiar Tema</a></li> <!-- Nueva opción -->
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="<?php echo site_url('logout'); ?>">Cerrar sesión</a></li>
                                </ul>
                            </div>
                        </div>

                    </div>



                    <!-- Modal para seleccionar avatar -->
                    <div class="modal fade" id="avatarModal" tabindex="-1" aria-labelledby="avatarModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="avatarModalLabel">Selecciona tu avatar</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <!-- Galería de avatares -->
                                    <div id="avatarGallery" class="row">
                                        <!-- Los avatares se cargarán aquí mediante AJAX -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal para elegir el tema -->
                    <div class="modal fade" id="themeModal" tabindex="-1" aria-labelledby="themeModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="themeModalLabel">Seleccionar Tema</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="themeForm">
                                        <div class="mb-3">
                                            <label for="colorThemeSelector" class="form-label">Elige un tema:</label>
                                            <select id="colorThemeSelector" class="form-select">
                                                <option value="default">Tema Azul</option>
                                                <option value="green">Tema Verde</option>
                                                <option value="orange">Tema Naranja</option>
                                                <option value="red">Tema Morado</option>
                                            </select>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>


                    <script>
                        //tema de fondo o colores de fondo
                        document.addEventListener('DOMContentLoaded', () => {
                            const colorThemeSelector = document.getElementById('colorThemeSelector');

                            // Aplicar el color guardado (si existe)
                            const savedColorTheme = localStorage.getItem('colorTheme');
                            if (savedColorTheme) {
                                document.body.classList.add(`${savedColorTheme}-theme`);
                                colorThemeSelector.value = savedColorTheme;
                            }

                            // Cambiar el color al seleccionar una opción
                            colorThemeSelector.addEventListener('change', function() {
                                const selectedColorTheme = this.value;

                                // Eliminar las clases de tema anteriores
                                document.body.classList.remove('green-theme', 'orange-theme', 'red-theme');

                                // Aplicar el nuevo tema
                                if (selectedColorTheme !== 'default') {
                                    document.body.classList.add(`${selectedColorTheme}-theme`);
                                }

                                // Guardar la selección en localStorage
                                localStorage.setItem('colorTheme', selectedColorTheme);
                            });
                        });
                    </script>
                    <script>
                        $(document).ready(function() {
                            // Función para cargar los avatares desde el servidor
                            function cargarAvatares() {
                                $.ajax({
                                    url: '<?php echo site_url('obtener_avatares'); ?>',
                                    type: 'GET',
                                    dataType: 'json',
                                    success: function(data) {
                                        let avatarGallery = $('#avatarGallery');
                                        avatarGallery.empty();
                                        $.each(data, function(index, avatar) {
                                            avatarGallery.append(`
                            <div class="col-md-3 mb-3">
                                <div class="card">
                                    <img src="<?php echo base_url(); ?>${avatar.ruta_imagen}" class="card-img-top img-thumbnail rounded-circle" alt="${avatar.nombre}">
                                    <div class="card-body text-center">
                                        <p class="card-title">${avatar.nombre}</p>
                                        <button class="btn btn-primary select-avatar" data-avatar-id="${avatar.id}" data-avatar-path="${avatar.ruta_imagen}"><i class="bi bi-hand-index"></i></button>
                                    </div>
                                </div>
                            </div>
                        `);
                                        });
                                    }
                                });
                            }

                            // Cargar los avatares cuando se abre el modal
                            $('#avatarModal').on('show.bs.modal', function() {
                                cargarAvatares();
                            });

                            // Manejar la selección del avatar
                            $(document).on('click', '.select-avatar', function() {
                                const avatarId = $(this).data('avatar-id');
                                const avatarPath = $(this).data('avatar-path');

                                // Enviar el ID del avatar al controlador para actualizar la base de datos
                                $.ajax({
                                    url: '<?php echo site_url('actualizar_avatar'); ?>',
                                    type: 'POST',
                                    data: {
                                        avatar_id: avatarId
                                    },
                                    dataType: 'json',
                                    success: function(response) {
                                        if (response.success) {
                                            // Actualizar la imagen del avatar en la interfaz
                                            $('#userAvatar').attr('src', '<?php echo base_url(); ?>' + avatarPath);

                                            // SweetAlert success
                                            Swal.fire({
                                                icon: 'success',
                                                title: '¡Éxito!',
                                                text: response.message
                                            });
                                        } else {
                                            // SweetAlert error
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Oops...',
                                                text: response.message
                                            });
                                        }
                                    }
                                });

                                // Cierra el modal después de seleccionar el avatar
                                $('#avatarModal').modal('hide');
                            });
                        });
                    </script>


                    <script>
                        /*
                        function updateDateTime() {
                            const options = {
                                timeZone: 'America/La_Paz',
                                year: 'numeric',
                                month: 'numeric',
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
                        setInterval(updateDateTime, 1000);*/
                    </script>