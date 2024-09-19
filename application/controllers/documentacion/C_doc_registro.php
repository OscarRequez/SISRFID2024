<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_doc_registro extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_permisos');
        $this->load->model('documentacion/M_doc_registro', 'mDocRegistro');
        $this->load->model('usuarios/M_roles', 'mRoles');
        $this->load->library('pdf_equipos'); // para poder usar pdfs
        // Cargar la librería de carga de archivos
        $this->load->library('upload');
        $this->load->library('pagination'); // Cargar la librería de paginación



        // Verificar si el usuario está autenticado
        if (!$this->session->userdata('user_id')) {
            redirect('login');
        }

        // Verificar si el usuario está activo en la base de datos
        $user_id = $this->session->userdata('user_id');
        $this->load->database(); // Cargar la base de datos
        $usuario = $this->db->get_where('usuarios', ['id' => $user_id, 'estado' => 1])->row();

        if (!$usuario) {
            // Si el usuario no existe o no está activo, destruir la sesión y redirigir al login
            $this->session->sess_destroy();
            redirect('login');
        }

        // Verifica permisos
        $rol_id = $this->session->userdata('role');
        if (!$this->M_permisos->tiene_permiso($rol_id, 'documentacion')) {
            redirect('no_autorizado');
        }
    }

    public function index()
    {
        $config['base_url'] = site_url('c_doc_registro/index');
        $config['total_rows'] = $this->mDocRegistro->contar_documentos();
        $config['per_page'] = 5; // Número de documentos por página
        $config['uri_segment'] = 3; // Segmento de la URI para la paginación
        $config['attributes'] = ['class' => 'page-link']; // Añadir clases a los enlaces de paginación

        $this->pagination->initialize($config);

        $data['links'] = $this->pagination->create_links();
        $data['documentos'] = $this->mDocRegistro->obtener_documentos($config['per_page'], $this->uri->segment(3));

        $this->load->view('documentacion/doc_registro', $data);
    }

    public function obtener_documentos()
    {
        $pagina = $this->input->get('pagina') ?? 1; // Página actual, por defecto la primera
        $cantidad = $this->input->get('cantidad') ?? 5; // Cantidad de documentos por página

        $offset = ($pagina - 1) * $cantidad;

        // Obtener los documentos con paginación
        $totalDocumentos = $this->mDocRegistro->contar_documentos(); // Método para contar el total de documentos
        $documentos = $this->mDocRegistro->obtener_documentos($cantidad, $offset);

        $respuesta = [
            'documentos' => $documentos,
            'totalDocumentos' => $totalDocumentos
        ];

        echo json_encode($respuesta);
    }

    public function agregar_documento()
    {
        // Verificar tamaño de la imagen
        if ($_FILES['imagen']['size'] > 2048 * 1024) { // Tamaño máximo: 2MB
            echo json_encode(array('status' => 'error', 'message' => 'La imagen de portada debe pesar como máximo 2MB.'));
            return; // Para detener la ejecución si el archivo es demasiado grande
        }
    
        // Verificar tamaño del documento
        if ($_FILES['documento']['size'] > 5120 * 1024) { // Tamaño máximo: 5MB
            echo json_encode(array('status' => 'error', 'message' => 'El documento debe pesar como máximo 5MB.'));
            return; // Para detener la ejecución si el archivo es demasiado grande
        }
    
        // Configuración para la subida de la imagen de portada
        $configImagen['upload_path'] = './assets/imagenes/documentos/';
        $configImagen['allowed_types'] = 'jpg|jpeg|png|gif';
        $configImagen['max_size'] = 2048;  // Tamaño máximo: 2MB
        $configImagen['file_name'] = time() . '_' . $_FILES['imagen']['name']; // Añadir timestamp al nombre del archivo
        $this->upload->initialize($configImagen);
    
        if (!$this->upload->do_upload('imagen')) {
            // Devolver error si la imagen no se subió
            echo json_encode(array('status' => 'error', 'message' => 'Error al subir la imagen: ' . $this->upload->display_errors()));
            return; // Para detener la ejecución si la subida falla
        } else {
            $imagenData = $this->upload->data();
        }
    
        // Configuración para la subida del documento (PDF, Word, etc.)
        $configDocumento['upload_path'] = './assets/documentos/';
        $configDocumento['allowed_types'] = 'pdf|doc|docx|txt';
        $configDocumento['max_size'] = 5120;  // Tamaño máximo: 5MB
        $configDocumento['file_name'] = time() . '_' . $_FILES['documento']['name']; // Añadir timestamp al nombre del archivo
        $this->upload->initialize($configDocumento);
    
        if (!$this->upload->do_upload('documento')) {
            // Devolver error si el documento no se subió
            echo json_encode(array('status' => 'error', 'message' => 'Error al subir el documento: ' . $this->upload->display_errors()));
            return; // Para detener la ejecución si la subida falla
        } else {
            $documentoData = $this->upload->data();
        }
    
        // Guardar los datos en la base de datos
        $data = array(
            'titulo' => $this->input->post('titulo'),
            'documento' => $documentoData['file_name'],  // Nombre del archivo del documento
            'imagen_portada' => $imagenData['file_name'],  // Nombre del archivo de la imagen de portada
            'usucre' => $this->session->userdata('email'),  // Asumiendo que estás guardando el correo del usuario actual
            'feccre' => date('Y-m-d H:i:s'),
            'estado' => 1  // Estado activo por defecto
        );
    
        // Insertar en la base de datos
        $insert_id = $this->mDocRegistro->insertar_documento($data);
    
        if ($insert_id) {
            // Enviar respuesta de éxito
            echo json_encode(array('status' => 'success', 'message' => 'Documento agregado exitosamente.'));
        } else {
            // Enviar respuesta de fallo
            echo json_encode(array('status' => 'error', 'message' => 'Error al guardar los datos en la base de datos.'));
        }
    }
    



    public function obtener_documento($id)
    {
        $documento = $this->mDocRegistro->obtener_documento_por_id($id);
        echo json_encode($documento);
    }
    /*
    public function editar_documento($id) {
        // Cargar el modelo

        // Obtener el documento existente
        $documento = $this->mDocRegistro->obtener_documento_por_id($id);

        if (!$documento) {
            // Manejar el caso donde no se encuentra el documento
            echo json_encode(['status' => 'error', 'message' => 'Documento no encontrado.']);
            return;
        }

        // Procesar la solicitud de edición
        $titulo = $this->input->post('titulo');
        $archivoNuevo = $_FILES['documento']['name'];
        $imagenNueva = $_FILES['imagen']['name'];

        // Configuración de la carga de archivos
        $config['upload_path'] = './assets/documentos/';
        $config['allowed_types'] = 'pdf|doc|docx|txt';
        $this->load->library('upload', $config);

        // Verificar si se ha cambiado el documento
        if (!empty($archivoNuevo)) {
            // Borrar el archivo anterior
            unlink('./assets/documentos/' . $documento['documento']);
            if (!$this->upload->do_upload('documento')) {
                echo json_encode(['status' => 'error', 'message' => $this->upload->display_errors()]);
                return;
            }
            $documentoNuevo = $this->upload->data('file_name');
        } else {
            $documentoNuevo = $documento['documento']; // Mantener el documento existente
        }

        // Verificar si se ha cambiado la imagen de portada
        if (!empty($imagenNueva)) {
            // Borrar la imagen anterior
            unlink('./assets/imagenes/documentos/' . $documento['imagen_portada']);
            $config['upload_path'] = './assets/imagenes/documentos/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $this->upload->initialize($config);

            if (!$this->upload->do_upload('imagen')) {
                echo json_encode(['status' => 'error', 'message' => $this->upload->display_errors()]);
                return;
            }
            $imagenNueva = $this->upload->data('file_name');
        } else {
            $imagenNueva = $documento['imagen_portada']; // Mantener la imagen existente
        }

        // Actualizar los datos del documento
        $data = [
            'titulo' => $titulo,
            'documento' => $documentoNuevo,
            'imagen_portada' => $imagenNueva,
            'fecmod' => date('Y-m-d H:i:s') // Actualizar la fecha de modificación
        ];

        // Guardar los cambios en la base de datos
        $this->mDocRegistro->actualizar_documento($id, $data);

        echo json_encode(['status' => 'success', 'message' => 'Documento editado con éxito.']);
    }
*/

    public function eliminar_documento()
    {
        $id = $this->input->post('id');
        $imagen = $this->input->post('imagen');
        $documento = $this->input->post('documento');

        // Eliminar archivo de imagen y documento
        unlink('./assets/imagenes/documentos/' . $imagen);
        unlink('./assets/documentos/' . $documento);

        // Eliminar el registro de la base de datos
        $this->mDocRegistro->eliminar_documento($id);

        echo json_encode(['status' => 'success', 'message' => 'Documento eliminado correctamente.']);
    }
}
