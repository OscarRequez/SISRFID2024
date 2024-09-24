<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_lectura extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }


    public function obtenerTareasLectura($offset, $cantidad, $usuario, $buscar = null)
    {
        $this->db->select('tareas_lectura.*, 
                       ubicaciones.nombre AS nombre_ubicacion, 
                       usuarios.nombre AS nombre_operario');
        $this->db->from('tareas_lectura');
        $this->db->join('ubicaciones', 'tareas_lectura.ubicacion_tarea = ubicaciones.id', 'left');
        $this->db->join('usuarios', 'tareas_lectura.operario = usuarios.id', 'left');

        // Filtro por usuario
        $this->db->where('tareas_lectura.operario', $usuario); 

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

    public function contarTareasLectura($usuario, $buscar = null)
    {
        $this->db->select('COUNT(tareas_lectura.id) AS total'); // Contamos el ID de tareas_lectura
        $this->db->from('tareas_lectura');
        $this->db->join('ubicaciones', 'tareas_lectura.ubicacion_tarea = ubicaciones.id', 'left');
        $this->db->join('usuarios', 'tareas_lectura.operario = usuarios.id', 'left');

        // Filtro por usuario
        $this->db->where('tareas_lectura.operario', $usuario);

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


    // Validar los tags RFID, eliminando duplicados
    public function validarTags($tags)
    {
        // Eliminar duplicados y filtrar los vacíos
        $tags_unicos = array_unique(array_filter(explode("\n", trim($tags))));
        return $tags_unicos;
    }

    // Verificar si los tags ya están registrados para una tarea específica
    public function verificarTagsRegistrados($tags, $tarea_id)
    {
        $this->db->where_in('rfid_tag', $tags);
        $this->db->where('tarea_id', $tarea_id);
        return $this->db->get('resultados_lectura')->result_array();
    }

    // Obtener los datos de la tarea de lectura por ID
    public function obtenerTareaPorId($tarea_id)
    {
        return $this->db->get_where('tareas_lectura', ['id' => $tarea_id])->row();
    }

    // Obtener equipos médicos por los tags leídos
    public function obtenerEquiposPorTags($tags)
    {
        $this->db->where_in('tag_rfid_id', $tags);
        return $this->db->get('equipos_medicos')->result_array();
    }

    // Insertar los resultados de la lectura
    public function insertarResultadosLectura($resultados)
    {
        return $this->db->insert_batch('resultados_lectura', $resultados);
    }

    // Actualizar los contadores en la tarea de lectura
    public function actualizarTareaLectura($tarea_id, $datos_actualizados)
    {
        $this->db->where('id', $tarea_id);
        return $this->db->update('tareas_lectura', $datos_actualizados);
    }

    //confirmar tarea

    public function actualizar_tarea($idTarea, $data)
    {
        // Verificar si el ID de la tarea es válido
        $this->db->where('id', $idTarea);
        return $this->db->update('tareas_lectura', $data); // Actualizar la tarea en la base de datos
    }


    // para el reporte:
    // Método para obtener datos de la tarea por su ID

    public function obtenerDatosTareaPorId($tareaId)
    {
        // Obtener la información de la tarea incluyendo el nombre de la ubicación y del operario
        $this->db->select('tl.*, u.nombre AS nombre_operario, u2.nombre AS nombre_ubicacion');
        $this->db->from('tareas_lectura tl');
        $this->db->join('usuarios u', 'tl.operario = u.id', 'left'); // Unir con la tabla usuarios para obtener el nombre del operario
        $this->db->join('ubicaciones u2', 'tl.ubicacion_tarea = u2.id', 'left'); // Unir con la tabla ubicaciones para obtener el nombre de la ubicación
        $this->db->where('tl.id', $tareaId);
        $query_tarea = $this->db->get();
        $tarea = $query_tarea->row_array();

        if ($tarea) {
            // Obtener los resultados relacionados a la tarea incluyendo el nombre de la ubicación del equipo
            $this->db->select('rl.*, u3.nombre AS nombre_ubicacion_eq');
            $this->db->from('resultados_lectura rl');
            $this->db->join('ubicaciones u3', 'rl.ubicacion_eq = u3.id', 'left'); // Unir con la tabla ubicaciones para obtener el nombre de la ubicación del equipo
            $this->db->where('rl.tarea_id', $tareaId);
            $query_resultados = $this->db->get();
            $resultados = $query_resultados->result_array();

            // Filtrar los resultados según su pertenencia al área de la tarea
            $equipos_leidos = [];
            $equipos_no_pertenecientes = [];
            $ids_equipos_leidos = []; // Para guardar los IDs de equipos leídos

            foreach ($resultados as $resultado) {
                // Verificar si el equipo pertenece a la ubicación de la tarea
                if ($resultado['ubicacion_eq'] == $tarea['ubicacion_tarea']) {
                    $equipos_leidos[] = $resultado; // Equipos que pertenecen
                    $ids_equipos_leidos[] = $resultado['equipo_id']; // Guardar el ID del equipo leído
                } else {
                    $equipos_no_pertenecientes[] = $resultado; // Equipos que no pertenecen
                }
            }

            // Obtener todos los equipos que deberían haber sido leídos para esta ubicación
            $this->db->select('em.*');
            $this->db->from('equipos_medicos em');
            $this->db->where('em.ubicacion_id', $tarea['ubicacion_tarea']);
            $query_equipos_ubicacion = $this->db->get();
            $equipos_ubicacion = $query_equipos_ubicacion->result_array();

            // Filtrar equipos no leídos
            $equipos_no_leidos = array_filter($equipos_ubicacion, function ($equipo) use ($ids_equipos_leidos) {
                return !in_array($equipo['id'], $ids_equipos_leidos);
            });

            // Combinar la información de la tarea y los resultados
            return [
                'tarea' => $tarea,
                'equipos_leidos' => $equipos_leidos, // Equipos que pertenecen
                'equipos_no_pertenecientes' => $equipos_no_pertenecientes, // Equipos que no pertenecen
                'equipos_no_leidos' => $equipos_no_leidos, // Equipos que no fueron leídos
            ];
        }

        // Si no se encuentra la tarea, retornar null
        return null;
    }

    //notificaion
    public function nueva_notificacion($data)
    {
        return $this->db->insert('notificaciones', $data);
    }
}
