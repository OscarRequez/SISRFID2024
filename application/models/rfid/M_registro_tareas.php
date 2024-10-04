<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_registro_tareas extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }


    // Función para insertar una nueva tarea de lectura
    public function insertar_tarea($data)
    {
        // Inserta los datos en la tabla 'tareas_lectura'
        return $this->db->insert('tareas_lectura', $data);
    }

    // Función para contar el total de equipos en una ubicación específica con estado 1 activo
    public function cantidad_equipos_ubicacion($ubicacion_id)
    {
        $this->db->select('COUNT(*) as total_equipos');
        $this->db->from('equipos_medicos');
        $this->db->where('ubicacion_id', $ubicacion_id); // Filtra por ubicación
        $this->db->where_in('estado', [1, 3]); // Filtra por estado 1 (activo) o 3 (mantenimiento)

        $query = $this->db->get();

        // Retorna el número total de equipos en esa ubicación
        if ($query->num_rows() > 0) {
            return $query->row()->total_equipos;
        } else {
            return 0; // Retorna 0 si no hay equipos en la ubicación
        }
    }

    public function operarios_por_rol($rol_id)
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
    public function asignar_operario($solicitud_id, $operario_id, $usumod,  $fecAsignacion, $fecmod)
    {
        // Datos a actualizar: técnico, estado, usumod, y fecmod
        $data = array(
            'operario' => $operario_id,
            'estado' => 2, // Cambia el estado a "En proceso" o el estado adecuado
            'usumod' => $usumod,
            'fecha_asignacion' => $fecAsignacion,
            'fecmod' => $fecmod
        );

        // Ejecutar la actualización
        $this->db->where('id', $solicitud_id);
        return $this->db->update('tareas_lectura', $data);
    }

    public function obtenerTareasAsignadas($offset, $cantidad, $buscar = null)
    {
        $this->db->select('tareas_lectura.*, 
                       ubicaciones.nombre AS nombre_ubicacion, 
                       usuarios.nombre AS nombre_operario');
        $this->db->from('tareas_lectura');
        $this->db->join('ubicaciones', 'tareas_lectura.ubicacion_tarea = ubicaciones.id', 'left');
        $this->db->join('usuarios', 'tareas_lectura.operario = usuarios.id', 'left');

        // Si hay un término de búsqueda, aplicamos los filtros
        if (!empty($buscar)) {
            $this->db->group_start(); // Abrimos el grupo de condiciones para el OR
            $this->db->like('tareas_lectura.id', $buscar);
            $this->db->or_like('ubicaciones.nombre', $buscar); // Buscar por nombre de ubicación
            $this->db->group_end(); // Cerramos el grupo de condiciones
        }

        // Ordenamos por fecha de creación (feccre) de forma descendente
        $this->db->order_by('tareas_lectura.feccre', 'DESC');

        // Aplicamos la paginación
        $this->db->limit($cantidad, $offset);

        // Ejecutamos la consulta
        $query = $this->db->get();

        // Retornamos los resultados como un array
        return $query->result_array();
    }

    public function contarTareasAsignadas($buscar = null)
    {
        $this->db->select('COUNT(tareas_lectura.id) AS total'); // Contamos el ID de tareas_lectura
        $this->db->from('tareas_lectura');
        $this->db->join('ubicaciones', 'tareas_lectura.ubicacion_tarea = ubicaciones.id', 'left');
        $this->db->join('usuarios', 'tareas_lectura.operario = usuarios.id', 'left');

        // Si hay un término de búsqueda, aplicamos los filtros
        if (!empty($buscar)) {
            $this->db->group_start(); // Abrimos el grupo de condiciones para el OR
            $this->db->like('tareas_lectura.id', $buscar);
            $this->db->or_like('ubicaciones.nombre', $buscar); // Buscar por nombre de ubicación
            $this->db->group_end(); // Cerramos el grupo de condiciones
        }

        // Ejecutamos la consulta
        $query = $this->db->get();

        // Retornamos el número total de registros
        return $query->row()->total;
    }

    // Función para confirmar una tarea
    public function confirmar_tarea_Admin($solicitud_id, $usuario_actual, $fecha_confirmacion) {
        // Datos a actualizar en la tabla de tareas
        $data = array(
            'estado' => 4, // Estado 4: Completado
            'usumod' => $usuario_actual,
            'usuconf' => $usuario_actual,
            'fecha_aprobacion' => $fecha_confirmacion,
            'fecmod' => $fecha_confirmacion 
        );

        // Ejecutar la actualización
        $this->db->where('id', $solicitud_id);
        return $this->db->update('tareas_lectura', $data); // Retorna true si la actualización es exitosa
    }


    //notificaion
    public function nueva_notificacion($data)
    {
        return $this->db->insert('notificaciones', $data);
    }
}
