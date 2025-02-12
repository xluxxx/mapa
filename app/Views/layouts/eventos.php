<!DOCTYPE html>

<html lang="en">

<body>
<?= $this->extend('layouts/header') ?>
<?=$this->section('content')?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach ?>
        </ul>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('message')): ?>
    <div class="alert alert-success">
        <?= session()->getFlashdata('message') ?>
    </div>
<?php endif; ?>

<div class="col-xl-12 col-lg-12">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Formulario de Evento</h4>
        </div>
        <div class="card-body">
            <div class="basic-form">
                <!-- El formulario envía los datos al método save del controlador -->
                <form >

                    <?= csrf_field() ?> <!-- CSRF Token para seguridad -->

                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Nombre del evento</label>
                            <input type="text" name="event_name" class="form-control" placeholder="nombre del evento" value="<?= old('event_name') ?>">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Descripción</label>
                            <input type="text" name="description" class="form-control" placeholder="agrega una pequeña descripcion" value="<?= old('description') ?>">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label>Fecha</label>
                            <input type="date" name="event_date" class="form-control" value="<?= old('event_date') ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-4">
                            <label class="form-label">Lugar</label>
                            <select name="event_place" id="inputState" class="default-select form-control wide">
                                <option selected>Choose...</option>
                                <option value="Place 1" <?= old('event_place') == 'Place 1' ? 'selected' : '' ?>>Place 1</option>
                                <option value="Place 2" <?= old('event_place') == 'Place 2' ? 'selected' : '' ?>>Place 2</option>
                                <!-- Agrega más lugares aquí -->
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelector("form").addEventListener("submit", function(event) {
    event.preventDefault(); // Evita el envío tradicional del formulario

    let formData = new FormData(this);

    fetch("<?= base_url('eventos/save') ?>", {
        method: "POST",
        body: formData
    })
    .then(response => response.json()) // Convertir la respuesta a JSON
    .then(data => {
        console.log("Respuesta del servidor:", data); // Imprimir en consola
        
        // Verificar si la respuesta fue exitosa
        if (data.success) {
            Swal.fire({
                title: "¡Éxito!",
                text: data.message || "El evento se ha guardado correctamente.",
                icon: "success"
            }).then(() => {
                location.reload(); // Recargar la página si es necesario
            });
        } else {
            Swal.fire({
                title: "Error",
                text: data.message || "Hubo un problema al guardar el evento.",
                icon: "error"
            });
        }
    })
    .catch(error => {
        console.error("Error:", error);
        Swal.fire({
            title: "Error",
            text: "Ocurrió un error inesperado.",
            icon: "error"
        });
    });
});

</script>

</body>
<?=$this->endSection(); ?>