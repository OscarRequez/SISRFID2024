<?php
class M_usuarios extends CI_Model {

    public function __construct() {
        $this->load->database(); // Cargar la base de datos
    }


    public function count_all_users($buscar = '') {
        if (!empty($buscar)) {
            $this->db->like('nombre', $buscar);
            $this->db->or_like('email', $buscar);
        }
        return $this->db->count_all_results('usuarios');
    }
    
    // ROLES DE USUARIO PARA CONTADOR Y PAGINACION
    public function get_users($limit, $start, $buscar = '') {
        if (!empty($buscar)) {
            $this->db->like('nombre', $buscar);
            $this->db->or_like('email', $buscar);
        }
        $this->db->limit($limit, $start);
        $this->db->select('usuarios.id, usuarios.nombre, usuarios.email, roles.nombre AS rol_nombre, usuarios.estado, usuarios.usucre, usuarios.feccre,');
            $this->db->from('usuarios');
            $this->db->join('roles', 'usuarios.rol_id = roles.id');
            $query = $this->db->get();
            return $query->result_array();
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


     // Método para actualizar los datos de un usuario
     public function actualizar_usuario($id, $data) {
        // Asegurarse de que el ID está presente
        if (!$id || empty($data)) {
            return false;
        }
        // Actualizar el registro en la base de datos
        $this->db->where('id', $id);
        return $this->db->update('usuarios', $data);
    }


    //cambiar estado de usuario 13/08/2024
    public function get_usuario($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('usuarios');
        return $query->row_array();
    }
    
    public function update_usuario_estado($id, $estado) {
        $this->db->where('id', $id);
        $this->db->update('usuarios', ['estado' => $estado]);
    }
    //cambiar estado de usuario 13/08/2024
}