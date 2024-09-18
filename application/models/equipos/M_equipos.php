<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_equipos extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // Obtener todos los equipos médicos (opcional)
    public function get_todos_equipos()
    {
        $this->db->select('equipos_medicos.*, ubicaciones.nombre as ubicacion_nombre');
        $this->db->from('equipos_medicos');
        $this->db->join('ubicaciones', 'ubicaciones.id = equipos_medicos.ubicacion_id', 'left');
        $query = $this->db->get();
        return $query->result();
    }

    // Obtener equipos médicos paginados con búsqueda opcional
    public function get_equipos_paginados($limit, $offset, $buscar = '')
    {
        $this->db->select('equipos_medicos.*, ubicaciones.nombre as ubicacion_nombre');
        $this->db->from('equipos_medicos');
        $this->db->join('ubicaciones', 'ubicaciones.id = equipos_medicos.ubicacion_id', 'left');

        if ($buscar) {
            $this->db->group_start();
            $this->db->like('equipos_medicos.nombre', $buscar);
            $this->db->or_like('equipos_medicos.numero_serie', $buscar);
            $this->db->or_like('equipos_medicos.tag_rfid_id', $buscar);
            $this->db->or_like('equipos_medicos.ubicacion_id', $buscar);
            $this->db->or_like('ubicaciones.nombre', $buscar);
            $this->db->group_end();
        }

        $this->db->limit($limit, $offset);
        $query = $this->db->get();
        return $query->result();
    }

    // Contar el total de registros con búsqueda opcional
    public function count_all_equipos($buscar = '')
    {
        $this->db->from('equipos_medicos');

        if ($buscar) {
            $this->db->group_start();
            $this->db->like('nombre', $buscar);
            $this->db->or_like('numero_serie', $buscar);
            $this->db->or_like('tag_rfid_id', $buscar);
            $this->db->or_like('ubicacion_id', $buscar);
            $this->db->group_end();
        }

        return $this->db->count_all_results();
    }

    // Obtener ubicaciones para select
    public function ubicaciones_para_select()
    {
        $query = $this->db->get('ubicaciones');
        return $query->result_array();
    }

     // Obtener tipos  para select
     public function tipos_equipo_select()
     {
         $query = $this->db->get('tipo_equipo');
         return $query->result_array();
     }
 

    // Verificación de número de serie que sea único
    public function existsNumeroSerie($numero_serie, $exclude_id = NULL)
    {
        if ($exclude_id !== NULL) {
            $this->db->where('id !=', $exclude_id);
        }
        $this->db->where('numero_serie', $numero_serie);
        $query = $this->db->get('equipos_medicos');
        return $query->num_rows() > 0;
    }

    // Verificación de tag RFID que sea único
    public function existsTagRfidId($tag_rfid_id, $exclude_id = NULL)
    {
        if ($exclude_id !== NULL) {
            $this->db->where('id !=', $exclude_id);
        }
        $this->db->where('tag_rfid_id', $tag_rfid_id);
        $query = $this->db->get('equipos_medicos');
        return $query->num_rows() > 0;
    }

    // Insertar un nuevo equipo médico
    public function insertar_equipo($data)
    {
        return $this->db->insert('equipos_medicos', $data);
    }

    // Actualizar un equipo médico existente
    public function actualizar_equipo($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('equipos_medicos', $data);
    }

    // Obtener un equipo médico por ID PARA EDITAR
    public function obtener_equipo_por_id($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('equipos_medicos');
        return $query->row();
    }

    //para almacenar el historial de un equipo que cambia de ubicacion
    public function insertar_movimiento_equipo($data)
    {
        $this->db->insert('mov_equipos', $data);
    }


    // Función para eliminar un equipo (cambiar su estado a inactivo)
    public function eliminar_equipo($id)
    {
        // Actualiza el estado del equipo a inactivo (0)
        $this->db->where('id', $id);
        $this->db->update('equipos_medicos', array('estado' => 0));

        // Verifica si la actualización fue exitosa
        return $this->db->affected_rows() > 0;
    }


    // Método para insertar los datos en la tabla equipos_eliminados
    public function insertar_equipo_eliminado($id, $equipo, $motivo, $usr_inactivador)
    {
        $data = array(
            'equipo_id' => $id,
            'nombre' => isset($equipo->nombre) ? $equipo->nombre : null,
            'numero_serie' => isset($equipo->numero_serie) ? $equipo->numero_serie : null,
            'ubicacion_ultima' => isset($equipo->ubicacion_id) ? $equipo->ubicacion_id : null,
            'estado_anterior' => isset($equipo->estado) ? $equipo->estado : null,
            'motivo' => $motivo,
            'usr_inactivador' => $usr_inactivador,
            'fecha_inactivacion' => date('Y-m-d H:i:s')
        );

        return $this->db->insert('equipos_eliminados', $data);
    }

    // Función para obtener la información de eliminación del equipo
    public function get_eliminacion_info($id)
    {
        $this->db->where('equipo_id', $id);
        $query = $this->db->get('equipos_eliminados');

        if ($query->num_rows() > 0) {
            return $query->row(); // Devuelve la primera fila del resultado con todos los campos
        }

        return false; // No se encontró información
    }

    //notificaion
    public function nueva_notificacion($data) {
        return $this->db->insert('notificaciones', $data);
    }
}
