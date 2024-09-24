<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_registro_tareas extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_permisos');
        $this->load->model('rfid/M_registro_tareas', 'mRegTareas');
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
        $this->load->view('rfid/registro_tareas'); // Cargar la vista correspondiente
    }

    public function tareas_asignadas_obtener()
    {
        $pagina = $this->input->post('pagina');
        $cantidad = $this->input->post('cantidad');
        $buscar = $this->input->post('filtro'); // Obtén el término de búsqueda si está presente
        $offset = ($pagina - 1) * $cantidad;

        // Llama al modelo con el término de búsqueda opcional
        $data['tareas'] = $this->mRegTareas->obtenerTareasAsignadas($offset, $cantidad, $buscar);
        $data['totalregistros'] = $this->mRegTareas->contarTareasAsignadas($buscar);

        echo json_encode($data);
    }

    public function obtener_operarios()
    {

        // Obtener los técnicos con rol_id = 3
        $operarios = $this->mRegTareas->operarios_por_rol(9); //9 es usaurios que son operarios en roles 

        // Devolver la respuesta en formato JSON
        echo json_encode(array('operarios' => $operarios));
    }


    public function asignar_operario()
    {
        $solicitud_id = $this->input->post('solicitud_id');
        $operario_id = $this->input->post('operario_id');

        // Datos de auditoría
        $usumod = $this->session->userdata('email');
        $fecmod = date('Y-m-d H:i:s');
        $fecAsignacion = date('Y-m-d H:i:s');

        // Asignar técnico a la solicitud
        $resultado = $this->mRegTareas->asignar_operario($solicitud_id, $operario_id, $usumod, $fecAsignacion,  $fecmod);

        if ($resultado) {

            //INSERTAR LA NOTIFICACION
            $notificacion = array(
                'titulo' => 'Nueva Tarea',
                'mensaje' => 'Usted tiene un nuevo trabajo asignado',
                'tipo' => 'info',
                'usuario_id' => $operario_id, //  = tecnico especifico
                'feccre' => date('Y-m-d H:i:s'),
            );

            // Insertar la notificación en la base de datos
            $notificacion_insertada = $this->mRegTareas->nueva_notificacion($notificacion);


            echo json_encode(array('status' => 'success', 'message' => 'Técnico asignado exitosamente.'));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Error al asignar técnico.'));
        }
    }

    public function crear_tarea_lectura()
    {
        if ($this->input->is_ajax_request()) {
            // Obtener los datos del formulario

            $descripcion = $this->input->post('descripcion');
            $tipo_tarea = $this->input->post('tipo_tarea');
            $ubicacion_tarea = $this->input->post('unidad');

            $total_equipos_area = $this->mRegTareas->cantidad_equipos_ubicacion($ubicacion_tarea);

            // Preparar los datos para la inserción
            $data = array(
                'descripcion_tarea' => $descripcion,
                'tipo_tarea' => $tipo_tarea,
                'ubicacion_tarea' => $ubicacion_tarea,
                'tipo_tarea' => $tipo_tarea,
                'total_equipos'=>$total_equipos_area,
                'estado' => 1,
                'usucre' => $this->session->userdata('email'),
                'feccre' => date('Y-m-d H:i:s')
            );

            // Insertar la solicitud en la base de datos
            $solicitud_insertada = $this->mRegTareas->insertar_tarea($data);



            if ($solicitud_insertada) {
                echo json_encode(array('status' => 'success', 'message' => 'Solicitud agregada exitosamente.'));
            } else {
                echo json_encode(array('status' => 'error', 'message' => 'Error al agregar la solicitud o notificación.'));
            }
        } else {
            redirect('lectura');
        }
    }

    public function confirmar_tarea_lectura_admin()
    {
        // Recibir datos enviados por AJAX
        $solicitud_id = $this->input->post('solicitud_id');
        $ubicacion = $this->input->post('ubicacion');

        // Obtener el email del usuario actual desde la sesión
        $usuario_actual = $this->session->userdata('email');
        $fecha_confirmacion = date('Y-m-d H:i:s'); // Obtener la fecha actual

        // Validar que los datos fueron recibidos correctamente
        if ($solicitud_id) {

            // Llamar al modelo para confirmar la solicitud y actualizar los estados y campos adicionales
            $resultado = $this->mRegTareas->confirmar_tarea_Admin($solicitud_id, $usuario_actual, $fecha_confirmacion);

            // Verificar si la actualización fue exitosa
            if ($resultado) {
                // Preparar los datos para la notificación
                $notificacion = array(
                    'titulo' => 'Proceso Completado',
                    'mensaje' => 'El proceso de Lectura del equipo  ' . $ubicacion . ', fue completado y confirmado.',
                    'tipo' => 'info',
                    'rol' => 3, // 3 = tecnico de soporte
                    'feccre' => date('Y-m-d H:i:s'),
                );

                // Insertar la notificación en la base de datos
                $notificacion_insertada = $this->mRegTareas->nueva_notificacion($notificacion);

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
}
