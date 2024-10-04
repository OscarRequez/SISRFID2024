<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte del Equipo</title>
    <style>
        h1 {
            text-align: center;
            margin-bottom: 10px;
        }

        p {
            font-size: 10px;
        }

        table {
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
    </style>
</head>

<h1>Reporte del Equipo</h1>
<p>
    <strong>Fecha del Reporte:</strong> <?php echo $reporte_info['fecha']; ?><br>
    <strong>Empresa:</strong> <?php echo $reporte_info['empresa']; ?><br>
    <strong>Elaborado por:</strong> <?php echo $reporte_info['elaborado_por']; ?>, <?php echo $reporte_info['puesto']; ?>
</p>
<hr>

<h3>Información General</h3>
<table style="width: 100%; border-collapse: collapse;">
    <tr>
        <td rowspan="2" style="text-align: center;">
            <img src="<?= base_url('assets/imagenes/equipos/' . $datosEquipo->Imagen) ?>" style="border-radius: 5px; max-width: 150px; max-height: 150px;" />
        </td>
        <td colspan="2"><strong>Descripción:</strong></td>
        <td colspan="1"><strong>Tipo de Equipo:</strong></td>
    </tr>
    <tr>
        <td colspan="2"><?= $datosEquipo->descripcion ?></td>
        <td colspan="1"><?= $datosEquipo->tipo_equipo ?></td>
    </tr>
    <tr>
        <td colspan="1"><strong>Número de Serie:</strong></td>
        <td colspan="1"><strong>Tag RFID ID:</strong></td>
        <td colspan="1"><strong>Ubicación Actual:</strong></td>
        <td colspan="1"><strong>Estado:</strong></td>
    </tr>
    <tr>
        <td colspan="1"><?= $datosEquipo->numero_serie ?></td>
        <td colspan="1"><?= $datosEquipo->tag_rfid_id ?></td>
        <td colspan="1"><?= $datosEquipo->ubicacion_nombre ?></td>
        <td colspan="1">
            <?php
            $estado = $datosEquipo->estado; // Accedemos al estado del equipo
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


    </tr>
</table>

<h3>Mantenimientos</h3>
<table border="1">
    <tr>
        <th><strong>ID Mant.</strong></th>
        <th><strong>Fecha</strong></th>
        <th><strong>Tipo de Mantenimiento</strong></th>
        <th><strong>Costo Total</strong></th>
    </tr>

    <?php
    $totalGastos = 0; // Inicializar el total de gastos
    if (!empty($mantenimientos)):
    ?>
        <?php foreach ($mantenimientos as $mantenimiento):
            $totalGastos += $mantenimiento->costoTotal; // Sumar cada costo total al total de gastos
        ?>
            <tr>
                <td><?= $mantenimiento->id ?></td>
                <td><?= $mantenimiento->fecha_solicitud ?></td>
                <td><?= $mantenimiento->tipo_mantenimiento ?></td>
                <td><?= number_format($mantenimiento->costoTotal, 2) ?></td>
            </tr>
        <?php endforeach; ?>

        <!-- Fila para mostrar el total de gastos en la última columna -->
        <tr>
            <td colspan="3" style="text-align: right;"><strong>Total Gastos:</strong></td>
            <td><strong><?= number_format($totalGastos, 2) ?></strong></td>
        </tr>

    <?php else: ?>
        <tr>
            <td colspan="3" style="text-align: center;">No se encontraron datos de mantenimientos</td>
        </tr>
    <?php endif; ?>
</table>


<h3>Historial de Movimientos</h3>
<table border="1">
    <tr>
        <th><strong>Fecha</strong></th>
        <th><strong>Ubicación Anterior</strong></th>
        <th><strong>Ubicación Nueva</strong></th>
        <th><strong>Motivo</strong></th>
    </tr>

    <?php if (!empty($movimientos)): ?>
        <?php foreach ($movimientos as $movimiento): ?>
            <tr>
                <td><?= $movimiento->fecha_movimiento ?></td>
                <td><?= $movimiento->ubicacion_anterior ?></td>
                <td><?= $movimiento->ubicacion_nueva ?></td>
                <td><?= $movimiento->motivo ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="4" style="text-align: center;">No se encontraron datos de movimientos</td>
        </tr>
    <?php endif; ?>
</table>