<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Equipos</title>
    <style>
        h2 {
            text-align: center;
            margin-bottom: 10px;
        }

        p {
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table,
        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
            white-space: nowrap;
            /* Previene que el texto se rompa en múltiples líneas */
            font-size: 7px;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .report-info {
            margin-bottom: 20px;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
        }

        /* Clases para ajustar el ancho de las columnas */
        .col-id {
            width: 25px;
            /* Menos espacio para ID */
        }

        .col-descripcion {
            width: 140px;
            /* Más espacio para Descripción */
        }

        .col-nombre {
            width: 80px;
            /* Espacio para Nombre */
        }

        .col-ubicacion {
            width: 70px;
            /* Espacio para Ubicación */
        }

        .col-fecha {
            width: 60px;
            /* Espacio para Fecha de Ingreso */
        }

        .col-costo {
            width: 50px;
            /* Espacio para Costo */
        }

        .col-estado {
            width: 50px;
            /* Espacio para Costo */
        }

        .col-codigo {
            width: 60px;
            /* Espacio para Costo */
        }
    </style>
</head>

<body>
    <h2>Reporte de los Equipos</h2>
    <p>
        <strong><?php echo $reporte_info['empresa']; ?></strong><br>
        <strong>Fecha:</strong> <?php echo $reporte_info['fecha']; ?><br>
        <strong>Elaborado por:</strong> <?php echo htmlspecialchars($reporte_info['elaborado_por']); ?> - <?php echo htmlspecialchars($reporte_info['puesto']); ?><br>
    </p>

    <hr>

    <div class="report-info">
        <h3>Detalles de Equipos</h3>
        <table>
            <thead>
                <tr>
                    <th class="col-id"><strong>ID</strong></th>
                    <th class="col-nombre"><strong>Nombre</strong></th>
                    <th class="col-descripcion"><strong>Descripción</strong></th>
                    <th class="col-estado"><strong>Estado</strong></th>
                    <th class="col-codigo"><strong>Código</strong></th>
                    <th class="col-ubicacion"><strong>Ubicación</strong></th>
                    <th class="col-fecha"><strong>Fecha de Ingreso</strong></th>
                    <th class="col-costo"><strong>Costo</strong></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $item): ?>
                    <tr>
                        <td class="col-id"><?php echo $item['id']; ?></td>
                        <td class="col-nombre"><?php echo $item['nombre']; ?></td>
                        <td class="col-descripcion"><?php echo $item['descripcion']; ?></td>
                        <td class="col-estado">
                            <?php
                            $estado = $item['estado'];
                            switch ($estado) {
                                case 1:
                                    echo 'Activo';
                                    break;
                                case 0:
                                    echo 'Inactivo';
                                    break;
                                case 3:
                                    echo 'Mantenimiento';
                                    break;
                                default:
                                    echo 'Desconocido';
                                    break;
                            }
                            ?>
                        </td>
                        <td class="col-codigo"><?php echo $item['numero_serie']; ?></td>
                        <td class="col-ubicacion"><?php echo $item['ubicacion_nombre']; ?></td>
                        <td class="col-fecha"><?php echo date('d/m/Y', strtotime($item['feccre'])); ?></td>
                        <td class="col-costo"><?php echo number_format($item['costo'], 2, ',', '.'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="footer">
      
    <p>Reporte generado por <?php echo $reporte_info['elaborado_por']; ?> en <?php echo $reporte_info['fecha']; ?></p>
    <p>Total Registros generados: <?php echo count($data); ?></p>

</div>
</body>

</html>