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
    public function asignar_tecnico($solicitud_id, $tecnico_id, $usumod,  $fecAsignacion, $fecmod)
    {
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

    // Actualizar el estado del equipo para asignar tecnico
    public function update_equipo_estado($equipo_id, $usumod, $fecmod, $estado)
    {
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

    //confirmar la reparacion
    // Método para calcular el costo total
    public function calcular_costo_total($solicitud_id)
    {
        $this->db->select_sum('costo_total');
        $this->db->where('tarea_id', $solicitud_id);
        $query = $this->db->get('materiales_mant');

        $resultado = $query->row();
        return $resultado->costo_total ? $resultado->costo_total : 0.00;
    }

    public function confirmar_solicitud($solicitud_id, $equipo_id, $usuario_actual, $fecha_actual, $costo_total)
    {
        // Iniciar una transacción
        $this->db->trans_begin();

        // Datos a actualizar en la tabla "mantenimiento"
        $data_mantenimiento = [
            'estado' => 3,                 // Estado confirmado
            'fecmod' => $fecha_actual,     // Fecha de modificación
            'fecha_conclusion' => $fecha_actual,     // Fecha de modificación
            'usuconf' => $usuario_actual,   // Usuario que modificó
            'usumod' => $usuario_actual,   // Usuario que modificó
            'costoTotal' => $costo_total    // Costo total calculado
        ];

        // Actualizar el estado y campos de auditoría en la tabla "mantenimiento"
        $this->db->where('id', $solicitud_id);
        $this->db->update('mantenimiento', $data_mantenimiento);

        // Datos a actualizar en la tabla "equipos_medicos"
        $data_equipos = [
            'estado' => 1,                 // Estado activo
            'fecmod' => $fecha_actual,     // Fecha de modificación
            'usumod' => $usuario_actual    // Usuario que modificó
        ];

        // Actualizar el estado y campos de auditoría en la tabla "equipos_medicos"
        $this->db->where('id', $equipo_id);
        $this->db->update('equipos_medicos', $data_equipos);

        // Verificar si ambas actualizaciones fueron exitosas
        if ($this->db->trans_status() === FALSE) {
            // Si algo falla, hacer rollback
            $this->db->trans_rollback();
            return false;
        } else {
            // Si todo está bien, hacer commit
            $this->db->trans_commit();
            return true;
        }
    }

    // Obtener datos de mantenimiento en el rango de fechas dado
    public function obtenerDatosPorFechas($fecha_inicio_formulario, $fecha_fin_formulario)
    {
        // Seleccionar los mantenimientos y datos relacionados
        $this->db->select('mantenimiento.id, mantenimiento.numero_serie_eq, mantenimiento.descripcion, 
                      mantenimiento.usu_tecnico, usuarios.nombre as tecnico_nombre, 
                      mantenimiento.fecha_solicitud, mantenimiento.fecha_conclusion, 
                      mantenimiento.estado, mantenimiento.costoTotal,
                      mantenimiento.observaciones, 
                      SUM(materiales_mant.cantidad * materiales_mant.costo_unitario) AS total_materiales')
            ->from('mantenimiento')
            ->join('usuarios', 'mantenimiento.usu_tecnico = usuarios.id') // Relación con la tabla de usuarios
            ->join('materiales_mant', 'mantenimiento.id = materiales_mant.tarea_id', 'left') // Relación con la tabla de materiales
            ->where('mantenimiento.fecha_solicitud >=', $fecha_inicio_formulario)
            ->where('mantenimiento.fecha_conclusion <=', $fecha_fin_formulario)
            ->group_by('mantenimiento.id'); // Agrupar por mantenimiento

        $query = $this->db->get();
        return $query->result_array(); // Retornar los resultados en forma de array
    }
    // Obtener el técnico que más mantenimientos realizó en el rango de fechas
    public function obtenerTecnicoConMasMantenimientos($fecha_inicio_formulario, $fecha_fin_formulario)
    {
        $this->db->select('mantenimiento.usu_tecnico, usuarios.nombre as tecnico_nombre, COUNT(mantenimiento.id) as cantidad_mantenimientos')
            ->from('mantenimiento')
            ->join('usuarios', 'mantenimiento.usu_tecnico = usuarios.id')
            ->where('mantenimiento.fecha_solicitud >=', $fecha_inicio_formulario)
            ->where('mantenimiento.fecha_conclusion <=', $fecha_fin_formulario)
            ->group_by('mantenimiento.usu_tecnico')
            ->order_by('cantidad_mantenimientos', 'DESC')
            ->limit(1); // Obtener solo el técnico con más mantenimientos

        $query = $this->db->get();
        return $query->row_array(); // Retornar el resultado como un solo registro
    }

    // Obtener la lista de todos los técnicos y la cantidad de mantenimientos realizados
    public function obtenerTecnicosConMantenimientos($fecha_inicio_formulario, $fecha_fin_formulario)
    {
        $this->db->select('mantenimiento.usu_tecnico, usuarios.nombre as tecnico_nombre, COUNT(mantenimiento.id) as cantidad_mantenimientos')
            ->from('mantenimiento')
            ->join('usuarios', 'mantenimiento.usu_tecnico = usuarios.id')
            ->where('mantenimiento.fecha_solicitud >=', $fecha_inicio_formulario)
            ->where('mantenimiento.fecha_conclusion <=', $fecha_fin_formulario)
            ->group_by('mantenimiento.usu_tecnico')
            ->order_by('cantidad_mantenimientos', 'DESC'); // Ordenar por cantidad de mantenimientos, descendente

        $query = $this->db->get();
        return $query->result_array(); // Retornar todos los técnicos con sus mantenimientos
    }

    // Obtener el gasto total por mes en el año actual
    public function obtenerGastosPorMes()
    {
        $this->db->select('MONTH(fecha_conclusion) as mes, SUM(costoTotal) as total_gastos')
            ->from('mantenimiento')
            ->where('YEAR(fecha_conclusion)', date('Y')) // Filtrar por el año actual
            ->group_by('MONTH(fecha_conclusion)') // Agrupar por mes
            ->order_by('mes', 'ASC'); // Ordenar por mes de manera ascendente

        $query = $this->db->get();
        return $query->result_array(); // Retornar los resultados en forma de array
    }

    //notificaion
    public function nueva_notificacion($data) {
        return $this->db->insert('notificaciones', $data);
    }
}
