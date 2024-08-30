<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_solicitud extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_permisos');
        $this->load->model('mantenimiento/M_solicitud', 'mSolicitud');
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
        if (!$this->M_permisos->tiene_permiso($rol_id, 'mantenimiento')) {
            redirect('no_autorizado');
        }
    }

    public function index()
    {

        $this->load->view('mantenimiento/solicitud'); // Cargar la vista correspondiente
    }

    public function solicitudes_obtener()
    {
        $pagina = $this->input->post('pagina');
        $cantidad = $this->input->post('cantidad');
        $offset = ($pagina - 1) * $cantidad;

        $data['solicitudes'] = $this->mSolicitud->obtenerSolicitudes($offset, $cantidad);
        $data['totalregistros'] = $this->mSolicitud->contarSolicitudes();

        echo json_encode($data);
    }


    public function crear_solicitud() {
        if ($this->input->is_ajax_request()) {
            // Obtener los datos del formulario
            $numero_serie_eq = $this->input->post('numero_serie_eq');
            $descripcion = $this->input->post('descripcion');
            $tipo_mantenimiento = $this->input->post('tipo_mantenimiento');
            $prioridad = $this->input->post('prioridad');
            $ubicacion_solicitud = $this->input->post('unidad');
    

            $equipo_id = $this->mSolicitud->obtenerEquipoIdPorNumeroSerie($numero_serie_eq);
    
            if ($equipo_id) {
                // Preparar los datos para la inserción
                $data = array(
                    'numero_serie_eq' => $numero_serie_eq,
                    'equipo_id' => $equipo_id,
                    'descripcion' => $descripcion,
                    'tipo_mantenimiento' => $tipo_mantenimiento,
                    'prioridad' => $prioridad,
                    'ubicacion_solicitud' => $ubicacion_solicitud,
                    'fecha_solicitud' => date('Y-m-d H:i:s'),
                    'estado' => 1,
                    'usucre' => $this->session->userdata('email'),
                    'feccre' => date('Y-m-d H:i:s'),
                    'fecmod' => date('Y-m-d H:i:s')
                
                );
    

                $insertado = $this->mSolicitud->insertarSolicitud($data);
    
                if ($insertado) {
                    echo json_encode(array('status' => 'success', 'message' => 'Solicitud agregada exitosamente.'));
                } else {
                    echo json_encode(array('status' => 'error', 'message' => 'Error al agregar la solicitud.'));
                }
            } else {
                // Número de serie no encontrado
                echo json_encode(array('status' => 'error', 'message' => 'El código de equipo ingresado no existe.'));
            }
        } else {
            redirect('solicitud');
        }
    }
}
