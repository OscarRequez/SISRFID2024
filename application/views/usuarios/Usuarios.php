<?php $this->load->view('templates/header'); ?>

<style>
    /* Contenedor de la paginación */
    .pagination {
        display: flex;
        justify-content: center;
        margin: 20px 0;
    }
</style>

<!-- Botón para agregar nuevo usuario -->
<div class="col py-3">
    <div class="row">
        <h3 class="mb-3">Registros de Usuarios</h3>
        <hr>
        <!-- Columna para el botón de agregar usuario -->
        <div class="col-md-6 py-3">
            <button type="button" class="btn mb-3 bg-primary text-white" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="bi bi-person-plus"></i> Agregar Usuario
            </button>
        </div>

        <!-- Columna para el campo de búsqueda -->
        <div class="col-md-6 py-3">
            <div class="d-flex align-items-center">
                <input type="text" class="form-control me-2 flex-grow-1" id="searchInput" placeholder="Buscar por nombre o DNI">
                <button class="btn btn-primary" id="searchBtn">Buscar</button>
            </div>
        </div>
    </div>


    <!-- Modal para agregar usuario -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addUserModalLabel">Agregar Nuevo Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="addUserForm">
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
                            <label for="userCedula" class="form-label">Cédula de Identidad</label>
                            <input type="number" class="form-control" id="userCedula" min="0" name="cedula" autocomplete="off">
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


    <!-- Validar cédula de identidad -->
    <script>
        document.getElementById('addUserForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Detener el envío normal del formulario
            var cedula = document.getElementById('userCedula').value.trim();

            // Validar si la cédula tiene al menos 7 dígitos
            if (cedula.length < 7) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'La cédula de identidad debe tener al menos 7 dígitos.'
                });
                return;
            }

            // Validar si la cédula es un número negativo
            if (parseInt(cedula) < 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'La cédula de identidad no puede ser un número negativo.'
                });
                return;
            }

            // Enviar el formulario vía AJAX
            // Enviar el formulario vía AJAX
            $.ajax({
                url: '<?php echo site_url('guardar_usuario'); ?>', // URL del controlador que guarda el usuario
                type: 'POST',
                data: $('#addUserForm').serialize(), // Serializa los datos del formulario
                dataType: 'json', // Asegurarse de que la respuesta es en JSON
                success: function(response) {
                    if (response.status === 'error') {
                        // Mostrar el mensaje de error con SweetAlert
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message // Mensaje de error desde el servidor
                        });
                    } else if (response.status === 'success') {
                        // Cerrar el modal
                        $('#addUserModal').modal('hide');

                        // Mostrar mensaje de éxito con SweetAlert
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });

                        // Recargar la página después de un corto tiempo
                        setTimeout(function() {
                            location.reload(); // Recargar la página después de cerrar el SweetAlert
                        }, 1600);
                    }
                },
                error: function(xhr, status, error) {
                    // Manejar el error y mostrar mensaje con SweetAlert
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al agregar el usuario',
                        text: 'Ocurrió un error. Por favor, inténtelo de nuevo.'
                    });
                }
            });
        });
    </script>

    <!-- HTML para la tabla y la paginación -->
    <div class="table table-responsive">
        <h4 class="mb-3">Usuarios del Sistema</h4>
        <table class="table table-hover table-bordered table-responsive">
            <thead class="bg-primary text-white">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Cedula de Identidad</th>
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
                <div class="modal-header  bg-primary text-white">
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
                            <label for="editCedula" class="form-label">Cédula de Identidad</label>
                            <input type="number" class="form-control" id="editCedula" required min="0" name="editCedula" required>
                        </div>
                        <div class="mb-3">
                            <label for="editEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="editRol" class="form-label">Rol</label>
                            <select class="form-control" id="editRol" name="rol">
                                <!-- Las opciones se agregarán dinámicamente -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editContraseña" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="editContraseña" name="contraseña">
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
    // Define la función en el contexto global
    function mostrarDatos(valorBuscar, pagina, cantidad) {
        $.ajax({
            url: "<?php echo site_url('usuarios_mostrar'); ?>",
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
                        '<td>' + item.cedula + '</td>' +
                        '<td>' + item.email + '</td>' +
                        '<td>' + item.rol_nombre + '</td>' +
                        '<td>' + item.usucre + '</td>' +
                        '<td>' + item.feccre + '</td>' +
                        '<td><center><i class="bi ' +
                        (item.estado == 1 ? 'fs-4 bi-person-circle' : 'fs-4 bi-person-circle') +
                        ' icon-toggle" data-id="' + item.id + '" data-estado="' + item.estado + '" style="color: ' +
                        (item.estado == 1 ? 'green' : 'red') + '; cursor: pointer;"></i></center></td>' +
                        '<td><center>' +
                        // Botón de editar con ícono de lápiz, desactivado si el estado es 0
                        '<button class="btn btn-sm editModal" style="background-color:#527E99; color:#DBE4DB" ' +
                        'data-id="' + item.id + '" data-nombre="' + item.nombre + '" data-cedula="' + item.cedula + '" ' +
                        'data-email="' + item.email + '" data-rol="' + item.rol_nombre + '" ' +
                        (item.estado == 2 ? 'disabled' : '') + '>' +
                        'Editar' + ' </button> ' +
                        // Botón de eliminar con ícono de papelera, desactivado si el estado es 0
                        '<button class="btn btn-sm" style="background-color:#F82A46; color:#DBE4DB" ' +
                        'onclick="confirmarEliminacion(' + item.id + ')" ' +
                        (item.estado == 2 ? 'disabled' : '') + '>' +
                        'Eliminar </button>' +
                        '</center></td>' +
                        '</tr>';

                });

                $("#tbclientes").html(filas);

                // Manejo de la paginación
                var totalRegistros = response.totalregistros;
                var cantidadRegistros = response.cantidad;
                var numeroLinks = Math.ceil(totalRegistros / cantidadRegistros);
                var paginador = "<ul class='pagination'>";

                // Botones "Primera" y "Anterior"
                if (pagina > 1) {
                    paginador += "<li class='page-item'><a class='page-link' href='javascript:void(0)' onclick='mostrarDatos(\"" + valorBuscar + "\", 1, " + cantidadRegistros + ")'>Primera</a></li>";
                    paginador += "<li class='page-item'><a class='page-link' href='javascript:void(0)' onclick='mostrarDatos(\"" + valorBuscar + "\", " + (pagina - 1) + ", " + cantidadRegistros + ")'>Anterior</a></li>";
                } else {
                    paginador += "<li class='page-item disabled'><a class='page-link' href='#'>Primera</a></li>";
                    paginador += "<li class='page-item disabled'><a class='page-link' href='#'>Anterior</a></li>";
                }

                // Botones de números de página
                var startPage = Math.max(1, pagina - 2);
                var endPage = Math.min(numeroLinks, pagina + 2);

                if (startPage > 1) {
                    paginador += "<li class='page-item'><a class='page-link' href='javascript:void(0)' onclick='mostrarDatos(\"" + valorBuscar + "\", 1, " + cantidadRegistros + ")'>1</a></li>";
                    if (startPage > 2) paginador += "<li class='page-item disabled'><a class='page-link'>...</a></li>";
                }

                for (var i = startPage; i <= endPage; i++) {
                    if (i == pagina) {
                        paginador += "<li class='page-item active'><a class='page-link' href='javascript:void(0)'>" + i + "</a></li>";
                    } else {
                        paginador += "<li class='page-item'><a class='page-link' href='javascript:void(0)' onclick='mostrarDatos(\"" + valorBuscar + "\", " + i + ", " + cantidadRegistros + ")'>" + i + "</a></li>";
                    }
                }

                if (endPage < numeroLinks) {
                    if (endPage < numeroLinks - 1) paginador += "<li class='page-item disabled'><a class='page-link'>...</a></li>";
                    paginador += "<li class='page-item'><a class='page-link' href='javascript:void(0)' onclick='mostrarDatos(\"" + valorBuscar + "\", " + numeroLinks + ", " + cantidadRegistros + ")'>" + numeroLinks + "</a></li>";
                }

                // Botones "Siguiente" y "Última"
                if (pagina < numeroLinks) {
                    paginador += "<li class='page-item'><a class='page-link' href='javascript:void(0)' onclick='mostrarDatos(\"" + valorBuscar + "\", " + (pagina + 1) + ", " + cantidadRegistros + ")'>Siguiente</a></li>";
                    paginador += "<li class='page-item'><a class='page-link' href='javascript:void(0)' onclick='mostrarDatos(\"" + valorBuscar + "\", " + numeroLinks + ", " + cantidadRegistros + ")'>Última</a></li>";
                } else {
                    paginador += "<li class='page-item disabled'><a class='page-link' href='#'>Siguiente</a></li>";
                    paginador += "<li class='page-item disabled'><a class='page-link' href='#'>Última</a></li>";
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
        // Inicializa la tabla con los datos iniciales
        mostrarDatos("", 1, 6); // Inicializa con página 1 y 6 registros por página

        // Maneja el clic en el botón de búsqueda
        $('#searchBtn').on('click', function() {
            var searchValue = $('#searchInput').val();
            mostrarDatos(searchValue, 1, 6); // Vuelve a mostrar los datos con el nuevo valor de búsqueda
        });

        // Maneja el presionar Enter en el campo de búsqueda
        $('#searchInput').on('keypress', function(e) {
            if (e.which == 13) { // Enter key pressed
                e.preventDefault();
                $('#searchBtn').click();
            }
        });
    });
</script>


<script>
    // Función para mostrar SweetAlert2 antes de eliminar
    function confirmarEliminacion(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminarlo',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Realizar la solicitud de eliminación
                var xhr = new XMLHttpRequest();
                xhr.open('GET', '<?php echo site_url('eliminar_estado_usuario/'); ?>' + id, true);
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.status === 'success') {
                            Swal.fire(
                                'Eliminado!',
                                response.message,
                                'success'
                            ).then(() => {
                                location.reload(); // Recarga la página para actualizar la lista
                            });
                        } else {
                            Swal.fire(
                                'Error!',
                                response.message,
                                'error'
                            );
                        }
                    } else {
                        Swal.fire(
                            'Error!',
                            'Hubo un problema con la solicitud.',
                            'error'
                        );
                    }
                };
                xhr.send();
            }
        });
    }
</script>

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

        document.addEventListener('click', function(event) {
                if (event.target.classList.contains('editModal')) {
                    var id = event.target.getAttribute('data-id');
                    var nombre = event.target.getAttribute('data-nombre');
                    var cedula = event.target.getAttribute('data-cedula');
                    var email = event.target.getAttribute('data-email');
                    var rol = event.target.getAttribute('data-rol');

                    document.getElementById('editNombre').value = nombre;
                    document.getElementById('editCedula').value = cedula;
                    document.getElementById('editEmail').value = email;
                    document.getElementById('editContraseña').value = '';

                    // Obtener roles y seleccionar el rol actual
                    obtenerRoles('editRol', rol);
                    editModal.show();

                    saveChangesBtn.removeEventListener('click', saveChangesHandler);
                    saveChangesBtn.addEventListener('click', saveChangesHandler);

                    function saveChangesHandler() {
                        var nombreEdit = document.getElementById('editNombre').value;
                        var cedulaEdit = document.getElementById('editCedula').value;
                        var emailEdit = document.getElementById('editEmail').value;
                        var rolEdit = document.getElementById('editRol').value;
                        var contraseñaEdit = document.getElementById('editContraseña').value;

                        // Validar que la cédula tenga al menos 7 dígitos
                        if (cedulaEdit.length < 7) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'La cédula debe tener al menos 7 dígitos.',
                                showConfirmButton: true
                            });
                            return; // Salir si la validación falla
                        }

                        // Validar que la cédula no sea negativa
                        if (parseInt(cedulaEdit) < 0) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'La cédula no puede ser un número negativo.',
                                showConfirmButton: true
                            });
                            return; // Salir si la validación falla
                        }

                        // Realizar la solicitud AJAX para actualizar el usuario
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', '<?php echo site_url('actualizar_usuario'); ?>', true);
                        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                        xhr.onload = function() {
                            var response = JSON.parse(xhr.responseText);

                            if (response.status === 'success') {
                                editModal.hide(); // Cierra el modal
                                Swal.fire({
                                    title: "Éxito!",
                                    text: "Datos actualizados exitosamente",
                                    icon: "success"
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload(); // Recarga la página si el usuario confirma
                                    }
                                });
                            }
                         else if (response.status === 'error') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,
                                showConfirmButton: true
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error desconocido al guardar los cambios.',
                                showConfirmButton: true
                            });
                        }
                    };

                    // Preparar los datos para enviar
                    var postData = 'id=' + encodeURIComponent(id) +
                        '&nombre=' + encodeURIComponent(nombreEdit) +
                        '&cedula=' + encodeURIComponent(cedulaEdit) +
                        '&email=' + encodeURIComponent(emailEdit) +
                        '&rol=' + encodeURIComponent(rolEdit) +
                        '&contraseña=' + encodeURIComponent(contraseñaEdit);

                    xhr.send(postData); // Enviar los datos
                }
            }
        });

    function obtenerRoles(selectId, selectedRol = null) {
        $.ajax({
            url: "<?php echo site_url('obtener_roles_ajax'); ?>",
            type: "GET",
            dataType: "json",
            success: function(data) {
                $('#' + selectId).empty();
                $.each(data, function(index, rol) {
                    var option = $('<option></option>').val(rol.id).text(rol.nombre);
                    if (selectedRol && rol.nombre === selectedRol) {
                        option.prop('selected', true);
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