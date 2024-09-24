<?php $this->load->view('templates/header'); ?>
<style>
    body {
        background-color: #f8f9fa;
        /* Fondo suave */
    }

    .card {
        border: 1px solid #ced4da;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s;
        margin-bottom: 20px;
        /* Espacio entre tarjetas */
    }

    .card:hover {
        transform: scale(1.02);
    }

    .card-body {
        padding: 20px;
    }

    #imagenEquipo {
        max-width: 100%;
        height: auto;
        border-radius: 10px;
    }

    .btn {
        margin-top: 10px;
    }
</style>

<div class="col py-3">
    <h2 class="text-center">Información de Equipos con RFID</h2>
    <hr>
    <div class="row mb-3 align-items-end">
        <div class="col-md-8">
            <label for="codigoRFID" class="form-label">Escanear Código RFID</label>
            <input type="text" id="codigoRFID" class="form-control" placeholder="Escanee o ingrese el código">
        </div>
        <div class="col-md-4">
            <button class="btn btn-primary" id="btnBuscar">Buscar Información</button>
        </div>
    </div>


    <div id="informacionEquipo" style="display: none;">
        <div class="row">
            <!-- Tarjeta 1: Imagen del equipo -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <img id="imagenEquipo" src="" alt="Imagen del equipo" class="img-fluid">
                    </div>
                </div>
            </div>

            <!-- Tarjeta 2: Datos del equipo -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title" id="tituloEquipo">Datos del Equipo</h5>
                        <p class="card-text" id="descripcionEquipo"></p>
                        <p class="card-text"><strong>Codigo:</strong> <span id="numeroSerieEquipo"></span></p>
                        <p class="card-text"><strong>Tipo:</strong> <span id="tipo_eq"></span></p>
                        <p class="card-text"><strong>Ubicación:</strong> <span id="ubicacionEquipo"></span></p>
                        <p class="card-text"><strong>Estado:</strong> <span id="estadoEquipo"></span></p>
                        <button class="btn btn-success reporte_rfid_PDF" data-id="">Generar Reporte</button>
                    </div>
                </div>
            </div>

            <!-- Tarjeta 3: Mantenimientos -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Historial de Mantenimientos</h5>
                        <p class="card-text">Cantidad de mantenimientos: <span id="numMantenimientos">0</span></p>
                        <ul id="listaMantenimientos" class="list-unstyled"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
    // Evento click del botón de búsqueda
    $('#btnBuscar').click(function() {
        buscarEquipoRFID();
    });

    // Evento keydown para detectar el Enter en el campo de texto
    $('#codigoRFID').keydown(function(e) {
        if (e.keyCode == 13) {  // Código de tecla Enter
            e.preventDefault();  // Evitar que el formulario se envíe si lo hay
            buscarEquipoRFID();
        }
    });

    function buscarEquipoRFID() {
        var codigoRFID = $('#codigoRFID').val();

        $.ajax({
            url: "<?php echo site_url('obtener_datos_equipo_rfid'); ?>",
            type: "POST",
            data: {
                codigo: codigoRFID
            },
            success: function(response) {
                response = JSON.parse(response); // Asegúrate de que estás analizando correctamente

                if (response.success && response.data.equipo) {
                    // Llenar las tarjetas con la información del equipo
                    $('#informacionEquipo').show(); // Mostrar la sección de información
                    $('#descripcionEquipo').text(response.data.equipo.descripcion);
                    $('#tipo_eq').text(response.data.equipo.tipo_nombre);
                    $('#numeroSerieEquipo').text(response.data.equipo.numero_serie);
                    $('#ubicacionEquipo').text(response.data.equipo.ubicacion_nombre); // Aquí puedes obtener el nombre si lo necesitas
                    $('#estadoEquipo').text(
                        response.data.equipo.estado == "1" ? "Activo" :
                        response.data.equipo.estado == "0" ? "Inactivo" :
                        response.data.equipo.estado == "3" ? "Mantenimiento" :
                        "Desconocido"
                    );
                    $('#imagenEquipo').attr('src', response.data.equipo.ruta_imagen);

                    // Asignar el ID del equipo al botón de generar reporte
                    $('.reporte_rfid_PDF').data('id', response.data.equipo.id); // Asigna el ID del equipo

                    // Llenar el historial de mantenimientos
                    $('#numMantenimientos').text(response.data.mantenimientos.length);

                    $('#listaMantenimientos').empty();
                    if (response.data.mantenimientos.length > 0) {
                        $.each(response.data.mantenimientos, function(index, mantenimiento) {
                            // Formatear la fecha
                            const fecha = new Date(mantenimiento.fecha_solicitud);
                            const opcionesFecha = {
                                day: '2-digit',
                                month: '2-digit',
                                year: 'numeric'
                            };
                            const fechaFormateada = fecha.toLocaleDateString('es-ES', opcionesFecha); // Formato día/mes/año

                            $('#listaMantenimientos').append(
                                `<li>${fechaFormateada} - Costo: ${mantenimiento.costoTotal}</li>`
                            );
                        });
                    } else {
                        $('#listaMantenimientos').append('<li>No hay mantenimientos registrados.</li>');
                    }

                    // Limpiar el campo de búsqueda después de obtener los datos
                    $('#codigoRFID').val('');
                } else {
                    // Manejo de errores
                    Swal.fire({
                        icon: 'warning',
                        title: 'Error',
                        text: 'No se encontró información del equipo.',
                        confirmButtonText: 'Ok'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al procesar la solicitud.',
                    confirmButtonText: 'Ok'
                });
            }
        });
    }
});

</script>

<script>

$(document).on('click', '.reporte_rfid_PDF', function() {
    var equipoId = $(this).data('id'); // Capturar el ID del equipo

        // SweetAlert de confirmación
        Swal.fire({
            title: 'Confirmar Reporte',
            text: "¿Deseas generar el reporte PDF para esta tarea?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, generar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Construir la URL solo con el ID de la tarea
                var url = '<?php echo site_url('reporte_rfid_equipo'); ?>' + '?tareaId=' + equipoId;

                // Abrir el reporte en una nueva pestaña
                window.open(url, '_blank');
            }
        });
    });
</script>