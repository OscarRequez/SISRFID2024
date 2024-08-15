<?php
class M_permisos extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        // Carga la base de datos
        $this->load->database();
    }

    /**
     * Verifica si un rol tiene permiso para un módulo y opcionalmente para un submódulo.
     *
     * @param int $rol_id - El ID del rol.
     * @param string $modulo - El nombre del módulo.
     * @param string|null $submodulo - El nombre del submódulo (opcional).
     * @return bool - Devuelve verdadero si tiene permiso, falso en caso contrario.
     */
    public function tiene_permiso($rol_id, $modulo, $submodulo = null) {
        $this->db->where('rol_id', $rol_id);
        $this->db->where('modulo', $modulo);

        if ($submodulo !== null) {
            $this->db->where('submodulo', $submodulo);
        }

        $query = $this->db->get('permisos');
        return $query->num_rows() > 0;
    }
}