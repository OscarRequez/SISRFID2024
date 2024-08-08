<?php
class Login extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_login');  // Asegúrate de que este es el modelo correcto para la autenticación
        $this->load->library('session');
        $this->load->helper('url');
    }

    public function index()
    {
        $this->load->view('login_view');
    }

    // INICIO OR-02 07/08/2024 OSCAR REQUEZ MODULO DE INICIO DE SESION
    public function authenticate()
    {
        $email = trim($this->input->post('email'));
        $password = trim($this->input->post('password'));
    
        // Validar usuario usando el email
        $user = $this->M_login->validate_user($email);
    
        if ($user) {
            // Verificar si la contraseña es correcta encriptada con password_verify (BCRYPT)
            if (password_verify($password, $user->contraseña)) {
                // Guardar la información del usuario en la sesión
                $this->session->set_userdata('user_id', $user->id);
                $this->session->set_userdata('role', $user->role);
    
                // Redirigir a la página principal del sistema
                redirect('usuarios');
            } else {
                $this->session->set_flashdata('error', 'Email o contraseña incorrectos.');
                redirect('login');
            }
        } else {
            $this->session->set_flashdata('error', 'Email o contraseña incorrectos.');
            redirect('login');
        }
    }
}
// FIN OR-02 07/08/2024 OSCAR REQUEZ MODULO DE INICIO DE SESION