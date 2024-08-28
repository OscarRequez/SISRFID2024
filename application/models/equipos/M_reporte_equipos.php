<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_reporte_equipos extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database(); // Asegúrate de cargar la base de datos
    }

    public function obtener_ubicaciones()
    {
        $query = $this->db->get('ubicaciones');
        return $query->result_array();
    }

    public function obtenerDatosSinPaginacion($estado, $ubicacion, $search, $numero_serie)
    {
        // Selecciona todas las columnas de equipos_medicos y el nombre de la ubicación
        $this->db->select('equipos_medicos.*, ubicaciones.nombre as ubicacion_nombre');
        $this->db->from('equipos_medicos');
        
        // Realiza un JOIN con la tabla ubicaciones para obtener el nombre de la ubicación
        $this->db->join('ubicaciones', 'equipos_medicos.ubicacion_id = ubicaciones.id', 'left');
        
        // Filtrar por estado si está presente
        if ($estado) {
            $this->db->where('equipos_medicos.estado', $estado);
        }
        
        // Filtrar por ubicación si está presente
        if ($ubicacion) {
            $this->db->where('equipos_medicos.ubicacion_id', $ubicacion);
        }
        
        // Filtrar por búsqueda en nombre y descripción si está presente
        if ($search) {
            $this->db->group_start();
            $this->db->like('equipos_medicos.nombre', $search);
            $this->db->or_like('equipos_medicos.descripcion', $search);
            $this->db->group_end();
        }
        
        // Filtrar por número de serie o tag RFID si está presente
        if ($numero_serie) {
            $this->db->group_start();
            $this->db->like('equipos_medicos.numero_serie', $numero_serie);
            $this->db->or_like('equipos_medicos.tag_rfid_id', $numero_serie);
            $this->db->group_end();
        }
        
        // Ejecutar la consulta y obtener los resultados
        $query = $this->db->get();
        return $query->result_array();
    }



    public function get_equipos($filtros, $pagina) {
        $limit = 10; // Número de resultados por página
        $offset = ($pagina - 1) * $limit;
    
        // Construcción de la consulta
        $this->db->select('equipos_medicos.nombre, equipos_medicos.numero_serie, equipos_medicos.tag_rfid_id, ubicaciones.nombre as ubicacion_nombre, equipos_medicos.estado');
        $this->db->from('equipos_medicos');
        $this->db->join('ubicaciones', 'equipos_medicos.ubicacion_id = ubicaciones.id', 'left'); // Corregido nombre de tabla y condición de join
    
        // Aplicar filtros
        if (isset($filtros['ubicacion']) && $filtros['ubicacion'] !== '') {
            $this->db->where('equipos_medicos.ubicacion_id', $filtros['ubicacion']);
        }
        if (isset($filtros['estado']) && $filtros['estado'] !== '') {
            $this->db->where('equipos_medicos.estado', $filtros['estado']);
        }
        if (!empty($filtros['search'])) {
            $this->db->group_start();
            $this->db->like('equipos_medicos.nombre', $filtros['search']);
            $this->db->or_like('equipos_medicos.descripcion', $filtros['search']);
            $this->db->group_end();
        }
        if (!empty($filtros['numero_serie'])) {
            $this->db->group_start();  // Inicia un grupo para condiciones OR
            $this->db->like('equipos_medicos.numero_serie', $filtros['numero_serie']);
            $this->db->or_like('equipos_medicos.tag_rfid_id', $filtros['numero_serie']);
            $this->db->group_end(); // Finaliza el grupo
        }
        
        $this->db->limit($limit, $offset);
    
        $query = $this->db->get();
        $equipos = $query->result_array();
    
        // Contar el total de registros
        $this->db->from('equipos_medicos');
        $this->db->join('ubicaciones', 'equipos_medicos.ubicacion_id = ubicaciones.id', 'left'); // Corregido nombre de tabla y condición de join
    
        // Aplicar los mismos filtros para contar
        if (isset($filtros['ubicacion']) && $filtros['ubicacion'] !== '') {
            $this->db->where('equipos_medicos.ubicacion_id', $filtros['ubicacion']);
        }
        if (isset($filtros['estado']) && $filtros['estado'] !== '') {
            $this->db->where('equipos_medicos.estado', $filtros['estado']);
        }
        if (!empty($filtros['search'])) {
            $this->db->group_start();
            $this->db->like('equipos_medicos.nombre', $filtros['search']);
            $this->db->or_like('equipos_medicos.descripcion', $filtros['search']);
            $this->db->group_end();
        }
        if (!empty($filtros['numero_serie'])) {
            $this->db->group_start();  // Inicia un grupo para condiciones OR
            $this->db->like('equipos_medicos.numero_serie', $filtros['numero_serie']);
            $this->db->or_like('equipos_medicos.tag_rfid_id', $filtros['numero_serie']);
            $this->db->group_end(); // Finaliza el grupo
        }
    
        $total_registros = $this->db->count_all_results();
    
        return [
            'equipos' => $equipos,
            'totalregistros' => $total_registros,
            'cantidad' => $limit
        ];
    }
    
}
