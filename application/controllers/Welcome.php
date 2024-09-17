<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Welcome extends MY_Controller
{

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     *	- or -
     * 		http://example.com/index.php/welcome/index
     *	- or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see https://codeigniter.com/userguide3/general/urls.html
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_permisos');
        $this->load->model('M_welcome', 'mWelcome');
        $this->load->database(); // Cargar la base de datos
        $this->load->library('session'); // Cargar la biblioteca de sesión

        // Verificar si el usuario está autenticado
        if (!$this->session->userdata('user_id')) {
            // Si no está autenticado, redirigir al login
            redirect('login');
        }

        // Verificar si el usuario está activo en la base de datos
        $user_id = $this->session->userdata('user_id');
        $usuario = $this->db->get_where('usuarios', ['id' => $user_id, 'estado' => 1])->row();

        if (!$usuario) {
            // Si el usuario no existe o no está activo, destruir la sesión y redirigir al login
            $this->session->sess_destroy();
            redirect('login');
        }

        // Verificar permisos para el módulo específico
        $rol_id = $this->session->userdata('role');
        if (!$this->M_permisos->tiene_permiso($rol_id, 'welcome')) {
            redirect('no_autorizado'); // Redirigir a una página de no autorizado
        }
    }
    public function index()
    {
        $this->load->view('welcome_message');
    }

    //AVATARES PARA EL PERFIL
    public function obtener_avatares()
    {
        $avatares = $this->mWelcome->obtener_avatares(); // Obtener todos los avatares
        echo json_encode($avatares); // Devolver los datos en formato JSON
    }



    public function actualizarAvatar()
    {
        // Obtener el ID del avatar desde la solicitud POST
        $avatarId = $this->input->post('avatar_id');
        // Obtener el ID del usuario desde la sesión
        $userId = $this->session->userdata('user_id');

        // Validar y actualizar el avatar del usuario en la base de datos
        if ($userId && $avatarId) {
            // Actualizar el avatar en la base de datos
            $actualizado = $this->mWelcome->actualizarAvatar($userId, $avatarId);

            if ($actualizado) {
                // Obtener la ruta del nuevo avatar
                $avatar = $this->mWelcome->obtenerAvatarUnicoPorId($avatarId);

                if ($avatar) {

                    $avatarPath = $avatar->ruta_imagen;
                    // Actualizar la sesión con la nueva ruta del avatar
                    $this->session->set_userdata('avatar_path', $avatarPath);
                } else {
                    // Si el avatar no se encuentra, usar la ruta por defecto
                    $this->session->set_userdata('avatar_path', 'assets/imagenes/avatars/defecto_avatar.png');
                }

                echo json_encode(['success' => true, 'message' => 'Avatar actualizado']);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se pudo actualizar el avatar']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
        }
    }

    //CONTAR EQUIPOS TOTALES
    public function cantidad_equipos()
    {
        $cantidad = $this->mWelcome->contar_equipos(); // Llama al método para contar equipos
        echo json_encode(['cantidad' => $cantidad]); // Devuelve la cantidad en formato JSON
    }

    public function distribucion_equipos()
    {
        $data = $this->mWelcome->contar_equipos_por_estado();
        echo json_encode($data);
    }

    //para la tabla las solicitudes:
    public function mantenimiento_mostrarSolicitudes()
    {

        $pagina = $this->input->post('pagina');
        $cantidad = $this->input->post('cantidad');

        $offset = ($pagina - 1) * $cantidad;

        $data = $this->mWelcome->obtenerSolicitudes($cantidad, $offset);
        $total = $this->mWelcome->contarSolicitudes();

        $response = [
            'solicitudes' => $data,
            'totalregistros' => $total
        ];

        echo json_encode($response);
    }

    //grafico por mes
    public function obtenerDatosGraficoSolicitudes()
    {
        $datos = $this->mWelcome->obtenerSolicitudesPorMes();

        echo json_encode($datos);
    }

    public function obtener_notificaciones()
    {
        $usuario_id = $this->session->userdata('user_id'); // Obtener el ID del usuario actual
        $rol_usuario = $this->session->userdata('role'); // Obtener el rol del usuario actual

        // Cargar el modelo necesario si aún no lo has hecho
        $this->load->model('mWelcome');

        // Obtener notificaciones desde el modelo
        $notificaciones = $this->mWelcome->get_notificaciones($usuario_id, $rol_usuario);

        // Enviar las notificaciones en formato JSON
        echo json_encode($notificaciones);
    }

    public function marcar_notificacion_leida()
    {
        $id = $this->input->post('id');

        if ($id) {
            // Actualizar la notificación como leída
            $this->db->where('id', $id);
            $this->db->update('notificaciones', array('leida' => 1));
        }
    }
}
