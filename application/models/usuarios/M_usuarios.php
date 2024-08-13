<?php
class M_usuarios extends CI_Model {

    public function __construct() {
        $this->load->database(); // Cargar la base de datos
    }

    public function get_usuarios_con_roles()
        {
            $this->db->select('usuarios.id, usuarios.nombre, usuarios.email, roles.nombre AS rol_nombre, usuarios.estado, usuarios.usucre, usuarios.feccre,');
            $this->db->from('usuarios');
            $this->db->join('roles', 'usuarios.rol_id = roles.id');
            $query = $this->db->get();
            return $query->result_array();
    }

    // Método para insertar un nuevo usuario
    public function insertar_usuario($data) {
        return $this->db->insert('usuarios', $data);
    }



}