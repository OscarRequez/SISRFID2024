<?php
class M_welcome extends CI_Model
{
    public function __construct()
    {
        $this->load->database(); // Cargar la base de datos
    }


    //avatares para el perfil
    public function obtener_avatares()
    {
        $query = $this->db->get('avatars'); // Nombre de la tabla de avatares
        return $query->result_array();
    }
    public function obtenerAvatarUnicoPorId($avatarId)
    {
        $this->db->select('ruta_imagen');
        $this->db->from('avatars');
        $this->db->where('id', $avatarId);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row(); // Devuelve un objeto con la ruta de la imagen del avatar
        } else {
            return null; // No se encontró el avatar
        }
    }

    public function actualizarAvatar($userId, $avatarId)
    {
        $data = ['ImagenUsu' => $avatarId];
        $this->db->where('id', $userId);
        return $this->db->update('usuarios', $data);
    }
}
