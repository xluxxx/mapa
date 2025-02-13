<?= $this->extend('layouts/header') ?>
<?=$this->section('content')?>

<div class="content-body">
    <!-- row -->
    <div class="container-fluid">
        <div class= "page-titles form-head d-flex flex-wrap justify-content-between align-items-center mb-4">
            <h2 class="text-black font-w600 mb-0 me-auto mb-2 pe-3">Todos los eventos</h2>

            <a href="javascript:void(0)" class="btn btn-primary btn-rounded me-3 " onclick="window.location.href='<?= base_url('Eventos/') ?>'">
                <i class="las la-download scale5 me-3"></i>
                Nuevo evento
            </a>

        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="table-responsive table-hover fs-14 card-table">
                    <table class="table display mb-4 dataTablesCard " id="tablaEventos" style="width:100%">
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
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery y DataTables (solo si no están ya en layouts/header) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {
    // Llamar a la API primero
    fetch("<?= base_url('eventos/getEventos') ?>")
        .then(response => response.json())
        .then(data => {
            console.log("Respuesta de la API:", data);

            // Verificar que la respuesta tenga la estructura correcta
            const eventos = data.data || []; // Si no existe `data`, asignamos un array vacío

            // Inicializar DataTable con los datos obtenidos
            $('#tablaEventos').DataTable({
                "processing": true,
                "serverSide": false,
                "data": eventos, // Insertamos los datos aquí
                "columns": [
                    { "data": "id" },
                    { "data": "event_name" },
                    { "data": "description" },
                    { "data": "event_date" },
                    { "data": "event_place" },
                    { "data": null, "render": function(data, type, row) {
                        return `<button class="btn-editar" data-id="${row.id}">Editar</button>`;
                    }},
                    { "data": null, "render": function(data, type, row) {
                        return `<button class="btn-eliminar" data-id="${row.id}">Eliminar</button>`;
                    }}
                ]
            });
        })
        .catch(error => console.error("Error al obtener los datos:", error));
});
</script>

<?=$this->endSection(); ?>
