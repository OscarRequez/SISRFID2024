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
}
