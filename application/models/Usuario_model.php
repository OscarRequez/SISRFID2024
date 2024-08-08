<?php
class Usuario_model extends CI_Model {

    public function __construct() {
        $this->load->database(); // Cargar la base de datos
    }

    public function get_usuarios_con_roles()
        {
            $this->db->select('usuarios.id, usuarios.nombre, usuarios.email, roles.nombre AS rol_nombre, usuarios.estado');
            $this->db->from('usuarios');
            $this->db->join('roles', 'usuarios.rol_id = roles.id');
            $query = $this->db->get();
            return $query->result_array();
    }

}