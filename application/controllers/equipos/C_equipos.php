<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_equipos extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_permisos');
        $this->load->model('equipos/M_equipos', 'mEquipos');
        $this->load->model('usuarios/M_roles', 'mRoles');
        // Cargar la biblioteca form_validation
        $this->load->library('form_validation');

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
        if (!$this->M_permisos->tiene_permiso($rol_id, 'equipos')) {
            redirect('no_autorizado');
        }
    }

    public function index()
    {
        $data['equipos_medicos'] = $this->mEquipos->get_todos_equipos(); // Obtener todos los equipos médicos
        $this->load->view('equipos/equipos', $data); // Cargar la vista correspondiente
    }

    public function mostrar_equipos()
    {
        $buscar = $this->input->post('buscar'); // Valor de búsqueda opcional
        $pagina = $this->input->post('nropagina'); // Número de página
        $cantidad = $this->input->post('cantidad'); // Cantidad de registros por página

        // Calcula el inicio para la paginación
        $inicio = ($pagina - 1) * $cantidad;

        // Obtén los equipos médicos paginados y el total de registros
        $data['equipos'] = $this->mEquipos->get_equipos_paginados($cantidad, $inicio, $buscar);
        $data['totalregistros'] = $this->mEquipos->count_all_equipos($buscar);
        $data['cantidad'] = $cantidad;

        // Devuelve los datos en formato JSON
        echo json_encode($data);
    }

    public function obtener_ubicaciones_para_select()
    {
        $ubicaciones = $this->mEquipos->ubicaciones_para_select();
        echo json_encode(array('ubicaciones' => $ubicaciones));
    }

    public function obtener_tipos_equipo_select()
    {
        $tipo_equipo = $this->mEquipos->tipos_equipo_select();
        echo json_encode(array('tipo_equipo' => $tipo_equipo));
    }

    // Método para agregar un equipo médico
    public function agregar_equipo()
    {
        // Reglas de validación
        $this->form_validation->set_rules('nombre', 'Nombre', 'required');
        $this->form_validation->set_rules('Descripcion', 'Descripción', 'required');
        $this->form_validation->set_rules('Codigo', 'Código', 'required');
        $this->form_validation->set_rules('RFID', 'RFID', 'required');
        $this->form_validation->set_rules('costo', 'costo', 'required');
        $this->form_validation->set_rules('tipo_eq', 'tipo_eq', 'required');
        $this->form_validation->set_rules('ubicacion', 'Ubicación', 'required');

        if ($this->form_validation->run() == FALSE) {
            // Redirige si hay errores de validación
            $response = array(
                'status' => 'error',
                'message' => validation_errors()
            );
            echo json_encode($response);
            return;
        }

        // Verificar si el número de serie o RFID ya existen
        $numero_serie = $this->input->post('Codigo');
        $tag_rfid_id = $this->input->post('RFID');

        if ($this->mEquipos->existsNumeroSerie($numero_serie)) {
            $response = array(
                'status' => 'Error en los datos',
                'message' => 'El Codigo del equipo ya existe.'
            );
            echo json_encode($response);
            return;
        }

        if ($this->mEquipos->existsTagRfidId($tag_rfid_id)) {
            $response = array(
                'status' => 'error',
                'message' => 'El Codigo RFID ya esta asignado a otro equipo.'
            );
            echo json_encode($response);
            return;
        }

        // Manejo de la imagen
        $config['upload_path'] = './assets/imagenes/equipos/';
        $config['allowed_types'] = 'jpg|jpeg|png';
        $config['file_name'] = 'equipo_' . date('YmdHis');
        $config['overwrite'] = TRUE;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('imagen')) {
            $response = array(
                'status' => 'error',
                'message' => $this->upload->display_errors()
            );
            echo json_encode($response);
            return;
        } else {
            $upload_data = $this->upload->data();
            $imagen_nombre = $upload_data['file_name'];
        }

        // Preparación de los datos para insertar en la base de datos
        $data = array(
            'nombre' => $this->input->post('nombre'),
            'descripcion' => $this->input->post('Descripcion'),
            'numero_serie' => $this->input->post('Codigo'),
            'tag_rfid_id' => $this->input->post('RFID'),
            'costo' => $this->input->post('costo'),
            'tipo_eq' => $this->input->post('tipo_eq'),
            'ubicacion_id' => $this->input->post('ubicacion'),
            'Imagen' => $imagen_nombre,
            'estado' => 1,
            'usucre' => $this->session->userdata('email'),
            'feccre' => date('Y-m-d H:i:s')
        );

        // Llamada al modelo para insertar los datos
        $this->mEquipos->insertar_equipo($data);

        // Devuelve un éxito si la inserción es correcta
        $response = array(
            'status' => 'success',
            'message' => 'Equipo médico agregado con éxito'
        );
        echo json_encode($response);
    }



    public function editar_equipo()
    {
        $id = $this->input->post('id'); // ID del equipo
        $motivo = $this->input->post('motivo'); // Motivo del cambio de ubicación

        // Validaciones de formulario
        $this->form_validation->set_rules('nombre', 'Nombre', 'required');
        $this->form_validation->set_rules('Descripcion', 'Descripción', 'required');
        $this->form_validation->set_rules('Codigo', 'Código', 'required');
        $this->form_validation->set_rules('RFID', 'RFID', 'required');
        $this->form_validation->set_rules('costo', 'costo', 'required');
        $this->form_validation->set_rules('tipo_eq', 'tipo_eq', 'required');
        $this->form_validation->set_rules('ubicacion', 'Ubicación', 'required');


        if ($this->form_validation->run() == FALSE) {
            $response = array(
                'status' => 'error',
                'message' => validation_errors()
            );
            echo json_encode($response);
            return;
        }

        // Obtén la información actual del equipo
        $equipo = $this->mEquipos->obtener_equipo_por_id($id);

        // Array para almacenar los campos que serán actualizados
        $data = array();

        // Comparar cada campo para ver si ha cambiado
        if ($this->input->post('nombre') !== $equipo->nombre) {
            $data['nombre'] = $this->input->post('nombre');
        }
        if ($this->input->post('Descripcion') !== $equipo->descripcion) {
            $data['descripcion'] = $this->input->post('Descripcion');
        }
        if ($this->input->post('Codigo') !== $equipo->numero_serie) {
            $data['numero_serie'] = $this->input->post('Codigo');
        }
        if ($this->input->post('RFID') !== $equipo->tag_rfid_id) {
            $data['tag_rfid_id'] = $this->input->post('RFID');
        }
        if ($this->input->post('costo') !== $equipo->costo) {
            $data['costo'] = $this->input->post('costo');
        }
        if ($this->input->post('tipo_eq') !== $equipo->tipo_eq) {
            $data['tipo_eq'] = $this->input->post('tipo_eq');
        }

        // Verificar si la ubicación ha cambiado
        if ($this->input->post('ubicacion') !== $equipo->ubicacion_id) {
            // Guardar la nueva ubicación
            $data['ubicacion_id'] = $this->input->post('ubicacion');

            // Crear registro en la tabla mov_equipos para el historial
            $historial_data = array(
                'equipo_medico_id' => $id, // ID del equipo médico
                'ubicacion_anterior_id' => $equipo->ubicacion_id, // Ubicación anterior
                'ubicacion_nueva_id' => $this->input->post('ubicacion'), // Nueva ubicación
                'fecha_movimiento' => date('Y-m-d H:i:s'), // Fecha y hora del movimiento
                'usuario_id' => $this->session->userdata('user_id'), // ID del usuario que realizó el cambio
                'estado' => 1, // Estado del registro
                'usucre' => $this->session->userdata('email'), // Usuario que creó el registro
                'feccre' => date('Y-m-d H:i:s'), // Fecha de creación del registro
                'motivo' => $motivo // Agregar el motivo del cambio
            );

            // Insertar el historial en la base de datos
            $this->mEquipos->insertar_movimiento_equipo($historial_data);

            // Preparar los datos para la notificación
            $notificacion = array(
                'titulo' => 'Movimiento de Equipo',
                'mensaje' => 'El equipo ' .  $this->input->post('Codigo') . ' fue cambiado de ubicación por el usuario ' . $this->session->userdata('username') . '',
                'tipo' => 'info',
                'rol' => 1, // 100 = administrador
                'feccre' => date('Y-m-d H:i:s'),
            );

            // Insertar la notificación en la base de datos
            $this->mEquipos->nueva_notificacion($notificacion);
        }

        // Siempre actualizar el estado, usuario y fecha de modificación
        //$data['estado'] = 1;
        $data['usumod'] = $this->session->userdata('email');
        $data['fecmod'] = date('Y-m-d H:i:s');

        // Manejar la carga de imagen si se ha subido una nueva
        if (!empty($_FILES['imagen']['name'])) {
            $config['upload_path'] = './assets/imagenes/equipos/';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['file_name'] = 'equipo_' . date('YmdHis');
            $config['overwrite'] = TRUE;

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('imagen')) {
                $response = array(
                    'status' => 'error',
                    'message' => $this->upload->display_errors()
                );
                echo json_encode($response);
                return;
            } else {
                // Eliminar la imagen anterior si existe
                if (!empty($equipo->imagen)) {
                    $path = './assets/imagenes/equipos/' . $equipo->imagen;
                    if (file_exists($path)) {
                        unlink($path);
                    }
                }
                $upload_data = $this->upload->data();
                $data['imagen'] = $upload_data['file_name'];
            }
        }

        // Solo actualizar si hay cambios
        if (!empty($data)) {
            $this->mEquipos->actualizar_equipo($id, $data);
            $response = array(
                'status' => 'success',
                'message' => 'Equipo médico actualizado con éxito'
            );
        } else {
            $response = array(
                'status' => 'info',
                'message' => 'No se realizaron cambios en el equipo.'
            );
        }

        echo json_encode($response);
    }


    // Método para eliminar un equipo médico
    public function eliminar_equipo()
    {
        $id = $this->input->post('id'); // Recibe el ID del equipo a eliminar
        $motivo = $this->input->post('motivo'); // Recibe el motivo de la inactivación
        $usr_inactivador = $this->session->userdata('email'); // Obtiene el usuario que está haciendo la inactivación

        if (empty($id) || empty($motivo)) {
            $response = array(
                'status' => 'error',
                'message' => 'ID del equipo o motivo no proporcionado.'
            );
            echo json_encode($response);
            return;
        }

        // Llama al modelo para obtener los datos del equipo antes de inactivarlo
        $equipo = $this->mEquipos->obtener_equipo_por_id($id);

        if (!$equipo) {
            $response = array(
                'status' => 'error',
                'message' => 'Equipo no encontrado.'
            );
            echo json_encode($response);
            return;
        }

        // Inserta en la tabla equipos_eliminados
        $eliminacion_result = $this->mEquipos->insertar_equipo_eliminado($id, $equipo, $motivo, $usr_inactivador);

        if ($eliminacion_result) {
            // Llama al modelo para actualizar el estado del equipo a inactivo (0)
            $result = $this->mEquipos->eliminar_equipo($id);

            if ($result) {
                $response = array(
                    'status' => 'success',
                    'message' => 'Equipo médico inactivado con éxito.'
                );
              
                // Preparar los datos para la notificación
                $notificacion = array(
                    'titulo' => ' Eliminacion de Equipo',
                    'mensaje' => 'El equipo con el id' .  $this->input->post('id') . ' fue Eliminado por el usuario ' . $this->session->userdata('username') . '',
                    'tipo' => 'info',
                    'rol' => 1, // 100 = administrador
                    'feccre' => date('Y-m-d H:i:s'),
                );

                // Insertar la notificación en la base de datos
                $this->mEquipos->nueva_notificacion($notificacion);

            } else {
                $response = array(
                    'status' => 'error',
                    'message' => 'No se pudo inactivar el equipo.'
                );
            }
        } else {
            $response = array(
                'status' => 'error',
                'message' => 'No se pudo registrar la inactivación en la tabla de eliminados.'
            );
        }

        echo json_encode($response);
    }

    public function obtener_motivo_eliminacion()
    {
        $id = $this->input->post('id'); // Obtener el ID del equipo enviado en la solicitud POST

        if (empty($id)) {
            $response = array(
                'status' => 'error',
                'message' => 'ID del equipo no proporcionado.'
            );
            echo json_encode($response);
            return;
        }

        // Llama al modelo para obtener la información de eliminación del equipo
        $eliminacion_info = $this->mEquipos->get_eliminacion_info($id);

        if ($eliminacion_info) {
            // Suponiendo que $eliminacion_info contiene los campos: motivo, usr_inactivador, fecha_inactivacion, numero_serie
            $response = array(
                'status' => 'success',
                'data' => array(
                    'motivo' => $eliminacion_info->motivo,
                    'usr_inactivador' => $eliminacion_info->usr_inactivador,
                    'fecha_inactivacion' => $eliminacion_info->fecha_inactivacion,
                    'numero_serie' => $eliminacion_info->numero_serie
                )
            );
        } else {
            $response = array(
                'status' => 'error',
                'message' => 'Información de eliminación no encontrada.'
            );
        }

        echo json_encode($response);
    }
}
