<?php
class M_doc_registro extends CI_Model
{

    public function __construct()
    {
        $this->load->database(); // Cargar la base de datos
    }

    public function contar_documentos() {
        return $this->db->count_all('documentos');
    }
    
    public function obtener_documentos($cantidad, $offset) {
        $this->db->order_by('feccre', 'DESC'); // Cambia 'feccre' por el campo por el que deseas ordenar
        $this->db->limit($cantidad, $offset);
        $query = $this->db->get('documentos');
        return $query->result_array();
    }
    

    public function insertar_documento($data)
    {
        // Insertar los datos en la tabla 'documentos'
        $this->db->insert('documentos', $data);

        // Verificar si la inserción fue exitosa
        if ($this->db->affected_rows() > 0) {
            // Devolver el ID del nuevo registro
            return $this->db->insert_id();
        } else {
            // Devolver false si ocurrió un error
            return false;
        }
    }

    public function obtener_documento_por_id($id) {
        return $this->db->get_where('documentos', ['id' => $id])->row();
    }

    public function actualizar_documento($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('documentos', $data);
    }

    public function eliminar_documento($id) {
        $this->db->where('id', $id);
        return $this->db->delete('documentos');
    }
}
