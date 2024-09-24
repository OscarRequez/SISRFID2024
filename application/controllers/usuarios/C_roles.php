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
        // Cargar la biblioteca form_validation
        $this->load->library('form_validation');

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

    public function agregarRolCheckbox() // AGREGA LOS ROLES DISPONIBLES A LOS MODULOS PARA QUE SE MUESTREN LOS CHECKBOX
    {
        $permisos = $this->mRoles->obtenerPermisosUnicos();
        echo json_encode(['modulos' => $permisos]);
    }

    //agregar rol a la BD
    public function agregar()
    {
        // Validar el formulario
        $this->form_validation->set_rules('nombreRol', 'Nombre del Rol', 'required');

        if ($this->form_validation->run() === FALSE) {
            // Redirige o muestra errores
            $this->session->set_flashdata('error', validation_errors());
            redirect('roles');
        } else {
            // Obtener datos del formulario
            $nombreRol = $this->input->post('nombreRol');
            $usucre = $this->session->userdata('email'); // Usuario creador
            $fechaActual = date('Y-m-d H:i:s'); // Fecha actual (formato completo)

            $dataRol = [
                'nombre' => $nombreRol,  // Asegúrate de que esto sea una cadena
                'estado' => 1,          // Valor numérico para el estado activo
                'usucre' => $usucre,
                'feccre' => $fechaActual
            ];

            // Insertar el rol
            $this->mRoles->insertarRol($dataRol);
            $rol_id = $this->db->insert_id(); // Obtener el ID del nuevo rol

            // Obtener módulos y submódulos
            $modulos = $this->input->post('modulos');
            $submodulos = $this->input->post('submodulos');

            // Insertar permisos
            if ($modulos) {
                foreach ($modulos as $modulo) {
                    // Insertar el módulo si no tiene submódulos específicos
                    if (!isset($submodulos[$modulo]) || empty($submodulos[$modulo])) {
                        $dataPermiso = [
                            'rol_id' => $rol_id,
                            'modulo' => $modulo,
                            'submodulo' => NULL,   // Submódulo es NULL si no hay submódulos
                            'usucre' => $usucre,
                            'feccre' => $fechaActual
                        ];
                        $this->mRoles->insertarPermiso($dataPermiso);
                    } else {
                        // Insertar sub-módulos
                        foreach ($submodulos[$modulo] as $submodulo) {
                            $dataPermiso = [
                                'rol_id' => $rol_id,
                                'modulo' => $modulo,
                                'submodulo' => $submodulo,
                                'usucre' => $usucre,
                                'feccre' => $fechaActual
                            ];
                            $this->mRoles->insertarPermiso($dataPermiso);
                        }
                    }
                }
            }

            // Redirigir o mostrar mensaje de éxito
            $this->session->set_flashdata('success', 'Rol y permisos agregados exitosamente.');
            redirect('roles');
        }
    }

    // Función para obtener permisos de un rol
    public function obtenerPermisosDeUnRol()
    {
        $rol_id = $this->input->post('rol_id');
        $permisos = $this->mRoles->obtenerPermisosDeUnRol($rol_id);
        echo json_encode(['permisos' => $permisos]);
    }


    //obtenerlos para editaros
    public function obtenerDatosRol($rol_id)
    {
        // Obtener los datos del rol
        $this->db->where('id', $rol_id);
        $rol = $this->db->get('roles')->row_array();

        // Obtener permisos del rol
        $this->db->select('modulo, submodulo');
        $this->db->from('permisos');
        $this->db->where('rol_id', $rol_id);
        $permisos = $this->db->get()->result_array();

        // Obtener módulos y submódulos únicos
        $modulos_submodulos = $this->mRoles->obtenerPermisosUnicos();

        $data = [
            'rol' => $rol,
            'permisos' => $permisos,
            'modulos_submodulos' => $modulos_submodulos
        ];

        echo json_encode($data);
    }


    //PARA ACTUALIZAR LOS ROLES:
    public function actualizar_permisos()
    {
        $rol_id = $this->input->post('rol_id');
        $nombre = $this->input->post('nombre');
        $estado = $this->input->post('estado');
        $modulos = $this->input->post('modulos');
        $submodulos = $this->input->post('submodulos');

        // Actualizar el nombre y estado del rol
        $data_rol = array(
            'nombre' => $nombre,
            'estado' => $estado,
            'fecmod' => date('Y-m-d H:i:s'),
            'usumod' => $this->session->userdata('email') // Asumiendo que tienes el ID del usuario en la sesión
        );
        $this->mRoles->actualizarRol($rol_id, $data_rol);

        // Actualizar los permisos del rol
        $this->mRoles->actualizarPermisos($rol_id, $modulos, $submodulos);

        echo json_encode(['success' => true]);
    }
}
