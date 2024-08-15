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
    <div class="modal fade" id="editarRolModal" tabindex="-1" aria-labelledby="editarRolModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarRolModalLabel">Editar Rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="<?= base_url('ruta/al/controlador/editar_rol') ?>">
                    <input type="hidden" id="rolId" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nombreRolEdit" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombreRolEdit" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="estadoRolEdit" class="form-label">Estado</label>
                            <select class="form-select" id="estadoRolEdit" name="estado" required>
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
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

</div>


<script>
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
                        if (modulo.submodulos.length > 0) {
                            modulosHtml += '<div class="ps-4">';
                            $.each(modulo.submodulos, function(key, submodulo) {
                                modulosHtml += '<div class="form-check">' +
                                    '<input class="form-check-input" type="checkbox" name="submodulos[]" value="' + submodulo + '" id="submodulo-' + submodulo + '">' +
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
    $('#agregarRolModal').on('shown.bs.modal', function () {
        cargarModulos();
    });
});
</script>


<script>
    var editarRolModal = document.getElementById('editarRolModal');
    editarRolModal.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var nombre = button.getAttribute('data-nombre');
        var estado = button.getAttribute('data-estado');

        var modalIdInput = editarRolModal.querySelector('#rolId');
        var modalNombreInput = editarRolModal.querySelector('#nombreRolEdit');
        var modalEstadoSelect = editarRolModal.querySelector('#estadoRolEdit');

        modalIdInput.value = id;
        modalNombreInput.value = nombre;
        modalEstadoSelect.value = estado;
    });
</script>

<script>
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