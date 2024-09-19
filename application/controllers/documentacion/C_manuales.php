<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_manuales extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_permisos');
        $this->load->model('documentacion/M_manuales', 'mManuales');
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
        $config['base_url'] = site_url('c_manuales/index');
        $config['total_rows'] = $this->mManuales->contar_documentos();
        $config['per_page'] = 5; // Número de documentos por página
        $config['uri_segment'] = 3; // Segmento de la URI para la paginación
        $config['attributes'] = ['class' => 'page-link']; // Añadir clases a los enlaces de paginación

        $this->pagination->initialize($config);

        $data['links'] = $this->pagination->create_links();
        $data['documentos'] = $this->mManuales->obtener_documentos($config['per_page'], $this->uri->segment(3));

        $this->load->view('documentacion/manuales', $data);
    }


}