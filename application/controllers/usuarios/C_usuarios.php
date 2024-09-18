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



    public function index()
    {
        $data['usuarios'] = $this->mUsuario->get_usuarios_con_roles(); // Obtener usuarios con sus roles
        // Carga la vista
        $this->load->view('usuarios/Usuarios', $data); // Cargar la vista correspondiente
    }

    public function usuarios_mostrar()
    {

        $buscar = $this->input->post('buscar');
        $pagina = $this->input->post('nropagina');
        $cantidad = $this->input->post('cantidad');

        // Calcula el inicio para la paginación
        $inicio = ($pagina - 1) * $cantidad;

        // Obtén los usuarios paginados y el total de registros
        $data['usuarios'] = $this->mUsuario->buscar_usuarios($cantidad, $inicio, $buscar);
        $data['totalregistros'] = $this->mUsuario->count_all_users($buscar);
        $data['cantidad'] = $cantidad;

        // Devuelve los datos en formato JSON
        echo json_encode($data);
    }


    public function obtener_roles_ajax()
    {

        $roles = $this->mRoles->obtener_roles(); // Método en tu modelo que recupera los roles
        echo json_encode($roles); // Envía los roles en formato JSON
    }

    // USUARIO NUEVO AGREGAR A LA BASE DE DATOS
    public function guardar()
    {
        if ($this->input->post()) {
            $nombre = $this->input->post('nombre');
            $cedula = $this->input->post('cedula');
            $email = $this->input->post('email');
            $password = $this->input->post('password');
            $rol_id = $this->input->post('rol'); // El ID del rol seleccionado

            // Verificar si la cédula ya existe en la base de datos
            $this->load->model('mUsuario'); // Asegúrate de cargar tu modelo

            $usuario_existente = $this->mUsuario->obtener_usuario_por_cedula($cedula);

            if ($usuario_existente) {
                // La cédula ya está en uso, enviar un mensaje de error a la vista
                $this->session->set_flashdata('error', 'La cédula de identidad ' . $cedula . ' ya está en uso para otro usuario.');
                redirect('usuarios'); // Cambia esto según tu vista actual
            } else {
                // Hashear la contraseña con el costo especificado
                $options = ['cost' => 12];
                $hashed_password = password_hash($password, PASSWORD_BCRYPT, $options);

                $data = array(
                    'nombre' => $nombre,
                    'email' => $email,
                    'cedula' => $cedula,
                    'contraseña' => $hashed_password,
                    'rol_id' => $rol_id,
                    'estado' => 1, // Estado activo por defecto
                    'usucre' => $this->session->userdata('email'), // Usuario creador
                    'feccre' => date('Y-m-d H:i:s'), // Fecha y hora actual
                );

                // Llamar al modelo para insertar el usuario en la base de datos
                $this->mUsuario->insertar_usuario($data);

                // Redirigir a la lista de usuarios o mostrar un mensaje de éxito
                $this->session->set_flashdata('success', 'Usuario agregado exitosamente.');
                redirect('usuarios'); // Cambia la redirección según tus necesidades
            }
        }
    }

    // USUARIO NUEVO AGREGAR A LA BASE DE DATOS
    public function actualizar()
    {
        $id = $this->input->post('id');
        $nombre = $this->input->post('nombre');
        $cedula = $this->input->post('cedula');
        $email = $this->input->post('email');
        $rol = $this->input->post('rol');
        $contraseña = $this->input->post('contraseña');

        // Verifica si la cédula ya existe para otro usuario
        $usuario_existente = $this->mUsuario->obtener_usuario_por_cedula($cedula);

        if ($usuario_existente && $usuario_existente->id != $id) {
            // Si existe otro usuario con la misma cédula y no es el actual, responde con un error
            echo json_encode(['status' => 'error', 'message' => 'La cédula de identidad ' . $cedula . ' ya está en uso para otro usuario.']);
            return;
        }

        // Construye el array de datos a actualizar
        $datos = array(
            'nombre' => $nombre,
            'email' => $email,
            'cedula' => $cedula,
            'rol_id' => $rol,
            'fecmod' => date('Y-m-d H:i:s'),
            'usumod' => $this->session->userdata('username')
        );

        if (!empty($contraseña)) {
            $datos['contraseña'] = password_hash($contraseña, PASSWORD_BCRYPT);
        }

        $this->mUsuario->actualizar_usuario($id, $datos);
        echo json_encode(['status' => 'success', 'message' => 'Usuario actualizado correctamente.']);
    }


    public function cambiar_estado_usuario($id)
    {

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

    public function eliminar($id)
    {
        // Verifica que el ID sea numérico
        if (!is_numeric($id)) {
            $response = array('status' => 'error', 'message' => 'ID inválido');
            echo json_encode($response);
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
            $response = array('status' => 'success', 'message' => 'Usuario eliminado exitosamente');
        } else {
            $response = array('status' => 'error', 'message' => 'Error al eliminado el usuario');
        }

        echo json_encode($response);
    }
}
