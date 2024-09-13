<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte del Historial del Equipo</title>
    <style>
        h1 {
            text-align: center;
        }


        table,
        th,
        td {
            border: 1px solid black;
            padding: 8px;
            font-size: 9px;
        }
    </style>
</head>

<body>
    <h1>Historial del Equipo</h1>
    <p>
        <strong>Fecha del reporte:</strong> <?php echo htmlspecialchars($reporte_info['fecha']); ?><br>
        <strong>Elaborado por:</strong> <?php echo htmlspecialchars($reporte_info['elaborado_por']); ?> - <?php echo htmlspecialchars($reporte_info['puesto']); ?><br>
        <strong>Empresa:</strong> <?php echo htmlspecialchars($reporte_info['empresa']); ?>
    </p>
    <!-- Línea de separación -->
    <hr>
    <!-- Información del equipo -->
    <h3>Información del Equipo</h3>
    <p>
        <strong>Código del Equipo:</strong> <?php echo isset($dataEquipo['datosEquipo']['numero_serie']) ? htmlspecialchars($dataEquipo['datosEquipo']['numero_serie']) : 'No disponible'; ?><br>
        <strong>ID del Equipo:</strong> <?php echo isset($dataEquipo['datosEquipo']['id']) ? htmlspecialchars($dataEquipo['datosEquipo']['id']) : 'No disponible'; ?><br>
        <strong>RFID:</strong> <?php echo isset($dataEquipo['datosEquipo']['tag_rfid_id']) ? htmlspecialchars($dataEquipo['datosEquipo']['tag_rfid_id']) : 'No disponible'; ?><br>
        <strong>Ubicación Actual:</strong> <?php echo isset($dataEquipo['datosEquipo']['ubicacion_nombre']) ? htmlspecialchars($dataEquipo['datosEquipo']['ubicacion_nombre']) : 'No disponible'; ?>
    </p>

    <!-- Historial de Ubicaciones -->
    <!-- Historial de Ubicaciones -->
    <h3>Historial de Ubicaciones</h3>
    <?php if (!empty($dataEquipo['historialMovimientos'])): ?>
        <table>
            <thead>
                <tr>
                    <th width="25px">#</th> <!-- Nueva columna para numeración -->
                    <th width="85px">Fecha de Movimiento</th>
                    <th width="85px">Ubicación Anterior</th>
                    <th width="85px">Ubicación Nueva</th>
                    <th width="85px">Usario que Cambio</th>
                    <th width="200px">Motivo</th>
                </tr>
            </thead>
            <tbody>
                <?php $contador = 1; // Inicializar contador 
                ?>
                <?php foreach ($dataEquipo['historialMovimientos'] as $historial): ?>
                    <tr>
                        <td width="25px"><?php echo $contador++; ?></td>
                        <td width="85px"><?php echo date('d/m/Y', strtotime($historial['fecha_movimiento'])); ?></td>
                        <td width="85px"><?php echo htmlspecialchars($historial['ubicacion_anterior_nombre']); ?></td>
                        <td width="85px"><?php echo htmlspecialchars($historial['ubicacion_nueva_nombre']); ?></td>
                        <td width="85px"><?php echo htmlspecialchars($historial['nombre_usuario']); ?></td>
                        <td width="200px"><?php echo htmlspecialchars($historial['motivo']); ?></td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay registros de historial de ubicaciones para este equipo.</p>
    <?php endif; ?>

    <!-- Historial de Mantenimientos -->
    <h3>Historial de Mantenimientos</h3>
    <?php if (!empty($dataMantenimiento)): ?>
        <table>
            <thead>
                <tr>
                    <th width="30px">#</th> <!-- Nueva columna para numeración -->
                    <th width="45px">ID</th>
                    <th width="70px">Técnico</th>
                    <th width="60px">Tipo</th>
                    <th width="90px">Área de Solicitud</th>
                    <th width="70px">F.Solicitud</th>
                    <th width="70px">F.Conclusión</th>
                    <th width="65px">Costo Total</th>
                    <th width="60px">Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $contador = 1; // Inicializar contador 
                $sumaCostoTotal = 0; // Inicializar la suma de los costos
                ?>
                <?php foreach ($dataMantenimiento as $mantenimiento): ?>
                    <tr>
                        <td width="30px"><?php echo $contador++; ?></td>
                        <td width="45px"><?php echo htmlspecialchars($mantenimiento['id']); ?></td>
                        <td width="70px"><?php echo htmlspecialchars($mantenimiento['nombre_tecnico']); ?></td>
                        <td width="60px"><?php echo htmlspecialchars($mantenimiento['tipo_mantenimiento']); ?></td>
                        <td width="90px" style="font-size:7px;"><?php echo htmlspecialchars($mantenimiento['nombre_ubicacion']); ?></td>
                        <td width="70px"><?php echo date('d/m/Y', strtotime($mantenimiento['fecha_solicitud'])); ?></td>
                        <td width="70px">
                            <?php
                            // Mostrar la fecha de conclusión solo si no es null o vacía
                            if (empty($mantenimiento['fecha_conclusion'])) {
                                echo ''; // Dejar en blanco si no hay fecha de conclusión
                            } else {
                                echo date('d/m/Y', strtotime($mantenimiento['fecha_conclusion']));
                            }
                            ?>
                        </td>
                        <td width="65px"><?php echo number_format($mantenimiento['costoTotal'], 2); ?> Bs.</td>
                        <td width="60px" style="font-size:7px;">
                            <?php
                            switch ($mantenimiento['estado']) {
                                case 1:
                                    echo 'Solicitado';
                                    break;
                                case 2:
                                    echo 'En Mant.';
                                    break;
                                case 3:
                                    echo 'Completado';
                                    break;
                                case 4:
                                    echo 'Por Confirmar';
                                    break;
                                default:
                                    echo 'Desconocido';
                                    break;
                            }
                            ?>
                        </td>
                    </tr>
                    <?php
                    // Sumar el costo total de cada mantenimiento
                    $sumaCostoTotal += $mantenimiento['costoTotal'];
                    ?>
                <?php endforeach; ?>
                <!-- Fila para el total de la inversión -->
                <tr>
                    <td colspan="7" style="text-align: right;"><strong>Total Inversión en Mantenimiento:</strong></td>
                    <td colspan="2"><strong><?php echo number_format($sumaCostoTotal, 2); ?> Bs.</strong></td>
                </tr>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay registros de mantenimiento para este equipo.</p>
    <?php endif; ?>


</body>

</html>