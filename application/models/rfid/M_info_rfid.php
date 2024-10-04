<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_info_rfid extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function obtenerEquipoPorCodigo($codigo)
    {
        // Consulta para obtener el equipo según el código RFID
        $this->db->where('tag_rfid_id', $codigo); // Filtramos por el código RFID
        $query = $this->db->get('equipos_medicos'); // Obtenemos el equipo

        if ($query->num_rows() > 0) {
            $equipo = $query->row(); // Obtenemos el primer resultado

            // Obtener el nombre de la ubicación
            $this->db->where('id', $equipo->ubicacion_id);
            $ubicacion = $this->db->get('ubicaciones')->row();
            $equipo->ubicacion_nombre = $ubicacion ? $ubicacion->nombre : 'Desconocido';

            // Obtener el nombre del tipo de equipo
            $this->db->where('id', $equipo->tipo_eq);
            $tipo = $this->db->get('tipo_equipo')->row();
            $equipo->tipo_nombre = $tipo ? $tipo->tipo : 'Desconocido';

            return $equipo; // Devolvemos el equipo con los nombres de ubicación y tipo
        }

        return null; // No se encontró el equipo
    }

    public function obtenerDatosEquipoPorRFID($codigo)
    {
        // Obtener el equipo por el código RFID
        $this->db->select('e.*, COUNT(m.id) AS num_mantenimientos');
        $this->db->from('equipos_medicos e');
        $this->db->join('mantenimiento m', 'e.id = m.equipo_id', 'left');
        $this->db->where('e.tag_rfid_id', $codigo);
        $this->db->group_by('e.id'); // Agrupamos por el ID del equipo
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $equipo = $query->row_array(); // Obtenemos el primer resultado
            // Obtener mantenimientos asociados
            $mantenimientos = $this->obtenerMantenimientosPorEquipo($equipo['id']);
            $equipo['mantenimientos'] = $mantenimientos; // Añadimos los mantenimientos al array del equipo
            return $equipo; // Devolvemos los datos del equipo
        }

        return null; // Si no se encuentra el equipo, retornamos null
    }

    function obtenerMantenimientosPorEquipo($equipo_id)
    {
        $this->db->select('m.*, mm.material, mm.cantidad, mm.costo_unitario, mm.costo_total');
        $this->db->from('mantenimiento m');
        $this->db->join('materiales_mant mm', 'm.id = mm.tarea_id', 'left');
        $this->db->where('m.equipo_id', $equipo_id);
        $query = $this->db->get();

        return $query->result_array(); // Devolvemos todos los mantenimientos
    }


    public function obtenerDatosEquipo($equipoId) {
        // Obtener información general del equipo
        $this->db->select('em.*, e.tipo AS tipo_equipo, u.nombre AS ubicacion_nombre');
        $this->db->from('equipos_medicos em');
        $this->db->join('tipo_equipo e', 'em.tipo_eq = e.id', 'left');
        $this->db->join('ubicaciones u', 'em.ubicacion_id = u.id', 'left'); // Agregar JOIN con la tabla ubicaciones
        $this->db->where('em.id', $equipoId);
        $query = $this->db->get();
    
        return $query->row();
    }
    
    public function obtenerMantenimientos($equipoId) {
        // Obtener historial de mantenimientos
        $this->db->select('m.id, m.fecha_solicitud, m.tipo_mantenimiento, m.costoTotal');
        $this->db->from('mantenimiento m');
        $this->db->where('m.equipo_id', $equipoId);
        $query = $this->db->get();

        return $query->result();
    }

    public function obtenerMovimientos($equipoId) {
        // Obtener historial de movimientos con nombres de ubicaciones
        $this->db->select('me.fecha_movimiento, 
                           ua.nombre AS ubicacion_anterior, 
                           un.nombre AS ubicacion_nueva, 
                           me.motivo');
        $this->db->from('mov_equipos me');
        $this->db->join('ubicaciones ua', 'me.ubicacion_anterior_id = ua.id', 'left'); // JOIN para ubicación anterior
        $this->db->join('ubicaciones un', 'me.ubicacion_nueva_id = un.id', 'left'); // JOIN para ubicación nueva
        $this->db->where('me.equipo_medico_id', $equipoId);
        $query = $this->db->get();
    
        return $query->result();
    }
    


}
