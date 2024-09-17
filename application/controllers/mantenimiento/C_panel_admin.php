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
        $this->load->library('pdf_equipos'); // para poder usar pdfs


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

    public function asignar_tecnico()
    {
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

            //INSERTAR LA NOTIFICACION
            $notificacion = array(
                'titulo' => 'Nueva Asignacion',
                'mensaje' => 'Usted tiene un nuevo trabajo de mantenimiento asignado',
                'tipo' => 'info',
                'usuario_id' => $tecnico_id, //  = tecnico especifico
                'feccre' => date('Y-m-d H:i:s'),
            );

            // Insertar la notificación en la base de datos
            $notificacion_insertada = $this->mPanelAdmin->nueva_notificacion($notificacion);


            echo json_encode(array('status' => 'success', 'message' => 'Técnico asignado exitosamente.'));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Error al asignar técnico.'));
        }
    }


    public function confirmar_solicitud()
    {
        // Recibir datos enviados por AJAX
        $solicitud_id = $this->input->post('solicitud_id');
        $equipo_id = $this->input->post('equipo_id');

        // Obtener el email del usuario actual desde la sesión
        $usuario_actual = $this->session->userdata('email');
        $fecha_confirmacion = date('Y-m-d H:i:s'); // Obtener la fecha actual

        // Validar que los datos fueron recibidos correctamente
        if ($solicitud_id && $equipo_id) {
            // Llamar al modelo para obtener el costo total
            $costo_total = $this->mPanelAdmin->calcular_costo_total($solicitud_id);

            // Llamar al modelo para confirmar la solicitud y actualizar los estados y campos adicionales
            $resultado = $this->mPanelAdmin->confirmar_solicitud($solicitud_id, $equipo_id, $usuario_actual, $fecha_confirmacion, $costo_total);

            // Verificar si la actualización fue exitosa
            if ($resultado) {
                // Preparar los datos para la notificación
                $notificacion = array(
                    'titulo' => 'Proceso Completado',
                    'mensaje' => 'El proceso de mantenimiento del equipo  ' . $equipo_id . ', fue completado y confirmado.',
                    'tipo' => 'info',
                    'rol' => 3, // 3 = tecnico de soporte
                    'feccre' => date('Y-m-d H:i:s'),
                );

                // Insertar la notificación en la base de datos
                $notificacion_insertada = $this->mPanelAdmin->nueva_notificacion($notificacion);

                // Responder con un mensaje de éxito
                echo json_encode(['status' => 'success', 'message' => 'Confirmación exitosa.']);
            } else {
                // Responder con un mensaje de error
                echo json_encode(['status' => 'error', 'message' => 'Error al confirmar la solicitud.']);
            }
        } else {
            // Responder con un error si no se enviaron los datos necesarios
            echo json_encode(['status' => 'error', 'message' => 'Datos incompletos.']);
        }
    }


    public function mantenimiento_reporte_fechas()
    {
        // Recibir fechas desde la URL (GET request)
        $fecha_inicio_formulario = $this->input->get('fechaInicio');
        $fecha_fin_formulario = $this->input->get('fechaFin');
        $check_only = $this->input->get('check'); // Recibir el parámetro 'check' para verificar si es solo una comprobación

        // Validar si las fechas fueron proporcionadas
        if ($fecha_inicio_formulario && $fecha_fin_formulario) {

            // Convertir las fechas al formato que utiliza la base de datos
            $fecha_inicio_formulario = date('Y-m-d', strtotime($fecha_inicio_formulario)) . ' 00:00:00';
            $fecha_fin_formulario = date('Y-m-d', strtotime($fecha_fin_formulario)) . ' 23:59:59';

            // Obtener datos entre las fechas dadas
            $datos_reporte = $this->mPanelAdmin->obtenerDatosPorFechas($fecha_inicio_formulario, $fecha_fin_formulario);

            // Verificar si hay datos antes de proceder
            if (!empty($datos_reporte)) {
                // Si la petición es solo para verificar los datos (check_only == true), responder con un JSON
                if ($check_only) {
                    echo json_encode(['hay_datos' => true]);
                    return;
                }

                // Obtener el técnico con más mantenimientos
                $tecnico_mas = $this->mPanelAdmin->obtenerTecnicoConMasMantenimientos($fecha_inicio_formulario, $fecha_fin_formulario);

                // Obtener todos los técnicos con sus mantenimientos
                $tecnicos_mantenimientos = $this->mPanelAdmin->obtenerTecnicosConMantenimientos($fecha_inicio_formulario, $fecha_fin_formulario);

                // Obtener el total de gastos por mes en el año actual
                $gastos_mes = $this->mPanelAdmin->obtenerGastosPorMes(date('Y'));

                // Cargar la biblioteca TCPDF
                $this->load->library('tcpdf');

                // Crear una nueva instancia de TCPDF
                $pdf = new TCPDF();

                // Establecer el título del documento
                $pdf->SetTitle('Reporte de Mantenimiento');

                // Añadir una página
                $pdf->AddPage();

                // Establecer encabezado y pie de página
                $pdf->SetHeaderData('', 0, 'Reporte de Mantenimiento', 'Generado por: ' . $this->session->userdata('username'));
                $pdf->SetFooterData('', 0, 'Página: {PAGENO}');

                // Establecer márgenes
                $pdf->SetMargins(10, 10, 10);
                $pdf->SetHeaderMargin(15);
                $pdf->SetFooterMargin(15);

                // Definir la fuente
                $pdf->SetFont('helvetica', '', 12);

                // Generar HTML del reporte con los datos obtenidos
                $reporte_info = [
                    'fecha' => date('d/m/Y'),
                    'empresa' => 'Hospital de Clínicas',
                    'direccion' => 'Av. Saavedra, La Paz, Bolivia',
                    'contacto' => '+519 73716289 - mamanioscar509@gmail.com',
                    'elaborado_por' => $this->session->userdata('username'),
                    'puesto' => $this->session->userdata('rol_name')
                ];

                // Cargar la vista del reporte con los datos
                $html = $this->load->view('mantenimiento/report_fecha_mant_pdf', [
                    'datos_reporte' => $datos_reporte,
                    'reporte_info' => $reporte_info,
                    'tecnico_mas' => $tecnico_mas,
                    'gastos_mes' => $gastos_mes,
                    'tecnicos_mantenimientos' => $tecnicos_mantenimientos // Pasar la lista de técnicos al reporte
                ], TRUE);

                // Escribir el HTML en el PDF
                $pdf->writeHTML($html);

                // Enviar el PDF al navegador
                $pdf->Output('reporte_mantenimiento.pdf', 'I');
            } else {
                // Si no hay datos, devolver un error JSON
                if ($check_only) {
                    echo json_encode(['hay_datos' => false, 'error' => 'No se encontraron mantenimientos finalizados para el rango de fechas especificado.']);
                } else {
                    echo json_encode(['error' => 'No se encontraron mantenimientos finalizados para el rango de fechas especificado.']);
                }
            }
        } else {
            echo json_encode(['error' => 'Fechas inválidas']);
        }
    }
}
