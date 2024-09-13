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
                    <th>#</th> <!-- Nueva columna para numeración -->
                    <th>Fecha de Movimiento</th>
                    <th>Ubicación Anterior</th>
                    <th>Ubicación Nueva</th>
                    <th>Uusario de Movimiento</th>
                </tr>
            </thead>
            <tbody>
                <?php $contador = 1; // Inicializar contador 
                ?>
                <?php foreach ($dataEquipo['historialMovimientos'] as $historial): ?>
                    <tr>
                        <td><?php echo $contador++; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($historial['fecha_movimiento'])); ?></td>
                        <td><?php echo htmlspecialchars($historial['ubicacion_anterior_nombre']); ?></td>
                        <td><?php echo htmlspecialchars($historial['ubicacion_nueva_nombre']); ?></td>
                        <td><?php echo htmlspecialchars($historial['nombre_usuario']); ?></td>

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
                    <th>#</th> <!-- Nueva columna para numeración -->
                    <th>Id Mant.</th>
                    <th>Técnico</th>
                    <th>Tipo</th>
                    <th>Área de Solicitud</th>
                    <th>Fecha de Solicitud</th>
                    <th>Fecha de Conclusión</th>
                    <th>Costo de Mant</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php $contador = 1; // Inicializar contador 
                ?>
                <?php foreach ($dataMantenimiento as $mantenimiento): ?>
                    <tr>
                        <td><?php echo $contador++; ?></td>
                        <td><?php echo htmlspecialchars($mantenimiento['id']); ?></td>
                        <td><?php echo htmlspecialchars($mantenimiento['nombre_tecnico']); ?></td>
                        <td><?php echo htmlspecialchars($mantenimiento['tipo_mantenimiento']); ?></td>
                        <td style="font-size:7px;"><?php echo htmlspecialchars($mantenimiento['nombre_ubicacion']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($mantenimiento['fecha_solicitud'])); ?></td>
                        <td>
                            <?php
                            // Mostrar la fecha de conclusión solo si no es null o vacía
                            if (empty($mantenimiento['fecha_conclusion'])) {
                                echo ''; // Dejar en blanco si no hay fecha de conclusión
                            } else {
                                echo date('d/m/Y', strtotime($mantenimiento['fecha_conclusion']));
                            }
                            ?>
                        </td>
                        <td><?php echo number_format($mantenimiento['costoTotal'], 2); ?> Bs.</td>
                        <td>
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
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay registros de mantenimiento para este equipo.</p>
    <?php endif; ?>

</body>

</html>