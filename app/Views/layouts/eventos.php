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

<div class="container-fluid">
    <div class="page-titles form-head d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div class="col-xl-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Formulario de Evento</h4>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="basic-form">
                <!-- El formulario envía los datos al método save del controlador -->
                <form action="<?= base_url('eventos/save') ?>" method="post" enctype="multipart/form-data"> <!-- Añadido el atributo enctype -->
                    <?= csrf_field() ?> <!-- CSRF Token para seguridad -->

                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Nombre del evento</label>
                            <input type="text" name="event_name" class="form-control" placeholder="Nombre del evento" value="<?= old('event_name') ?>">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Descripción</label>
                            <input type="text" name="description" class="form-control" placeholder="Agrega una pequeña descripcion" value="<?= old('description') ?>">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">clave del evento </label>
                            <input type="text" name="clave" class="form-control" placeholder="Agrega la clave del evento" value="<?= old('clave') ?>">
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
                                <option selected>Elegir estado...</option>
                                <option value="Aguascalientes" <?= old('event_place') == 'Aguascalientes' ? 'selected' : '' ?>>Aguascalientes</option>
                                <option value="Baja California" <?= old('event_place') == 'Baja California' ? 'selected' : '' ?>>Baja California</option>
                                <option value="Baja California Sur" <?= old('event_place') == 'Baja California Sur' ? 'selected' : '' ?>>Baja California Sur</option>
                                <option value="Campeche" <?= old('event_place') == 'Campeche' ? 'selected' : '' ?>>Campeche</option>
                                <option value="Coahuila" <?= old('event_place') == 'Coahuila' ? 'selected' : '' ?>>Coahuila</option>
                                <option value="Colima" <?= old('event_place') == 'Colima' ? 'selected' : '' ?>>Colima</option>
                                <option value="Chiapas" <?= old('event_place') == 'Chiapas' ? 'selected' : '' ?>>Chiapas</option>
                                <option value="Chihuahua" <?= old('event_place') == 'Chihuahua' ? 'selected' : '' ?>>Chihuahua</option>
                                <option value="Ciudad de México" <?= old('event_place') == 'Ciudad de México' ? 'selected' : '' ?>>Ciudad de México</option>
                                <option value="Durango" <?= old('event_place') == 'Durango' ? 'selected' : '' ?>>Durango</option>
                                <option value="Guanajuato" <?= old('event_place') == 'Guanajuato' ? 'selected' : '' ?>>Guanajuato</option>
                                <option value="Guerrero" <?= old('event_place') == 'Guerrero' ? 'selected' : '' ?>>Guerrero</option>
                                <option value="Hidalgo" <?= old('event_place') == 'Hidalgo' ? 'selected' : '' ?>>Hidalgo</option>
                                <option value="Jalisco" <?= old('event_place') == 'Jalisco' ? 'selected' : '' ?>>Jalisco</option>
                                <option value="Estado de México" <?= old('event_place') == 'Estado de México' ? 'selected' : '' ?>>Estado de México</option>
                                <option value="Michoacán" <?= old('event_place') == 'Michoacán' ? 'selected' : '' ?>>Michoacán</option>
                                <option value="Morelos" <?= old('event_place') == 'Morelos' ? 'selected' : '' ?>>Morelos</option>
                                <option value="Nayarit" <?= old('event_place') == 'Nayarit' ? 'selected' : '' ?>>Nayarit</option>
                                <option value="Nuevo León" <?= old('event_place') == 'Nuevo León' ? 'selected' : '' ?>>Nuevo León</option>
                                <option value="Oaxaca" <?= old('event_place') == 'Oaxaca' ? 'selected' : '' ?>>Oaxaca</option>
                                <option value="Puebla" <?= old('event_place') == 'Puebla' ? 'selected' : '' ?>>Puebla</option>
                                <option value="Querétaro" <?= old('event_place') == 'Querétaro' ? 'selected' : '' ?>>Querétaro</option>
                                <option value="Quintana Roo" <?= old('event_place') == 'Quintana Roo' ? 'selected' : '' ?>>Quintana Roo</option>
                                <option value="San Luis Potosí" <?= old('event_place') == 'San Luis Potosí' ? 'selected' : '' ?>>San Luis Potosí</option>
                                <option value="Sinaloa" <?= old('event_place') == 'Sinaloa' ? 'selected' : '' ?>>Sinaloa</option>
                                <option value="Sonora" <?= old('event_place') == 'Sonora' ? 'selected' : '' ?>>Sonora</option>
                                <option value="Tabasco" <?= old('event_place') == 'Tabasco' ? 'selected' : '' ?>>Tabasco</option>
                                <option value="Tamaulipas" <?= old('event_place') == 'Tamaulipas' ? 'selected' : '' ?>>Tamaulipas</option>
                                <option value="Tlaxcala" <?= old('event_place') == 'Tlaxcala' ? 'selected' : '' ?>>Tlaxcala</option>
                                <option value="Veracruz" <?= old('event_place') == 'Veracruz' ? 'selected' : '' ?>>Veracruz</option>
                                <option value="Yucatán" <?= old('event_place') == 'Yucatán' ? 'selected' : '' ?>>Yucatán</option>
                                <option value="Zacatecas" <?= old('event_place') == 'Zacatecas' ? 'selected' : '' ?>>Zacatecas</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Subir Plano</label>
                            <input type="file" id="fileInput" name="name_file" class="form-control" accept=".svg,.jpg,.png"> <!-- Aquí puedes limitar el tipo de archivo si es necesario -->
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
                window.location.href = "<?= base_url('panel/Home') ?>"; // Recargar la página si es necesario
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
