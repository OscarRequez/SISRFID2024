<!DOCTYPE html>
<html>
<head>
    <style>
        /* Aquí puedes agregar estilos CSS personalizados para tu PDF */
    </style>
</head>
<body>
    <h1>Reporte de Equipos</h1>
    <table border="1" cellspacing="0" cellpadding="5">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Ubicación</th>
                <th>Estado</th>
                <!-- Agrega más columnas según sea necesario -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row): ?>
            <tr>
                <td><?php echo $row->id; ?></td>
                <td><?php echo $row->nombre; ?></td>
                <td><?php echo $row->ubicacion; ?></td>
                <td><?php echo $row->estado; ?></td>
                <!-- Agrega más columnas según sea necesario -->
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>