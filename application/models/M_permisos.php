<?php
class M_permisos extends CI_Model {
    
    public function tiene_permiso($rol_id, $modulo) {
        $this->db->where('rol_id', $rol_id);
        $this->db->where('modulo', $modulo);
        $query = $this->db->get('permisos');
        return $query->num_rows() > 0;
    }
}