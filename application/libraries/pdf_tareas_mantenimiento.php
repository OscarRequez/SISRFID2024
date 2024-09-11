<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

// Incluye la biblioteca TCPDF
require_once(APPPATH . 'libraries/tcpdf/tcpdf.php');

class pdf_tareas_mantenimiento extends TCPDF
{
    public function __construct()
    {
        parent::__construct();
    }

    // Puedes agregar métodos personalizados aquí si es necesario
}