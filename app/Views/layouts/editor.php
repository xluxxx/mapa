<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title> SISTEMA CONFIGURADOR DE MAPAS INTERACTIVOS</title>
	<!-- Favicon icon -->
	<link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/mophy/images/logod.png')?>">
	<link href="<?= base_url('assets/mophy/vendor/jqvmap/css/jqvmap.min.css')?>" rel="stylesheet">
	<link rel="stylesheet" href="<?= base_url('assets/mophy/vendor/chartist/css/chartist.min.css')?>">
	<link href="<?= base_url('assets/mophy/vendor/datatables/css/jquery.dataTables.min.css')?>" rel="stylesheet">
	<link href="<?= base_url('assets/mophy/vendor/bootstrap-select/dist/css/bootstrap-select.min.css')?>" rel="stylesheet">
	<link href="<?= base_url('assets/mophy/css/style.css')?>" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://unpkg.com/konva@9/konva.min.js"></script>
	<?= $this->renderSection('customCSS')?>

</head>
<body>

    <div id="main-wrapper">

    		<?= $this->include('layouts/editor_header');?>

				<!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
					<!-- row -->
					<div class="container-fluid">
						<?= $this->renderSection('content'); ?>	
					</div>
				</div>
        <!--**********************************
            Content body end
        ***********************************-->
    </div>

    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Required vendors -->
		<script src="<?= base_url('assets/mophy/vendor/global/global.min.js'); ?>"></script>
		<script src="<?= base_url('assets/mophy/vendor/bootstrap-select/dist/js/bootstrap-select.min.js'); ?>"></script>
		<script src="<?= base_url('assets/mophy/vendor/chart.js/Chart.bundle.min.js'); ?>"></script>
		<script src="<?= base_url('assets/mophy/js/custom.min.js'); ?>"></script>
		<script src="<?= base_url('assets/mophy/js/deznav-init.js'); ?>"></script>
		<script src="<?= base_url('assets/mophy/vendor/toastr/js/toastr.min.js')?>"></script>

		<?= $this->renderSection('customJS')?>
		
</body>
</html>