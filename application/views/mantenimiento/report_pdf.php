<!DOCTYPE html>
<html>

<head>
    <title>Reporte de Tareas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th,
        td {
            border: 1px solid #C5C7C8;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        h2 {
            text-align: center;
        }

        p {
            text-align: center;
        }

        .section-title {
            margin-top: 20px;
            font-size: 15px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <h2>SOLICITUD DE TRABAJO - MANTENIMIENTO</h2>
    <p>Fecha del reporte: <?php echo $reporte_info['fecha']; ?></p>
    <p>Elaborado por: <?php echo $reporte_info['elaborado_por']; ?> (<?php echo $reporte_info['puesto']; ?>)</p>

    <div class="section-title">Datos de la Tarea</div>
    <br>
    <table>
        <tbody>
            <tr>
                <td><strong>ID:</strong></td>
                <td><?php echo $data[0]['id']; ?></td>
            </tr>
            <tr>
                <td><strong>Número de Serie del Equipo:</strong></td>
                <td><?php echo $data[0]['numero_serie_eq']; ?></td>
            </tr>
            <tr>
                <td><strong>Equipo ID:</strong></td>
                <td><?php echo $data[0]['equipo_id']; ?></td>
            </tr>
            <tr>
                <td><strong>Descripción:</strong></td>
                <td><?php echo $data[0]['descripcion']; ?></td>
            </tr>
            <tr>
                <td><strong>Técnico:</strong></td>
                <td><?php echo $data[0]['nombre_tecnico']; ?></td>
            </tr>
            <tr>
                <td><strong>Tipo de Mantenimiento:</strong></td>
                <td><?php echo $data[0]['tipo_mantenimiento']; ?></td>
            </tr>
            <tr>
                <td><strong>Prioridad:</strong></td>
                <td><?php echo $data[0]['prioridad']; ?></td>
            </tr>
            <tr>
                <td><strong>Unidad de la Solicitud:</strong></td>
                <td><?php echo $data[0]['nombre_ubicacion']; ?></td>
            </tr>
            <tr>
                <td><strong>Observaciones:</strong></td>
                <td><?php echo $data[0]['observaciones']; ?></td>
            </tr>
            <tr>
                <td><strong>Fecha Solicitud:</strong></td>
                <td><?php echo $data[0]['fecha_solicitud']; ?></td>
            </tr>
            <tr>
                <td><strong>Fecha Asignación:</strong></td>
                <td><?php echo $data[0]['fecha_asignacion']; ?></td>
            </tr>
            <tr>
                <td><strong>Fecha Conclusión:</strong></td>
                <td><?php echo $data[0]['fecha_conclusion']; ?></td>
            </tr>
            <tr>
                <td><strong>Estado:</strong></td>
                <td>
                    <?php
                    // Traducción del estado
                    switch ($data[0]['estado']) {
                        case 1:
                            echo 'Solicitado';
                            break;
                        case 2:
                            echo 'En Mantenimiento';
                            break;
                        case 3:
                            echo 'Completado';
                            break;
                        case 4:
                            echo 'Por Confirmar';
                            break;
                        default:
                            echo 'Estado Desconocido'; // En caso de que el estado no coincida con ninguno de los casos
                            break;
                    }
                    ?>
                </td>
            </tr>
        </tbody>
    </table>
    <br>
    <div class="section-title">Materiales Relacionados</div>
    <br>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Material</th>
                <th>Cantidad</th>
                <th>Costo Unitario</th>
                <th>Costo Total</th>
                <th>Factura</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total_gasto = 0;
            $counter = 1;
            $has_materials = false; // Bandera para verificar si hay materiales

            foreach ($data as $material):
                if (isset($material['material_id'])):
                    $has_materials = true; // Hay al menos un material
                    $total_gasto += $material['costo_total']; // Suma del costo total
            ?>
                    <tr>
                        <td><?php echo $counter++; ?></td> <!-- Numeración de los materiales -->
                        <td><?php echo $material['material']; ?></td>
                        <td><?php echo $material['cantidad']; ?></td>
                        <td><?php echo $material['costo_unitario']; ?></td>
                        <td><?php echo $material['costo_total']; ?></td>
                        <td><?php echo $material['factura']; ?></td>
                    </tr>
                <?php
                endif;
            endforeach;

            if (!$has_materials): // Si no hay materiales
                ?>
                <tr>
                    <td colspan="6" style="text-align: center;">No se utilizaron materiales para esta tarea.</td>
                </tr>
            <?php
            endif;
            ?>
        </tbody>
    </table>

    <br>
    <div class="section-title">Resumen de Gastos</div>
    <br>
    <table>
        <tbody>
            <tr>
                <td><strong>Total Gasto:</strong></td>
                <td><?php echo number_format($total_gasto, 2); ?> Bs</td>
            </tr>
        </tbody>
    </table>
</body>

</html>