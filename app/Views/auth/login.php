<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SISTEMA CONFIGURADOR DE MAPAS INTERACTIVOS</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/mophy/images/sta.jpg'); ?>">

    <!-- Estilos -->
    <link href="<?= base_url('assets/mophy/css/style.css'); ?>" rel="stylesheet">
</head>

<body class="h-100">
    <div class="authincation h-100">
        <div class="container h-100">
            <div class="row justify-content-center h-100 align-items-center">
                <div class="col-md-6">
                    <div class="authincation-content">
                        <div class="row no-gutters">
                            <div class="col-xl-12">
                                <div class="auth-form"style="background-color: white;">
                                    <div class="text-center mb-2">
                                        <a href="<?= base_url(); ?>">
                                            <img src="<?= base_url('assets/mophy/images/logocompleto.png'); ?>" alt="Logo" class="img-fluid">
                                        </a>
                                    </div>
                                    <h1 class="text-center mb-2 text-black fs-3 fw-bold">SISTEMA CONFIGURADOR DE MAPAS INTERACTIVOS</h1>
                                    <h4 class="text-center mb-4 text-black">Ingrese su usuario y contraseña</h4>

                                    <!-- Mensajes de error -->
                                    <?php if (isset($message)): ?>
                                        <div class="alert alert-danger"><?= $message; ?></div>
                                    <?php endif; ?>

                                    <?= form_open('auth/login'); ?>
                                    <div class="form-group">
                                        <label class="mb-1 text-black"><strong>Email/Username</strong></label>
                                        <?= form_input($identity, '', 'class="form-control" placeholder="Ingrese su email o usuario"'); ?>
                                    </div>
                                    <div class="form-group">
                                        <label class="mb-1 text-black"><strong>Contraseña</strong></label>
                                        <?= form_password($password, '', 'class="form-control" placeholder="Ingrese su contraseña"'); ?>
                                    </div>
                                    <div class="form-row d-flex justify-content-between mt-4 mb-2">
                                        <div class="form-group">

                                        </div>

                                    </div>
                                    <div class="text-center">
                                        <?= form_submit('submit', 'Iniciar sesión', 'class="btn btn-primary btn-rounded"'); ?>
                                    </div>
                                    <?= form_close(); ?>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="<?= base_url('assets/mophy/vendor/global/global.min.js'); ?>"></script>
    <script src="<?= base_url('assets/mophy/vendor/bootstrap-select/dist/js/bootstrap-select.min.js'); ?>"></script>
    <script src="<?= base_url('assets/mophy/js/custom.min.js'); ?>"></script>
    <script src="<?= base_url('assets/mophy/js/deznav-init.js'); ?>"></script>

</body>

</html>