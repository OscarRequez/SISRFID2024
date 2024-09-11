<?php
class M_solicitud extends CI_Model
{

    public function __construct()
    {
        $this->load->database(); // Cargar la base de datos
    }

    public function obtenerSolicitudes($offset, $cantidad)
    {
        // Definir el límite y el offset para la paginación
        $this->db->limit($cantidad, $offset);

        // Realizar la unión entre la tabla 'mantenimiento' y 'ubicaciones'
        $this->db->select('mantenimiento.*, ubicaciones.nombre AS ubicacion_nombre');
        $this->db->from('mantenimiento');
        $this->db->join('ubicaciones', 'mantenimiento.ubicacion_solicitud = ubicaciones.id', 'left');

        // Ordenar por fecha de modificación en orden descendente
        $this->db->order_by('mantenimiento.fecha_solicitud', 'DESC');

        // Ejecutar la consulta
        $query = $this->db->get();

        // Devolver el resultado como un array
        return $query->result_array();
    }

    public function contarSolicitudes()
    {
        return $this->db->count_all('mantenimiento');
    }

    // Verificar si ya existe un proceso activo para el equipo
    public function verificarProcesoActivo($numero_serie_eq)
    {
        $this->db->where('numero_serie_eq', $numero_serie_eq);
        $this->db->where_in('estado', array(1, 2, 4)); // Estado 1 o 2 indica proceso activo
        $query = $this->db->get('mantenimiento');

        return $query->num_rows() > 0;
    }

    // VALIDAMOS SI EL NUMERO DE SERIE EXISTE (CODIGO O RFID)
    public function obtenerEquipoIdPorNumeroSerie($numero_serie_eq)
    {
        $this->db->select('id');
        $this->db->from('equipos_medicos');
        $this->db->where('numero_serie', $numero_serie_eq);
        $this->db->or_where('tag_rfid_id', $numero_serie_eq);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row()->id;
        }
        return false;
    }

    public function insertarSolicitud($data)
    {
        return $this->db->insert('mantenimiento', $data);
    }
}
