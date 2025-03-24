<? if (strlen($evento['name_file']) <=0 || $evento['name_file'] == null): ?>
		<div class="alert alert-danger" role="alert">
				No se ha cargado el plano del evento
		</div>
<? endif;  ?>
		<?= $this->extend('layouts/editor'); ?>

		<?= $this->section('customCSS'); ?>
			<style>
					#dashboard {
							display: flex;
							justify-content: center;
							align-items: center;
							padding: 10px;
							background-color: #f8f9fa;
					}

					.rectangle,
					.circle,
					.delete,
					.save {
							cursor: pointer;
							margin: 0 10px;
					}

					body.nuevo_rectangulo {
						#container {
							opacity: 0.5;
						}
						.input_cont {
							display: none;
						}
						/* todo: dejar que los inputs deseados se vean, como el de nombre del stand */


					}
				#stand-form {
					display: none;
					font-size: 10px;
					font-weight: 400;
					height: fit-content;
					.form-control {
						padding: 0.3rem 0.5rem;
						border-radius: 0.2px;
					}
					.form-select {
							padding: 0.8rem 2rem 0.8rem 1rem;
							font-size: 10px;
							text-align: left;
							border-radius: 0.2px;
					}
				}
				#stand-form.shown {
					display: block;
				}
				
		</style>	
		<?= $this->endSection(); ?>

		<?= $this->section('content'); ?>

			<!-- Dashboard de figuras con iconos y nombres -->
			<div id="dashboard">
					<div class="rectangle btn btn-info" onclick="agregarRectangulo()">
							<i class="fas fa-add"></i> Agregar Figura
					</div>
					<!--<div class="circle btn btn-xs btn-info" onclick="addCircle()">
							<i class="fas fa-circle"></i> Círculo
					</div>-->
					<button type="button" class="delete btn btn-danger" onclick="deleteSelectedShape()" disabled="disabled">
							<i class="fas fa-trash"></i> Eliminar Figura
					</button>
					<button type="button" class="editFigura btn btn-warning" onclick="editarFigura()" disabled="disabled">
							<i class="fas fa-edit"></i> Editar Figura
					</button>
					<button type="button" class="save btn btn-success" onclick="guardarFiguras()">
							<i class="fas fa-save"></i> Guardar Mapa
					</button>
			</div>

			<!-- Contenedor del lienzo -->
			 <div>
			<div id="container" style="border: solid 1px black; width: 1200px; height: 800px; position: relative;"></div>
			    <!-- Buscador y lista de stands -->
				<div id="searchContainer" style="position: absolute; top: 20%; left: 18%; width: 250px; background: white; padding: 10px; border-radius: 8px; box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2); z-index: 20;">
					<input type="text" id="searchInput" placeholder="Buscar empresa, booth o categoría" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; outline: none;">
					<ul id="searchResults" style="list-style: none; padding: 0; margin: 10px 0; max-height: 300px; overflow-y: auto;"></ul>
				</div>
				<!-- Controles dentro del contenedor del lienzo -->
				<div id="controls" style="position: absolute; top: 20%; right: 20%; display: flex; flex-direction: column; gap: 5px; z-index: 10;">
					<button id="zoomIn" style="width: 40px; height: 40px; border-radius: 50%; background: white; border: 1px solid gray; cursor: pointer;">+</button>
					<button id="zoomOut" style="width: 40px; height: 40px; border-radius: 50%; background: white; border: 1px solid gray; cursor: pointer;">-</button>
					<button id="reset" style="width: 40px; height: 40px; border-radius: 50%; background: white; border: 1px solid gray; cursor: pointer;">🔄</button>
				</div>
				</div>
			<!-- Modal de Empresa -->
				<div class="modal fade" id="modalEmpresa" tabindex="-1" aria-labelledby="modalEmpresaLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="modalEmpresaLabel">Detalles de la Empresa</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<div class="text-center">
						<img id="empresaLogo" src="" alt="Logo de la Empresa" style="max-width: 150px; display: none;">
						</div>
						<p><strong>Nombre:</strong> <span id="empresaNombre"></span></p>
						<p><strong>Correo:</strong> <span id="empresaCorreo"></span></p>
						<p><strong>Página Web:</strong> <span id="empresaPagina"></span></p>
						<p><strong>Teléfono:</strong> <span id="empresaTel"></span></p>
						<p><strong>Stand:</strong> <span id="empresaStand"></span></p>
						<p><strong>Representante:</strong> <span id="empresaRepresentante"></span></p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
					</div>
					</div>
				</div>
				</div>

		<?= $this->endSection(); ?>

		<?= $this->section('customJS'); ?>
		<script src="<?= base_url('assets/js/mapa_editor.js?v=1.14'); ?>" 
				evento="<?= $evento['id']; ?>" 
				imagen="<?= base_url('public/uploads/planos/' . $evento['name_file']); ?>"
				url_guardado="<?= base_url('Mapa/guardar_posiciones/'); ?>"
				url_carga="<?= base_url('Mapa/buscar_stands/'); ?>"
				defer ></script>
		<script>
				// evento de domcontentloaded 
				document.addEventListener('DOMContentLoaded', function () {
					let sidebar = document.querySelector('.deznav .deznav-scroll');
					standForm = document.getElementById('stand-form');
					sidebar.appendChild(standForm);

					fnIniciarForm();
					sts_mostrarForm();
				});
				
		</script>

		<div id="stand-form" class="card">
			<div class="card-header bg-primary text-white p-3">
					<h3 class="m-0">Stand Information</h3>
			</div>
			<div class="card-body p-3">
					<form id="_sts_standForm">
							<div class="row mb-3">
									<div class="col-md-6 mb-3 input_cont cont_id">
											<label for="_sts_id" class="form-label">ID</label>
											<input type="number" class="form-control" id="_sts_id" name="_sts_id" value="-1" min="-1" step="1" required>
											<div class="invalid-feedback">Please enter a valid integer number.</div>
									</div>
									<div class="col-md-12  mb-3 input_cont cont_tipo_stand">
											<label for="_sts_type" class="form-label">Type</label>
											<select class="form-select" id="_sts_type" name="_sts_type" required>
													<!-- Options will be populated by JavaScript -->
											</select>
											<div class="invalid-feedback">Please select a type.</div>
									</div>

									<div class="col-md-6 mb-3 input_cont cont_posX">
											<label for="_sts_x" class="form-label">X Position</label>
											<input type="number" class="form-control" id="_sts_x" name="_sts_x" min="0" step="0.01" required>
											<div class="invalid-feedback">Please enter a valid number greater than or equal to 0.</div>
									</div>
									<div class="col-md-6 mb-3 input_cont cont_posY">
											<label for="_sts_y" class="form-label">Y Position</label>
											<input type="number" class="form-control" id="_sts_y" name="_sts_y" min="0" step="0.01" required>
											<div class="invalid-feedback">Please enter a valid number greater than or equal to 0.</div>
									</div>

									<div class="col-md-6 mb-3 input_cont cont_width">
											<label for="_sts_width" class="form-label">Width</label>
											<input type="number" class="form-control" id="_sts_width" name="_sts_width" min="0" max="999" step="1" required>
											<div class="invalid-feedback">Please enter a valid integer between 0 and 999.</div>
									</div>

									<div class="col-md-6 mb-3 input_cont cont_height">
											<label for="_sts_height" class="form-label">Height</label>
											<input type="number" class="form-control" id="_sts_height" name="_sts_height" min="0" max="999" step="1" required>
											<div class="invalid-feedback">Please enter a valid integer between 0 and 999.</div>
									</div>

									<div class="col-md-6 mb-3 input_cont cont_radius">
											<label for="_sts_radius" class="form-label">Radius</label>
											<input type="number" class="form-control" id="_sts_radius" name="_sts_radius" min="0" max="999" step="1">
											<div class="invalid-feedback">Please enter a valid integer between 0 and 999.</div>
									</div>

									<div class="col-md-6 mb-3 input_cont cont_stroke_width">	
											<label for="_sts_stroke_width" class="form-label">Stroke Width</label>
											<input type="number" class="form-control" id="_sts_stroke_width" name="_sts_stroke_width" min="0" max="100" step="1" required>
											<div class="invalid-feedback">Please enter a valid integer between 0 and 100.</div>
									</div>
							</div>
							
							<div class="col-md-12 mb-3 input_cont cont_numStand">
									<label for="_sts_numero" class="form-label">Número de Stand</label>
									<input type="text" class="form-control" id="_sts_numero" name="_sts_numero" maxlength="200" required>
									<div class="invalid-feedback">This field is required (max 200 characters).</div>
							</div>

							<div class="mb-3 input_cont cont_nombre">
									<label for="_sts_nombre" class="form-label">Name</label>
									<input type="text" class="form-control" id="_sts_nombre" name="_sts_nombre" maxlength="200" required>
									<div class="invalid-feedback">This field is required (max 200 characters).</div>
							</div>

							<div class="mb-3 input_cont cont_contacto">
									<label for="_sts_contacto" class="form-label">Contact</label>
									<input type="text" class="form-control" id="_sts_contacto" name="_sts_contacto" maxlength="300">
									<div class="form-text">Optional, max 300 characters.</div>
							</div>

							<div class="mb-3 form-check input_cont cont_status">
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
		<?= $this->endSection(); ?>

</body>
</html>