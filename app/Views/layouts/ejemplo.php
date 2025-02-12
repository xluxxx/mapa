<!DOCTYPE html>
<html lang="es">
<?= $this->extend('layouts/header') ?>
<?=$this->section('content')?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
</head>
<body>
<h2 class="text-black font-w600 mb-0 me-auto mb-2 pe-3">Todos los eventos</h2>

<div class="dropdown custom-dropdown me-3 mb-0">
    <div class="btn bg-white btn-rounded" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <!-- Aquí podrías agregar un ícono o texto para el dropdown -->
    </div>
</div>

<!-- Botón modificado para redirigir a otra ventana -->
<button type="button" class="btn btn-primary btn-rounded me-3" 
    onclick="window.location.href='<?= base_url('Eventos/') ?>'">
    Nuevo evento
</button>


<div class="dropdown custom-dropdown mb-0">
    <!-- Espacio para contenido adicional si es necesario -->
</div>
    <table id="tablaEventos" class="display" style="width:100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Fecha</th>
                <th>Lugar</th>
                <th>Acciones</th>
            </tr>
        </thead>
    </table>

    <script>
        $(document).ready(function() {
            $('#tablaEventos').DataTable({
                "processing": true,
                "serverSide": false, 
                "ajax": {
                    "url": "<?= base_url('eventos/getEventos') ?>", // Ruta al método del controlador
                    "type": "GET"
                }
            });
        });
    </script>
				<?=$this->endSection(); ?>
</body>
</html>
