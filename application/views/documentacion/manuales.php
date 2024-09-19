<?php $this->load->view('templates/header'); ?>

<style>
    .pagination {
        display: flex;
        justify-content: center;
        margin: 20px 0;
    }

    #imagen_doc {
        height: 260px;
        /* Ajusta el tamaño de la imagen de portada según tus necesidades */
        object-fit: cover;
        /* Asegura que la imagen se ajuste correctamente */
    }

    #imagen_doc:hover {
        transform: scale(1.05);
        /* Aumenta el tamaño de la imagen al pasar el ratón sobre ella */
        opacity: 1.1;
        /* Opcional: Añade un efecto de opacidad para mejorar la visibilidad */
    }

    #card_doc {
        width: 220px;
    }

    .card-body {
        text-align: center;
        /* Centra el texto en la tarjeta */
    }
</style>



<div class="col py-3">
    <h2>Documentación y Manuales</h2>

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
                                <div class="col-md-3">
                                    <div class="card mb-4" id="card_doc">
                                        <img id="imagen_doc" src="<?php echo base_url('assets/imagenes/documentos/') . '/'; ?>${documento.imagen_portada}" class="card-img-top" alt="Imagen de portada">
                                        <div class="card-body">
                                            <h5 class="card-title">${documento.titulo}</h5>
                                            <p class="card-text">
                                                 ${documento.feccre}
                                            </p>
                                            <a href="<?php echo base_url('assets/documentos/') . '/'; ?>${documento.documento}" class="btn btn-primary" target="_blank">
                                                <i class="bi bi-box-arrow-in-up-right"> </i> Leer
                                            </a>
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

        // Manejo de los enlaces de paginación
        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            var page = $(this).text(); // Obtener el número de página del enlace
            cargar_docs(page); // Cargar los documentos para la nueva página
        });
    });
</script>


<?php $this->load->view('templates/footer'); ?>