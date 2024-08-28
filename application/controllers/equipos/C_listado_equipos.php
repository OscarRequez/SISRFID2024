<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_listado_equipos extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_permisos');
        $this->load->model('equipos/M_listado_equipos', 'mLEquipos');
        $this->load->model('usuarios/M_roles', 'mRoles');
        // Cargar la biblioteca form_validation
        $this->load->library('form_validation');

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
        if (!$this->M_permisos->tiene_permiso($rol_id, 'equipos')) {
            redirect('no_autorizado');
        }
    }


    public function index()
    {
        $data['equipos_medicos'] = $this->mLEquipos->get_todos_equipos(); // Obtener todos los equipos médicos
        $this->load->view('equipos/listado_equipos', $data); // Cargar la vista correspondiente
    }

    public function mostrar_equipos()
    {
        $buscar = $this->input->post('buscar'); // Valor de búsqueda opcional
        $pagina = $this->input->post('nropagina'); // Número de página
        $cantidad = $this->input->post('cantidad'); // Cantidad de registros por página

        // Calcula el inicio para la paginación
        $inicio = ($pagina - 1) * $cantidad;

        // Obtén los equipos médicos paginados y el total de registros
        $data['equipos'] = $this->mLEquipos->get_equipos_paginados($cantidad, $inicio, $buscar);
        $data['totalregistros'] = $this->mLEquipos->count_all_equipos($buscar);
        $data['cantidad'] = $cantidad;

        // Devuelve los datos en formato JSON
        echo json_encode($data);
    }

}