<?php
class M_panel_Admin extends CI_Model
{

    public function __construct()
    {
        $this->load->database(); // Cargar la base de datos
    }

    public function obtenerSolicitudesAdmin($offset, $cantidad)
    {
        // Definir el límite y el offset para la paginación
        $this->db->limit($cantidad, $offset);

        // Realizar la unión entre la tabla 'mantenimiento', 'ubicaciones' y 'usuarios'
        $this->db->select('mantenimiento.*, ubicaciones.nombre AS ubicacion_nombre, usuarios.nombre AS usu_tecnico, equipos_medicos.imagen AS Imagen');
        $this->db->from('mantenimiento');
        $this->db->join('ubicaciones', 'mantenimiento.ubicacion_solicitud = ubicaciones.id', 'left');
        $this->db->join('usuarios', 'mantenimiento.usu_tecnico = usuarios.id', 'left'); // Unir con la tabla 'usuarios'
        $this->db->join('equipos_medicos', '(mantenimiento.numero_serie_eq = equipos_medicos.numero_serie OR mantenimiento.numero_serie_eq = equipos_medicos.tag_rfid_id)', 'left'); // Unir con la tabla 'equipos_medicos'

        // Ordenar por fecha de solicitud en orden descendente
        $this->db->order_by('mantenimiento.fecha_solicitud', 'DESC');

        // Ejecutar la consulta
        $query = $this->db->get();

        // Devolver el resultado como un array
        return $query->result_array();
    }
    public function contarSolicitudesAdmin()
    {
        return $this->db->count_all('mantenimiento');
    }


    public function get_tecnicos_por_rol($rol_id)
    {
        // Consulta para obtener técnicos con rol_id = 3
        $this->db->select('id, nombre');
        $this->db->from('usuarios');
        $this->db->where('rol_id', $rol_id);
        $query = $this->db->get();

        // Devolver el resultado
        return $query->result_array();
    }

    // Actualizar la solicitud y asignar técnico con datos de auditoría
    public function asignar_tecnico($solicitud_id, $tecnico_id, $usumod,  $fecAsignacion, $fecmod) {
        // Datos a actualizar: técnico, estado, usumod, y fecmod
        $data = array(
            'usu_tecnico' => $tecnico_id,
            'estado' => 2, // Cambia el estado a "En proceso" o el estado adecuado
            'usumod' => $usumod,
            'fecha_asignacion' => $fecAsignacion,
            'fecmod' => $fecmod
        );

        // Ejecutar la actualización
        $this->db->where('id', $solicitud_id);
        return $this->db->update('mantenimiento', $data);
    }

    // Obtener el equipo_id asociado a una solicitud
    public function equipo_id_para_solicitud($solicitud_id)
    {
        $this->db->select('equipo_id');
        $this->db->where('id', $solicitud_id);
        $query = $this->db->get('mantenimiento');

        if ($query->num_rows() > 0) {
            return $query->row()->equipo_id;
        } else {
            return false;
        }
    }

    // Actualizar el estado del equipo
     public function update_equipo_estado($equipo_id, $usumod, $fecmod, $estado) {
        // Datos a actualizar: estado, usumod, y fecmod
        $data = array(
            'estado' => $estado,
            'usumod' => $usumod,
            'fecmod' => $fecmod
        );

        // Ejecutar la actualización
        $this->db->where('id', $equipo_id);
        return $this->db->update('equipos_medicos', $data);
    }
}
