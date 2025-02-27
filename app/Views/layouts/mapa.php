<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konva Dashboard con Iconos y Nombres</title>
    <!-- Incluir FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Estilos para el menú contextual */
        #contextMenu {
            display: none;
            position: absolute;
            width: 100px;
            background-color: white;
            box-shadow: 0 0 5px grey;
            border-radius: 3px;
            z-index: 1000; /* Asegurar que esté por encima de otros elementos */
        }

        #contextMenu button {
            width: 100%;
            background-color: white;
            border: none;
            margin: 0;
            padding: 10px;
            text-align: left;
        }

        #contextMenu button:hover {
            background-color: lightgray;
        }

        /* Estilos generales para el dashboard */
        #dashboard {
            margin-bottom: 20px;
            display: flex;
            gap: 15px; /* Espacio entre los iconos */
            flex-wrap: wrap; /* Permitir que los iconos se envuelvan en pantallas pequeñas */
        }

        /* Estilos base para los iconos */
        #dashboard .icon {
            padding: 10px 20px;
            border: none;
            border-radius: 10px; /* Bordes redondeados */
            background-color: #ffffff; /* Fondo blanco */
            color: #333; /* Color del icono */
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Sombra ligera */
            display: flex;
            align-items: center;
            gap: 10px; /* Espacio entre el icono y el texto */
        }

        /* Efecto hover (al pasar el mouse) */
        #dashboard .icon:hover {
            background-color: #f0f0f0; /* Cambiar color de fondo */
            transform: translateY(-3px); /* Levantar el icono ligeramente */
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15); /* Sombra más pronunciada */
        }

        /* Efecto active (al hacer clic) */
        #dashboard .icon:active {
            transform: translateY(0); /* Volver a la posición original */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Restaurar sombra */
        }

        /* Colores específicos para cada icono */
        #dashboard .icon.rectangle {
            color: #2196F3; /* Azul */
        }

        #dashboard .icon.circle {
            color: #FF9800; /* Naranja */
        }

        #dashboard .icon.triangle {
            color: #F44336; /* Rojo */
        }

        #dashboard .icon.delete {
            color: #9E9E9E; /* Gris */
        }
    </style>
    <script src="https://unpkg.com/konva@9/konva.min.js"></script>
</head>
<body>
    <?= $this->extend('layouts/header') ?>
    <?= $this->section('content') ?>

    <!-- Dashboard de figuras con iconos y nombres -->
    <div id="dashboard">
        <div class="icon rectangle" onclick="addRectangle()">
            <i class="fas fa-square"></i> Rectángulo
        </div>
        <div class="icon circle" onclick="addCircle()">
            <i class="fas fa-circle"></i> Círculo
        </div>
        <div class="icon triangle" onclick="addTriangle()">
            <i class="fas fa-play"></i> Triángulo
        </div>
        <div class="icon delete" onclick="deleteSelectedShape()">
            <i class="fas fa-trash"></i> Eliminar
        </div>
    </div>

    <!-- Contenedor del lienzo -->
    <div id="container" style="border-style: solid; width: 100%; height: 500px;"></div>

    <script>
        // Inicializar el escenario y las capas
        var container = document.getElementById('container');
        var stage = new Konva.Stage({
            container: container,
            width: container.offsetWidth,
            height: container.offsetHeight
        });

        // Crear una capa para el fondo
        var backgroundLayer = new Konva.Layer();
        stage.add(backgroundLayer);

        // Crear una capa para los elementos (rectángulos, círculos, etc.)
        var layer = new Konva.Layer();
        stage.add(layer);

        // Variables para el zoom
        var scaleBy = 1.1; // Factor de escala (10% de zoom)
        var scale = 1; // Escala actual

        // Variables para el movimiento (pan)
        var isDragging = false;
        var lastPointerPosition;

        // Variables para dibujar rectángulos
        var isDrawing = false;
        var rect;
        var startX, startY;

        // Crear un Transformer para ajustar los rectángulos
        var tr = new Konva.Transformer({
            nodes: [], // Inicialmente sin nodos asociados
            boundBoxFunc: (oldBox, newBox) => {
                // Limitar el tamaño de la figura para que no se salga del escenario
                const box = newBox;
                const isOut =
                    box.x < 0 ||
                    box.y < 0 ||
                    box.x + box.width > stage.width() ||
                    box.y + box.height > stage.height();

                if (isOut) {
                    return oldBox; // Mantener el tamaño anterior si se sale del escenario
                }
                return newBox;
            },
        });
        layer.add(tr); // Añadir el Transformer a la capa

        // Evento para hacer zoom con la rueda del mouse
        container.addEventListener('wheel', function (e) {
            e.preventDefault(); // Evitar el comportamiento predeterminado del scroll

            var oldScale = scale; // Guardar la escala actual
            var pointer = stage.getPointerPosition(); // Obtener la posición del mouse

            // Calcular la nueva escala
            if (e.deltaY < 0) {
                // Zoom in (acercar)
                scale = scale * scaleBy;
            } else {
                // Zoom out (alejar)
                scale = scale / scaleBy;
            }

            // Limitar el zoom mínimo y máximo (opcional)
            scale = Math.max(0.5, Math.min(scale, 3)); // Límites: 0.5x a 3x

            // Aplicar la nueva escala al escenario
            stage.scale({ x: scale, y: scale });

            // Ajustar la posición del escenario para que el zoom se centre en el puntero del mouse
            var newPos = {
                x: pointer.x - (pointer.x - stage.x()) * (scale / oldScale),
                y: pointer.y - (pointer.y - stage.y()) * (scale / oldScale)
            };

            stage.position(newPos);
            stage.batchDraw(); // Redibujar el escenario
        });

        // Eventos para el movimiento (pan) con el botón izquierdo del mouse
        stage.on('mousedown', function (e) {
            if (e.evt.button === 0 && e.target === stage) { // Botón izquierdo y clic en el escenario (no en un objeto)
                isDragging = true;
                lastPointerPosition = stage.getPointerPosition();
            } else if (e.evt.button === 0) { // Botón izquierdo para dibujar
                var pos = stage.getPointerPosition();

                // Verificar si el clic ocurre dentro de una figura existente
                var shape = stage.getIntersection(pos);
                if (shape && (shape instanceof Konva.Rect || shape instanceof Konva.Circle || shape instanceof Konva.RegularPolygon)) {
                    // Si el clic ocurre dentro de una figura, no crear una nueva
                    return;
                }

                isDrawing = true;
                // Ajustar las coordenadas iniciales teniendo en cuenta la escala y la posición del escenario
                startX = (pos.x - stage.x()) / scale;
                startY = (pos.y - stage.y()) / scale;

                // Crear un nuevo rectángulo
                rect = new Konva.Rect({
                    x: startX,
                    y: startY,
                    width: 0,
                    height: 0,
                    fill: 'rgba(255, 0, 0, 0.5)', // Color semitransparente
                    stroke: 'red',
                    strokeWidth: 2,
                    draggable: true // Permitir que el rectángulo sea arrastrable
                });

                // Añadir el rectángulo a la capa
                layer.add(rect);

                // Asociar el Transformer al nuevo rectángulo
                tr.nodes([rect]);
                layer.batchDraw();
            }
        });

        stage.on('mousemove', function (e) {
            if (isDragging) {
                var pos = stage.getPointerPosition();
                var dx = pos.x - lastPointerPosition.x;
                var dy = pos.y - lastPointerPosition.y;

                // Mover el escenario
                stage.x(stage.x() + dx);
                stage.y(stage.y() + dy);

                // Actualizar la última posición
                lastPointerPosition = pos;

                // Redibujar el escenario
                stage.batchDraw();
            } else if (isDrawing) {
                var pos = stage.getPointerPosition();
                // Ajustar las coordenadas actuales teniendo en cuenta la escala y la posición del escenario
                var currentX = (pos.x - stage.x()) / scale;
                var currentY = (pos.y - stage.y()) / scale;

                // Ajustar el tamaño del rectángulo
                rect.width(currentX - startX);
                rect.height(currentY - startY);

                // Redibujar la capa
                layer.batchDraw();
            }
        });

        stage.on('mouseup', function (e) {
            isDragging = false;
            isDrawing = false;
        });

        // Cargar la imagen de fondo
        var image = new Image();
        image.src = "<?= base_url('public/uploads/planos/' . $evento['name_file']) ?>"; // Ruta de la imagen
        image.onload = function () {
            // Escalar la imagen para que se ajuste al contenedor
            var scaleFactor = Math.min(
                stage.width() / image.width,
                stage.height() / image.height
            );

            var width = image.width * scaleFactor;
            var height = image.height * scaleFactor;

            // Crear un objeto Konva.Image con la imagen cargada
            var konvaImage = new Konva.Image({
                x: (stage.width() - width) / 2, // Centrar la imagen horizontalmente
                y: (stage.height() - height) / 2, // Centrar la imagen verticalmente
                image: image, // Imagen cargada
                width: width, // Ancho escalado
                height: height // Alto escalado
            });

            // Añadir la imagen a la capa de fondo
            backgroundLayer.add(konvaImage);

            // Dibujar la capa de fondo
            backgroundLayer.draw();
        };

        // Funciones para agregar figuras desde el dashboard
        function addRectangle() {
            var pos = stage.getPointerPosition(); // Obtener la posición actual del puntero
            if (!pos) return; // Si no hay posición, salir

            // Ajustar las coordenadas teniendo en cuenta la escala y el desplazamiento
            var x = (pos.x - stage.x()) / scale;
            var y = (pos.y - stage.y()) / scale;

            var rect = new Konva.Rect({
                x: x,
                y: y,
                width: 100,
                height: 50,
                fill: 'rgba(255, 0, 0, 0.5)',
                stroke: 'red',
                strokeWidth: 2,
                draggable: true
            });
            layer.add(rect);
            tr.nodes([rect]);
            layer.batchDraw();
        }

        function addCircle() {
            var pos = stage.getPointerPosition(); // Obtener la posición actual del puntero
            if (!pos) return; // Si no hay posición, salir

            // Ajustar las coordenadas teniendo en cuenta la escala y el desplazamiento
            var x = (pos.x - stage.x()) / scale;
            var y = (pos.y - stage.y()) / scale;

            var circle = new Konva.Circle({
                x: x,
                y: y,
                radius: 50,
                fill: 'rgba(0, 255, 0, 0.5)',
                stroke: 'green',
                strokeWidth: 2,
                draggable: true
            });
            layer.add(circle);
            tr.nodes([circle]);
            layer.batchDraw();
        }

        function addTriangle() {
            var pos = stage.getPointerPosition(); // Obtener la posición actual del puntero
            if (!pos) return; // Si no hay posición, salir

            // Ajustar las coordenadas teniendo en cuenta la escala y el desplazamiento
            var x = (pos.x - stage.x()) / scale;
            var y = (pos.y - stage.y()) / scale;

            var triangle = new Konva.RegularPolygon({
                x: x,
                y: y,
                sides: 3,
                radius: 50,
                fill: 'rgba(0, 0, 255, 0.5)',
                stroke: 'blue',
                strokeWidth: 2,
                draggable: true
            });
            layer.add(triangle);
            tr.nodes([triangle]);
            layer.batchDraw();
        }

        // Función para eliminar la figura seleccionada
        function deleteSelectedShape() {
            var selectedNode = tr.nodes()[0]; // Obtener la figura seleccionada
            if (selectedNode) {
                selectedNode.destroy(); // Eliminar la figura
                tr.nodes([]); // Deseleccionar el Transformer
                layer.batchDraw(); // Redibujar la capa
            }
        }

        // Evento para eliminar la figura seleccionada con la tecla "Delete" o "Supr"
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Delete' || e.key === 'Supr') { // Verificar si se presionó la tecla "Delete" o "Supr"
                e.preventDefault(); // Evitar el comportamiento predeterminado
                deleteSelectedShape(); // Eliminar la figura seleccionada
            }
        });

        // Seleccionar figuras
        stage.on('click tap', function (e) {
            if (e.target === stage) {
                tr.nodes([]);
                layer.batchDraw();
            } else {
                tr.nodes([e.target]);
                layer.batchDraw();
            }
        });
    </script>

    <?= $this->endSection() ?>
</body>
</html>