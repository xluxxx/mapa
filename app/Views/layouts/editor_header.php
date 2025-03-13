        <!--************
            Nav header start
        *************-->
        <div class="nav-header">
            <a onclick="window.location.href='<?= base_url('Home/') ?>'" class="brand-logo">
                <img class="logo-abbr" src="<?= base_url('assets/mophy/images/sta.jpg')?>" alt="">
                <img class="logo-compact" src="<?= base_url('assets/mophy/images/logocompleto.png')?>" alt="">
                <img class="brand-title" src="<?= base_url('assets/mophy/images/logocompleto.png')?>" alt="">
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
																			<svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
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
							<div id="stand-form" class="card">

									<div class="card-header bg-primary text-white">
											<h3>Stand Information</h3>
									</div>
									<div class="card-body">
											<form id="_sts_standForm">
													<div class="row mb-3">
															<div class="col-md-6">
																	<label for="_sts_id" class="form-label">ID</label>
																	<input type="number" class="form-control" id="_sts_id" name="_sts_id" value="-1" min="-1" step="1" required>
																	<div class="invalid-feedback">Please enter a valid integer number.</div>
															</div>
															<div class="col-md-6">
																	<label for="_sts_type" class="form-label">Type</label>
																	<select class="form-select" id="_sts_type" name="_sts_type" required>
																			<!-- Options will be populated by JavaScript -->
																	</select>
																	<div class="invalid-feedback">Please select a type.</div>
															</div>
													</div>

													<div class="row mb-3">
															<div class="col-md-6">
																	<label for="_sts_x" class="form-label">X Position</label>
																	<input type="number" class="form-control" id="_sts_x" name="_sts_x" min="0" step="0.01" required>
																	<div class="invalid-feedback">Please enter a valid number greater than or equal to 0.</div>
															</div>
															<div class="col-md-6">
																	<label for="_sts_y" class="form-label">Y Position</label>
																	<input type="number" class="form-control" id="_sts_y" name="_sts_y" min="0" step="0.01" required>
																	<div class="invalid-feedback">Please enter a valid number greater than or equal to 0.</div>
															</div>
													</div>

													<div class="row mb-3">
															<div class="col-md-4">
																	<label for="_sts_width" class="form-label">Width</label>
																	<input type="number" class="form-control" id="_sts_width" name="_sts_width" min="0" max="999" step="1" required>
																	<div class="invalid-feedback">Please enter a valid integer between 0 and 999.</div>
															</div>
															<div class="col-md-4">
																	<label for="_sts_height" class="form-label">Height</label>
																	<input type="number" class="form-control" id="_sts_height" name="_sts_height" min="0" max="999" step="1" required>
																	<div class="invalid-feedback">Please enter a valid integer between 0 and 999.</div>
															</div>
															<div class="col-md-4">
																	<label for="_sts_radius" class="form-label">Radius</label>
																	<input type="number" class="form-control" id="_sts_radius" name="_sts_radius" min="0" max="999" step="1">
																	<div class="invalid-feedback">Please enter a valid integer between 0 and 999.</div>
															</div>
													</div>

													<div class="row mb-3">
															<div class="col-md-6">
																	<label for="_sts_stroke_width" class="form-label">Stroke Width</label>
																	<input type="number" class="form-control" id="_sts_stroke_width" name="_sts_stroke_width" min="0" max="100" step="1" required>
																	<div class="invalid-feedback">Please enter a valid integer between 0 and 100.</div>
															</div>
															<div class="col-md-6">
																	<label for="_sts_numero" class="form-label">Number</label>
																	<input type="text" class="form-control" id="_sts_numero" name="_sts_numero" maxlength="200" required>
																	<div class="invalid-feedback">This field is required (max 200 characters).</div>
															</div>
													</div>

													<div class="mb-3">
															<label for="_sts_nombre" class="form-label">Name</label>
															<input type="text" class="form-control" id="_sts_nombre" name="_sts_nombre" maxlength="200" required>
															<div class="invalid-feedback">This field is required (max 200 characters).</div>
													</div>

													<div class="mb-3">
															<label for="_sts_contacto" class="form-label">Contact</label>
															<input type="text" class="form-control" id="_sts_contacto" name="_sts_contacto" maxlength="300">
															<div class="form-text">Optional, max 300 characters.</div>
													</div>

													<div class="mb-3 form-check">
															<input type="checkbox" class="form-check-input" id="_sts_status" name="_sts_status">
															<label class="form-check-label" for="_sts_status">Active Status</label>
													</div>

													<input type="hidden" id="_sts_id_evento" name="_sts_id_evento" value="-1">

													<div class="d-grid gap-2 d-md-flex justify-content-md-end">
															<button type="button" class="btn btn-secondary me-md-2" id="_sts_resetBtn">Reset</button>
															<button type="button" class="btn btn-primary" id="_sts_submitBtn">Submit</button>
													</div>
											</form>
									</div>

							</div>
						</div>
        </div>
        <!--************
            Sidebar end
        *************-->