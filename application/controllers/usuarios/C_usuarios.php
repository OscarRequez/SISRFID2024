<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_usuarios extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_permisos');
        $this->load->model('usuarios/M_usuarios', 'mUsuario');
        $this->load->model('usuarios/M_roles','mRoles');
        
        // Verificar si el usuario está autenticado
        if (!$this->session->userdata('user_id')) {
            redirect('login');
        }

        // Verifica permisos
        $rol_id = $this->session->userdata('role');
        if (!$this->M_permisos->tiene_permiso($rol_id, 'usuarios')) {
            redirect('no_autorizado');
        }

        $this->load->database(); // Cargar la base de datos
    }

    public function index()
    {
        $data['usuarios'] = $this->mUsuario->get_usuarios_con_roles(); // Obtener usuarios con sus roles
        $this->load->view('usuarios/Usuarios', $data); // Cargar la vista correspondiente
    }

    public function obtener_roles_ajax() {
        
        $roles = $this->mRoles->obtener_roles(); // Método en tu modelo que recupera los roles
        echo json_encode($roles); // Envía los roles en formato JSON
    }


    public function guardar() {
        if ($this->input->post()) {
            $nombre = $this->input->post('nombre');
            $email = $this->input->post('email');
            $password = $this->input->post('password');
            $rol_id = $this->input->post('rol'); // El ID del rol seleccionado

            // Hashear la contraseña
            // Hashear la contraseña con el costo especificado
            $options = ['cost' => 12];
            $hashed_password = password_hash($password, PASSWORD_BCRYPT, $options);
            
            $data = array(
                'nombre' => $nombre,
                'email' => $email,
                'contraseña' => $hashed_password,
                'rol_id' => $rol_id,
                'estado' => 1, // Estado activo por defecto
                'usucre' => $this->session->userdata('email'), // Usuario creador
                'feccre' => date('Y-m-d H:i:s'), // Fecha y hora actual
            );

            // Llamar al modelo para insertar el usuario en la base de datos
            $this->mUsuario->insertar_usuario($data);

            // Redirigir a la lista de usuarios o mostrar un mensaje de éxito
            redirect('usuarios'); // Cambia la redirección según tus necesidades
        }
    }

}




