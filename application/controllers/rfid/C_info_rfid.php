<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_info_rfid extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_permisos');
        $this->load->model('rfid/M_info_rfid', 'mInfRfid');
        $this->load->model('usuarios/M_roles', 'mRoles');
        $this->load->library('form_validation');
        $this->load->library('pdf_equipos'); // para poder usar pdfs

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
        if (!$this->M_permisos->tiene_permiso($rol_id, 'rfid')) {
            redirect('no_autorizado');
        }
    }

    public function index()
    {
        $this->load->view('rfid/info_rfid'); // Cargar la vista correspondiente
    }

    public function obtener_datos_equipo_rfid()
    {
        $codigo = $this->input->post('codigo');

        if (empty($codigo)) {
            echo json_encode(['success' => false, 'message' => 'El código RFID no puede estar vacío.']);
            return;
        }

        // Obtener datos del equipo
        $equipo = $this->mInfRfid->obtenerEquipoPorCodigo($codigo);

        if ($equipo) {
            // Obtener mantenimientos relacionados al equipo
            $mantenimientos = $this->mInfRfid->obtenerMantenimientosPorEquipo($equipo->id);

            // Asumimos que en tu base de datos hay un campo 'imagen' que contiene solo el nombre del archivo
            $equipo->ruta_imagen = base_url('assets/imagenes/equipos/' . $equipo->Imagen); // Construir la ruta completa

            // Preparar los datos para la respuesta
            $response = [
                'success' => true,
                'data' => [
                    'equipo' => $equipo,
                    'mantenimientos' => $mantenimientos
                ]
            ];

            echo json_encode($response);
        } else {
            echo json_encode(['success' => false, 'message' => 'Código no enlazado a ningún equipo.']);
        }
    }


    public function reporte_rfid_equipo()
    {
        $tareaId = $this->input->get('tareaId');

        // Obtener datos del equipo
        $datosEquipo = $this->mInfRfid->obtenerDatosEquipo($tareaId);
        $mantenimientos = $this->mInfRfid->obtenerMantenimientos($tareaId);
        $movimientos = $this->mInfRfid->obtenerMovimientos($tareaId);

        // Datos adicionales para el reporte
        $reporte_info = [
            'fecha' => date('d/m/Y'),
            'empresa' => 'Hospital de Clínicas',
            'direccion' => 'Av. Saavedra, La Paz, Bolivia',
            'contacto' => '+519 73716289 - mamanioscar509@gmail.com',
            'elaborado_por' => $this->session->userdata('username'),
            'puesto' => $this->session->userdata('rol_name')
        ];

        // Crear el PDF
        $pdf = new TCPDF();
        $pdf->SetTitle('Reporte del equipo');
        $pdf->SetMargins(10, 20, 10);
        $pdf->AddPage();

        // Generar HTML del reporte
        $html = $this->load->view('rfid/report_rfid_eq_pdf', [
            'datosEquipo' => $datosEquipo,
            'mantenimientos' => $mantenimientos,
            'movimientos' => $movimientos,
            'reporte_info' => $reporte_info
        ], TRUE);

        $pdf->writeHTML($html);
        $pdf->Output('report_tareas.pdf', 'I');
    }
}
