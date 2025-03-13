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
    <meta property="og:image" content="https://mophy.dexignzone.com/xhtml/social-image.png" />
    <meta name="format-detection" content="telephone=no">
    <title> SISTEMA CONFIGURADOR DE MAPAS INTERACTIVOS</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/mophy/images/logod.png') ?>">
    <link href="<?= base_url('assets/mophy/vendor/jqvmap/css/jqvmap.min.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/mophy/vendor/chartist/css/chartist.min.css') ?>">
    <link href="<?= base_url('assets/mophy/vendor/datatables/css/jquery.dataTables.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/mophy/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') ?>"
        rel="stylesheet">
    <link href="<?= base_url('assets/mophy/css/style.css') ?>" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/konva@9/konva.min.js"></script>

</head>

<body>
    <!--************
        Main wrapper start
    *************-->
    <div id="main-wrapper">

        <!--************
            Nav header start
        *************-->
        <div class="nav-header">
            <a onclick="window.location.href='<?= base_url('Home/') ?>'" class="brand-logo">
                <img class="logo-abbr" src="<?= base_url('assets/mophy/images/sta.jpg') ?>" alt="">
                <img class="logo-compact" src="<?= base_url('assets/mophy/images/logocompleto.png') ?>" alt="">
                <img class="brand-title" src="<?= base_url('assets/mophy/images/logocompleto.png') ?>" alt="">
            </a>

            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span><span class="line"></span><span class="line"></span>
                </div>
            </div>
        </div>
        <!--************
            Nav header end
        *************-->

        <!--************
            Header start
        *************-->
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
                            <a class="nav-link" href="javascript:void(0)" role="button" data-bs-toggle="dropdown">
                                <div class="header-info">
                                    <!-- Mostrar el nombre del usuario -->
                                    <span class="text-black">Hola,<strong><?= session()->get('user'); ?></strong></span>
                                    <p class="fs-12 mb-0">Admin</p>
                                </div>

                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="<?= site_url('auth/logout') ?>" class="dropdown-item ai-icon">
                                    <svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger"
                                        width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                        <polyline points="16 17 21 12 16 7"></polyline>
                                        <line x1="21" y1="12" x2="9" y2="12"></line>
                                    </svg>
                                    <span class="ms-2">Logout </span>
                                </a>
                            </div>
                        </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
        <!--************
            Header end ti-comment-alt
        *************-->

        <!--************
            Sidebar start
        *************-->
        <div class="deznav">
            <div class="deznav-scroll">
                <ul class="metismenu" id="menu">
                    <li>
                        <a onclick="window.location.href='<?= base_url('Eventos/') ?>'">
                            <i class="flaticon-381-notepad"></i>
                            <span class="nav-text">Crear evento</span>
                        </a>
                    </li>
                    <li>
                        <a onclick="window.location.href='<?= base_url('Home/') ?>'">
                            <i class="flaticon-381-television"></i>
                            <span class="nav-text">Ver eventos</span>
                        </a>
                    </li>
                </ul>

                <div class="copyright">
                    <p><strong>SERVICIOS INTEGRALES DE EXPOSICIONES SA DE CV.</strong> más de 30 años de experiencia en
                        el mercado.</p>
                    <p>Made with <span class="heart"></span> by Lu</p>
                </div>
            </div>
        </div>

        <!--************
            Sidebar end
        *************-->

        <div class="content-body">
            <?= $this->renderSection('content'); ?>
            <!-- row 
                    <div class="container-fluid">
                    </div>-->
        </div>

    </div>
    <!--************
        Main wrapper end
    *************-->
    <!-- vendors -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?= base_url('assets/mophy/vendor/global/global.min.js') ?>"></script>
    <script src="<?= base_url('assets/mophy/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') ?>"></script>
    <script src="<?= base_url('assets/mophy/vendor/chart.js/Chart.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/mophy/js/custom.min.js') ?>"></script>
    <script src="<?= base_url('assets/mophy/js/deznav-init.js'); ?>"></script>

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/dataTables.bootstrap4.min.js"
        integrity="sha512-OQlawZneA7zzfI6B1n1tjUuo3C5mtYuAWpQdg+iI9mkDoo7iFzTqnQHf+K5ThOWNJ9AbXL4+ZDwH7ykySPQc+A=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>