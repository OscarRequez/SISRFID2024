<?php
class M_manuales extends CI_Model
{

    public function __construct()
    {
        $this->load->database(); // Cargar la base de datos
    }

    public function contar_documentos()
    {
        return $this->db->count_all('documentos');
    }

    public function obtener_documentos($cantidad, $offset)
    {
        $this->db->order_by('feccre', 'DESC'); // Cambia 'feccre' por el campo por el que deseas ordenar
        $this->db->limit($cantidad, $offset);
        $query = $this->db->get('documentos');
        return $query->result_array();
    }
}
