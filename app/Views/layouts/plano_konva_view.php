<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa Interactivo</title>
    <link href="<?= base_url('assets/mophy/vendor/jqvmap/css/jqvmap.min.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/mophy/vendor/chartist/css/chartist.min.css') ?>">
    <link href="<?= base_url('assets/mophy/vendor/datatables/css/jquery.dataTables.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/mophy/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/mophy/css/style.css') ?>" rel="stylesheet">
	<link href="<?= base_url('assets/mophy/vendor/lightgallery/css/lightgallery.min.css')?>" rel="stylesheet">

</head>

<body>
    <style>
html, body {
    margin: 0;
    padding: 0;
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
}

#container {
    border: 1px solid black;
    width: 1400px;
    height: 800px;
    position: relative;
    box-sizing: border-box;
}
    </style>

    <div id="container" style="border: solid 1px black; position: relative;"></div>
    <!-- Buscador y lista de stands -->
    <div id="searchContainer" style="position: absolute; top: 5%; left: 5%; width: 250px; background: white; padding: 10px; border-radius: 8px; box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2); z-index: 20;">
        <input type="text" id="searchInput" placeholder="Buscar empresa, booth o categoría" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; outline: none;">
        <ul id="searchResults" style="list-style: none; padding: 0; margin: 10px 0; max-height: 300px; overflow-y: auto;"></ul>
    </div>
    <!-- Controles dentro del contenedor del lienzo -->
    <div id="controls" style="position: absolute; top: 5%; right: 10%; display: flex; flex-direction: column; gap: 5px; z-index: 10;">
        <button id="zoomIn" style="width: 40px; height: 40px; border-radius: 50%; background: white; border: 1px solid gray; cursor: pointer;">+</button>
        <button id="zoomOut" style="width: 40px; height: 40px; border-radius: 50%; background: white; border: 1px solid gray; cursor: pointer;">-</button>
        <button id="reset" style="width: 40px; height: 40px; border-radius: 50%; background: white; border: 1px solid gray; cursor: pointer;">🔄</button>
    </div>
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
                            <a href="ruta/imagen1.jpg" id="empresaLogoLink" data-exthumbimage="ruta/imagen1-thumb.jpg" class="lg-item col-lg-3 col-md-6 mb-4">
                                <img id="empresaLogo" class="w-100 rounded" src="ruta/imagen1-thumb.jpg" data-src="ruta/imagen1.jpg" alt="Logo de la Empresa" style="max-width: 100%;">
                            </a>

						</div>
							<div class="col-md-6 .ms-auto">
								<center><label><strong>Render:</strong></label>	</center>	
                                <a href="ruta/imagen2.jpg" id="detRenderLink" data-exthumbimage="ruta/imagen2-thumb.jpg" class="lg-item col-lg-3 col-md-6 mb-4">
                                    <img id="empresaRender" class="w-100 rounded" src="ruta/imagen2-thumb.jpg" data-src="ruta/imagen2.jpg" alt="Render de la Empresa" style="max-width: 100%;">
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
</body>
<script src="<?= base_url('assets/mophy/vendor/global/global.min.js'); ?>"></script>
<script src="<?= base_url('assets/mophy/vendor/bootstrap-select/dist/js/bootstrap-select.min.js'); ?>"></script>
<script src="<?= base_url('assets/mophy/vendor/chart.js/Chart.bundle.min.js'); ?>"></script>
<script src="<?= base_url('assets/mophy/js/custom.min.js'); ?>"></script>
<script src="<?= base_url('assets/mophy/js/deznav-init.js'); ?>"></script>
<script src="<?= base_url('assets/mophy/vendor/toastr/js/toastr.min.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://unpkg.com/konva@9/konva.min.js"></script>
<script src="<?= base_url('assets/js/mapa_view.js?v=1.12'); ?>"
    evento="<?= $evento['id']; ?>"
    imagen="<?= base_url('public/uploads/planos/' . $evento['name_file']); ?>"
    url_guardado="<?= base_url('Mapa/guardar_posiciones/'); ?>"
    url_carga="<?= base_url('Mapa/buscar_stands/'); ?>"
    base_url="<?= base_url(); ?>"
	defer></script>
	<script src="<?= base_url('assets/mophy/vendor/lightgallery/js/lightgallery-all.min.js'); ?>"></script>



</html>