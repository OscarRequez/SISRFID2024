<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #BDC4DB;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background: #CACDD8;
            /* Color de fondo */
            padding: 30px;
            /* Espaciado interno */
            border-radius: 8px;
            /* Bordes redondeados */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2), 16px 16px rgba(0, 0, 0, 0.19);
            /* Sombras más pronunciadas */
            width: 320;
            /* Ancho del contenedor */
            height: 320;
            /* Alto del contenedor */
            max-width: 100%;
            /* Asegura que no exceda el ancho de su contenedor padre */
            margin: auto;
            /* Centra el contenedor horizontalmente */
        }

        .login-container h2 {
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: bold;
        }

        .btn-primary {
            background-color: #7887C7;
            border-color: #7887C7;
        }

        .btn-primary:hover {
            background-color: #C39BF2;
            border-color: #C39BF2;
        }

        .alert {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4 login-container">
                <center>
                    <h4 style="color:#7887C7"><i class="fs-4 bi bi-person-circle" style="color:#7887C7"> </i>INICIAR SESIÓN</h4>
                </center> <br>
                <?php if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
                <?php endif; ?>
                <form method="post" action="<?php echo site_url('C_login/authenticate'); ?>">
                    <div class="form-group">
                        <label style="color:#7887C7" for="email">Usuario:</label>
                        <input type="text" class="form-control" id="email" name="email" placeholder="Introduce tu email" required>
                    </div>
                    <div class="form-group">
                        <label style="color:#7887C7" for="password">Contraseña:</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Introduce tu contraseña" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Iniciar Sesión</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>