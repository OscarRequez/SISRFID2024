<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database(); // Cargar la base de datos
        $this->load->model('Usuario_model'); // Cargar el modelo

        // Verificar si el usuario está autenticado
        if (!$this->session->userdata('user_id')) {
            // Si no está autenticado, redirigir al login
            redirect('login');
        }
    }

    public function index()
    {
        $this->load->model('Usuario_model');
        $data['usuarios'] = $this->Usuario_model->get_usuarios_con_roles();
        $this->load->view('Usuarios_view', $data);
    }
}