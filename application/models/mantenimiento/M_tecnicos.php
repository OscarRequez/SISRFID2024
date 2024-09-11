<?php
class M_tecnicos extends CI_Model
{
    public function __construct()
    {
        parent::__construct(); // Llama al constructor del padre
        $this->load->database(); // Carga la base de datos
    }

    // Obtiene las tareas del usuario con paginación
    public function obtener_tareas($cantidad, $offset, $usuario_id)
    {
        $this->db->limit($cantidad, $offset);
        $this->db->select('mantenimiento.*, ubicaciones.nombre AS ubicacion_nombre, usuarios.nombre AS usu_tecnico, equipos_medicos.imagen AS Imagen');
        $this->db->from('mantenimiento');
        $this->db->join('ubicaciones', 'mantenimiento.ubicacion_solicitud = ubicaciones.id', 'left');
        $this->db->join('usuarios', 'mantenimiento.usu_tecnico = usuarios.id', 'left');
        $this->db->join('equipos_medicos', '(mantenimiento.numero_serie_eq = equipos_medicos.numero_serie OR mantenimiento.numero_serie_eq = equipos_medicos.tag_rfid_id)', 'left');
        $this->db->where('mantenimiento.usu_tecnico', $usuario_id);
        $this->db->order_by('mantenimiento.fecha_solicitud', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // Cuenta el número de tareas asignadas a un usuario
    public function contar_tareas($usuario_id)
    {
        $this->db->where('usu_tecnico', $usuario_id);
        return $this->db->count_all_results('mantenimiento');
    }

    // Inserta un nuevo material
    public function insertar_material($data)
    {
        return $this->db->insert('materiales_mant', $data);
    }

    // Actualiza el estado y las observaciones de una tarea
    public function actualizar_mantenimiento($tarea_id, $estado, $observaciones = null)
    {
        $data = array('estado' => $estado);
        if ($observaciones !== null) {
            $data['observaciones'] = $observaciones;
        }
        $this->db->where('id', $tarea_id);
        return $this->db->update('mantenimiento', $data);
    }
<<<<<<< HEAD

    public function obtenerDatosTareaPorId($id)
    {
        // Verifica que el ID sea un número válido
        if (!is_numeric($id)) {
            return false; // O maneja el error de otra manera
        }

        // Consulta para obtener los datos de la tarea y los materiales relacionados, incluyendo el nombre del técnico y la ubicación
        $this->db->select('
        mantenimiento.id, mantenimiento.numero_serie_eq, mantenimiento.equipo_id, mantenimiento.descripcion,
        usuarios.nombre AS nombre_tecnico, mantenimiento.tipo_mantenimiento, mantenimiento.prioridad,
        ubicaciones.nombre AS nombre_ubicacion, mantenimiento.observaciones, mantenimiento.fecha_solicitud,
        mantenimiento.fecha_asignacion, mantenimiento.fecha_conclusion, mantenimiento.estado,
        materiales_mant.id AS material_id, materiales_mant.material, materiales_mant.cantidad,
        materiales_mant.costo_unitario, materiales_mant.costo_total, materiales_mant.factura,
        materiales_mant.estado AS material_estado
    ');
        $this->db->from('mantenimiento');
        $this->db->join('usuarios', 'usuarios.id = mantenimiento.usu_tecnico', 'left'); // Join con la tabla usuarios
        $this->db->join('ubicaciones', 'ubicaciones.id = mantenimiento.ubicacion_solicitud', 'left'); // Join con la tabla ubicaciones
        $this->db->join('materiales_mant', 'materiales_mant.tarea_id = mantenimiento.id', 'left');
        $this->db->where('mantenimiento.id', $id);
        $query = $this->db->get();

        // Devuelve los datos de la tarea y los materiales, si existe
        if ($query->num_rows() > 0) {
            return $query->result_array(); // Devuelve un array de resultados
        } else {
            return false; // O maneja el caso en que no se encuentra la tarea
        }
    }
=======
>>>>>>> 67684c6a8174cde41beb7ad36074a1796d6d3059
}
