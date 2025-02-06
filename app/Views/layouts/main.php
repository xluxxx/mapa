<!DOCTYPE html>

<html lang="en">

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
    <title> SISTEMA CONFIGURADOR DE MAPAS INTERACTIVOS</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="<?php base_url()?>assets/mophy/images/sta.jpg">
    <link href="<?php base_url()?>assets/mophy/vendor/jqvmap/css/jqvmap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="<?php base_url()?>assets/mophy/vendor/chartist/css/chartist.min.css">
	<link href="<?php base_url()?>assets/mophy/vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="<?php base_url()?>assets/mophy/vendor/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet">
    <link href="<?php base_url()?>assets/mophy/css/style.css" rel="stylesheet">
	
</head>
<body>

    <!--*******************
        Preloader start
    ********************-->
    <div id="preloader">
        <div class="sk-three-bounce">
            <div class="sk-child sk-bounce1"></div>
            <div class="sk-child sk-bounce2"></div>
            <div class="sk-child sk-bounce3"></div>
        </div>
    </div>
    <!--*******************
        Preloader end
    ********************-->
    <div id="main-wrapper">
    	<?= $this->include('layouts/header');?>
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
    <script src="<?php base_url()?>assets/mophy/vendor/global/global.min.js"></script>
	<script src="<?php base_url()?>assets/mophy/vendor/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
	<script src="<?php base_url()?>assets/mophy/vendor/chart.js/Chart.bundle.min.js"></script>
    <script src="<?php base_url()?>assets/mophy/js/custom.min.js"></script>
	<script src="<?php base_url()?>assets/mophy/js/deznav-init.js"></script>
    
		
	<!-- Datatable -->
	<script src="<?php base_url()?>assets/mophy/vendor/datatables/js/jquery.dataTables.min.js"></script>
	<script>
		(function($) {
			var table = $('#example5').DataTable({
				searching: false,
				paging:true,
				select: false,
				//info: false,         
				lengthChange:false 
				
			});
			$('#example tbody').on('click', 'tr', function () {
				var data = table.row( this ).data();
				
			});
		})(jQuery);
	</script>
	
</html>