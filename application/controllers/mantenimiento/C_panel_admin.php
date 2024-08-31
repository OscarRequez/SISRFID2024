<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_panel_Admin extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_permisos');
        $this->load->model('mantenimiento/M_panel_Admin', 'mPanelAdmin');
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

        $this->load->view('mantenimiento/panel_admin'); // Cargar la vista correspondiente
    }

    public function solicitudes_obtener_Admin()
    {
        $pagina = $this->input->post('pagina');
        $cantidad = $this->input->post('cantidad');
        $offset = ($pagina - 1) * $cantidad;

        $data['solicitudes'] = $this->mPanelAdmin->obtenerSolicitudesAdmin($offset, $cantidad);
        $data['totalregistros'] = $this->mPanelAdmin->contarSolicitudesAdmin();

        echo json_encode($data);
    }

    public function obtener_tecnicos()
    {

        // Obtener los técnicos con rol_id = 3
        $tecnicos = $this->mPanelAdmin->get_tecnicos_por_rol(3); //3 es usaurios que son tecnicos en roles 

        // Devolver la respuesta en formato JSON
        echo json_encode(array('tecnicos' => $tecnicos));
    }

    public function asignar_tecnico() {
        $solicitud_id = $this->input->post('solicitud_id');
        $tecnico_id = $this->input->post('tecnico_id');
    
        // Datos de auditoría
        $usumod = $this->session->userdata('email');
        $fecmod = date('Y-m-d H:i:s');
        $fecAsignacion = date('Y-m-d H:i:s');
    
        // Asignar técnico a la solicitud
        $resultado = $this->mPanelAdmin->asignar_tecnico($solicitud_id, $tecnico_id, $usumod, $fecAsignacion,  $fecmod);
    
        if ($resultado) {
            // Obtener equipo_id asociado a la solicitud
            $equipo_id = $this->mPanelAdmin->equipo_id_para_solicitud($solicitud_id);
    
            // Actualizar estado del equipo
            if ($equipo_id) {
                $this->mPanelAdmin->update_equipo_estado($equipo_id, $usumod, $fecmod, 3);
            }
    
            echo json_encode(array('status' => 'success', 'message' => 'Técnico asignado exitosamente.'));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Error al asignar técnico.'));
        }
    }
}
