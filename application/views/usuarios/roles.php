<?php $this->load->view('templates/header'); ?>

<style>
    th,
    td {
        border: 1px solid #ddd;
        padding: 8px;
    }

    th {
        background-color: #f2f2f2;
    }

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    /* Contenedor de la paginación */
    .pagination {
        display: flex;
        justify-content: center;
        margin: 20px 0;
    }

    /* Estilo de los enlaces de paginación */
    .pagination a {
        color: #404BDF;
        /* Color del texto */
        padding: 8px 16px;
        margin: 0 4px;
        border: 1px solid #dee2e6;
        /* Borde del enlace */
        border-radius: 4px;
        text-decoration: none;
        font-size: 16px;
    }

    /* Estilo del enlace activo */
    .pagination .active a {
        background-color: #7A7CE5;
        /* Fondo del enlace activo */
        color: #fff;
        /* Color del texto activo */
        border-color: #7A84E5;
    }

    /* Estilo de los enlaces deshabilitados */
    .pagination .disabled a {
        color: #6c757d;
        /* Color del texto deshabilitado */
        border-color: #dee2e6;
        cursor: not-allowed;
    }

    /* Estilo de los iconos de navegación */
    .pagination .disabled a,
    .pagination .active a {
        cursor: default;
    }

    /* Estilo para el primer y último enlace de paginación */
    .pagination .first,
    .pagination .last {
        font-weight: bold;
    }
</style>

<style>
    .modulo-item {
        margin-bottom: 20px;
    }

    .modulo-title {
        font-size: 1.2em;
        font-weight: bold;
        color: #007bff;
        /* Color del módulo */
    }

    .submodulo-list {
        list-style-type: none;
        padding-left: 20px;
    }

    .submodulo-item {
        font-size: 1em;
        color: #333;
        /* Color del submódulo */
        margin-bottom: 5px;
    }

    .submodulo-item::before {
        content: "• ";
        color: #007bff;
        /* Color del punto */
    }
</style>


<div class="col py-3">

    <div class="row">
        <div class="col py-3">
            <div class="row">
                <div class="col-md-6">
                    <!-- Botón para abrir el modal de agregar rol -->
                    <button type="button" class="btn mb-3" style="background-color:#7A7CE5; color:#fff" data-bs-toggle="modal" data-bs-target="#agregarRolModal">
                        <i class="bi bi-plus-circle"></i> Agregar Rol
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para agregar rol -->
    <div class="modal fade" id="agregarRolModal" tabindex="-1" aria-labelledby="agregarRolModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="agregarRolModalLabel">Agregar Nuevo Rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formAgregarRol" action="<?php echo site_url('roles/agregar'); ?>" method="post">
                        <div class="mb-3">
                            <label for="nombreRol" class="form-label">Nombre del Rol</label>
                            <input type="text" class="form-control" id="nombreRol" name="nombreRol" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Permisos</label>
                            <div id="modulos">
                                <p>No hay módulos disponibles.</p> <!-- Mensaje de módulos no disponibles -->
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para editar rol -->
    <!-- Modal -->
    <div class="modal fade" id="editarRolModal" tabindex="-1" aria-labelledby="editarRolModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarRolModalLabel">Editar Rol <span id="rolNombreTitulo"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Contenido del modal -->
                    <input type="hidden" id="rolId">
                    <div class="mb-3">
                        <label for="nombreRolEdit" class="form-label">Nombre del Rol</label>
                        <input type="text" class="form-control" id="nombreRolEdit">
                    </div>
                    <div class="mb-3">
                        <label for="estadoRolEdit" class="form-label">Estado</label>
                        <input type="checkbox" id="estadoRolEdit">
                    </div>
                    <div id="editarPermisos"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary">Guardar cambios</button>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <h1 class="mb-3">Lista de Roles de Usuario</h1>
        <div class="table table-responsive">
            <table class="table table-hover table-bordered table-responsive">
                <thead style="background-color:#7A7CE5; color:#fff">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbRoles">
                    <!-- Contenido cargado por JavaScript -->
                </tbody>
            </table>

            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <!-- Contenido cargado por JavaScript -->
                </ul>
            </nav>

        </div>
    </div>

    <!-- Modal para ver permisos -->
    <div class="modal fade" id="verPermisosModal" tabindex="-1" aria-labelledby="verPermisosModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="verPermisosModalLabel">Permisos del Rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="verPermisosBody">
                    <!-- Los permisos del rol se cargarán aquí -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

</div>


<script>
    // PARA OBTENER LOS ROLES Y COLOCARLOS EN EL CHECKBOX DE MODAL AGREGAR
    $(document).ready(function() {
        // Función para cargar módulos y submódulos
        function cargarModulos() {
            $.ajax({
                url: "<?php echo site_url('permisos/obtenerPermisosUnicos'); ?>", // Cambia esta URL según la ruta de tu controlador
                type: "GET",
                dataType: "json",
                success: function(response) {
                    console.log(response); // Depuración: Verifica la respuesta en la consola

                    var modulosHtml = "";
                    if (response.modulos && response.modulos.length > 0) {
                        $.each(response.modulos, function(key, modulo) {
                            modulosHtml += '<div class="form-check">' +
                                '<input class="form-check-input" type="checkbox" name="modulos[]" value="' + modulo.modulo + '" id="modulo-' + modulo.modulo + '">' +
                                '<label class="form-check-label" for="modulo-' + modulo.modulo + '">' +
                                modulo.modulo +
                                '</label>';

                            // Submódulos
                            if (modulo.submodulos && modulo.submodulos.length > 0) {
                                modulosHtml += '<div class="ps-4">';
                                $.each(modulo.submodulos, function(key, submodulo) {
                                    modulosHtml += '<div class="form-check">' +
                                        '<input class="form-check-input" type="checkbox" name="submodulos[' + modulo.modulo + '][]" value="' + submodulo + '" id="submodulo-' + submodulo + '">' +
                                        '<label class="form-check-label" for="submodulo-' + submodulo + '">' +
                                        submodulo +
                                        '</label>' +
                                        '</div>';
                                });
                                modulosHtml += '</div>';
                            }

                            modulosHtml += '</div>';
                        });
                    } else {
                        modulosHtml = "<p>No hay módulos disponibles.</p>";
                    }

                    $("#modulos").html(modulosHtml);
                },
                error: function(xhr, status, error) {
                    console.error("Error al cargar los módulos:", error);
                    $("#modulos").html("<p>Error al cargar los módulos.</p>");
                }
            });
        }

        // Llama a la función para cargar los módulos cuando se abre el modal
        $('#agregarRolModal').on('shown.bs.modal', function() {
            cargarModulos();
        });
    });
</script>

<script>
    //PARA EDITAR, PERO SOLO LLENA LOS CAMPOS DEL REGISTRO EN EL MODAL EDITAR

    var editarRolModal = document.getElementById('editarRolModal');
      // Actualizar el título del modal con el nombre del rol

    editarRolModal.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var nombre = button.getAttribute('data-nombre');
        var estado = button.getAttribute('data-estado');

        // Concatenar el nombre del rol al título del modal
        var paratitulo = button.getAttribute('data-nombre');
        $('#editarRolModalLabel').text("Editar Rol " + paratitulo);

        var modalIdInput = editarRolModal.querySelector('#rolId');
        var modalNombreInput = editarRolModal.querySelector('#nombreRolEdit');
        var modalEstadoCheckbox = editarRolModal.querySelector('#estadoRolEdit');

        // Limpiar el contenido previo del modal
        modalIdInput.value = '';
        modalNombreInput.value = '';
        modalEstadoCheckbox.checked = false;
        $("#editarPermisos").html(''); // Limpiar los permisos del modal

        // Establecer los valores actuales
        modalIdInput.value = id;
        modalNombreInput.value = nombre;
        modalEstadoCheckbox.checked = estado == 1;

        // Cargar los módulos y submódulos
        cargarModulos2();
        cargarPermisosDelRol(id); // Cargar permisos del rol para marcar los checkboxes
    });

    

    // Función para cargar módulos y submódulos
    function cargarModulos2() {
        $.ajax({
            url: "<?php echo site_url('permisos/obtenerPermisosUnicos'); ?>",
            type: "GET",
            dataType: "json",
            success: function(response) {
                console.log(response); // Depuración: Verifica la respuesta en la consola

                var modulosHtml = "";
                if (response.modulos && response.modulos.length > 0) {
                    $.each(response.modulos, function(key, modulo) {
                        modulosHtml += '<div class="form-check">' +
                            '<input class="form-check-input" type="checkbox" name="modulos[]" value="' + modulo.modulo + '" id="modulo-' + modulo.modulo + '">' +
                            '<label class="form-check-label" for="modulo-' + modulo.modulo + '">' +
                            modulo.modulo +
                            '</label>';

                        // Submódulos
                        if (modulo.submodulos && modulo.submodulos.length > 0) {
                            modulosHtml += '<div class="ps-4">';
                            $.each(modulo.submodulos, function(key, submodulo) {
                                modulosHtml += '<div class="form-check">' +
                                    '<input class="form-check-input" type="checkbox" name="submodulos[' + modulo.modulo + '][]" value="' + submodulo + '" id="' + modulo.modulo + '_submodulo_' + submodulo + '">' +
                                    '<label class="form-check-label" for="' + modulo.modulo + '_submodulo_' + submodulo + '">' +
                                    submodulo +
                                    '</label>' +
                                    '</div>';
                            });
                            modulosHtml += '</div>';
                        }

                        modulosHtml += '</div>';
                    });
                } else {
                    modulosHtml = "<p>No hay módulos disponibles.</p>";
                }

                $("#editarPermisos").html(modulosHtml);
            },
            error: function(xhr, status, error) {
                console.error("Error al cargar los módulos:", error);
                $("#editarPermisos").html("<p>Error al cargar los módulos.</p>");
            }
        });
    }

    // Función para cargar los permisos del rol y marcar los checkboxes correspondientes
    function cargarPermisosDelRol(rolId) {
        $.ajax({
            url: "<?php echo site_url('usuarios/obtenerPermisosDeUnRol'); ?>",
            type: "POST",
            data: {
                rol_id: rolId
            },
            dataType: "json",
            success: function(response) {
                console.log("Respuesta del servidor:", response);

                if (response.permisos && response.permisos.length > 0) {
                    console.log("Permisos a procesar:", response.permisos);

                    $.each(response.permisos, function(key, permiso) {
                        console.log("Procesando permiso:", permiso);

                        // Marca el checkbox del módulo
                        var moduloCheckbox = $('#modulo-' + permiso.modulo);
                        if (moduloCheckbox.length > 0) {
                            console.log("Marcando checkbox del módulo:", moduloCheckbox.attr('id'));
                            moduloCheckbox.prop('checked', true);
                        } else {
                            console.warn("Checkbox del módulo no encontrado:", permiso.modulo);
                        }

                        // Marca el checkbox del submódulo si existe
                        if (permiso.submodulo) {
                            var submoduloCheckbox = $('#' + permiso.modulo + '_submodulo_' + permiso.submodulo);
                            if (submoduloCheckbox.length > 0) {
                                console.log("Marcando checkbox del submódulo:", submoduloCheckbox.attr('id'));
                                submoduloCheckbox.prop('checked', true);
                            } else {
                                console.warn("Checkbox del submódulo no encontrado:", permiso.submodulo);
                            }
                        }
                    });
                } else {
                    console.log("No se encontraron permisos para este rol.");
                }
            },
            error: function(xhr, status, error) {
                console.error("Error al cargar los permisos del rol:", error);
            }
        });
    }
</script>



<script>
    // TABLA DE ROLES
    function mostrarRoles(pagina = 1, cantidad = 5) {
        $.ajax({
            url: "<?php echo site_url('usuarios/mostrarRoles'); ?>",
            type: "POST",
            data: {
                pagina: pagina,
                cantidad: cantidad
            },
            dataType: "json",
            success: function(response) {
                var filas = "";
                $.each(response.roles, function(key, item) {
                    filas += '<tr>' +
                        '<td>' + item.id + '</td>' +
                        '<td>' + item.nombre + '</td>' +
                        '<td>' + (item.estado == 1 ? 'Activo' : 'Inactivo') + '</td>' +
                        '<td>' +
                        '<button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editarRolModal" data-id="' + item.id + '" data-nombre="' + item.nombre + '" data-estado="' + item.estado + '">' +
                        'Editar</button>' +
                        ' <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#verPermisosModal" data-id="' + item.id + '" data-nombre="' + item.nombre + '" onclick="mostrarPermisos(' + item.id + ', \'' + item.nombre + '\')">' +
                        '<i class="bi bi-eye"></i>Ver</button>' +
                        '</td>' +
                        '</tr>';
                });

                $("#tbRoles").html(filas);

                // Paginación
                var totalregistros = response.totalregistros;
                var numerolinks = Math.ceil(totalregistros / cantidad);
                var paginador = "";

                if (pagina > 1) {
                    paginador += "<li class='page-item'><a class='page-link' href='javascript:void(0)' onclick='mostrarRoles(1, " + cantidad + ")'>&laquo;</a></li>";
                    paginador += "<li class='page-item'><a class='page-link' href='javascript:void(0)' onclick='mostrarRoles(" + (pagina - 1) + ", " + cantidad + ")'>&lsaquo;</a></li>";
                } else {
                    paginador += "<li class='page-item disabled'><a class='page-link' href='#'>&laquo;</a></li>";
                    paginador += "<li class='page-item disabled'><a class='page-link' href='#'>&lsaquo;</a></li>";
                }

                for (var i = 1; i <= numerolinks; i++) {
                    paginador += "<li class='page-item" + (i === pagina ? " active" : "") + "'><a class='page-link' href='javascript:void(0)' onclick='mostrarRoles(" + i + ", " + cantidad + ")'>" + i + "</a></li>";
                }

                if (pagina < numerolinks) {
                    paginador += "<li class='page-item'><a class='page-link' href='javascript:void(0)' onclick='mostrarRoles(" + (pagina + 1) + ", " + cantidad + ")'>&rsaquo;</a></li>";
                    paginador += "<li class='page-item'><a class='page-link' href='javascript:void(0)' onclick='mostrarRoles(" + numerolinks + ", " + cantidad + ")'>&raquo;</a></li>";
                } else {
                    paginador += "<li class='page-item disabled'><a class='page-link' href='#'>&rsaquo;</a></li>";
                    paginador += "<li class='page-item disabled'><a class='page-link' href='#'>&raquo;</a></li>";
                }

                $(".pagination").html(paginador);
            }
        });
    }

    $(document).ready(function() {
        mostrarRoles(); // Inicializa con página 1 y 5 registros por página
    });
</script>

<script>
    // Función para mostrar permisos del rol en el modal
    function mostrarPermisos(rolId, rolNombre) {
        console.log('Nombre: ', rolNombre);
        $.ajax({
            url: "<?php echo site_url('usuarios/obtenerPermisosDeUnRol'); ?>",
            type: "POST",
            data: {
                rol_id: rolId
            },
            dataType: "json",
            success: function(response) {
                var permisos = response.permisos;
                var permisosHtml = "";
                if (permisos && permisos.length > 0) {
                    var permisosPorModulo = {};

                    // Organizar permisos por módulo
                    $.each(permisos, function(key, permiso) {
                        if (!permisosPorModulo[permiso.modulo]) {
                            permisosPorModulo[permiso.modulo] = [];
                        }
                        permisosPorModulo[permiso.modulo].push(permiso.submodulo || 'Sin Submodulo');
                    });

                    // Construir HTML para cada módulo y submódulo
                    $.each(permisosPorModulo, function(modulo, submodulos) {
                        permisosHtml += '<div class="modulo-item">' +
                            '<strong>MÓDULO:</strong> ' + modulo.toUpperCase() + '<br>';

                        if (submodulos.length > 0) {
                            $.each(submodulos, function(index, submodulo) {
                                permisosHtml += '&nbsp;&nbsp;<strong>Submodulo:</strong> ' + submodulo + '<br>';
                            });
                        } else {
                            permisosHtml += '&nbsp;&nbsp;<strong>Submodulo</strong> N/A<br>';
                        }

                        permisosHtml += '</div><hr>'; // Separador entre módulos
                    });
                } else {
                    permisosHtml = "<p>No hay permisos asignados para este rol.</p>";
                }

                // Actualizar el título del modal con el nombre del rol
                $("#verPermisosModalLabel").text("Permisos del Rol " + rolNombre);

                // Actualizar el cuerpo del modal con los permisos
                $("#verPermisosBody").html(permisosHtml);

                // Mostrar el modal
                $("#verPermisosModal").modal('show');
            },
            error: function(xhr, status, error) {
                console.error("Error al cargar los permisos:", error);
                $("#verPermisosBody").html("<p>Error al cargar los permisos.</p>");
            }
        });
    }

    // Manejar el evento para abrir el modal con permisos
    $(document).on('click', '.btn-ver-permisos', function() {
        var rol_id = $(this).data('id');
        var rol_nombre = $(this).data('nombre'); // Obtener el nombre del rol
        mostrarPermisos(rol_id, rol_nombre);
    });
</script>