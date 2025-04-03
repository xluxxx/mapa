<?= $this->extend('layouts/header') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="page-titles form-head d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="text-black font-w600 mb-0 me-3">Todos los eventos</h2>
            <button type="button" class="btn btn-success"
                onclick="window.location.href='<?= base_url('Eventos/') ?>'">
                Nuevo evento
            </button>
        </div>
    </div>
</div>

<div class="row p-5">
    <div class="col-lg-12" style="background:white">
        <div class="table-responsive table-hover fs-14 card-table">
            <table id="tablaEventos" class="table header-border table-responsive-sm"
                style="width:100% ; background:white">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Clave</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Fecha</th>
                        <th>Lugar</th>
                        <th>Plano</th>
                        <th>Acciones</th>

                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>


<div class="modal fade show" id="editarModal" style="display: none;" aria-modal="true" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar evento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <form id="editarForm">
                    <input type="hidden" id="id" name="id">
                    <div class="form-group">
                        <label for="nombre_generico">Clave del evento </label>
                        <input type="text" class="form-control" id="clave" name="clave">
                    </div>
                    <div class="form-group">
                        <label for="nombre_generico">Nombre </label>
                        <input type="text" class="form-control" id="nombre" name="nombre">
                    </div>
                    <div class="form-group">
                        <label for="nombre_comercial">Descripción</label>
                        <input type="text" class="form-control" id="description" name="description">
                    </div>
                    <div class="form-group">
                        <label for="presentacione">Fecha</label>
                        <input type="date" class="form-control" id="fecha" name="fecha">
                    </div>
                    <div class="form-group">
                        <label for="presentacione">Lugar</label>
                        <select name="lugar" id="lugar" class="form-control wide">
                            <option value="Aguascalientes">Aguascalientes</option>
                            <option value="Baja California">Baja California</option>
                            <option value="Baja California Sur">Baja California Sur</option>
                            <option value="Campeche">Campeche</option>
                            <option value="Coahuila">Coahuila</option>
                            <option value="Colima">Colima</option>
                            <option value="Chiapas">Chiapas</option>
                            <option value="Chihuahua">Chihuahua</option>
                            <option value="Ciudad de México">Ciudad de México</option>
                            <option value="Durango">Durango</option>
                            <option value="Guanajuato">Guanajuato</option>
                            <option value="Guerrero">Guerrero</option>
                            <option value="Hidalgo">Hidalgo</option>
                            <option value="Jalisco">Jalisco</option>
                            <option value="Estado de México">Estado de México</option>
                            <option value="Michoacán">Michoacán</option>
                            <option value="Morelos">Morelos</option>
                            <option value="Nayarit">Nayarit</option>
                            <option value="Nuevo León">Nuevo León</option>
                            <option value="Oaxaca">Oaxaca</option>
                            <option value="Puebla">Puebla</option>
                            <option value="Querétaro">Querétaro</option>
                            <option value="Quintana Roo">Quintana Roo</option>
                            <option value="San Luis Potosí">San Luis Potosí</option>
                            <option value="Sinaloa">Sinaloa</option>
                            <option value="Sonora">Sonora</option>
                            <option value="Tabasco">Tabasco</option>
                            <option value="Tamaulipas">>Tamaulipas</option>
                            <option value="Tlaxcala">Tlaxcala</option>
                            <option value="Veracruz">Veracruz</option>
                            <option value="Yucatán">Yucatán</option>
                            <option value="Zacatecas">Zacatecas</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarCambios">Guardar cambios</button>
            </div>
        </div>
    </div>
</div>

<!-- jQuery y DataTables (solo si no están ya en layouts/header) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function () {
        table = $('#tablaEventos').DataTable({
            "processing": true,
            "serverSide": false,
            "ajax": {
                "url": "<?= base_url('eventos/getEventos') ?>",
                "type": "GET",
                "dataSrc": "data"
            },
            "columns": [
                { "data": "id" },
                { "data": "clave" },
                { "data": "event_name" },
                { "data": "description" },
                { "data": "event_date" },
                { "data": "event_place" },
                { "data": "plano" },
                { "data": "acciones", "orderable": false }
            ],
            "order": [[1, "desc"]],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
            },
        });
        // Manejador de eventos para el botón "Eliminar"
        $('#tablaEventos').on('click', '.btn-eliminar', function () {
            var $button = $(this); // Almacenar la referencia al botón
            var id = $button.data('id');

            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡No podrás revertir esto!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminarlo!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Desactivar el botón mientras se realiza la solicitud
                    $button.prop('disabled', true);

                    // Mostrar un SweetAlert de "Eliminando..."
                    Swal.fire({
                        title: 'Eliminando...',
                        text: 'Por favor, espera.',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        allowEscapeKey: false,
                        allowEnterKey: false,
                        onOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Realizar la llamada AJAX para eliminar el registro
                    var url_ = "eliminarEvento";
                    $.ajax({
                        url: '<?php echo base_url() ?>' + url_, // Ajusta la URL según tu estructura de rutas
                        type: 'POST',
                        data: { id: id },
                        success: function (response) {
                            // Cerrar el SweetAlert de "Eliminando..."
                            Swal.close();

                            // Mostrar un mensaje de éxito
                            Swal.fire(
                                '¡Eliminado!',
                                'El registro ha sido eliminado.',
                                'success'
                            );

                            // Redibujar la tabla
                            table.ajax.reload(null, false); // false para mantener la posición actual en la tabla
                        },
                        error: function (xhr, status, error) {
                            // Cerrar el SweetAlert de "Eliminando..."
                            Swal.close();

                            // Mostrar un mensaje de error
                            Swal.fire(
                                'Error',
                                'No se pudo eliminar el registro.',
                                'error'
                            );
                        },
                        complete: function () {
                            // Reactivar el botón una vez que la solicitud ha finalizado
                            $button.prop('disabled', false);
                        }
                    });
                }
            });
        });
        $('#tablaEventos').on('click', '.btn-editar', function () {
            // Obtener los datos de la fila
            var data = table.row($(this).parents('tr')).data();

            // Llenar el formulario del modal con los datos
            $('#id').val(data.id);
            $('#clave').val(data.clave);
            $('#nombre').val(data.event_name);
            $('#description').val(data.description);
            $('#fecha').val(data.event_date);
            console.log(data.id);
            $('#lugar').val(data.event_place); // Asigna "no" como valor predeterminado si está vacío

            // Mostrar el modal
            $('#editarModal').modal('show');
        });

        $('#guardarCambios').on('click', function () {
            // Obtener los datos del formulario
            var id = $('#id').val();
            var clave = $('#clave').val();
            var nombre_evento = $('#nombre').val();
            var descripcion_evento = $('#description').val();
            var fecha_evento = $('#fecha').val();
            var lugar_evento = $('#lugar').val();
            // Desactivar el botón mientras se realiza la solicitud
            $(this).prop('disabled', true);

            // Mostrar SweetAlert de "Guardando..."
            Swal.fire({
                title: 'Guardando...',
                text: 'Por favor, espera.',
                timer: 0, // El temporizador se controlará manualmente
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false,
                onOpen: () => {
                    Swal.showLoading();
                }
            });

            // Aquí puedes hacer una petición AJAX para actualizar los datos en el servidor
            var url_ = "actualizarEvento";
            $.ajax({
                url: '<?php echo base_url() ?>' + url_, // Ajusta la URL según tu estructura de rutas
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    id: id,
                    clave: clave,
                    nombre: nombre_evento,
                    descripcion: descripcion_evento,
                    fecha: fecha_evento,
                    lugar: lugar_evento
                }),
                success: function (response) {
                    // Cerrar el SweetAlert de "Guardando..."
                    Swal.close();

                    // Ocultar el modal
                    $('#editarModal').modal('hide');

                    // Recargar la tabla para mostrar los cambios
                    table.ajax.reload();

                    // Mostrar mensaje de éxito
                    Swal.fire({
                        icon: 'success',
                        title: 'Actualizado',
                        text: 'El Evento ha sido actualizado correctamente'
                    });
                },
                error: function (xhr, status, error) {
                    // Cerrar el SweetAlert de "Guardando..."
                    Swal.close();

                    // Mostrar mensaje de error
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Hubo un error al actualizar el Evento'
                    });

                    console.error('Error al actualizar el Evento:', error);
                },
                complete: function () {
                    // Reactivar el botón una vez que la solicitud ha finalizado
                    $('#guardarCambios').prop('disabled', false);
                }
            });
        });


    });

    const plano = (id) => {
        console.log(id)
        var url_ = "obtenerEvento";
        $.ajax({
            url: "<?= base_url('eventos/obtenerEvento') ?>",
            type: 'POST',
            data: { id: id },
            success: function (response) {
                console.log(response)
            },
            error: function (xhr, status, error) {
                // Cerrar el SweetAlert de "Eliminando..."
                Swal.close();

                // Mostrar un mensaje de error
                Swal.fire(
                    'Error',
                    'No se pudo eliminar el registro.',
                    'error'
                );
            },
            complete: function () {
                // Reactivar el botón una vez que la solicitud ha finalizado
                $button.prop('disabled', false);
            }
        });

    }
</script>

<?= $this->endSection(); ?>