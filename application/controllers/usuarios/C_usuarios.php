<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_usuarios extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database(); // Cargar la base de datos
        $this->load->model('usuarios/M_usuarios', 'mUsuario'); // Cargar el modelo

        // Verificar si el usuario está autenticado
        if (!$this->session->userdata('user_id')) {
            // Si no está autenticado, redirigir al login
            redirect('login');
        }
    }

    public function index()
    {
        $data['usuarios'] = $this->mUsuario->get_usuarios_con_roles(); // Obtener usuarios con sus roles
        $this->load->view('usuarios/usuarios', $data); // Cargar la vista correspondiente
    }
}