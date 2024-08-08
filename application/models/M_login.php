<?php
class M_login extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
    }
    // INICIO OR-02 07/08/2024 OSCAR REQUEZ MODULO DE INICIO DE SESION
    public function validate_user($email)
    {
        // Consulta a la base de datos solo por email
        $this->db->where('email', $email);
        $query = $this->db->get('usuarios');

        if ($query->num_rows() == 1) {
            return $query->row(); // Retorna la fila correspondiente al usuario
        } else {
            return false; // Retorna false si no se encuentra al usuario
        }
    }
} // FIN OR-02 07/08/2024 OSCAR REQUEZ MODULO DE INICIO DE SESION