<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }

        h2 {
            text-align: center;
        }

        /* Estilos CSS personalizados para la tabla del PDF */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;

        }


        table,
        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
            white-space: nowrap;
            /* Previene que el texto se rompa en múltiples líneas */
            font-size: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        h4,
        h3,
        p {
            margin: 0 0 10px 0;

        }
    </style>
</head>

<body>
    <h2>Reporte de Mantenimiento</h2>
    <p>
        <strong>Fecha del Reporte:</strong> <?php echo $reporte_info['fecha']; ?><br>
        <strong>Empresa:</strong> <?php echo $reporte_info['empresa']; ?><br>
        <strong>Elaborado por:</strong> <?php echo $reporte_info['elaborado_por']; ?>, <?php echo $reporte_info['puesto']; ?>
    </p>
    <hr>
    <h4>Mantenimientos Realizados</h4>
    <table>
        <thead>
            <tr>
                <th width="25px"><strong>#</strong></th> <!-- Nueva columna para numeración -->
                <th width="40px"><strong>ID</strong></th>
                <th width="60px"><strong>Cod. Equipo</strong></th>
                <th width="70px"><strong>Técnico</strong></th>
                <th width="70px"><strong>F. Solicitud</strong></th>
                <th width="80px"><strong>F. Conclusión</strong></th>
                <th width="60px"><strong>Costo Total(Bs)</strong></th>
                <th width="160px"><strong>Observaciones</strong></th>
            </tr>
        </thead>
        <tbody>
            <?php $numero = 1; // Inicializamos el contador 
            ?>
            <?php foreach ($datos_reporte as $dato): ?>
                <tr>
                    <td width="25px"><?php echo $numero++; ?></td> <!-- Numeración automática -->
                    <td width="40px"><?php echo $dato['id']; ?></td>
                    <td width="60px"><?php echo $dato['numero_serie_eq']; ?></td>
                    <td width="70px"><?php echo $dato['tecnico_nombre']; ?></td>
                    <td width="70px"><?php echo date('d/m/Y', strtotime($dato['fecha_solicitud'])); ?></td>
                    <td width="80px"><?php echo date('d/m/Y', strtotime($dato['fecha_conclusion'])); ?></td>
                    <td width="60px"><?php echo number_format($dato['total_materiales'], 2); ?></td>
                    <td width="160px"><?php echo $dato['observaciones']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <br>

    <h4>Técnico con Más Mantenimientos</h4>
    <table>
        <thead>
            <tr>
                <th><strong>Técnico</strong></th>
                <th><strong>Cantidad de Mantenimientos</strong></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo $tecnico_mas['tecnico_nombre']; ?></td>
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
                <th><strong>Técnico</strong></th>
                <th><strong>Cantidad de Mantenimientos</strong></th>
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

    <h4>Gastos por Mes del año <?php echo date('Y'); ?></h4>
    <table>
        <thead>
            <tr>
                <th><strong>Mes</strong></th>
                <th><strong>Total Gastos</strong></th>
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

            // Inicializa la variable para la suma total
            $total_gastos = 0;

            // Muestra los gastos por mes
            foreach ($gastos_mes as $gasto):
                $total_gastos += $gasto['total_gastos']; // Acumula el total
            ?>
                <tr>
                    <td><?php echo $meses[$gasto['mes']]; ?></td>
                    <td><?php echo number_format($gasto['total_gastos'], 2); ?></td>
                </tr>
            <?php endforeach; ?>

            <!-- Fila para mostrar la suma total -->
            <tr>
                <td><strong>Total de Gasto acumulado</strong></td>
                <td><strong><?php echo number_format($total_gastos, 2); ?></strong></td>
            </tr>
        </tbody>
    </table>



</body>


</html>