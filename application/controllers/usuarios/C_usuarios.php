<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_usuarios extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_permisos');
        $this->load->model('usuarios/M_usuarios', 'mUsuario');
        $this->load->model('usuarios/M_roles', 'mRoles');
        
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
        if (!$this->M_permisos->tiene_permiso($rol_id, 'usuarios')) {
            redirect('no_autorizado');
        }
    }


    
    public function index() {
        $data['usuarios'] = $this->mUsuario->get_usuarios_con_roles(); // Obtener usuarios con sus roles
        // Carga la vista
        $this->load->view('usuarios/Usuarios', $data); // Cargar la vista correspondiente
    }

    public function mostrar() {
   
        $buscar = $this->input->post('buscar');
        $pagina = $this->input->post('nropagina');
        $cantidad = $this->input->post('cantidad');
    
        // Calcula el inicio para la paginación
        $inicio = ($pagina - 1) * $cantidad;
    
        // Obtén los usuarios paginados y el total de registros
        $data['usuarios'] = $this->mUsuario->get_users($cantidad, $inicio, $buscar);
        $data['totalregistros'] = $this->mUsuario->count_all_users($buscar);
        $data['cantidad'] = $cantidad;
    
        // Devuelve los datos en formato JSON
        echo json_encode($data);
    }
    

    public function obtener_roles_ajax() {
        
        $roles = $this->mRoles->obtener_roles(); // Método en tu modelo que recupera los roles
        echo json_encode($roles); // Envía los roles en formato JSON
    }

    // USUARIO NUEVO AGREGAR A LA BASE DE DATOS
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
    // USUARIO NUEVO AGREGAR A LA BASE DE DATOS

    public function actualizar() {
        // Obtén los datos del formulario
        $id = $this->input->post('id');
        $nombre = $this->input->post('nombre');
        $email = $this->input->post('email');
        $rol = $this->input->post('rol');
        $contraseña = $this->input->post('contraseña');

        // Debug para ver si se recibe la contraseña
        var_dump($contraseña);
        
        // Obtén el usuario que está realizando la actualización
        $usuario_modificador = $this->session->userdata('username'); // O el valor que uses para identificar al usuario
        
        // Construye el array de datos a actualizar
        $datos = array(
            'nombre' => $nombre,
            'email' => $email,
            'rol_id' => $rol,
            'fecmod' => date('Y-m-d H:i:s'), // Fecha y hora actual
            'usumod' => $usuario_modificador // Usuario que realiza la modificación
        );
        
        // Si se ha enviado una nueva contraseña, agrégala a los datos a actualizar
        if (!empty($contraseña)) {
            // Verifica que la contraseña tenga el formato adecuado si es necesario
            $datos['contraseña'] = password_hash($contraseña, PASSWORD_BCRYPT); // Asegúrate de cifrar la contraseña
        }
        
        // Llama al modelo para realizar la actualización
        $this->mUsuario->actualizar_usuario($id, $datos);
        
        // Redirige o muestra un mensaje según el resultado
        redirect('usuarios'); // Redirige a la lista de usuarios o la página que desees
    }

    public function cambiar_estado_usuario($id) {

        // Obtener el estado actual del usuario
        $usuario = $this->mUsuario->get_usuario($id);
    
        // Cambiar el estado
        $nuevo_estado = $usuario['estado'] == 1 ? 0 : 1;
        
        // Actualizar el estado en la base de datos
        $this->mUsuario->update_usuario_estado($id, $nuevo_estado);
        
        // Responder con éxito (puedes devolver JSON si es necesario)
        echo json_encode(['success' => true]);
    }


    //16/08/2024 eliminar el usuario de la tabla

    public function eliminar($id) {
        // Verifica que el ID sea numérico
        if (!is_numeric($id)) {
            show_error('ID inválido');
            return;
        }

        // Prepara los datos para actualizar
        $data = array(
            'estado' => 3, // El estado que deseas asignar
            'fecmod' => date('Y-m-d H:i:s'), // Fecha de modificación
            'usumod' => $this->session->userdata('email') // ID del usuario que hace la modificación
        );

        // Llama al modelo para actualizar el estado
        $result = $this->mUsuario->actualizar_usuario_eliminado($id, $data);

        if ($result) {
            // Redirige de vuelta a la lista de usuarios
            $this->session->set_flashdata('success', 'Usuario actualizado exitosamente');
            redirect('usuarios'); // Cambia 'listar' por el nombre de tu método para listar usuarios
        } else {
            // Redirige con un mensaje de error
            $this->session->set_flashdata('error', 'Error al actualizar el usuario');
            redirect('usuarios'); // Cambia 'listar' por el nombre de tu método para listar usuarios
        }
    }

}




