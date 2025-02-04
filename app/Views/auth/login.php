<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="keywords" content="admin, dashboard" />
	<meta name="author" content="DexignZone" />
	<meta name="robots" content="index, follow" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="MOPHY : Payment Admin Dashboard  Bootstrap 5 Template" />
	<meta property="og:title" content="MOPHY : Payment Admin Dashboard  Bootstrap 5 Template" />
	<meta property="og:description" content="MOPHY : Payment Admin Dashboard  Bootstrap 5 Template" />
	<meta property="og:image" content="https://mophy.dexignzone.com/xhtml/social-image.png"/>
	<meta name="format-detection" content="telephone=no">
    <title>incia sesión</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="./images/favicon.png">
    <link href="<?= base_url('assets/mophy/css/style.css'); ?>" rel="stylesheet">
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>

<body class="h-100">
    <div class="authincation h-100">
        <div class="container h-100">
            <div class="row justify-content-center h-100 align-items-center">
                <div class="col-md-6">
                    <div class="authincation-content">
                        <div class="row no-gutters">
                            <div class="col-xl-12">
                                <div class="auth-form">
                                    <h1>BIENVENIDO</h1>
                                    <p>Ingrese su usuario contraseña</p>
                                    <div id="infoMessage">
                                        <?php if (isset($message)) {
                                            echo $message;
                                        } ?>
                                    </div>
                                    <?php echo form_open('auth/login');?>
                                        <p>
                                            <?php echo form_label(lang('Auth.login_identity_label'), 'identity');?>
                                            <?php echo form_input($identity);?>
                                        </p>
                                        <p>
                                            <?php echo form_label(lang('Auth.login_password_label'), 'password');?>
                                            <?php echo form_input($password);?>
                                        </p>
                                        <p>
                                            <?php echo form_label(lang('Auth.login_remember_label'), 'remember');?>
                                            <?php echo form_checkbox('remember', '1', false, 'id="remember"');?>
                                        </p>
                                        <p><?php echo form_submit('submit', lang('Auth.login_submit_btn'));?></p>
                                    <?php echo form_close();?>

                                    <p><a href="forgot_password"><?php echo lang('Auth.login_forgot_password');?></a></p>

                                    <!-- <form id="loginForm">
                                        <div class="form-group">
                                            <label class="mb-1 text-white"><strong>Usuario</strong></label>
                                            <input type="text" name="usuario" class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="mb-1 text-white"><strong>PIN</strong></label>
                                            <input type="password" name="pin" class="form-control" required>
                                        </div>
                                        <div class="form-row d-flex justify-content-between mt-4 mb-2">
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox ms-1 text-white">
                                                    <input type="checkbox" class="form-check-input" id="basic_checkbox_1">
                                                    <label class="custom-control-label" for="basic_checkbox_1">Recordar mis datos</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" class="btn bg-white text-primary btn-block">Sign Me In</button>
                                        </div>
                                    </form> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="<?= base_url(); ?>assets/mophy/vendor/global/global.min.js"></script>
	<script src="<?= base_url('assets/mophy/vendor/bootstrap-select/dist/js/bootstrap-select.min.js'); ?>"></script>
    <script src="<?= base_url('assets/mophy/js/custom.min.js'); ?>"></script>
    <script src="<?= base_url('assets/mophy/js/deznav-init.js'); ?>"></script>
    <script src="<?= base_url('assets/mophy/js/deznav-init.js'); ?>"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
</body>

</html>