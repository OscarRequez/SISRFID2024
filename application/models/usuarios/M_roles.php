<?php
class M_Roles extends CI_Model {

    public function __construct() {
        $this->load->database(); // Cargar la base de datos
    }

    public function get_roles()
        {
            $this->db->select('id, nombre, estado');
            $this->db->from('roles');
            $query = $this->db->get();
            return $query->result_array();
    }

    public function obtener_roles() {
        $query = $this->db->get('roles'); // 'roles' es el nombre de la tabla
        return $query->result(); // Devolver el resultado como un array de objetos
    }


    public function getRoles($limit, $offset) {
        $this->db->limit($limit, $offset);
        $query = $this->db->get('roles');
        return $query->result_array();
    }
    
    public function countRoles() {
        return $this->db->count_all('roles');
    }
    


    // Obtener módulos y submódulos únicos
    public function obtenerPermisosUnicos() {
        // Obtener todos los módulos y submódulos únicos, ordenados alfabéticamente
        $this->db->select('modulo, submodulo');
        $this->db->from('permisos');
        $this->db->group_by(['modulo', 'submodulo']);
        $this->db->order_by('modulo DESC, submodulo DESC'); // Ordenar por módulo y submódulo en orden ascendente
        $query = $this->db->get();
        $permisos = $query->result_array();
    
        $result = [];
    
        // Organizar los submódulos bajo su correspondiente módulo
        foreach ($permisos as $permiso) {
            $modulo_nombre = $permiso['modulo'];
            $submodulo_nombre = $permiso['submodulo'];
    
            // Verifica si el módulo ya está en el resultado
            if (!isset($result[$modulo_nombre])) {
                $result[$modulo_nombre] = [
                    'modulo' => $modulo_nombre,
                    'submodulos' => []
                ];
            }
    
            // Añadir el submódulo a la lista correspondiente del módulo, solo si no es null
            if (!is_null($submodulo_nombre)) {
                $result[$modulo_nombre]['submodulos'][] = $submodulo_nombre;
            }
        }
    
        // Convierte la estructura asociativa en un array indexado
        return array_values($result);
    }
    

}