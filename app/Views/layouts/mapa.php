<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Incluir FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="<?= base_url('assets/mophy//vendor/toastr/css/toastr.min.css')?>" rel="stylesheet">

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
        <div class="icon delete" onclick="deleteSelectedShape()">
            <i class="fas fa-trash"></i> Eliminar
        </div>
        <div class="icon save" onclick="guardarFiguras()">
            <i class="fas fa-save"></i> Guardar Mapa
        </div>
        
    </div>

    <!-- Contenedor del lienzo -->
    <div id="container" style="border-style: solid; width: 100%; height: 85vh;"></div>

    <script src="<?= base_url('assets/mophy/vendor/toastr/js/toastr.min.js')?>"></script>
    <script>
        const id_evento = Number("<?php echo $evento['id'] ?>");
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

        let shapes = [];

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

        // Funciones para agregar figuras desde el dashboard
        function addRectangle() {
            const pos = stage.getPointerPosition();
            if (!pos) return; // Verificar si hay una posición válida

            // Ajustar las coordenadas al zoom y desplazamiento
            const x = (pos.x - stage.x()) / scale;
            const y = (pos.y - stage.y()) / scale;

            // Crear un nuevo rectángulo
            const rect = new Konva.Rect({
                x: x,
                y: y,
                width: 50,
                height: 50,
                fill: 'rgba(255, 0, 0, 0.5)',
                stroke: 'red',
                strokeWidth: 2,
                draggable: true
            });

            layer.add(rect);
            tr.nodes([rect]);
            layer.batchDraw();

            // Añadir la forma al array shapes
            shapes.push({
                type: rect.getClassName(),
                x: rect.x(),
                y: rect.y(),
                width: rect.width(),
                height: rect.height(),
                fill: rect.fill(),
                stroke: rect.stroke(),
                stroke_width: rect.strokeWidth(),
                shape: rect,
                shapeIndex: rect.index,
                info: null
            });
        }

        function addCircle() {
            const pos = stage.getPointerPosition();
            if (!pos) return; // Verificar si hay una posición válida

            // Ajustar las coordenadas al zoom y desplazamiento
            const x = (pos.x - stage.x()) / scale;
            const y = (pos.y - stage.y()) / scale;

            // Crear un nuevo círculo
            const circle = new Konva.Circle({
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

            // Añadir la forma al array shapes
            shapes.push({
                type: circle.getClassName(),
                x: circle.x(),
                y: circle.y(),
                radius: circle.radius(),
                fill: circle.fill(),
                stroke: circle.stroke(),
                stroke_width: circle.strokeWidth(),
                shape: circle,
                shapeIndex: circle.index,
                info: null
            });
        }

        // Eventos para el movimiento (pan) con el botón izquierdo del mouse
        stage.on('mousedown', function (e) {
            if (e.evt.button === 0 && e.target === stage) { // Botón izquierdo y clic en el escenario (no en un objeto)
                isDragging = true;
                lastPointerPosition = stage.getPointerPosition();
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

        // Función para eliminar la figura seleccionada
        function deleteSelectedShape() {
            var selectedNode = tr.nodes()[0]; // Obtener la figura seleccionada
            if (selectedNode) {
                selectedNode.destroy(); // Eliminar la figura
                tr.nodes([]); // Deseleccionar el Transformer
                layer.batchDraw(); // Redibujar la capa
            }
        }

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

        //Formulario de datos del espacio
        const abrirFormulario = (shape) => {
            Swal.fire({
                title: "Registrar Stand",
                html: `
                    <input id="stand" class="swal2-input" placeholder="Número de Stand" required>
                    <input id="empresa" class="swal2-input" placeholder="Nombre de la Empresa" required>
                    <input id="paginaweb" class="swal2-input" placeholder="Página Web" required>

                    <input type="file" id="logo" class="swal2-file">
                `,
                showCancelButton: true,
                confirmButtonText: "Guardar",
                preConfirm: () => {
                    const stand = document.getElementById("stand").value;
                    const empresa = document.getElementById("empresa").value;
                    const paginaweb = document.getElementById("paginaweb").value;
                    const logoInput = document.getElementById("logo");

                    if (!stand || !empresa || !paginaweb) {
                        Swal.showValidationMessage("Todos los campos son obligatorios");
                        return false;
                    }

                    const logoFile = logoInput.files[0];
                    let logoURL = "";
                    if (logoFile) {
                        logoURL = URL.createObjectURL(logoFile);
                    }

                    return { stand, empresa, paginaweb, logoURL, shape };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const { stand, empresa, paginaweb, logoURL, shape } = result.value;
                    const data = { stand, empresa, paginaweb, logoURL };
                    const fig = shapes.find((fig) => fig.shapeIndex == shape.index);

                    if (fig) {
                        fig.info = data;
                        fig.fill = 'rgba(23, 148, 55, 0.5)';
                    }

                    toastr.success('Información guardada correctamente', 'Éxito');
                }
            });
        }

        layer.on('dblclick dbltap', function (e) {
            let stage = e.target.getStage(); // Asegurar que tenemos el stage
            stage.setPointersPositions(e); // Registrar manualmente la posición del puntero

            let shape = e.target;
            if (shape instanceof Konva.Rect || shape instanceof Konva.Circle) {
                console.log('Doble clic en:', shape);
                abrirFormulario(shape);
            }
        });

        function updateShapeInfo(shape) {
            const index = shapes.findIndex(s => s.shape === shape);
            if (index !== -1) {
                shapes[index] = {
                    ...shapes[index],
                    x: shape.x(),
                    y: shape.y(),
                    width: shape.width ? shape.width() : null,
                    height: shape.height ? shape.height() : null,
                    radius: shape.radius ? shape.radius() : null,
                    fill: shape.fill(),
                    stroke: shape.stroke(),
                    stroke_width: shape.strokeWidth()
                };
            }
        }

        // Escuchar eventos de cambio en las formas
        layer.on('dragmove transform transformend', function (e) {
            const shape = e.target;
            if (shape instanceof Konva.Rect || shape instanceof Konva.Circle) {
                updateShapeInfo(shape);
            }
        });

        // Función para guardar las figuras
        const guardarFiguras = () => {
            console.log(id_evento);

            fetch("<?= base_url('Mapa/guardar_posiciones/')?>" + id_evento, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ shapes })
            })
            .then(response => response.json())
            .then(data => console.log("Guardado en BD:", data))
            .catch(error => console.error("Error al guardar:", error));
        };
    </script>

    <?= $this->endSection() ?>
</body>
</html>