<!DOCTYPE html>

<html lang="en">

<body>

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

    </div>
    <!--**********************************
        Main wrapper end
    ***********************************-->

    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Required vendors -->
   
	
</html>