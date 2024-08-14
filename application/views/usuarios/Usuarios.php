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
        color: #0BAC52;
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

<!-- Botón para agregar nuevo usuario -->
<div class="col py-3">
    <div class="row">
        <div class="col py-3">
            <div class="row">
                <div class="col-md-6">
                    <button type="button" style="background-color:#7A84E5; color:#fff" class="btn mb-3" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="bi bi-person-plus"></i> Agregar Usuario
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para agregar usuario -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Agregar Nuevo Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addUserForm" method="post" action="<?php echo site_url('guardar_usuario'); ?>" autocomplete="off">
                        <div class="mb-3">
                            <label for="userEmail" class="form-label">Email</label>
                            <input type="text" class="form-control" id="userEmail" name="email" required autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="userPassword" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="userPassword" name="password" required autocomplete="new-password">
                        </div>
                        <div class="mb-3">
                            <label for="userName" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="userName" name="nombre" required autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="userRol" class="form-label">Rol</label>
                            <select class="form-control" id="userRol" name="rol" required>
                                <!-- Las opciones se agregarán dinámicamente con AJAX -->
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Agregar Usuario</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script>
        function mostrarDatos(valorBuscar, pagina, cantidad) {
            $.ajax({
                url: "<?php echo site_url('usuarios/mostrar'); ?>",
                type: "POST",
                data: {
                    buscar: valorBuscar,
                    nropagina: pagina,
                    cantidad: cantidad
                },
                dataType: "json",
                success: function(response) {
                    var filas = "";
                    $.each(response.usuarios, function(key, item) {
                        filas += '<tr>' +
                            '<td>' + item.id + '</td>' +
                            '<td>' + item.nombre + '</td>' +
                            '<td>' + item.email + '</td>' +
                            '<td>' + item.rol_nombre + '</td>' +
                            '<td>' + item.usucre + '</td>' +
                            '<td>' + item.feccre + '</td>' +
                            '<td><center><i class="bi ' +
                            (item.estado == 1 ? ' fs-4 bi bi-person-circle' : ' fs-4 bi bi-person-circle') +
                            ' icon-toggle" data-id="' + item.id + '" data-estado="' + item.estado + '" style="color: ' +
                            (item.estado == 1 ? 'green' : 'red') + '; cursor: pointer;"></i></center></td>' +
                            '<td><center><button class="btn btn-sm editModal" style="background-color:#527E99; color:#DBE4DB" data-id="' + item.id + '" data-nombre="' + item.nombre + '" data-email="' + item.email + '" data-rol="' + item.rol_nombre + '">Editar</button>' +' | '+
                            '<a href="<?php echo site_url('usuarios/eliminar/'); ?>' + item.id + '" class="btn btn-sm" style="background-color:#F82A46; color:#DBE4DB"  onclick="return confirm(\'¿Estás seguro de que deseas eliminar este usuario?\');">Eliminar</a></center></td>' +
                            '</tr>';
                    });

                    $("#tbclientes").html(filas);

                    // Paginación
                    var totalregistros = response.totalregistros;
                    var cantidadregistros = response.cantidad;
                    var numerolinks = Math.ceil(totalregistros / cantidadregistros);
                    var paginador = "<ul class='pagination'>";

                    if (pagina > 1) {
                        paginador += "<li><a href='javascript:void(0)' onclick='mostrarDatos(\"" + valorBuscar + "\", 1, " + cantidad + ")'>&laquo;</a></li>";
                        paginador += "<li><a href='javascript:void(0)' onclick='mostrarDatos(\"" + valorBuscar + "\", " + (pagina - 1) + ", " + cantidad + ")'>&lsaquo;</a></li>";
                    } else {
                        paginador += "<li class='disabled'><a href='#'>&laquo;</a></li>";
                        paginador += "<li class='disabled'><a href='#'>&lsaquo;</a></li>";
                    }

                    for (var i = 1; i <= numerolinks; i++) {
                        paginador += "<li" + (i === pagina ? " class='active'" : "") + "><a href='javascript:void(0)' onclick='mostrarDatos(\"" + valorBuscar + "\", " + i + ", " + cantidad + ")'>" + i + "</a></li>";
                    }

                    if (pagina < numerolinks) {
                        paginador += "<li><a href='javascript:void(0)' onclick='mostrarDatos(\"" + valorBuscar + "\", " + (pagina + 1) + ", " + cantidad + ")'>&rsaquo;</a></li>";
                        paginador += "<li><a href='javascript:void(0)' onclick='mostrarDatos(\"" + valorBuscar + "\", " + numerolinks + ", " + cantidad + ")'>&raquo;</a></li>";
                    } else {
                        paginador += "<li class='disabled'><a href='#'>&rsaquo;</a></li>";
                        paginador += "<li class='disabled'><a href='#'>&raquo;</a></li>";
                    }

                    paginador += "</ul>";
                    $(".paginacion").html(paginador);
                }
            });
        }

        // Inicializa la tabla con los datos iniciales
        $(document).ready(function() {
            mostrarDatos("", 1, 5); // Inicializa con página 1 y 10 registros por página
        });
    </script>

    <!-- HTML para la tabla y la paginación -->
    <div class="table-responsive">
        <h4 class="mb-3">Lista de Usuarios</h4>
        <table class="table table-hover  table-bordered table-responsive">
            <thead style="background-color:#7A7CE5; color:#fff">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Creado por</th>
                    <th>Fecha creación</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="tbclientes">
                <!-- Las filas se llenarán con JavaScript -->
            </tbody>
        </table>
    </div>
    <div class="paginacion">
        <!-- La paginación se llenará con JavaScript -->
    </div>

    <!-- Modal de Edición (ya existente) -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Editar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        <div class="mb-3">
                            <label for="editNombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="editNombre" name="nombre">
                        </div>
                        <div class="mb-3">
                            <label for="editEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail" name="email">
                        </div>
                        <div class="mb-3">
                            <label for="editRol" class="form-label">Rol</label>
                            <select class="form-control" id="editRol" name="rol">
                                <!-- Las opciones se agregarán dinámicamente -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editContraseña" class="form-label">Contraseña</label>
                            <input type="text" class="form-control" id="editContraseña" name="contraseña">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="saveChanges">Guardar cambios</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Código para manejar el modal de agregar usuario -->
<script>
    $(document).ready(function() {
        // Detecta cuando se abre el modal de agregar usuario
        $('#addUserModal').on('show.bs.modal', function(e) {
            // Llama a la función para obtener los roles y llenar el select
            obtenerRolesAdd('userRol');
        });
    });
</script>

<!-- Código para obtener roles y llenar los selects -->
<script>
    // Función para obtener roles y llenar el select correspondiente
    function obtenerRolesAdd(selectId, selectedRol = null) {
        $.ajax({
            url: "<?php echo site_url('obtener_roles_ajax'); ?>", // URL del controlador que devuelve los roles
            type: "GET", // Tipo de solicitud
            dataType: "json", // Tipo de datos que se espera en la respuesta
            success: function(data) {
                // Limpiar el select antes de llenarlo
                $('#' + selectId).empty();

                // Recorrer cada rol y agregarlo al select
                $.each(data, function(index, rol) {
                    let option = '<option value="' + rol.id + '">' + rol.nombre + '</option>';
                    $('#' + selectId).append(option);
                });

                // Si hay un rol seleccionado, seleccionarlo en el dropdown
                if (selectedRol) {
                    $('#' + selectId).val(selectedRol);
                }
            },
            error: function() {
                alert('Error al obtener los roles');
            }
        });
    }
</script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        var editModal = new bootstrap.Modal(document.getElementById('editModal'));
        var saveChangesBtn = document.getElementById('saveChanges');

        // Manejar el clic en el botón de edición
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('editModal')) {
                var id = event.target.getAttribute('data-id');
                var nombre = event.target.getAttribute('data-nombre');
                var email = event.target.getAttribute('data-email');
                var rol = event.target.getAttribute('data-rol'); // Usa 'data-rol'

                // Establecer los valores en el formulario de edición
                document.getElementById('editNombre').value = nombre;
                document.getElementById('editEmail').value = email;

                // Cargar los roles y seleccionar el rol actual
                obtenerRoles('editRol', rol);

                // Mostrar el modal
                editModal.show();

                // Eliminar event listeners anteriores para evitar múltiples adiciones
                saveChangesBtn.removeEventListener('click', saveChangesHandler);
                saveChangesBtn.addEventListener('click', saveChangesHandler);

                function saveChangesHandler() {
                    var nombreEdit = document.getElementById('editNombre').value;
                    var emailEdit = document.getElementById('editEmail').value;
                    var rolEdit = document.getElementById('editRol').value;

                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', '<?php echo site_url('actualizar_usuario'); ?>', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            editModal.hide();
                            Swal.fire({
                                position: "top-end",
                                icon: "success",
                                title: "Editado con exito!",
                                showConfirmButton: false,
                                timer: 500
                            });
                            location.reload(); // Recarga la página solo si se guardan los cambios

                        } else {
                            alert('Error al guardar los cambios.');
                        }
                    };
                    xhr.send('id=' + encodeURIComponent(id) + '&nombre=' + encodeURIComponent(nombreEdit) + '&email=' + encodeURIComponent(emailEdit) + '&rol=' + encodeURIComponent(rolEdit));
                }
            }
        });

        // Función para obtener roles y llenar el select correspondiente
        function obtenerRoles(selectId, selectedRol = null) {
            $.ajax({
                url: "<?php echo site_url('obtener_roles_ajax'); ?>", // URL del controlador que devuelve los roles
                type: "GET", // Tipo de solicitud
                dataType: "json", // Tipo de datos que se espera en la respuesta
                success: function(data) {
                    // Limpiar el select antes de llenarlo
                    $('#' + selectId).empty();

                    // Recorrer cada rol y agregarlo al select
                    $.each(data, function(index, rol) {
                        var option = $('<option></option>').val(rol.id).text(rol.nombre);

                        // Comparar el rol actual con el rol seleccionado
                        if (selectedRol && rol.nombre === selectedRol) {
                            option.prop('selected', true); // Marca la opción como seleccionada
                        }

                        $('#' + selectId).append(option);
                    });
                },
                error: function() {
                    alert('Error al obtener los roles');
                }
            });
        }
    });
</script>


<script>
    //estado de los usuarios
    $(document).ready(function() {
        // Manejar el clic en el icono para cambiar el estado
        $(document).on('click', '.icon-toggle', function() {
            var icon = $(this);
            var id = icon.data('id');
            var currentStatus = icon.data('estado');
            var newStatus = currentStatus === 1 ? 0 : 1;
            var newIconClass = newStatus === 1 ? 'bi-hand-thumbs-up-fill' : 'bi-hand-thumbs-down-fill';
            var newColor = newStatus === 1 ? 'green' : 'red';
            console.log("aqui el id", id);
            var url = "<?php echo site_url('cambiar_estado_usuario'); ?>" + '/' + id;
            console.log('URL de la solicitud AJAX:', url); // Verifica la URL

            $.ajax({
                url: "<?php echo site_url('cambiar_estado_usuario'); ?>" + '/' + id, // Asegúrate de que la URL esté bien construida
                type: "GET",
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.success) {
                        // Actualiza el icono y su color
                        icon.removeClass('bi-hand-thumbs-up-fill bi-hand-thumbs-down-fill')
                            .addClass(newIconClass)
                            .css('color', newColor);

                        Swal.fire({
                            title: "Completado",
                            text: "Cambio de estado Exitoso!",
                            icon: "success"
                        });

                        // Actualiza el estado en el atributo data
                        icon.data('estado', newStatus);
                    } else {
                        alert('Error al actualizar el estado1.');
                    }
                },
                error: function() {
                    alert('Error al actualizar el estado.');
                }
            });
        });
    });
</script>