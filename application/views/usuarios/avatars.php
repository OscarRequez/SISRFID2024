<?php $this->load->view('templates/header'); ?>
<style>
    .card-img-top {

        object-fit: cover;
        /* Ajusta la imagen para cubrir el contenedor sin distorsión */
        transition: transform 0.3s ease;
        border-radius: 50%;
    }

    .card-img-top:hover {
        transform: scale(1.1);
        /* Agranda la imagen al pasar el mouse sobre ella */
    }
</style>

<style>
    .card-img-top {
        object-fit: cover;
        /* Ajusta la imagen para cubrir el contenedor sin distorsión */
        width: 100%;
        /* Hace que la imagen se ajuste al ancho del contenedor */
        height: 150px;
        /* Ajusta la altura fija para todas las imágenes */
        border-radius: 50%;
        /* Hace que la imagen sea circular */
    }

    .card-body {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .card-title {
        text-transform: lowercase;
        /* Convierte todo el texto a mayúsculas */
    }
</style>


<div class="col py-3">
    <h3>Seccion de Avatars</h3>
    <hr>
    <!-- Botón para abrir el modal -->

        <div class="row">
            <div class="col-md-6">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAvatarModal">
                Agregar Avatar <i class="bi bi-emoji-smile"></i>
             </button>
                <br> <br> </div>
        </div>

    <!-- Modal para agregar un nuevo avatar -->
    <div class="modal fade" id="addAvatarModal" tabindex="-1" aria-labelledby="addAvatarModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addAvatarModalLabel">Agregar Nuevo Avatar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addAvatarForm" action="<?php echo site_url('avatars_agregar'); ?>" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="avatarName" class="form-label">Nombre del Avatar</label>
                            <input type="text" class="form-control" id="avatarName" name="avatar_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="avatarImage" class="form-label">Imagen del Avatar</label>
                            <input type="file" class="form-control" id="avatarImage" name="avatar_image" accept="image/*" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Agregar Avatar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Galería de avatares existentes -->


    <div class="row">
        <?php foreach ($avatares as $avatar): ?>
            <div class="col-md-2 mb-3">
                <div class="card d-flex flex-column" style="height: 100%;">
                    <img src="<?php echo base_url($avatar['ruta_imagen']); ?>" class="card-img-top img-thumbnail" alt="<?php echo $avatar['nombre']; ?>">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <center>
                            <h5 class="card-title"><?php echo $avatar['nombre']; ?></h5>
                        </center>
                        <!-- Botón para eliminar avatar -->
                        <button type="button" class="btn btn-danger" onclick="confirmarEliminacion(<?php echo $avatar['id']; ?>)">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
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
                // Realizar la solicitud AJAX para eliminar el avatar
                $.ajax({
                    url: '<?php echo site_url('eliminar_avatar'); ?>',
                    type: 'POST',
                    data: {
                        id: id
                    }, // Enviar el ID como parte de los datos POST
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                title: 'Eliminado!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'Aceptar'
                            }).then(() => {
                                // Recargar la página para actualizar la galería
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message,
                                icon: 'error',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'Aceptar'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Ocurrió un error al intentar eliminar el avatar.',
                            icon: 'error',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                });
            }
        });
    }
</script>


<?php $this->load->view('templates/footer'); ?>