<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Equipos</title>
    <style>
        body {
            font-family: Georgia, 'Times New Roman', Times, serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }

        .header {
            display: none;
            /* Ocultar por defecto */
        }

        .header h2 {
            text-align: center;
            text-transform: uppercase;
            font-size: 14px;
            margin: 0;
        }

        .header p {
            text-align: center;
            margin: 5px 0;
        }

        @media print {
            .header {
                display: block;
                background: white;
                padding: 10px;
                margin-bottom: 20px;
                page-break-after: avoid;
                border-bottom: 1px solid #000;

            }

            table {
                margin-top: 60px;
                /* Espacio suficiente para la cabecera en la primera página */
            }

            .page-break {
                page-break-before: always;
            }
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
            background-color: #223361;
            color: white;
        }

        .small-column {
            width: 18;
        }

        .small-column_nombre {
            width: 100px;
        }

        .small-column_ubicacion {
            width: 90px;
        }

        .small-column_RFID {
            width: 70px;
        }

        .small-column_estado {
            width: 70px;
        }

        .footer {
            border-top: 1px solid #000;
            margin-top: 20px;
            text-align: center;
            padding: 10px 0;
            font-size: 10px;
        }
    </style>
</head>

<body>
    <!-- Cabecera que se mostrará solo en la primera página -->
    <div class="header">
        <h2>Reporte de los Equipos</h2>
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
                    <th class="small-column">#</th>
                    <th class="small-column">ID</th>
                    <th class="small-column_nombre">Nombre</th>
                    <th class="small-column_nombre">Descripción</th>
                    <th class="small-column_ubicacion">Ubicación</th>
                    <th class="small-column_estado">Estado</th>
                    <th class="small-column_RFID">Codigo Eq.</th>
                    <th class="small-column_RFID">RFID</th>
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
                                        echo 'Mantenimiento';
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
                        <td colspan="8">No se encontraron datos.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pie de página -->
    <div class="footer">
        <p>Reporte generado por el sistema de informacion de equipos.</p>
    </div>
</body>

</html>