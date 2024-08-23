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

//usuarios
$route['usuarios'] = 'usuarios/C_usuarios';
$route['roles'] = 'usuarios/C_roles';
$route['obtener_roles_ajax'] = 'usuarios/C_usuarios/obtener_roles_ajax';
$route['guardar_usuario'] = 'usuarios/C_usuarios/guardar';
$route['actualizar_usuario'] = 'usuarios/C_usuarios/actualizar';
$route['cambiar_estado_usuario/(:num)'] = 'usuarios/C_usuarios/cambiar_estado_usuario/$1';
$route['usuarios/mostrar'] = 'usuarios/C_usuarios/mostrar';
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
$route['avatars_eliminar/(:num)'] = 'usuarios/C_avatars/eliminar/$1';// Ruta para eliminar un avatar (este método usa un ID como parámetro)
$route['obtener_avatares'] = 'Welcome/obtener_avatares';
$route['actualizar_avatar'] = 'Welcome/actualizarAvatar';


//EQUIPOS 
$route['equipos'] = 'equipos/C_equipos';// Ruta para la página principal de EQUIPOS
$route['equipos_mostrar'] = 'equipos/C_equipos/mostrar_equipos';
$route['obtener_ubicaciones_select'] = 'equipos/C_equipos/obtener_ubicaciones_para_select';
$route['agregar_equipo'] = 'equipos/C_equipos/agregar_equipo';
$route['editar_equipo'] = 'equipos/C_equipos/editar_equipo';
$route['eliminar_equipo'] = 'equipos/C_equipos/eliminar_equipo';
$route['obtener_motivo_eliminacion'] = 'equipos/C_equipos/obtener_motivo_eliminacion';








