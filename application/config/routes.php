<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['login'] = 'C_login';  // Define 'login' como el controlador predeterminado
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

//login
$route['authenticate'] = 'C_login/authenticate';
$route['welcome'] = 'welcome'; // Ruta de ejemplo para la página de bienvenida
$route['login_error'] = 'C_login'; // CUANDO DA ERROR
$route['logout'] = 'C_login/logout'; //CERRAR SESION

//welcome
$route['cantidad_equipos'] = 'welcome/cantidad_equipos';
$route['distribucion_equipos'] = 'welcome/distribucion_equipos';  
$route['mantenimiento_mostrarSolicitudes'] = 'welcome/mantenimiento_mostrarSolicitudes';
$route['obtenerDatosGraficoSolicitudes'] = 'welcome/obtenerDatosGraficoSolicitudes';
$route['obtener_notificaciones'] = 'welcome/obtener_notificaciones';
$route['marcar_notificacion_leida'] = 'welcome/marcar_notificacion_leida';


//usuarios
$route['usuarios'] = 'usuarios/C_usuarios';
$route['roles'] = 'usuarios/C_roles';
$route['obtener_roles_ajax'] = 'usuarios/C_usuarios/obtener_roles_ajax';
$route['guardar_usuario'] = 'usuarios/C_usuarios/guardar';
$route['actualizar_usuario'] = 'usuarios/C_usuarios/actualizar';
$route['cambiar_estado_usuario/(:num)'] = 'usuarios/C_usuarios/cambiar_estado_usuario/$1';
$route['usuarios_mostrar'] = 'usuarios/C_usuarios/usuarios_mostrar';
$route['eliminar_estado_usuario/(:num)'] = 'usuarios/C_usuarios/eliminar/$1';

//Roles de usuario
$route['usuarios/mostrarRoles'] = 'usuarios/C_roles/mostrarRoles';
$route['permisos/obtenerPermisosUnicos'] = 'usuarios/C_roles/agregarRolCheckbox';
$route['roles/agregar'] = 'usuarios/C_roles/agregar';
$route['usuarios/obtenerPermisosDeUnRol'] = 'usuarios/C_roles/obtenerPermisosDeUnRol/$1';// Ruta para obtener permisos de un rol
$route['checksParaEditar'] = 'usuarios/C_roles/agregarRolCheckbox';
$route['roles_actualizar_Permisos'] = 'usuarios/C_roles/actualizar_permisos';//PARA ACTUALIZAR LOS PERMISOS DE UN ROL

//AVATARES
$route['avatars'] = 'usuarios/C_avatars';// Ruta para la página principal de avatares
$route['avatars_agregar'] = 'usuarios/C_avatars/agregar';// Ruta para agregar un nuevo avatar (puedes usar POST para este método)
$route['eliminar_avatar'] = 'usuarios/C_avatars/eliminar'; // Ruta para eliminar un avatar
$route['obtener_avatares'] = 'Welcome/obtener_avatares';
$route['actualizar_avatar'] = 'Welcome/actualizarAvatar';

//EQUIPOS 
$route['equipos'] = 'equipos/C_equipos';// Ruta para la página principal de EQUIPOS
$route['equipos_mostrar'] = 'equipos/C_equipos/mostrar_equipos';
$route['obtener_ubicaciones_select'] = 'equipos/C_equipos/obtener_ubicaciones_para_select';
$route['obtener_tipos_equipo_select'] = 'equipos/C_equipos/obtener_tipos_equipo_select';
$route['agregar_equipo'] = 'equipos/C_equipos/agregar_equipo';
$route['editar_equipo'] = 'equipos/C_equipos/editar_equipo';
$route['eliminar_equipo'] = 'equipos/C_equipos/eliminar_equipo';
$route['obtener_motivo_eliminacion'] = 'equipos/C_equipos/obtener_motivo_eliminacion';

//LISTADO DE EQUIPOS
$route['listado'] = 'equipos/C_listado_equipos';// Ruta para la página principal de EQUIPOS-LISTADO
$route['mostrar_equipos'] = 'equipos/C_listado_equipos/mostrar_equipos';

//REPORTES EQUIPOS
$route['reportes'] = 'equipos/C_reporte_equipos';// Ruta para la página principal de EQUIPOS-REPORTES
$route['cargar_datos'] = 'equipos/C_reporte_equipos/cargar_datos';
$route['generar_reporte'] = 'equipos/C_reporte_equipos/generar_reporte';
$route['reporte_historial_equipo'] = 'equipos/C_reporte_equipos/reporte_historial_equipo';

//MANTENIMIENTO
//SOLICITUDES
$route['solicitud'] = 'mantenimiento/C_solicitud';// Ruta para la página principal 
$route['solicitudes_obtener'] = 'mantenimiento/C_solicitud/solicitudes_obtener';// Ruta para la página principal de EQUIPOS
$route['crear_solicitud'] = 'mantenimiento/C_solicitud/crear_solicitud';// Ruta para la página principal de EQUIPOS
//PANEL_ADMIN
$route['panel'] = 'mantenimiento/C_panel_admin';// Ruta para la página principal
$route['solicitudes_obtener'] = 'mantenimiento/C_panel_admin/solicitudes_obtener_Admin';// Ruta para la página principal de EQUIPOS
$route['obtener_tecnicos'] = 'mantenimiento/C_panel_admin/obtener_tecnicos';//datos para el select de tecnicos
$route['asignar_tecnico'] = 'mantenimiento/C_panel_admin/asignar_tecnico';// asignar un tecxnico a una solicitud
$route['confirmar_solicitud'] = 'mantenimiento/C_panel_admin/confirmar_solicitud';//
$route['mantenimiento_reporte_fechas'] = 'mantenimiento/C_panel_admin/mantenimiento_reporte_fechas';//

//TECNICOS
$route['tecnicos'] = 'mantenimiento/C_tecnicos';// Ruta
$route['obtener_tareas'] = 'mantenimiento/C_tecnicos/obtener_tareas';// Ruta
$route['marcar_tarea_hecha'] = 'mantenimiento/C_tecnicos/marcar_tarea_hecha';// Ruta
$route['generar_reporte_tareas'] = 'mantenimiento/C_tecnicos/generar_reporte_tareas';

//DOCUMENTACION
$route['registro'] = 'documentacion/C_doc_registro';// Ruta
$route['obtener_documentos'] = 'documentacion/C_doc_registro/obtener_documentos';// Ruta
$route['agregar_documento'] = 'documentacion/C_doc_registro/agregar_documento';// Ruta
$route['editar_documento'] = 'documentacion/C_doc_registro/editar_documento';// Ruta
$route['eliminar_documento'] = 'documentacion/C_doc_registro/eliminar_documento';// Ruta
//DOCUMENTACION solo vista
$route['manuales'] = 'documentacion/C_manuales';// Ruta










