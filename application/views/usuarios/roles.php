<?php $this->load->view('templates/header'); ?>

<style>


    th,td {
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
        <div class="table-responsive">
            <h1 class="mb-3">Lista de Roles de Usuario</h1>
            <table class="table table-striped table-bordered table-responsive">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($roles)): ?>
                        <?php foreach ($roles as $rol): ?>
                            <tr>
                                <td><?php echo $rol['id']; ?></td>
                                <td><?php echo $rol['nombre']; ?></td>
                                <td><?php echo ($rol['estado'] == 1 ? 'Activo' : 'Inactivo'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No se encontraron roles</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>