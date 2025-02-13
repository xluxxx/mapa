<?= $this->extend('layouts/header') ?>
<?=$this->section('content')?>

<div class="container-fluid">
 <div class= "page-titles form-head d-flex flex-wrap justify-content-between align-items-center mb-4">
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="text-black font-w600 mb-0 me-3">Todos los eventos</h2>
    <button type="button" class="btn btn-primary btn-rounded" 
        onclick="window.location.href='<?= base_url('Eventos/') ?>'">
        Nuevo evento
    </button>
</div>
</button>
</div>
</div>

<div class="container-fluid">
 <table id="tablaEventos" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Fecha</th>
                        <th>Lugar</th>
                        <th>Acciones</th>
                        <th>Mapa</th>
                    </tr>
                </thead>
            </table>
 </div>
</div>

<!-- jQuery y DataTables (solo si no están ya en layouts/header) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {
    $('#tablaEventos').DataTable({
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": "<?= base_url('eventos/getEventos') ?>",
            "type": "GET",
            "dataSrc": "data"
        },
        "columns": [
            { "data": "id" },
            { "data": "event_name" },
            { "data": "description" },
            { "data": "event_date" },
            { "data": "event_place" },
            { "data": "acciones", "orderable": false }
        ]
    });
});
</script>

<?=$this->endSection(); ?>
