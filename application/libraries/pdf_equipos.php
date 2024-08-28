<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Incluye la biblioteca TCPDF
require_once(APPPATH . 'libraries/tcpdf/tcpdf.php');

class pdf_equipos extends TCPDF
{
    public function __construct()
    {
        parent::__construct();
    }
}