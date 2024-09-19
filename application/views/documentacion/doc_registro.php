<?php $this->load->view('templates/header'); ?>

<style>
    .pagination {
        display: flex;
        justify-content: center;
        margin: 20px 0;
    }

    #card_doc {
       
        /* Ancho fijo para todas las tarjetas con ID card_doc */
        height: 350px;
        /* Altura fija para todas las tarjetas con ID card_doc */
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
        margin: 10px;
    }

    #card_doc .card-img-top {
        height: 230px;
        /* Altura fija para la imagen */
        object-fit: cover;
        /* Ajusta la imagen para cubrir el contenedor sin distorsión */
        transition: transform 0.3s ease;
    }

    #card_doc .card-img-top:hover {
        transform: scale(1.05);
        /* Aumenta el tamaño de la imagen al pasar el ratón sobre ella */
        opacity: 1.1;
        /* Opcional: Añade un efecto de opacidad para mejorar la visibilidad */
    }

    #card_doc .card-body {
        text-align: center;

    }
</style>



<div class="col py-3">
    <h2>Documentación y Manuales</h2>
    <hr>
    <div class="row">
        <div class="col-md-6">
            <!-- Botón para abrir el modal de nueva solicitud -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#agregarDocumentoModal">
                Agregar Documento
            </button>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="agregarDocumentoModal" tabindex="-1" aria-labelledby="agregarDocumentoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="agregarDocumentoModalLabel">Agregar Documento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formAgregarDocumento" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="tituloDocumento" class="form-label">Título</label>
                            <input type="text" class="form-control" id="tituloDocumento" name="titulo" required>
                        </div>
                        <div class="mb-3">
                            <label for="archivoDocumento" class="form-label">Documento (PDF u otro formato)</label>
                            <input type="file" class="form-control" id="archivoDocumento" name="documento" accept=".pdf,.doc,.docx,.txt" required>
                        </div>
                        <div class="mb-3">
                            <label for="imagenPortada" class="form-label">Imagen de Portada</label>
                            <input type="file" class="form-control" id="imagenPortada" name="imagen" accept="image/*" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar Documento</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="container my-4">
        <div class="row" id="documentos-container">
            <!-- Aquí se cargarán las tarjetas de documentos mediante AJAX -->
        </div>
        <div id="paginacion" class="pagination-container">
            <!-- Aquí se cargarán los botones de paginación -->
        </div>
    </div>
</div>

<script>
    function cargar_docs(pagina = 1, cantidad = 4) {
        $.ajax({
            url: '<?php echo site_url('obtener_documentos'); ?>',
            type: 'GET',
            data: {
                pagina: pagina,
                cantidad: cantidad
            },
            dataType: 'json',
            success: function(response) {
                $('#documentos-container').empty();
                if (response.documentos.length > 0) {
                    $.each(response.documentos, function(index, documento) {
                        var cardHtml = `
                                <div class="col-md-3 col-sm-6 mb-4">
                                    <div class="card" id="card_doc">
                                        <img id="imagen_doc" class="card-img-top img-thumbnail" src="<?php echo base_url('assets/imagenes/documentos/') . '/'; ?>${documento.imagen_portada}" alt="Imagen de portada">
                                        <div class="card-body">
                                           <h5 style="font-size:15px" class="card-title">${documento.titulo}</h5>
                                            <p style="font-size:12px"class="card-text">${documento.feccre}</p>
                                        </div>
                                        <div class="card-footer text-center">
                                            <a href="<?php echo base_url('assets/documentos/') . '/'; ?>${documento.documento}" class="btn btn-primary" target="_blank">
                                                <i class="bi bi-box-arrow-in-up-right"></i>
                                            </a>
                                            <button class="btn btn-danger btn-delete" data-id="${documento.id}" data-imagen="${documento.imagen_portada}" data-documento="${documento.documento}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                `;
                        $('#documentos-container').append(cardHtml);
                    });
                } else {
                    $('#documentos-container').append('<div class="col-md-12"><p>No se encontraron documentos.</p></div>');
                }

                // Manejar la paginación
                var totalregistros = response.totalDocumentos;
                var numerolinks = Math.ceil(totalregistros / cantidad);
                var paginador = "<ul class='pagination'>";

                // Botones "Primera" y "Anterior"
                if (pagina > 1) {
                    paginador += "<li class='page-item'><a class='page-link' href='javascript:void(0)' onclick='cargar_docs(1, " + cantidad + ")'>Primera</a></li>";
                    paginador += "<li class='page-item'><a class='page-link' href='javascript:void(0)' onclick='cargar_docs(" + (pagina - 1) + ", " + cantidad + ")'>Anterior</a></li>";
                } else {
                    paginador += "<li class='page-item disabled'><a class='page-link' href='#'>Primera</a></li>";
                    paginador += "<li class='page-item disabled'><a class='page-link' href='#'>Anterior</a></li>";
                }

                // Botones de números de página
                var startPage = Math.max(1, pagina - 2);
                var endPage = Math.min(numerolinks, pagina + 2);

                if (startPage > 1) {
                    paginador += "<li class='page-item'><a class='page-link' href='javascript:void(0)' onclick='cargar_docs(1, " + cantidad + ")'>1</a></li>";
                    if (startPage > 2) paginador += "<li class='page-item disabled'><a class='page-link'>...</a></li>";
                }

                for (var i = startPage; i <= endPage; i++) {
                    if (i == pagina) {
                        paginador += "<li class='page-item active'><a class='page-link' href='javascript:void(0)'>" + i + "</a></li>";
                    } else {
                        paginador += "<li class='page-item'><a class='page-link' href='javascript:void(0)' onclick='cargar_docs(" + i + ", " + cantidad + ")'>" + i + "</a></li>";
                    }
                }

                if (endPage < numerolinks) {
                    if (endPage < numerolinks - 1) paginador += "<li class='page-item disabled'><a class='page-link'>...</a></li>";
                    paginador += "<li class='page-item'><a class='page-link' href='javascript:void(0)' onclick='cargar_docs(" + numerolinks + ", " + cantidad + ")'>" + numerolinks + "</a></li>";
                }

                // Botones "Siguiente" y "Última"
                if (pagina < numerolinks) {
                    paginador += "<li class='page-item'><a class='page-link' href='javascript:void(0)' onclick='cargar_docs(" + (pagina + 1) + ", " + cantidad + ")'>Siguiente</a></li>";
                    paginador += "<li class='page-item'><a class='page-link' href='javascript:void(0)' onclick='cargar_docs(" + numerolinks + ", " + cantidad + ")'>Última</a></li>";
                } else {
                    paginador += "<li class='page-item disabled'><a class='page-link' href='#'>Siguiente</a></li>";
                    paginador += "<li class='page-item disabled'><a class='page-link' href='#'>Última</a></li>";
                }

                paginador += "</ul>";
                $("#paginacion").html(paginador); // Insertar la paginación en el contenedor
            },
            error: function(xhr, status, error) {
                console.error("Error en la solicitud:", status, error);
            }
        });
    }

    $(document).ready(function() {
        // Llama a la función para cargar los documentos cuando la página se carga
        cargar_docs(1, 4);

        // Manejo del formulario de agregar documento
        $('#formAgregarDocumento').on('submit', function(e) {
            e.preventDefault(); // Evitar el envío por defecto

            var formData = new FormData(this); // Crear un objeto FormData

            $.ajax({
                url: '<?php echo site_url('agregar_documento'); ?>',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    var res = JSON.parse(response); // Convertir el JSON en objeto
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: res.message
                        }).then(() => {
                            $('#agregarDocumentoModal').modal('hide'); // Cerrar el modal
                            $('#formAgregarDocumento')[0].reset(); // Limpiar el formulario
                            cargar_docs(1, 4); // Volver a cargar los documentos
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: res.message
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Hubo un problema con el envío del formulario'
                    });
                }
            });
        });


        $(document).on('click', '.btn-delete', function() {
            var id = $(this).data('id');
            var imagen = $(this).data('imagen');
            var documento = $(this).data('documento');

            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡No podrás recuperar este documento!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminarlo'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?php echo site_url('eliminar_documento'); ?>',
                        type: 'POST',
                        data: {
                            id: id,
                            imagen: imagen,
                            documento: documento
                        },
                        success: function(response) {
                            var res = JSON.parse(response);
                            Swal.fire({
                                icon: 'success',
                                title: 'Eliminado',
                                text: res.message
                            }).then(() => {
                                cargar_docs(1, 4); // Recargar documentos
                            });
                        }
                    });
                }
            });
        });


        // Manejo de los enlaces de paginación
        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            var page = $(this).text(); // Obtener el número de página del enlace
            cargar_docs(page); // Cargar los documentos para la nueva página
        });
    });
</script>


<?php $this->load->view('templates/footer'); ?>