<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_roles extends MY_Controller {

    public function __construct() {
        parent::__construct();
         //permisos
         $this->load->model('M_permisos');
         $rol_id = $this->session->userdata('role');
         if (!$this->M_permisos->tiene_permiso($rol_id, 'usuarios')) {
             redirect('no_autorizado'); // Redirigir a una página de no autorizado
         
             // Verificar si el usuario está autenticado
             if (!$this->session->userdata('user_id')) {
                 // Si no está autenticado, redirigir al login
                 redirect('login');
             }
         }
        $this->load->database(); // Cargar la base de datos
        $this->load->model('usuarios/M_roles', 'mRoles'); // Cargar el modelo

        // Verificar si el usuario está autenticado
        if (!$this->session->userdata('user_id')) {
            // Si no está autenticado, redirigir al login
            redirect('login');
        }
    }

    public function index()
    {
        $data['roles'] = $this->mRoles->get_roles(); // Obtener roles
        $this->load->view('usuarios/roles', $data); // Cargar la vista correspondiente
    }
}