<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Equipos</title>
    <style>
        body {
            font-family: Georgia, 'Times New Roman', Times, serif;
            font-size: 9px;
        }

        th {
            border: 1px rgb(36, 158, 75, 0.6);

        }

        td {
            border: 1px rgb(36, 158, 75, 0.6);
            padding: 8px;
            text-align: left;
        }

        .header {
            display: none;
            /* Ocultar por defecto */
        }

        .header h2 {
            /* Elimina padding del título */
            text-align: center;
            text-transform: uppercase;
            font-size: 12px;

        }

        @media print {
            .header {

                background: white;
                padding: 10px;
                margin-bottom: 20px;
                /* Espacio debajo de la cabecera */
                page-break-after: avoid;
                /* Evitar el salto de página después de la cabecera */
            }

            table {
                margin-top: 100px;
                /* Espacio suficiente para la cabecera en la primera página */
            }

            .page-break {
                page-break-before: always;
                /* Forzar salto de página antes de la tabla si es necesario */
            }
        }

        .small-column {
            width: 20px;
        }

        .small-column_nombre {
            width: 130px;
        }

        .small-column_ubicacion {
            width: 70px;
        }

        .small-column_RFID {
            width: 60px;
        }

        .small-column_estado {
            width: 50px;
        }

        /*footer*/
        .footer {
            border-top: 1px solid #000;
        }
    </style>
</head>

<body>
    <!-- Cabecera que se mostrará solo en la primera página -->
    <div class="header">
        <h2>Reporte de los equipos</h2>
        <p>
            <strong><?php echo $reporte_info['empresa']; ?></strong><br>
            <strong>Fecha:</strong> <?php echo $reporte_info['fecha']; ?><br>
            <strong>Empresa:</strong> <?php echo $reporte_info['empresa']; ?><br>
            <strong>Dirección:</strong> <?php echo $reporte_info['direccion']; ?><br>
            <strong>Contacto:</strong> <?php echo $reporte_info['contacto']; ?><br>
            <strong>Elaborado por:</strong> <?php echo $reporte_info['elaborado_por']; ?><br>
            <strong>Rol:</strong> <?php echo $reporte_info['puesto']; ?>
        </p>
    </div>

    <!-- Contenido principal de la tabla -->
    <div class="content">
        <table>
            <thead>
                <tr>
                    <th style="background-color:#223361; color:white" class="small-column">#</th>
                    <th style="background-color:#223361; color:white" class="small-column">ID</th>
                    <th style="background-color:#223361; color:white" class="small-column_nombre">NOMBRE</th>
                    <th style="background-color:#223361; color:white" class="small-column_nombre">DESCRIPCION</th>
                    <th style="background-color:#223361; color:white" class="small-column_ubicacion">UBICACION</th>
                    <th style="background-color:#223361; color:white" class="small-column_estado">ESTADO</th>
                    <th style="background-color:#223361; color:white" class="small-column_RFID">CODIGO</th>
                    <th style="background-color:#223361; color:white" class="small-column_RFID">RFID</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data) && is_array($data)): ?>
                    <?php
                    $contador = 1;
                    foreach ($data as $item):
                    ?>
                        <tr>
                            <td class="small-column"><?php echo $contador++; ?></td>
                            <td class="small-column"><?php echo htmlspecialchars($item['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="small-column_nombre"><?php echo htmlspecialchars($item['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="small-column_nombre"><?php echo htmlspecialchars($item['descripcion'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="small-column_ubicacion"><?php echo htmlspecialchars(ucfirst(strtolower($item['ubicacion_nombre'])), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="small-column_estado">
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
                                        echo 'Mant.';
                                        break;
                                    default:
                                        echo 'Desconocido';
                                        break;
                                }
                                ?>
                            </td>
                            <td class="small-column_RFID"><?php echo htmlspecialchars($item['numero_serie'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="small-column_RFID"><?php echo htmlspecialchars($item['tag_rfid_id'], ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No se encontraron datos.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>

</html>