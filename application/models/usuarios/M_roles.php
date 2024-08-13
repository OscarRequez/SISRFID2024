<?php
class M_Roles extends CI_Model {

    public function __construct() {
        $this->load->database(); // Cargar la base de datos
    }

    public function get_roles()
        {
            $this->db->select('id, nombre, estado');
            $this->db->from('roles');
            $query = $this->db->get();
            return $query->result_array();
    }

    public function obtener_roles() {
        $query = $this->db->get('roles'); // 'roles' es el nombre de la tabla
        return $query->result(); // Devolver el resultado como un array de objetos
    }

}