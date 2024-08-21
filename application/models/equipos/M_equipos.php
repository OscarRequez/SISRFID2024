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
        $query = $this->db->get('equipos_medicos');
        return $query->result();
    }

    // Obtener equipos médicos paginados con búsqueda opcional
    public function get_equipos_paginados($limit, $offset, $buscar = '')
    {
        $this->db->select('*');
        $this->db->from('equipos_medicos');
        
        if ($buscar) {
            // Filtrado por término de búsqueda
            $this->db->group_start();
            $this->db->like('nombre', $buscar);
            $this->db->or_like('numero_serie', $buscar);
            $this->db->or_like('tag_rfid_id', $buscar);
            $this->db->or_like('ubicacion_id', $buscar);
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
            // Filtrado por término de búsqueda
            $this->db->group_start();
            $this->db->like('nombre', $buscar);
            $this->db->or_like('numero_serie', $buscar);
            $this->db->or_like('tag_rfid_id', $buscar);
            $this->db->or_like('ubicacion_id', $buscar);
            $this->db->group_end();
        }

        return $this->db->count_all_results();
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

    // Eliminar un equipo médico
    public function eliminar_equipo($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('equipos_medicos');
    }

    // Obtener un equipo médico por ID
    public function obtener_equipo_por_id($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('equipos_medicos');
        return $query->row();
    }
}
