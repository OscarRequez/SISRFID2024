<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_listado_equipos extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
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


}