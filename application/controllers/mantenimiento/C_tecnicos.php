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
        $this->mTecnicos->actualizar_mantenimiento($tarea_id, 4, $observaciones); // 4= estado, en proceso de confirmacion

        // Finalizar transacción
        $this->db->trans_complete();


        if ($this->db->trans_status() === FALSE) {
            // Si ocurre un error
            echo json_encode(array('status' => 'error', 'message' => 'Error al marcar la tarea como hecha.'));
        } else {
            // Éxito
            //INSERTAR LA NOTIFICACION
            $notificacion = array(
                'titulo' => 'Tareas por confirmar',
                'mensaje' => 'Existen trabajos de mantenimiento por confirmar',
                'tipo' => 'info',
                'rol' => 1, // 1 = admin
                'feccre' => date('Y-m-d H:i:s'),
            );

            // Insertar la notificación en la base de datos
            $notificacion_insertada = $this->mTecnicos->nueva_notificacion($notificacion);
            echo json_encode(array('status' => 'success', 'message' => 'Tarea marcada como hecha.'));
        }
    }

    // reporte tarea que se usa tambien para Panel admin
    public function generar_reporte_tareas()
    {
        // Obtener el ID de la tarea desde los parámetros de la solicitud
        $tareaId = $this->input->get('tareaId');
        $format = $this->input->get('format') ?: 'pdf'; // PDF por defecto

        // Llamar al modelo para obtener los datos de la tarea según su ID
        $data = $this->mTecnicos->obtenerDatosTareaPorId($tareaId);

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
            // Verificar si TCPDF está cargado
            if (!class_exists('TCPDF')) {
                show_error('La clase TCPDF no está disponible. Por favor, asegúrate de que la biblioteca esté instalada y cargada.');
                return;
            }

            // Crear una nueva instancia de TCPDF
            $pdf = new TCPDF();

            // Establecer el título del documento
            $pdf->SetTitle('Reporte de la Tarea');
             // Establecer los márgenes
             $pdf->SetMargins(10, 20, 10); // Ajusta los márgenes izquierdo, superior y derecho
             $pdf->SetHeaderMargin(10); // Ajusta el margen inferior del encabezado

            // Añadir una página
            $pdf->AddPage();
            $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 001', PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

            // Generar HTML del reporte con los datos y la información adicional
            $html = $this->load->view('mantenimiento/report_pdf', ['data' => $data, 'reporte_info' => $reporte_info], TRUE);
            $pdf->writeHTML($html);

            // Enviar el PDF al navegador
            $pdf->Output('report_tareas.pdf', 'I'); // 'I' para mostrar en el navegador

        } else if ($format == 'excel') {
            // Si es Excel, cargar la biblioteca correspondiente
            $this->load->library('excel');
            $this->excel->generate_excel('reporte_tareas.xlsx', $data);
        }
    }
}
