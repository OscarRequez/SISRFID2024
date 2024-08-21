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

    // Método para agregar un equipo médico
    public function agregar_equipo()
    {
        $this->form_validation->set_rules('nombre', 'Nombre', 'required');
        $this->form_validation->set_rules('marca', 'Marca', 'required');
        $this->form_validation->set_rules('modelo', 'Modelo', 'required');
        $this->form_validation->set_rules('ubicacion', 'Ubicación', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('equipos');
        } else {
            // Manejo de la imagen
            $config['upload_path'] = './uploads/equipos/';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['file_name'] = 'equipo_' . date('YmdHis'); // Nombre único basado en la fecha y hora
            $config['overwrite'] = TRUE;

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('imagen')) {
                $this->session->set_flashdata('error', $this->upload->display_errors());
                redirect('equipos');
            } else {
                $upload_data = $this->upload->data();
                $imagen_nombre = $upload_data['file_name'];
            }

            $data = array(
                'nombre' => $this->input->post('nombre'),
                'marca' => $this->input->post('marca'),
                'modelo' => $this->input->post('modelo'),
                'ubicacion' => $this->input->post('ubicacion'),
                'imagen' => $imagen_nombre, // Guardar el nombre de la imagen
                'estado' => $this->input->post('estado') ? 1 : 0, // Estado activo/inactivo
                'usucre' => $this->session->userdata('user_id'), // Usuario que creó el equipo
                'feccre' => date('Y-m-d H:i:s') // Fecha de creación
            );
            $this->mEquipos->insertar_equipo($data);
            $this->session->set_flashdata('success', 'Equipo médico agregado con éxito');
            redirect('equipos');
        }
    }

    // Método para editar un equipo médico
    public function editar_equipo($id)
    {
        $this->form_validation->set_rules('nombre', 'Nombre', 'required');
        $this->form_validation->set_rules('marca', 'Marca', 'required');
        $this->form_validation->set_rules('modelo', 'Modelo', 'required');
        $this->form_validation->set_rules('ubicacion', 'Ubicación', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('equipos');
        } else {
            $data = array(
                'nombre' => $this->input->post('nombre'),
                'marca' => $this->input->post('marca'),
                'modelo' => $this->input->post('modelo'),
                'ubicacion' => $this->input->post('ubicacion'),
                'estado' => $this->input->post('estado') ? 1 : 0, // Estado activo/inactivo
                'usumod' => $this->session->userdata('user_id'), // Usuario que modificó el equipo
                'fecmod' => date('Y-m-d H:i:s') // Fecha de modificación
            );

            // Manejo de la imagen si es que se sube una nueva
            if (!empty($_FILES['imagen']['name'])) {
                $config['upload_path'] = './uploads/equipos/';
                $config['allowed_types'] = 'jpg|jpeg|png';
                $config['file_name'] = 'equipo_' . date('YmdHis'); // Nombre único basado en la fecha y hora
                $config['overwrite'] = TRUE;

                $this->load->library('upload', $config);

                if (!$this->upload->do_upload('imagen')) {
                    $this->session->set_flashdata('error', $this->upload->display_errors());
                    redirect('equipos');
                } else {
                    $upload_data = $this->upload->data();
                    $data['imagen'] = $upload_data['file_name'];
                }
            }

            $this->mEquipos->actualizar_equipo($id, $data);
            $this->session->set_flashdata('success', 'Equipo médico actualizado con éxito');
            redirect('equipos');
        }
    }

    // Método para eliminar un equipo médico
    public function eliminar_equipo($id)
    {
        $equipo = $this->mEquipos->obtener_equipo_por_id($id);

        // Eliminar la imagen del equipo si existe
        if ($equipo && !empty($equipo->imagen)) {
            $ruta_imagen = './uploads/equipos/' . $equipo->imagen;
            if (file_exists($ruta_imagen)) {
                unlink($ruta_imagen); // Eliminar el archivo de imagen
            }
        }

        $this->mEquipos->eliminar_equipo($id);
        $this->session->set_flashdata('success', 'Equipo médico eliminado con éxito');
        redirect('equipos');
    }

    public function guardar_equipo()
    {
        $id = $this->input->post('id');
        $nombre = $this->input->post('nombre');
        $marca = $this->input->post('marca');
        $modelo = $this->input->post('modelo');
        $ubicacion = $this->input->post('ubicacion');
        $estado = $this->input->post('estado');

        // Manejo de la imagen
        $config['upload_path'] = './uploads/equipos/';
        $config['allowed_types'] = 'jpg|jpeg|png';
        $config['file_name'] = date('YmdHis') . '_' . $_FILES['imagen']['name'];

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('imagen')) {
            $imagen = $this->upload->data('file_name');
        } else {
            $imagen = NULL; // O mantener la imagen existente en caso de editar
        }

        $data = array(
            'nombre' => $nombre,
            'marca' => $marca,
            'modelo' => $modelo,
            'ubicacion' => $ubicacion,
            'estado' => $estado,
            'imagen' => $imagen,
            'usumod' => $this->session->userdata('user_id'),
            'fecmod' => date('Y-m-d H:i:s')
        );

        if ($id) {
            $this->mEquipos->actualizar_equipo($id, $data);
        } else {
            $data['usucre'] = $this->session->userdata('user_id');
            $data['feccre'] = date('Y-m-d H:i:s');
            $this->mEquipos->insertar_equipo($data);
        }

        $this->session->set_flashdata('success', 'Equipo médico guardado con éxito');
        redirect('equipos');
    }
}
