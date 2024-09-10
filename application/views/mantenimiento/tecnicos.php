<?php $this->load->view('templates/header'); ?>

<style>
    .pagination {
        display: flex;
        justify-content: center;
        margin: 20px 0;
    }
</style>

<div class="col py-3">
    <h2>Tareas de Mantenimiento</h2>
    <br>
    <!-- Tabla para mostrar las solicitudes -->
    <div class="table-responsive">
        <table class="table table-striped table-responsive">
            <thead class="bg-primary text-white">
                <tr>
                    <th>ID</th>
                    <th>Id equipo | Codigo</th>
                    <th>Descripción</th>
                    <th>Prioridad</th>
                    <th>Tipo</th>
                    <th>Fecha de Solicitud</th>
                    <th>Área de Solicitud</th>
                    <th>Estado</th>
                    <th>Imagen</th>
                    <th>
                        <center>Accion</center>
                    </th>
                </tr>
            </thead>
            <tbody id="TrabajosTableBody">
                <!-- Las filas de la tabla se llenarán dinámicamente aquí -->
            </tbody>
        </table>
    </div>

    <!-- Controles de paginación -->
    <div class="pagination-container">
        <ul class="pagination" id="paginacion"></ul>
    </div>
</div>

<!-- Modal para ver la imagen -->
<div class="modal fade" id="imagenModal" tabindex="-1" aria-labelledby="imagenModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imagenModalLabel">Imagen del Equipo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImagen" src="" alt="Imagen del equipo" width="300px" class="img-fluid">
            </div>
            <p id="modalDescripcion"><strong>Descricion:</strong> </p>
        </div>
    </div>
</div>

<!-- Modal para marcar como hecho -->
<div class="modal fade" id="hechoModal" tabindex="-1" aria-labelledby="hechoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="hechoModalLabel">Marcar Tarea como Hecha</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formHecho">
                    <input type="hidden" id="tareaId" name="tarea_id">

                    <!-- Sección de materiales utilizados -->
                    <div class="mb-3">
                        <label for="materiales" class="form-label">Materiales Utilizados</label>
                        <div id="materialesContainer">
                            <div class="row mb-3 material-row">
                                <div class="col-12 col-md-3 mb-2 mb-md-0">
                                    <input type="text" class="form-control" name="material[]" placeholder="Material" required>
                                </div>
                                <div class="col-12 col-md-2 mb-2 mb-md-0">
                                    <input type="number" class="form-control cantidad" name="cantidad[]" placeholder="Cantidad" required>
                                </div>
                                <div class="col-12 col-md-2 mb-2 mb-md-0">
                                    <input type="number" class="form-control costo_unitario" name="costo_unitario[]" placeholder="Costo Unitario" step="0.01" required>
                                </div>
                                <div class="col-12 col-md-2 mb-2 mb-md-0">
                                    <input type="number" class="form-control costo_total" name="costo_total[]" placeholder="Costo Total" step="0.01" readonly>
                                </div>
                                <div class="col-12 col-md-2 mb-2 mb-md-0">
                                    <input type="text" class="form-control" name="factura[]" placeholder="Nro Factura" required>
                                </div>
                                <div class="col-12 col-md-1 d-flex align-items-end justify-content-center mb-2 mb-md-0">
                                    <button type="button" class="btn btn-danger btn-sm removeMaterial">
                                        <i class="bi bi-trash3 removeMaterial"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-secondary" id="addMaterial">Agregar Material</button>
                    </div>

                    <!-- Total de materiales -->
                    <div class="mb-3">
                        <label for="totalMateriales" class="form-label">Total de Materiales</label>
                        <input type="number" class="form-control" id="totalMateriales" name="total_materiales" placeholder="Total Materiales" step="0.01" readonly>
                    </div>

                    <!-- Observaciones -->
                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
                    </div>

                    <!-- Botón de guardar -->
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    let paginaActual = 1;

    function cargarTareas(pagina) {
        $.ajax({
            url: "<?php echo site_url('obtener_tareas'); ?>",
            type: "POST",
            data: {
                pagina: pagina,
                cantidad: 5 // Número de registros por página
            },
            dataType: "json",
            success: function(response) {
                let filas = "";

                // Verificar si hay tareas
                if (response.tareas.length === 0) {
                    filas = '<tr><td colspan="9" class="text-center">Sin tareas asignadas</td></tr>';
                } else {
                    // Si hay tareas, construir las filas de la tabla
                    $.each(response.tareas, function(key, tarea) {
                        filas += '<tr>' +
                            '<td>' + tarea.id + '</td>' +
                            '<td>' + tarea.equipo_id + ' | ' + tarea.numero_serie_eq + '</td>' +
                            '<td>' + tarea.descripcion + '</td>' +
                            '<td>' + tarea.prioridad + '</td>' +
                            '<td>' + tarea.tipo_mantenimiento + '</td>' +
                            '<td>' + tarea.fecha_solicitud + '</td>' +
                            '<td>' + tarea.ubicacion_nombre + '</td>' +
                            '<td><center><i class="bi ' +
                            (tarea.estado == 1 ? 'fs-4 bi-send-check-fill' :
                                tarea.estado == 2 ? 'fs-4 bi-wrench-adjustable' :
                                tarea.estado == 4 ? 'fs-4 bi-check-circle' :
                                'fs-4 bi-check-circle-fill') +
                            '" style="color: ' +
                            (tarea.estado == 1 ? '#1F3BB6' :
                                tarea.estado == 2 ? '#D93C54' :
                                tarea.estado == 4 ? '#05A01A' :
                                'green') + '; cursor: ' +
                            (tarea.estado == 1 || tarea.estado == 2 || tarea.estado == 4 ? 'pointer' : 'default') + ';" ' +
                            'title="' + (tarea.estado == 1 ? 'Solicitado' :
                                tarea.estado == 2 ? 'En Mantenimiento' :
                                tarea.estado == 4 ? 'Por Confirmar' :
                                'Completado') +
                            '"></i></center></td>' +
                            '<td><center>' +
                            (tarea.Imagen ?
                                '<button class="btn btn-primary btn-sm  verImagen" data-imagen="' + tarea.Imagen + '" data-descripcion="' + tarea.descripcion + '"><i class="bi bi-eye"></i></button>' :
                                '<button class="btn btn-secondary btn-sm disabled"><i class="bi bi-eye"></i></button>') +
                            '</center></td>' +
                            '<td><div class="d-flex justify-content-center">' +
                            // Botón Reporte PDF
                            '<button class="btn btn-danger btn-sm  reportePDF ' +
                            (tarea.estado == 3 || tarea.estado == 4 ? '' : 'disabled') + '" ' +
                            'data-id="' + tarea.id + '">' +
                            '<i class="bi bi-file-earmark-pdf"></i>' +
                            '</button>' + 
                            // Botón Hecho
                            '<button class="btn btn-primary btn-hecho ms-2 ' +
                            (tarea.estado == 4 ? 'disabled' : '') + '" ' +
                            'data-bs-toggle="modal" data-bs-target="#hechoModal" ' +
                            'data-id="' + tarea.id + '">' +
                            (tarea.estado == 4 ? 'Por Confirmar' : 'Hecho') +
                            '</button>' +                          
                            '</div></td>' +
                            '</tr>';


                    });
                }

                // Insertar las filas en el cuerpo de la tabla
                $("#TrabajosTableBody").html(filas);

                // Paginación
                let totalregistros = response.totalregistros;
                let numerolinks = Math.ceil(totalregistros / 5);
                let paginador = "<ul class='pagination'>";

                if (pagina > 1) {
                    paginador += "<li class='page-item'><a class='page-link' href='#' data-pagina='1'>Primera</a></li>";
                    paginador += "<li class='page-item'><a class='page-link' href='#' data-pagina='" + (pagina - 1) + "'>Anterior</a></li>";
                } else {
                    paginador += "<li class='page-item disabled'><a class='page-link'>Primera</a></li>";
                    paginador += "<li class='page-item disabled'><a class='page-link'>Anterior</a></li>";
                }

                for (let i = 1; i <= numerolinks; i++) {
                    if (i == pagina) {
                        paginador += "<li class='page-item active'><a class='page-link' href='#'>" + i + "</a></li>";
                    } else {
                        paginador += "<li class='page-item'><a class='page-link' href='#' data-pagina='" + i + "'>" + i + "</a></li>";
                    }
                }

                if (pagina < numerolinks) {
                    paginador += "<li class='page-item'><a class='page-link' href='#' data-pagina='" + (pagina + 1) + "'>Siguiente</a></li>";
                    paginador += "<li class='page-item'><a class='page-link' href='#' data-pagina='" + numerolinks + "'>Última</a></li>";
                } else {
                    paginador += "<li class='page-item disabled'><a class='page-link'>Siguiente</a></li>";
                    paginador += "<li class='page-item disabled'><a class='page-link'>Última</a></li>";
                }

                paginador += "</ul>";
                $("#paginacion").html(paginador);
            },
            error: function(xhr, status, error) {
                console.error("Error en la solicitud:", status, error);
            }
        });
    }

    // Manejo del botón de ver imagen
    $("#TrabajosTableBody").on("click", ".verImagen", function() {
        var imagen = $(this).data("imagen");
        var descripcion = $(this).data("descripcion");
        $("#modalImagen").attr("src", "<?php echo base_url('assets/imagenes/equipos/') ?>" + imagen);
        // Asignar la descripción al elemento <p> en el modal
        $("#modalDescripcion").text('Descricion: ' + descripcion);
        $("#imagenModal").modal('show');
    });

    $(document).ready(function() {
        // Cargar las tareas iniciales
        cargarTareas(paginaActual);

        // Manejar clic en la paginación
        $(document).on('click', '.page-link', function(e) {
            e.preventDefault();
            let pagina = $(this).data('pagina');
            if (pagina) {
                paginaActual = pagina;
                cargarTareas(paginaActual);
            }
        });
    });
</script>

<script>
    //abrir mnodal
    $(document).on('click', '.btn-hecho', function() {
        // Obtener el ID de la tarea desde el atributo data-id del botón
        var tareaId = $(this).data('id');

        // Asignar el ID de la tarea al campo oculto del formulario en el modal
        $('#tareaId').val(tareaId);

        // Abrir el modal
        $('#hechoModal').modal('show');
    });



    // Manejar el envío del formulario
    $('#formHecho').submit(function(e) {
        e.preventDefault();

        var tareaId = $('#tareaId').val();
        var comentarios = $('#comentarios').val();

        $.ajax({
            url: "<?php echo site_url('marcar_tarea_hecha'); ?>", // Cambia esta URL al endpoint correcto
            type: "POST",
            data: {
                tarea_id: tareaId,
                comentarios: comentarios
            },
            success: function(response) {
                $('#hechoModal').modal('hide');
                Swal.fire({
                    title: "Exito",
                    text: " Tarea completada!",
                    icon: "success"
                });
                // Aquí puedes recargar la tabla o hacer otras acciones
                cargarTareas(paginaActual);
            },
            error: function(xhr, status, error) {
                console.error("Error al marcar tarea:", status, error);
            }
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let materialIndex = 1;

        // Añadir otro material
        document.getElementById('addMaterial').addEventListener('click', function() {
            materialIndex++;
            const materialesContainer = document.getElementById('materialesContainer');

            // Crear un nuevo div y añadirlo al contenedor
            const newMaterialRow = document.createElement('div');
            newMaterialRow.classList.add('row', 'mb-3', 'material-row');
            newMaterialRow.innerHTML = `
            <div class="col-12 col-md-3 mb-2 mb-md-0">
                <input type="text" class="form-control" name="material[]" placeholder="Material" required>
            </div>
            <div class="col-12 col-md-2 mb-2 mb-md-0">
                <input type="number" class="form-control cantidad" name="cantidad[]" placeholder="Cantidad" required>
            </div>
            <div class="col-12 col-md-2 mb-2 mb-md-0">
                <input type="number" class="form-control costo_unitario" name="costo_unitario[]" placeholder="Costo Unitario" step="0.01" required>
            </div>
            <div class="col-12 col-md-2 mb-2 mb-md-0">
                <input type="number" class="form-control costo_total" name="costo_total[]" placeholder="Costo Total" step="0.01" readonly>
            </div>
            <div class="col-12 col-md-2 mb-2 mb-md-0">
                <input type="text" class="form-control" name="factura[]" placeholder="Nro Factura" required>
            </div>
            <div class="col-12 col-md-1 d-flex align-items-end justify-content-center mb-2 mb-md-0">
                <button type="button" class="btn btn-danger btn-sm removeMaterial">
                    <i class="bi bi-trash3 removeMaterial"></i>
                </button>
            </div>
        `;
            materialesContainer.appendChild(newMaterialRow);
        });

        // Eliminar material
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('removeMaterial')) {
                event.target.closest('.material-row').remove();
                calcularTotalMateriales(); // Recalcular total cuando se elimina un material
            }
        });

        // Calcular costo total automáticamente
        document.addEventListener('input', function(event) {
            if (event.target.classList.contains('costo_unitario') || event.target.classList.contains('cantidad')) {
                const row = event.target.closest('.material-row');
                const costoUnitario = parseFloat(row.querySelector('.costo_unitario').value) || 0;
                const cantidad = parseFloat(row.querySelector('.cantidad').value) || 0;
                const costoTotalInput = row.querySelector('.costo_total');
                costoTotalInput.value = (costoUnitario * cantidad).toFixed(2);
                calcularTotalMateriales(); // Recalcular total cada vez que se modifica un valor
            }
        });

        function calcularTotalMateriales() {
            let total = 0;
            document.querySelectorAll('.costo_total').forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            document.getElementById('totalMateriales').value = total.toFixed(2);
        }
    });

    //enviar los datos del formulario
    // Manejar el envío del formulario
    $('#formHecho').submit(function(e) {
        e.preventDefault();

        var formData = $(this).serialize();
        console.log("Datos del formulario:", formData);

        $.ajax({
            url: "<?php echo site_url('marcar_tarea_hecha'); ?>",
            type: "POST",
            data: formData,
            success: function(response) {
                var jsonResponse = JSON.parse(response);
                if (jsonResponse.status === 'success') {
                    $('#hechoModal').modal('hide');
                    alert(jsonResponse.message);
                    // Aquí podrías agregar más lógica para actualizar la interfaz
                } else {
                    alert(jsonResponse.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("Error al marcar tarea:", status, error);
            }
        });
    });
</script>

<script>
    $(document).on('click', '.reportePDF', function() {
        var tareaId = $(this).data('id');

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
                // Aquí pones la lógica para generar el PDF
                // Puedes hacer una redirección o una llamada AJAX
                window.location.href = '/ruta/a/tu/controlador/generar_pdf/' + tareaId;
            }
        });
    });
</script>

<?php $this->load->view('templates/footer'); ?>