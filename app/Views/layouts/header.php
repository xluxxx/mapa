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
    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">

        <!--**********************************
            Nav header start
        ***********************************-->
        <div class="nav-header">
            <a href="index.html" class="brand-logo">
                <img class="logo-abbr" src="<?php base_url()?>assets/mophy/images/sta.jpg" alt="">
                <img class="logo-compact" src="<?php base_url()?>assets/mophy/images/logocompleto.png" alt="">
                <img class="brand-title" src="<?php base_url()?>assets/mophy/images/logocompleto.png" alt="">
            </a>

            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span><span class="line"></span><span class="line"></span>
                </div>
            </div>
        </div>
        <!--**********************************
            Nav header end
        ***********************************-->
		
		<!--**********************************
            Header start
        ***********************************-->
        <div class="header">
            <div class="header-content">
                <nav class="navbar navbar-expand">
                    <div class="collapse navbar-collapse justify-content-between">
                        <div class="header-left">
                            <div class="dashboard_bar">
								<div class="input-group search-area d-lg-inline-flex d-none">
									<div class="input-group-append">
									</div>
								
								</div>
                            </div>
                        </div>
						<li class="nav-item dropdown header-profile">
    <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown">
        <div class="header-info">
            <!-- Mostrar el nombre del usuario -->
            <span class="text-black">Hello,<strong><?= session()->get('user'); ?></strong></span>
            <p class="fs-12 mb-0">Admin</p>
        </div>
        <img src="images/profile/17.jpg" width="20" alt=""/>
    </a>
    <div class="dropdown-menu dropdown-menu-end">
	<a href="<?= site_url('auth/logout') ?>" class="dropdown-item ai-icon">
    <svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
        <polyline points="16 17 21 12 16 7"></polyline>
        <line x1="21" y1="12" x2="9" y2="12"></line>
    </svg>
    <span class="ms-2">Logout </span>
</a>
    </div>

                        </ul>
                    </div>
                </nav>
            </div>
        </div>
        <!--**********************************
            Header end ti-comment-alt
        ***********************************-->

        <!--**********************************
            Sidebar start
        ***********************************-->
        <div class="deznav">
            <div class="deznav-scroll">
				<ul class="metismenu" id="menu">
                    <li><a class="has-arrow ai-icon" href="javascript:void(0)" aria-expanded="false">
							<i class="flaticon-381-networking"></i>
							<span class="nav-text">eventos</span>
						</a>
                        <ul aria-expanded="false">
							<li><a onclick="window.location.href='<?= base_url('Eventos/') ?>'">crear evento </a></li>
						</ul>
                    </li>
                   
				<div class="copyright">
					<p><strong>Mophy Payment Admin Dashboard</strong> © 2022 All Rights Reserved</p>
					<p>Made with <span class="heart"></span> by DexignZone</p>
				</div>
			</div>
        </div>
        <!--**********************************
            Sidebar end
        ***********************************-->
		
		<!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            <!-- row -->
			<div class="container-fluid">
				<div class= "page-titles form-head d-flex flex-wrap justify-content-between align-items-center mb-4">
					<?= $this->renderSection('content'); ?>
				</div>
            </div>
        </div>
        <!--**********************************
            Content body end
        ***********************************-->

        <!--**********************************
            Footer start
        ***********************************-->
        <div class="footer">
            <div class="copyright">
                <p>Copyright © Designed &amp; Developed by <a href="http://dexignzone.com/" target="_blank">DexignZone</a> 2022</p>
            </div>
        </div>
		<?php 
var_dump(session()->get('todo')->email);
?>
        <!--**********************************
            Footer end
        ***********************************-->

    </div>
    <!--**********************************
        Main wrapper end
    ***********************************-->

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