<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_reporte_equipos extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_permisos');
        $this->load->model('equipos/M_reporte_equipos', 'mREquipos');
        $this->load->library('pdf_equipos');
        // Verificar si el usuario está autenticado
        if (!$this->session->userdata('user_id')) {
            redirect('login');
        }
        // Verificar si el usuario está activo en la base de datos
        $user_id = $this->session->userdata('user_id');
        $this->load->database();
        $usuario = $this->db->get_where('usuarios', ['id' => $user_id, 'estado' => 1])->row();
        if (!$usuario) {
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
        $data['ubicaciones'] = $this->mREquipos->obtener_ubicaciones(); // Obtener ubicaciones
        $this->load->view('equipos/reporte_equipos', $data);
    }

    public function generar_reporte() // Reporte PDF o Excel
    {
        // Obtener filtros y formato desde la solicitud
        $estado = $this->input->get('estado');
        $ubicacion = $this->input->get('ubicacion');
        $search = $this->input->get('search');
        $numero_serie = $this->input->get('numero_serie');
        $format = $this->input->get('format') ?: 'pdf'; // PDF por defecto

        // Llamar al modelo para obtener los datos
        $data = $this->mREquipos->obtenerDatosSinPaginacion($estado, $ubicacion, $search, $numero_serie);

        // Datos adicionales para el reporte
        $reporte_info = [
            'fecha' => date('d/m/Y'),
            'empresa' => 'Hospital de Clínicas',
            'direccion' => 'Av. Saavedra, La Paz, Bolivia',
            'contacto' => '+519 73716289 - mamanioscar509@gmail.com',
            'elaborado_por' => $this->session->userdata('username'),
            'puesto' => $this->session->userdata('rol_name')
        ];

        if ($format == 'pdf') {
            
            // Crear una nueva instancia de TCPDF
            $pdf = new TCPDF();

            // Establecer el título del documento
            $pdf->SetTitle('Reporte de los Equipos');
            

            // Añadir una página
            $pdf->AddPage();
            $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

            // Generar HTML del reporte con datos adicionales
            $html = $this->load->view('equipos/report_pdf', ['data' => $data, 'reporte_info' => $reporte_info], TRUE);
            $pdf->writeHTML($html);

            // Enviar el PDF al navegador para que se abra en una nueva pestaña
            $pdf->Output('report_equipos.pdf', 'I'); // 'I' para mostrar en el navegador

        } else if ($format == 'excel') {
            $this->load->library('excel');
            $this->excel->generate_excel('reporte_equipos.xlsx', $data);
        }
    }
    public function cargar_datos() //para la tabla de la vista donde se aplicacnlos filtros
    {
        $pagina = $this->input->post('pagina');
        $filtros = $this->input->post('filtros');

        // Llama al modelo para obtener los datos con filtros
        $data = $this->mREquipos->get_equipos($filtros, $pagina);

        // Devuelve los datos como JSON
        echo json_encode($data);
    }
}
