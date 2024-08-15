<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_roles extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_permisos');
        $this->load->database(); // Cargar la base de datos
        $this->load->model('usuarios/M_roles', 'mRoles'); // Cargar el modelo

        // Verificar si el usuario está autenticado
        if (!$this->session->userdata('user_id')) {
            // Si no está autenticado, redirigir al login
            redirect('login');
        }

        // Verificar si el usuario está activo en la base de datos
        $user_id = $this->session->userdata('user_id');
        $usuario = $this->db->get_where('usuarios', ['id' => $user_id, 'estado' => 1])->row();

        if (!$usuario) {
            // Si el usuario no existe o no está activo, destruir la sesión y redirigir al login
            $this->session->sess_destroy();
            redirect('login');
        }

        // Verificar permisos
        $rol_id = $this->session->userdata('role');
        if (!$this->M_permisos->tiene_permiso($rol_id, 'usuarios')) {
            redirect('no_autorizado'); // Redirigir a una página de no autorizado
        }
    }

    public function index()
    {
        $data['roles'] = $this->mRoles->get_roles(); // Obtener roles
        $this->load->view('usuarios/roles', $data); // Cargar la vista correspondiente
    }

    public function mostrarRoles()
    {

        // Configuración de la paginación
        $pagina = $this->input->post('pagina') ?: 1;
        $cantidad = $this->input->post('cantidad') ?: 5;
        $offset = ($pagina - 1) * $cantidad;

        $data['roles'] = $this->mRoles->getRoles($cantidad, $offset);
        $data['totalregistros'] = $this->mRoles->countRoles();

        echo json_encode($data);
    }

    public function agregarRol()
    {
        $permisos = $this->mRoles->obtenerPermisosUnicos();
        echo json_encode(['modulos' => $permisos]);
    }
}
