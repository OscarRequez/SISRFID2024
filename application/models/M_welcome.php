<?php
class M_welcome extends CI_Model
{
    public function __construct()
    {
        $this->load->database(); // Cargar la base de datos
    }


    //avatares para el perfil
    public function obtener_avatares()
    {
        $query = $this->db->get('avatars'); // Nombre de la tabla de avatares
        return $query->result_array();
    }
    public function obtenerAvatarUnicoPorId($avatarId)
    {
        $this->db->select('ruta_imagen');
        $this->db->from('avatars');
        $this->db->where('id', $avatarId);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row(); // Devuelve un objeto con la ruta de la imagen del avatar
        } else {
            return null; // No se encontró el avatar
        }
    }

    public function actualizarAvatar($userId, $avatarId)
    {
        $data = ['ImagenUsu' => $avatarId];
        $this->db->where('id', $userId);
        return $this->db->update('usuarios', $data);
    }

    //CANTIDAD TOTAL DE EQUIPOS
    public function contar_equipos() {
        return $this->db->count_all('equipos_medicos'); // Reemplaza 'equipos' con el nombre de tu tabla
    }

    public function contar_equipos_por_estado() {
        $this->db->select('estado, COUNT(*) as cantidad');
        $this->db->group_by('estado');
        $query = $this->db->get('equipos_medicos'); // Reemplaza 'equipos' con el nombre de tu tabla

        $result = $query->result_array();
        return $result;
    }

    //datos para la tabla 
    public function obtenerSolicitudes($limit, $offset) {
         // Definir el límite y el offset para la paginación
    $this->db->limit($limit, $offset);
    
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

    public function contarSolicitudes() {
        return $this->db->count_all('mantenimiento');
    }



    //PARA EL GRAFICO DE SOLICITUDES POR MES
    public function obtenerSolicitudesPorMes() {
        $this->db->select('MONTH(fecha_solicitud) as mes, COUNT(id) as total');
        $this->db->from('mantenimiento');
        $this->db->group_by('MONTH(fecha_solicitud)');
        $this->db->order_by('mes', 'ASC');
        $query = $this->db->get();
    
        $resultados = $query->result_array();
    
        // Crear un array con los meses del año inicializados en 0
        $datos_mensuales = array_fill(1, 12, 0);
    
        // Rellenar el array con los datos obtenidos de la consulta
        foreach ($resultados as $resultado) {
            $datos_mensuales[$resultado['mes']] = $resultado['total'];
        }
    
        return $datos_mensuales; // Devuelve un array con los 12 meses
    }

    public function get_notificaciones($usuario_id, $rol_usuario) {
        $this->db->select('*');
        $this->db->from('notificaciones');
        $this->db->group_start();
        $this->db->where('usuario_id', $usuario_id);
        $this->db->or_where('rol', $rol_usuario);
        $this->db->group_end();
        $this->db->order_by('feccre', 'DESC');
        
        $query = $this->db->get();
        return $query->result_array();
    }
    
}
