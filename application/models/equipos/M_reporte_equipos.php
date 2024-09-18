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
        if ($estado !== null && $estado !== '') {
            $this->db->where('equipos_medicos.estado', $estado);
        }

        // Filtrar por ubicación si está presente
        if ($ubicacion !== null && $ubicacion !== '') {
            $this->db->where('equipos_medicos.ubicacion_id', $ubicacion);
        }

        // Filtrar por búsqueda en nombre y descripción si está presente
        if ($search !== null && $search !== '') {
            $this->db->group_start();
            $this->db->like('equipos_medicos.nombre', $search);
            $this->db->or_like('equipos_medicos.descripcion', $search);
            $this->db->group_end();
        }

        // Filtrar por número de serie o tag RFID si está presente
        if ($numero_serie !== null && $numero_serie !== '') {
            $this->db->group_start();  // Inicia un grupo para condiciones OR
            $this->db->like('equipos_medicos.numero_serie', $numero_serie);
            $this->db->or_like('equipos_medicos.tag_rfid_id', $numero_serie);
            $this->db->group_end(); // Finaliza el grupo
        }

        // Ejecutar la consulta y obtener los resultados
        $query = $this->db->get();
        return $query->result_array();
    }




    public function get_equipos($filtros, $pagina)
    {
        $limit = 5; // Número de resultados por página
        $offset = ($pagina - 1) * $limit;

        // Construcción de la consulta
        $this->db->select('equipos_medicos.id, equipos_medicos.nombre, equipos_medicos.numero_serie, equipos_medicos.tag_rfid_id, ubicaciones.nombre as ubicacion_nombre, equipos_medicos.estado');
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


    public function obtenerDatosEquipoPorId($equipo_id)
    {
        if (empty($equipo_id)) {
            throw new Exception('El ID del equipo no puede estar vacío.');
        }

        // Obtener detalles actuales del equipo médico y el nombre de la ubicación
        $this->db->select('e.id, e.numero_serie, e.tag_rfid_id, e.ubicacion_id, e.estado, u.nombre AS ubicacion_nombre');
        $this->db->from('equipos_medicos e');

        // Hacer JOIN con la tabla ubicaciones usando el campo ubicacion_id
        $this->db->join('ubicaciones u', 'e.ubicacion_id = u.id', 'left');

        // Filtrar por equipo_id
        $this->db->where('e.id', $equipo_id);
        $query = $this->db->get();

        // Verifica si la consulta devolvió algún resultado
        if ($query->num_rows() > 0) {
            $datosEquipo = $query->row_array(); // Usamos row_array ya que solo debería haber un resultado
        } else {
            // Si no se encuentra el equipo, manejar el error
            $datosEquipo = []; // O podrías lanzar una excepción o manejar el error de otra manera
        }

        // Obtener el historial de movimientos del equipo, incluyendo el nombre del usuario que hizo el movimiento
        $this->db->select('m.id, m.ubicacion_anterior_id, ua.nombre as ubicacion_anterior_nombre, 
                            m.ubicacion_nueva_id, un.nombre as ubicacion_nueva_nombre, 
                            m.fecha_movimiento, m.usuario_id, u.nombre as nombre_usuario, 
                            m.motivo'); // Agrega el campo motivo aquí
        $this->db->from('mov_equipos m');
        $this->db->join('ubicaciones ua', 'm.ubicacion_anterior_id = ua.id', 'left');
        $this->db->join('ubicaciones un', 'm.ubicacion_nueva_id = un.id', 'left');
        $this->db->join('usuarios u', 'm.usuario_id = u.id', 'left'); // Join con la tabla usuarios para obtener el nombre del usuario
        $this->db->where('m.equipo_medico_id', $equipo_id);
        $this->db->order_by('m.fecha_movimiento', 'ASC');
        $query = $this->db->get();

        $historialMovimientos = $query->result_array(); // Puede haber múltiples registros

        return [
            'datosEquipo' => $datosEquipo,
            'historialMovimientos' => $historialMovimientos
        ];
    }



    public function obtenerHistorialMantenimientosPorEquipoId($equipoId)
    {
        // Selecciona todos los campos de la tabla mantenimiento, el nombre de la ubicación, el nombre del técnico y el ID del mantenimiento
        $this->db->select('mantenimiento.id, mantenimiento.*, ubicaciones.nombre AS nombre_ubicacion, usuarios.nombre AS nombre_tecnico');

        // Define la tabla principal
        $this->db->from('mantenimiento');

        // Hacer JOIN con la tabla ubicaciones usando el campo ubicacion_solicitud
        $this->db->join('ubicaciones', 'mantenimiento.ubicacion_solicitud = ubicaciones.id', 'left');

        // Hacer JOIN con la tabla usuarios usando el campo usu_tecnico
        $this->db->join('usuarios', 'mantenimiento.usu_tecnico = usuarios.id', 'left');

        // Filtrar por equipo_id
        $this->db->where('mantenimiento.equipo_id', $equipoId);

        // Ejecutar la consulta
        $query = $this->db->get();

        // Retornar el resultado
        return $query->result_array();
    }
}
