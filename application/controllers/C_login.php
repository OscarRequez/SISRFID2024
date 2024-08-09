<?php
class C_Login extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->model('M_login');
    }

    public function index()
    {
        $this->load->view('Templates/login_view');
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
                $this->session->set_userdata('role', $user->role);  // ID del rol
                $this->session->set_userdata('rol_name', $user->rol_name); // Nombre del rol
                $this->session->set_userdata('username', $user->nombre); // Guardar el nombre del usuario
    
                // Redirigir a la página principal del sistema
                redirect('welcome');
            } else {
                $this->session->set_flashdata('error', 'Email o contraseña incorrectos.');
                redirect('login_error');
            }
        } else {
            $this->session->set_flashdata('error', 'Email o contraseña incorrectos.');
            redirect('login_error');
        }
    }

    // Método para cerrar sesión
    public function logout()
    {
        // Destruir la sesión
        $this->session->sess_destroy();

        // Redirigir al login después de cerrar sesión
        redirect('login');
    }
}

// FIN OR-02 07/08/2024 OSCAR REQUEZ MODULO DE INICIO DE SESION
