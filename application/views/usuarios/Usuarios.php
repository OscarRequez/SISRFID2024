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
</style>

<div class="col py-3">
    <div class="row">
        <div class="col py-3">
            <div class="row">
                <div class="col-md-6">
                    <!-- Botón para agregar nuevo usuario -->
                    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="bi bi-person-plus"></i> Agregar Usuario
                    </button>
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




        <div class="table-responsive">
            <h1 class="mb-3">Lista de Usuarios</h1>
            <table class="table table-striped table-bordered table-responsive">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Creado por</th>
                        <th>Fecha creacion</th>
                        <th>Estado</th>
                        <th>Acciones</th> <!-- Nueva columna para botones -->
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($usuarios)): ?>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?php echo $usuario['id']; ?></td>
                                <td><?php echo $usuario['nombre']; ?></td>
                                <td><?php echo $usuario['email']; ?></td>
                                <td><?php echo $usuario['rol_nombre']; ?></td>
                                <td><?php echo $usuario['usucre']; ?></td>
                                <td><?php echo $usuario['feccre']; ?></td>
                                <td>
                                    <?php if ($usuario['estado'] == 1): ?>
                                        <center><i class=" bi bi-hand-thumbs-up-fill" style="color: green;"></i> </center>
                                    <?php else: ?>
                                        <center><i class="bi bi-hand-thumbs-down-fill" style="color: red;"></i> </center>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-warning btn-sm editBtn" data-id="<?php echo $usuario['id']; ?>" data-nombre="<?php echo $usuario['nombre']; ?>" data-email="<?php echo $usuario['email']; ?>" edit-rol="<?php echo $usuario['rol_nombre']; ?>">Editar</button>
                                    <a href="<?php echo site_url('usuarios/eliminar/' . $usuario['id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No se encontraron usuarios</td> <!-- Ajusta el colspan a 6 para la nueva columna -->
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de Edición -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Editar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Aquí va el formulario de edición -->
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
                        <input type="text" class="form-control" id="editContraseña" name="Contraseña">
                    </div>
                    <!-- Agrega más campos según tus necesidades -->
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="saveChanges">Guardar cambios</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var editModal = new bootstrap.Modal(document.getElementById('editModal'));
        var saveChangesBtn = document.getElementById('saveChanges');

        // Maneja el clic en el botón Editar
        document.querySelectorAll('.editBtn').forEach(function(button) {
            button.addEventListener('click', function() {
                var id = this.getAttribute('data-id');
                var nombre = this.getAttribute('data-nombre');
                var email = this.getAttribute('data-email');
                var rol = this.getAttribute('data-rol');

                document.getElementById('editNombre').value = nombre;
                document.getElementById('editEmail').value = email;
                loadRoles(rol); // Carga los roles y selecciona el rol actual

                editModal.show();

                saveChangesBtn.addEventListener('click', function() {
                    var nombreEdit = document.getElementById('editNombre').value;
                    var emailEdit = document.getElementById('editEmail').value;
                    var rolEdit = document.getElementById('editRol').value;

                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', '<?php echo site_url('usuarios/actualizar'); ?>', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            editModal.hide();
                            location.reload();
                        } else {
                            alert('Error al guardar los cambios.');
                        }
                    };
                    xhr.send('id=' + encodeURIComponent(id) + '&nombre=' + encodeURIComponent(nombreEdit) + '&email=' + encodeURIComponent(emailEdit) + '&rol=' + encodeURIComponent(rolEdit));
                });
            });
        });

        // Función para cargar roles en el select
        function loadRoles(selectedRol) {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '<?php echo site_url('roles/get_roles'); ?>', true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var roles = JSON.parse(xhr.responseText);
                    var select = document.getElementById('editRol');
                    select.innerHTML = ''; // Limpia las opciones existentes

                    roles.forEach(function(role) {
                        var option = document.createElement('option');
                        option.value = role.id;
                        option.textContent = role.nombre;
                        if (role.id == selectedRol) {
                            option.selected = true;
                        }
                        select.appendChild(option);
                    });
                }
            };
            xhr.send();
        }
    });
</script>

<!-- obtener los roles para el selct de agregar usuario-->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Detecta cuando se abre el modal
        $('#addUserModal').on('show.bs.modal', function(e) {
            // Llama a la función para obtener los roles
            obtenerRoles();
        });

        // Función para obtener roles a través de AJAX
        function obtenerRoles() {
            $.ajax({
                url: "<?php echo site_url('obtener_roles_ajax'); ?>", // URL del controlador que devuelve los roles
                type: "GET", // Tipo de solicitud
                dataType: "json", // Tipo de datos que se espera en la respuesta
                success: function(data) {
                    // Limpiar el select antes de llenarlo
                    $('#userRol').empty();

                    // Recorrer cada rol y agregarlo al select
                    $.each(data, function(index, rol) {
                        $('#userRol').append('<option value="' + rol.id + '">' + rol.nombre + '</option>');
                    });
                },
                error: function() {
                    alert('Error al obtener los roles');
                }
            });
        }
    });
</script>

<!-- obtener los roles para el selct de agregar usuario-->