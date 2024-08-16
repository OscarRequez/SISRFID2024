<?php
class M_Roles extends CI_Model
{

    public function __construct()
    {
        $this->load->database(); // Cargar la base de datos
    }

    public function get_roles()
    {
        $this->db->select('id, nombre, estado');
        $this->db->from('roles');
        $query = $this->db->get();
        return $query->result_array();
    }

    //OBTENER ROLES PARA LOS SELECTS DE USUARIO
    public function obtener_roles()
    {
        $this->db->where('estado', 1); // Filtrar roles con estado 1
        $query = $this->db->get('roles'); // 'roles' es el nombre de la tabla
        return $query->result(); // Devolver el resultado como un array de objetos
    }

    // para mostrar la tabla con  paginacion
    public function getRoles($limit, $offset)
    {
        $this->db->limit($limit, $offset);
        $query = $this->db->get('roles');
        return $query->result_array();
    }

    public function countRoles()
    {
        return $this->db->count_all('roles');
    }
    // para mostrar la tabla con  paginacion


    // Obtener módulos y submódulos únicos para los checkbox disponibles al abrier el modal de agregar
    public function obtenerPermisosUnicos()
    {
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
    } // Obtener módulos y submódulos únicos para los checkbox disponibles al abrier el modal de agregar


    // Función para insertar un nuevo rol
    public function insertarRol($data)
    {
        return $this->db->insert('roles', $data);
    }

    // Función para insertar permisos
    public function insertarPermiso($data)
    {
        return $this->db->insert('permisos', $data);
    }

    // Función para obtener permisos de un rol
    public function obtenerPermisosDeUnRol($rol_id)
    {
        $this->db->select('modulo, submodulo');
        $this->db->from('permisos');
        $this->db->where('rol_id', $rol_id);
        $query = $this->db->get();
        return $query->result_array();
    }


    //PARA ACTUAÑIZAR LOS PERMISOS DE UN ROL
    // Método para actualizar el rol en la tabla de roles
    public function actualizarRol($rol_id, $data)
    {
        $this->db->where('id', $rol_id);
        $this->db->update('roles', $data);
    }

    // Método para actualizar los permisos asociados al rol
    public function actualizarPermisos($rol_id, $modulos, $submodulos)
    {
        // Primero, eliminamos todos los permisos actuales del rol si necesitamos datos de auditoria, se revisa el rol si fue modificado
        $this->db->where('rol_id', $rol_id);
        $this->db->delete('permisos');

        // Ahora, insertamos los nuevos permisos
        foreach ($modulos as $modulo) {
            // Insertar permiso del módulo
            $data = array(
                'rol_id' => $rol_id,
                'modulo' => $modulo,
                'submodulo' => null, // Sin submódulo asociado
                'usucre' => $this->session->userdata('email'), // Asumiendo que el ID del usuario está en la sesión
                'feccre' => date('Y-m-d H:i:s')
            );
            $this->db->insert('permisos', $data);

            // Insertar submódulos asociados si los hay
            if (isset($submodulos[$modulo])) {
                foreach ($submodulos[$modulo] as $submodulo) {
                    $data = array(
                        'rol_id' => $rol_id,
                        'modulo' => $modulo,
                        'submodulo' => $submodulo,
                        'usucre' => $this->session->userdata('email'),
                        'feccre' => date('Y-m-d H:i:s')
                    );
                    $this->db->insert('permisos', $data);
                }
            }
        }
    }
}
