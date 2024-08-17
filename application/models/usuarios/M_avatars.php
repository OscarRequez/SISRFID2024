<?php
class M_avatars extends CI_Model
{
    public function __construct()
    {
        $this->load->database(); // Cargar la base de datos
    }

    public function get_all()
    {
        $query = $this->db->get('avatars');
        return $query->result_array();
    }

    public function insert_avatar($data)
    {
        return $this->db->insert('avatars', $data);
    }

    public function get_avatar_by_id($id)
    {
        $query = $this->db->get_where('avatars', ['id' => $id]);
        return $query->row_array();
    }

    public function delete_avatar($id)
    {
        return $this->db->delete('avatars', ['id' => $id]);
    }


}
