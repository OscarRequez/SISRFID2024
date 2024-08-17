<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_avatars extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_permisos');
        $this->load->model('usuarios/M_avatars', 'mAvatars');

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
        if (!$this->M_permisos->tiene_permiso($rol_id, 'usuarios')) {
            redirect('no_autorizado');
        }
    }

    public function index()
    {
        $data['avatares'] = $this->mAvatars->get_all(); // Obtener avatares
        $this->load->view('usuarios/avatars', $data); // Cargar la vista
    }

    public function agregar()
    {
        // Cargar librerías necesarias
        $this->load->library('upload');

        // Configuración de la carga de archivos
        $config['upload_path'] = './assets/imagenes/avatars/';
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size'] = 2048; // 2 MB

        // Obtener la fecha y hora actuales para generar un nombre de archivo único
        $dateTime = new DateTime();
        $formattedDate = $dateTime->format('Ymd_His');
        $config['file_name'] = $formattedDate . '_' . $_FILES['avatar_image']['name'];

        $this->upload->initialize($config);

        if ($this->upload->do_upload('avatar_image')) {
            $uploadData = $this->upload->data();
            $data = array(
                'nombre' => $this->input->post('avatar_name'),
                'ruta_imagen' => 'assets/imagenes/avatars/' . $uploadData['file_name'],
                'usucre' => $this->session->userdata('email'),
                'feccre' => date('Y-m-d H:i:s')
            );
            $this->mAvatars->insert_avatar($data);
            redirect('avatars');
        } else {
            // Manejar errores de carga
            $error = $this->upload->display_errors();
            $this->session->set_flashdata('error', $error);
            redirect('avatars');
        }
    }

    public function eliminar($id)
    {
        // Obtener el avatar para eliminar el archivo
        $avatar = $this->mAvatars->get_avatar_by_id($id);

        if ($avatar) {
            // Eliminar el archivo de imagen
            unlink('./' . $avatar['ruta_imagen']);

            // Eliminar el registro en la base de datos
            $this->mAvatars->delete_avatar($id);
        }

        redirect('c_avatars');
    }


}

