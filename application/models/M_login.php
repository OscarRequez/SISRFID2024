<?php
class M_login extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
    }
    // INICIO OR-02 07/08/2024 OSCAR REQUEZ MODULO DE INICIO DE SESION
    public function validate_user($email) {
        // Seleccionar todos los campos de usuarios y el nombre del rol de roles
        $this->db->select('usuarios.id, usuarios.nombre, usuarios.rol_id as role, roles.nombre AS rol_name, usuarios.contraseña, usuarios.email');
        $this->db->from('usuarios');
        $this->db->join('roles', 'usuarios.rol_id = roles.id'); 
        $this->db->where('usuarios.email', $email);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->row(); // Retorna un objeto con los datos del usuario y el nombre del rol
        } else {
            return false;
        }
    }
}
 // FIN OR-02 07/08/2024 OSCAR REQUEZ MODULO DE INICIO DE SESION