<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de la Tarea de Lectura</title>
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
            padding: 9px;
            text-align: left;
            white-space: nowrap;
            font-size: 8px;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .no-data {
            text-align: center;
            color: red;
            font-weight: bold;
        }

        /* Clases para ajustar el ancho de las columnas */
        .col-id {
            width: 30px;
            /* Menos espacio para ID */
        }

        .col-descripcion {
            width: 250px;
            /* Más espacio para Descripción */
        }

        .col-rfid {
            width: 90px;
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
            width: 80px;
            /* Espacio para Costo */
        }
    </style>
</head>

<body>

    <h2 class="header">Reporte de Tarea de Lectura RFID</h2>
    <p>
        <strong>Fecha generación de Reporte:</strong> <?= $reporte_info['fecha'] ?> <br>
        <strong>Ubicación:</strong> <?= $data['tarea']['nombre_ubicacion'] ?> <br>
        <strong>Operario:</strong> <?= $data['tarea']['nombre_operario'] ?><br>
        <strong>Fecha Asignación:</strong> <?= $data['tarea']['fecha_asignacion'] ?><br>
        <strong>Fecha Fin:</strong> <?= $data['tarea']['fecha_fin'] ?><br>
        <strong>Fecha Aprobación:</strong> <?= $data['tarea']['fecha_aprobacion'] ?> <br>
        <strong>Estado de la tarea:</strong> <?php
                                                // Traducción del estado
                                                switch ($data['tarea']['estado']) {
                                                    case 1:
                                                        echo 'Solicitado';
                                                        break;
                                                    case 2:
                                                        echo 'En Proceso';
                                                        break;
                                                    case 3:
                                                        echo 'Por Confirmar';
                                                        break;
                                                    case 4:
                                                        echo 'Completado';
                                                        break;
                                                    default:
                                                        echo 'Estado Desconocido'; // En caso de que el estado no coincida con ninguno de los casos
                                                        break;
                                                }
                                                ?>

    </p>

    <hr>

    <h3>Detalles de la Tarea</h3>
    <table>
        <thead>
            <tr>
                <th scope="col"><strong>ID de Tarea</strong></th>
                <th scope="col"><strong>Descripción</strong></th>
                <th scope="col"><strong>Total Equipos</strong></th>
                <th scope="col"><strong>Total Leídos</strong></th>
                <th scope="col"><strong>Total Faltantes</strong></th>
                <th scope="col"><strong>Total Otros</strong></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= $data['tarea']['id'] ?></td>
                <td><?= $data['tarea']['descripcion_tarea'] ?></td>
                <td><?= $data['tarea']['total_equipos'] ?></td>
                <td><?= $data['tarea']['total_leidos'] ?></td>
                <td><?= $data['tarea']['total_no_leidos'] ?></td>
                <td><?= $data['tarea']['total_otros'] ?></td>
            </tr>
        </tbody>
    </table>

    <table>
        <tr>
            <th><strong>Observaciones</strong></th>
            <td><?= $data['tarea']['observaciones'] ?></td>
        </tr>
    </table>
    <hr>

    <h3>Resultados de la Lectura</h3>
    <h4>Equipos Leídos (Pertenecen al Área)</h4>
    <table>
        <thead>
            <tr>
                <th scope="col"><strong>ID</strong></th>
                <th scope="col"><strong>Etiqueta RFID</strong></th>
                <th scope="col"><strong>Equipo ID</strong></th>
                <th scope="col"><strong>Ubicación</strong></th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($data['equipos_leidos']) && !empty($data['equipos_leidos'])): ?>
                <?php foreach ($data['equipos_leidos'] as $resultado): ?>
                    <tr>
                        <td><?= $resultado['id'] ?></td>
                        <td><?= $resultado['rfid_tag'] ?></td>
                        <td><?= $resultado['equipo_id'] ?></td>
                        <td><?= $resultado['nombre_ubicacion_eq'] ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="no-data">No se encontraron resultados de lectura para equipos que pertenecen.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h4>Equipos No Pertenecientes</h4>
    <table>
        <thead>
            <tr>
                <th scope="col"><strong>ID</strong></th>
                <th scope="col"><strong>Etiqueta RFID</strong></th>
                <th scope="col"><strong>Equipo ID</strong></th>
                <th scope="col"><strong>Pertenece a</strong></th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($data['equipos_no_pertenecientes']) && !empty($data['equipos_no_pertenecientes'])): ?>
                <?php foreach ($data['equipos_no_pertenecientes'] as $resultado): ?>
                    <tr>
                        <td><?= $resultado['id'] ?></td>
                        <td><?= $resultado['rfid_tag'] ?></td>
                        <td><?= $resultado['equipo_id'] ?></td>
                        <td><?= $resultado['nombre_ubicacion_eq'] ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="no-data">No se encontraron resultados de lectura para equipos que no pertenecen.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h3>Equipos No Leídos</h3>
    <table class="table">
        <thead>
            <tr>
                <th class="col-id"><strong>ID</strong></th>
                <th class="col-descripcion"><strong>Descripción</strong></th>
                <th class="col-estado"><strong>Estado</strong></th>
                <th class="col-codigo"><strong>Código</strong></th>
                <th class="col-rfid"><strong>Tag RFID</strong></th>
                <th class="col-costo"><strong>Costo</strong></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($equipos_no_leidos)): ?>
                <tr>
                    <td colspan="6" style="text-align: center;">
                        No se encontraron equipos no leídos</td>
                </tr>
            <?php else: ?>
                <?php foreach ($equipos_no_leidos as $equipo): ?>
                    <tr>
                        <td class="col-id"><?php echo $equipo['id']; ?></td>
                        <td class="col-descripcion"><?php echo $equipo['descripcion']; ?></td>
                        <td class="col-estado">
                            <?php
                            $estado = $equipo['estado'];
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
                        <td class="col-codigo"><?php echo $equipo['numero_serie']; ?></td>
                        <td class="col-rfid"><?php echo $equipo['tag_rfid_id']; ?></td>
                        <td class="col-costo"><?php echo number_format($equipo['costo'], 2, ',', '.'); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

</body>

</html>