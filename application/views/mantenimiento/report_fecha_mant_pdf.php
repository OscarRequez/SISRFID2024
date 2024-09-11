<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Georgia, 'Times New Roman', Times, serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }

        /* Estilos CSS personalizados para la tabla del PDF */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;

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

        h4,
        h3,
        p {
            margin: 0 0 10px 0;

        }

        .small-column {
            width: 18;
        }

        .small-column-obs {
            width: 118;
        }
    </style>
</head>

<body>
    <h2>Reporte de Mantenimiento</h2>
    <p><strong>Fecha del Reporte:</strong> <?php echo $reporte_info['fecha']; ?></p>
    <p><strong>Empresa:</strong> <?php echo $reporte_info['empresa']; ?></p>
    <p><strong>Dirección:</strong> <?php echo $reporte_info['direccion']; ?></p>
    <p><strong>Contacto:</strong> <?php echo $reporte_info['contacto']; ?></p>
    <p><strong>Elaborado por:</strong> <?php echo $reporte_info['elaborado_por']; ?>, <?php echo $reporte_info['puesto']; ?></p>
    <!-- Pie de página -->

    <h4>Mantenimientos Realizados</h4>
    <table>
        <thead>
            <tr>
                <th class="small-column">#</th> <!-- Nueva columna para numeración -->
                <th>ID Mantenimiento</th>
                <th>Equipo</th>
                <th>Técnico Encargado</th>
                <th>Fecha Solicitud</th>
                <th>Fecha Conclusión</th>
                <th>Costo Total de Materiales</th>
                <th class="small-column-obs">Observaciones</th>
            </tr>
        </thead>
        <tbody>
            <?php $numero = 1; // Inicializamos el contador 
            ?>
            <?php foreach ($datos_reporte as $dato): ?>
                <tr>
                    <td class="small-column"><?php echo $numero++; ?></td> <!-- Numeración automática -->
                    <td><?php echo $dato['id']; ?></td>
                    <td><?php echo $dato['numero_serie_eq']; ?></td>
                    <td><?php echo $dato['tecnico_nombre']; ?></td>
                    <td><?php echo date('d/m/Y', strtotime($dato['fecha_solicitud'])); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($dato['fecha_conclusion'])); ?></td>
                    <td><?php echo number_format($dato['total_materiales'], 2); ?></td>
                    <td class="small-column-obs"><?php echo $dato['observaciones']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h4>Técnico con Más Mantenimientos</h4>
    <table>
        <thead>
            <tr>
                <th>Técnico</th>
                <th>Cantidad de Mantenimientos</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo $tecnico_mas['tecnico_nombre']; ?> </td>
                <td><?php echo $tecnico_mas['cantidad_mantenimientos']; ?></td>
            </tr>
        </tbody>
    </table>

    <?php
    // Inicializa la variable para el total
    $total_mantenimientos = 0;
    ?>

    <h4>Técnicos y Cantidad de Mantenimientos</h4>
    <table>
        <thead>
            <tr>
                <th>Técnico</th>
                <th>Cantidad de Mantenimientos</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tecnicos_mantenimientos as $tecnico): ?>
                <tr>
                    <td><?php echo htmlspecialchars($tecnico['tecnico_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($tecnico['cantidad_mantenimientos']); ?></td>
                </tr>
                <?php
                // Suma las cantidades
                $total_mantenimientos += $tecnico['cantidad_mantenimientos'];
                ?>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td><strong>Total trabajos de Mantenimiento</strong></td>
                <td><strong><?php echo number_format($total_mantenimientos); ?></strong></td>
            </tr>
        </tfoot>
    </table>

    <h4>Gastos por Mes (Año Actual)</h4>
    <table>
        <thead>
            <tr>
                <th>Mes</th>
                <th>Total Gastos</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Array de nombres de meses en español
            $meses = [
                1 => 'Enero',
                2 => 'Febrero',
                3 => 'Marzo',
                4 => 'Abril',
                5 => 'Mayo',
                6 => 'Junio',
                7 => 'Julio',
                8 => 'Agosto',
                9 => 'Septiembre',
                10 => 'Octubre',
                11 => 'Noviembre',
                12 => 'Diciembre'
            ];

            foreach ($gastos_mes as $gasto): ?>
                <tr>
                    <td><?php echo $meses[$gasto['mes']]; ?></td>
                    <td><?php echo number_format($gasto['total_gastos'], 2); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>


</html>