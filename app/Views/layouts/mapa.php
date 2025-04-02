<? if (strlen($evento['name_file']) <= 0 || $evento['name_file'] == null): ?>
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
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalEmpresaLabel">Detalles de la Empresa</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">

				<div id="lightgallery" class="row">
					<div class="row">
						<div class="col-md-5 .ms-auto">
							<center><label><strong>Logo empresa:</strong></label>	</center>			
							<a href="#" id="empresaLogoLink" data-exthumbimage="" class="lg-item col-lg-3 col-md-6 mb-4">
								<img id="empresaLogo" class="w-100 rounded" src="" data-src="" alt="Logo de la Empresa" style="max-width: 100%; display: none;">
							</a>
						</div>
							<div class="col-md-6 .ms-auto">
								<center><label><strong>Render:</strong></label>	</center>	
								<a href="#" id="detRenderLink" data-exthumbimage="" class="lg-item col-lg-3 col-md-6 mb-4">
									<img id="empresaRender" class="w-100 rounded" src="" data-src="" alt="Render de la Empresa" style="max-width: 100%; display: none;">
								</a>
							</div>
					</div>
				</div>
				<p><strong>Status:</strong> <span id="empresaStatus"></span></p>
				<p><strong>Nombre:</strong> <span id="empresaNombre"></span></p>
				<p><strong>Correo:</strong> <span id="empresaCorreo"></span></p>
				<p><strong>Teléfono:</strong> <span id="empresaTel"></span></p>
				<p><strong>Stand:</strong> <span id="empresaStand"></span></p>
				<p><strong>Página Web:</strong> <span id="empresaPagina"></span></p>
				<p><strong>Representante:</strong> <span id="empresaRepresentante"></span></p>
				<p><strong>Detalles:</strong> <span id="empresaDescripcion"></span></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('customJS'); ?>
<script src="<?= base_url('assets/js/mapa_editor.js?v=1.17'); ?>"
	evento="<?= $evento['id']; ?>"
	imagen="<?= base_url('public/uploads/planos/' . $evento['name_file']); ?>"
	url_guardado="<?= base_url('Mapa/guardar_posiciones/'); ?>"
	url_carga="<?= base_url('Mapa/buscar_stands/'); ?>"
	base_url="<?= base_url(); ?>"
	defer></script>
	<script src="<?= base_url('assets/mophy/vendor/lightgallery/js/lightgallery-all.min.js'); ?>"></script>

<?= $this->endSection(); ?>

</body>

</html>