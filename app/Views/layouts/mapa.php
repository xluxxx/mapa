<!DOCTYPE html>
<html lang="en">

<body>
    
<?= $this->extend('layouts/header') ?>
<?=$this->section('content')?>

<div id="container" style="border-style: solid; width:100%;height:100%" >
</div>

<script>
    // Crear un escenario de Konva
    var container = document.getElementById('container');
    var stage = new Konva.Stage({
        container: container, // id del contenedor
        width: container.offsetWidth,
        height: container.offsetHeight
    });

    // Crear una capa
    var layer = new Konva.Layer();
    stage.add(layer);

    // Crear un rectángulo
    var rect = new Konva.Rect({
        x: 50, // Posición inicial en el lienzo
        y: 50,
        width: 100,
        height: 100,
        fill: 'red',
        draggable: true // Hacer el rectángulo arrastrable
    });

    // Añadir el rectángulo a la capa
    layer.add(rect);

    // Dibujar la capa
    layer.draw();
</script>
</body>
</html>


<?=$this->endSection(); ?>