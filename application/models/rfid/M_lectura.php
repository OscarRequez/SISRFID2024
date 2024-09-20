<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_lectura extends CI_Model
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

    // Función para contar el total de equipos en una ubicación específica
    public function cantidad_equipos_ubicacion($ubicacion_id)
    {
        $this->db->select('COUNT(*) as total_equipos');
        $this->db->from('equipos_medicos');
        $this->db->where('ubicacion_id', $ubicacion_id); // Filtra por ubicación
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


    public function obtenerTareasLectura($offset, $cantidad, $buscar = null)
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
            $this->db->or_like('ubicaciones.nombre_ubicacion', $buscar);
            $this->db->or_like('usuarios.nombre', $buscar);
            $this->db->group_end(); // Cerramos el grupo de condiciones
        }

        // Aplicamos la paginación
        $this->db->limit($cantidad, $offset);

        // Ejecutamos la consulta
        $query = $this->db->get();

        // Retornamos los resultados como un array
        return $query->result_array();
    }

  public function contarTareasLectura($buscar = null)
{
    $this->db->select('COUNT(tareas_lectura.id) AS total'); // Contamos el ID de tareas_lectura
    $this->db->from('tareas_lectura');
    $this->db->join('ubicaciones', 'tareas_lectura.ubicacion_tarea = ubicaciones.id', 'left');
    $this->db->join('usuarios', 'tareas_lectura.operario = usuarios.id', 'left');

    // Si hay un término de búsqueda, aplicamos los filtros
    if (!empty($buscar)) {
        $this->db->group_start(); // Abrimos el grupo de condiciones para el OR
        $this->db->like('tareas_lectura.id', $buscar);
        $this->db->or_like('ubicaciones.nombre_ubicacion', $buscar);
        $this->db->or_like('usuarios.nombre', $buscar);
        $this->db->or_like('tareas_lectura.descripcion_tarea', $buscar);
        $this->db->or_like('tareas_lectura.tipo_tarea', $buscar);
        $this->db->group_end(); // Cerramos el grupo de condiciones
    }

    // Ejecutamos la consulta
    $query = $this->db->get();

    // Retornamos el número total de registros
    return $query->row()->total;
}


    //notificaion
    public function nueva_notificacion($data)
    {
        return $this->db->insert('notificaciones', $data);
    }
}
