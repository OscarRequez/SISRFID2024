<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_tecnicos extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_permisos');
        $this->load->model('mantenimiento/M_tecnicos', 'mTecnicos');
        $this->load->model('usuarios/M_roles', 'mRoles');
        $this->load->library('form_validation');

        // Verificar si el usuario está autenticado
        if (!$this->session->userdata('user_id')) {
            redirect('login');
        }

        // Verificar si el usuario está activo
        $user_id = $this->session->userdata('user_id');
        $this->load->database(); // Cargar la base de datos
        $usuario = $this->db->get_where('usuarios', ['id' => $user_id, 'estado' => 1])->row();

        if (!$usuario) {
            // Si el usuario no está activo, destruir la sesión y redirigir al login
            $this->session->sess_destroy();
            redirect('login');
        }

        // Verificar permisos
        $rol_id = $this->session->userdata('role');
        if (!$this->M_permisos->tiene_permiso($rol_id, 'mantenimiento')) {
            redirect('no_autorizado');
        }
    }

    public function index()
    {
        $this->load->view('mantenimiento/tecnicos'); // Cargar la vista correspondiente
    }

    public function obtener_tareas()
    {
        $pagina = $this->input->post('pagina');
        $cantidad = $this->input->post('cantidad');
        $usuario = $this->session->userdata('user_id');
        $offset = ($pagina - 1) * $cantidad;

        // Obtener datos de tareas y total de registros
        $data['tareas'] = $this->mTecnicos->obtener_tareas($cantidad, $offset, $usuario);
        $data['totalregistros'] = $this->mTecnicos->contar_tareas($usuario);

        // Enviar respuesta en JSON
        echo json_encode($data);
    }

    public function marcar_tarea_hecha()
    {
        // Obtener datos del formulario
        $tarea_id = $this->input->post('tarea_id');
        $observaciones = $this->input->post('observaciones');
        $materiales = $this->input->post('material');
        $cantidades = $this->input->post('cantidad');
        $costos_unitarios = $this->input->post('costo_unitario');
        $costos_totales = $this->input->post('costo_total');
        $facturas = $this->input->post('factura');

        // Comenzar transacción
        $this->db->trans_start();

        // Insertar los materiales
        foreach ($materiales as $index => $material) {
            if (!empty($material)) {
                $data = array(
                    'tarea_id' => $tarea_id,
                    'material' => $material,
                    'cantidad' => $cantidades[$index],
                    'costo_unitario' => $costos_unitarios[$index],
                    'costo_total' => $costos_totales[$index],
                    'factura' => $facturas[$index],
                    'estado' => 1,
                    'usucre' => $this->session->userdata('email')

                );
                $this->mTecnicos->insertar_material($data);
            }
        }

        // Actualizar el estado de la tarea
        $this->mTecnicos->actualizar_mantenimiento($tarea_id, 4, $observaciones);// 4= estado, en proceso de confirmacion

        // Finalizar transacción
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            // Si ocurre un error
            echo json_encode(array('status' => 'error', 'message' => 'Error al marcar la tarea como hecha.'));
        } else {
            // Éxito
            echo json_encode(array('status' => 'success', 'message' => 'Tarea marcada como hecha.'));
        }
    }
}
