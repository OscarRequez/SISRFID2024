<?php $this->load->view('templates/header'); ?>
<style>

    /* Estilo para las tarjetas de avatares */
    .card {
        transition: transform 0.3s ease;
    }

    .card:hover {
        transform: scale(1.05);
        /* Agranda la tarjeta al pasar el mouse sobre ella */
    }

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

<div class="container py-3">
    <h2 class="col py-3">Gestionar Avatares</h2>
    
    <!-- Formulario para agregar un nuevo avatar -->
    <div class="col py-3">
        <h4>Agregar Nuevo Avatar</h4>
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

    <!-- Galería de avatares existentes -->
    <h4>Avatares Existentes</h4>
    <div class="row">
        <?php foreach ($avatares as $avatar): ?>
            <div class="col-md-3 ">
                <div class="card">
                    <img src="<?php echo base_url($avatar['ruta_imagen']); ?>" class="card-img-top img-thumbnail" alt="<?php echo $avatar['nombre']; ?>">
                    <div class="card-body text-center">
                        <h5 class="card-title"><?php echo $avatar['nombre']; ?></h5>
                        <!-- Botón para eliminar avatar -->
                        <form action="<?php echo site_url('avatars_eliminar/' . $avatar['id']); ?>" method="post" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este avatar?');">
                            <button type="submit" class="btn btn-danger">Eliminar</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php $this->load->view('templates/footer'); ?>

