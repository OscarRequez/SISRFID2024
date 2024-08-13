<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Welcome extends MY_Controller
{

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/userguide3/general/urls.html
	 */
	public function __construct()
	{
		parent::__construct();
		//permisos
		$this->load->model('M_permisos');
		$rol_id = $this->session->userdata('role');
		if (!$this->M_permisos->tiene_permiso($rol_id, 'welcome')) {
			redirect('no_autorizado'); // Redirigir a una página de no autorizado

			// Verificar si el usuario está autenticado
			if (!$this->session->userdata('user_id')) {
				// Si no está autenticado, redirigir al login
				redirect('login');
			}
		}
	}
	public function index()
	{
		$this->load->view('welcome_message');
	}
}
