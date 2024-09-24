<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_lectura extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_permisos');
        $this->load->model('rfid/M_lectura', 'mLectura');
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
        $this->load->view('rfid/lectura'); // Cargar la vista correspondiente
    }

    public function tareas_lectura_obtener()
    {
        $pagina = $this->input->post('pagina');
        $cantidad = $this->input->post('cantidad');
        $buscar = $this->input->post('filtro'); // Obtén el término de búsqueda si está presente
        $usuario = $this->session->userdata('user_id');

        $offset = ($pagina - 1) * $cantidad;

        // Llama al modelo con el término de búsqueda opcional
        $data['tareas'] = $this->mLectura->obtenerTareasLectura($offset, $cantidad, $usuario, $buscar);
        $data['totalregistros'] = $this->mLectura->contarTareasLectura($usuario, $buscar);

        echo json_encode($data);
    }

    public function procesar_lectura_tags()
    {
        // Obtener los tags del textarea
        $tags_input = $this->input->post('rfid_tags');
        $tarea_id = $this->input->post('tarea_id');

        // Validar los tags en el modelo (eliminar duplicados y vacíos)
        $tags = $this->mLectura->validarTags($tags_input);

        if (empty($tags)) {
            echo json_encode(array('status' => 'error', 'message' => 'No se han detectado tags RFID.'));
            return;
        }

        // Verificar si los tags ya están registrados para la tarea
        $tags_registrados = $this->mLectura->verificarTagsRegistrados($tags, $tarea_id);
        $tags_registrados_ids = array_column($tags_registrados, 'rfid_tag');

        if (count($tags_registrados_ids) == count($tags)) {
            echo json_encode(array('status' => 'error', 'message' => 'Todos los tags ya están registrados.'));
            return;
        } elseif (!empty($tags_registrados_ids)) {
            // Filtrar los tags no registrados
            $tags = array_diff($tags, $tags_registrados_ids);
        }

        // Obtener la tarea de lectura por su ID
        $tarea = $this->mLectura->obtenerTareaPorId($tarea_id);
        if (!$tarea) {
            echo json_encode(array('status' => 'error', 'message' => 'La tarea de lectura no existe.'));
            return;
        }

        // Obtener los equipos médicos que coinciden con los tags leídos
        $equipos = $this->mLectura->obtenerEquiposPorTags($tags);
        if (empty($equipos)) {
            echo json_encode(array('status' => 'error', 'message' => 'No se encontraron equipos médicos con los tags proporcionados o los tags ya fueron registrados anteriormente en esta tarea.'));
            return;
        }

        // Inicializar contadores
        $total_leidos = $tarea->total_leidos;
        $total_otros = $tarea->total_otros;
        $resultados_lectura = [];
        $mensaje_tags_existentes = [];

        // Procesar los equipos
        foreach ($equipos as $equipo) {
            $leido = false;
            if ($equipo['ubicacion_id'] == $tarea->ubicacion_tarea) {
                $leido = true;
                $total_leidos++;
            } else {
                $total_otros++;
            }

            // Preparar los datos para insertar en la tabla resultados_lectura
            $resultados_lectura[] = [
                'tarea_id' => $tarea_id,
                'equipo_id' => $equipo['id'],
                'rfid_tag' => $equipo['tag_rfid_id'],
                'ubicacion_eq' => $equipo['ubicacion_id'],
                'leido' => $leido,
                'usucre' => $this->session->userdata('email'),
                'feccre' => date('Y-m-d H:i:s')
            ];
        }

        // Insertar los resultados de la lectura en la base de datos
        if (!empty($resultados_lectura)) {
            if (!$this->mLectura->insertarResultadosLectura($resultados_lectura)) {
                echo json_encode(array('status' => 'error', 'message' => 'Error al guardar los resultados de la lectura.'));
                return;
            }
        }

        // Actualizar los contadores en la tarea de lectura
        if (!$this->mLectura->actualizarTareaLectura($tarea_id, [
            'total_leidos' => $total_leidos,
            'total_otros' => $total_otros,
            'total_no_leidos' => $tarea->total_equipos - $total_leidos,
            'usumod' => $this->session->userdata('email'),
            'fecmod' => date('Y-m-d H:i:s'),
        ])) {
            echo json_encode(array('status' => 'error', 'message' => 'Error al actualizar la tarea de lectura.'));
            return;
        }

        // Responder con éxito
        echo json_encode(array('status' => 'success', 'message' => 'Lectura de tags procesada correctamente.'));
    }

    //confirmar tarea de lectura:

    public function confirmar_tarea_lectura()
    {
        // Recibir los datos por POST
        $idTarea = $this->input->post('id');
        $observacion = $this->input->post('observacion');

        // Verificar si se recibió el ID de la tarea
        if (empty($idTarea)) {
            echo json_encode(['success' => false, 'message' => 'ID de tarea no proporcionado.']);
            return;
        }

        // Preparar los datos para la actualización
        $data = [
            'estado' => 3, // Cambia el estado a "por confirmar"
            'observaciones' => $observacion,
            'usumod' => $this->session->userdata('email'), // Usuario que modifica
            'fecmod' => date('Y-m-d H:i:s'), // Fecha de modificación
            'fecha_fin' => date('Y-m-d H:i:s'), // Fecha de finalización
        ];

        // Llamar al modelo para actualizar la tarea
        $resultado = $this->mLectura->actualizar_tarea($idTarea, $data);

        // Verificar el resultado y responder con JSON
        if ($resultado) {

            //INSERTAR LA NOTIFICACION
            $notificacion = array(
                'titulo' => 'Tareas por confirmar',
                'mensaje' => 'Existen trabajos de lectura RFID por confirmar',
                'tipo' => 'info',
                'rol' => 4, // 4 = gestor de inventario
                'feccre' => date('Y-m-d H:i:s'),
            );

            // Insertar la notificación en la base de datos
            $notificacion_insertada = $this->mLectura->nueva_notificacion($notificacion);

            echo json_encode(['success' => true, 'message' => 'Tarea confirmada correctamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al confirmar la tarea.']);
        }
    }


    public function generar_reporte_tarea_lectura()
    {
        // Obtener el ID de la tarea desde los parámetros de la solicitud
        $tareaId = $this->input->get('tareaId');
        $format = $this->input->get('format') ?: 'pdf'; // PDF por defecto

        // Llamar al modelo para obtener los datos de la tarea según su ID
        $data = $this->mLectura->obtenerDatosTareaPorId($tareaId);

        // Validar si se obtuvieron los datos de la tarea
        if (!$data) {
            show_error('No se encontraron datos para la tarea solicitada.');
            return;
        }

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
            $pdf->SetTitle('Reporte de la Tarea de Lectura RFID');

            // Establecer los márgenes
            $pdf->SetMargins(10, 20, 10);
            $pdf->SetHeaderMargin(10);

            // Añadir una página
            $pdf->AddPage();
            $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'Reporte Tarea de Lectura', PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

            // Generar HTML del reporte con los datos y la información adicional
            $html = $this->load->view('rfid/reporte_tarea_lectura_pdf', [
                'data' => $data,
                'reporte_info' => $reporte_info,
                'equipos_no_leidos' => $data['equipos_no_leidos'], // Agregar equipos no leídos
            ], TRUE);
            $pdf->writeHTML($html);

            // Enviar el PDF al navegador
            $pdf->Output('report_tareas.pdf', 'I'); // 'I' para mostrar en el navegador

        } else if ($format == 'excel') {
            // Si es Excel, cargar la biblioteca correspondiente
            $this->excel->generate_excel('reporte_tareas.xlsx', $data);
        }
    }
}
