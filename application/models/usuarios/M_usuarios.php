<?php
class M_usuarios extends CI_Model
{

    public function __construct()
    {
        $this->load->database(); // Cargar la base de datos
    }


    public function count_all_users($buscar = '')
    {
        // Filtrar por búsqueda si se proporciona
        if (!empty($buscar)) {
            $this->db->like('nombre', $buscar);
            $this->db->or_like('email', $buscar);
        }

        // Filtrar por estado 1 o 0
        $this->db->where_in('estado', [0, 1]);

        // Contar los resultados
        return $this->db->count_all_results('usuarios');
    }

    // ROLES DE USUARIO PARA CONTADOR Y PAGINACION
    public function buscar_usuarios($limit, $start, $buscar = '')
    {
        // Filtrar por búsqueda si se proporciona
        if (!empty($buscar)) {
            $this->db->like('usuarios.nombre', $buscar);
            $this->db->or_like('usuarios.ci', $buscar);
        }

        // Filtrar por estado 1 o 0 donde 1 activo 0 inactio y 3 es eliminado para que no se muestre en la tabla
        $this->db->where_in('usuarios.estado', [0, 1]);

        // Configurar límite y desplazamiento
        $this->db->limit($limit, $start);

        // Seleccionar campos necesarios y hacer la consulta
        $this->db->select('usuarios.id, usuarios.nombre, usuarios.cedula, usuarios.email, roles.nombre AS rol_nombre, usuarios.estado, usuarios.usucre, usuarios.feccre');
        $this->db->from('usuarios');
        $this->db->join('roles', 'usuarios.rol_id = roles.id');

        // Ejecutar la consulta y devolver los resultados
        $query = $this->db->get();
        return $query->result_array();
    }

    // Verificar si la cédula ya existe
    public function obtener_usuario_por_cedula($cedula)
    {
        $this->db->where('cedula', $cedula);
        $query = $this->db->get('usuarios'); // Cambia 'usuarios' por el nombre real de tu tabla
        if ($query->num_rows() > 0) {
            return $query->row(); // Retorna el usuario si existe
        } else {
            return false; // Retorna false si no existe
        }
    }

    public function get_usuarios_con_roles()
    {
        $this->db->select('usuarios.id, usuarios.nombre, usuarios.email, roles.nombre AS rol_nombre, usuarios.estado, usuarios.usucre, usuarios.feccre,');
        $this->db->from('usuarios');
        $this->db->join('roles', 'usuarios.rol_id = roles.id');
        $query = $this->db->get();
        return $query->result_array();
    }

    // Método para insertar un nuevo usuario
    public function insertar_usuario($data)
    {
        return $this->db->insert('usuarios', $data);
    }


    // Método para actualizar los datos de un usuario
    public function actualizar_usuario($id, $data)
    {
        // Asegurarse de que el ID está presente
        if (!$id || empty($data)) {
            return false;
        }
        // Actualizar el registro en la base de datos
        $this->db->where('id', $id);
        return $this->db->update('usuarios', $data);
    }


    //cambiar estado de usuario 13/08/2024
    public function get_usuario($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('usuarios');
        return $query->row_array();
    }

    public function update_usuario_estado($id, $estado)
    {
        $this->db->where('id', $id);
        $this->db->update('usuarios', ['estado' => $estado]);
    }

    //cambiar estado de usuario 13/08/2024
    // Método para actualizar el estado del usuario
    public function actualizar_usuario_eliminado($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('usuarios', $data);
    }
}
