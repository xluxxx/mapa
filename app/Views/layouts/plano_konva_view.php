<!DOCTYPE html>
<html>
<head>
    <title>Plano: <?= esc($evento['clave'] ?? 'Evento') ?></title>
    <script src="https://unpkg.com/konva@8.3.2/konva.min.js"></script>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: Arial;
            background-color: #f5f5f5;
            overflow: hidden;
        }
        #konva-container {
            background: white;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Plano: <?= esc($evento['clave'] ?? 'Evento') ?></h1>
    </div>

    <div id="konva-container"></div>

    <script>
        // 1. Configuración básica del lienzo
        const containerWidth = window.innerWidth * 0.95;
        const containerHeight = window.innerHeight * 0.8;
        
        const stage = new Konva.Stage({
            container: 'konva-container',
            width: containerWidth,
            height: containerHeight,
            draggable: true // Permitir movimiento/pan
        });

        const layer = new Konva.Layer();
        stage.add(layer);

        // 2. Cargar imagen de fondo
        const bgImg = new Image();
        bgImg.crossOrigin = 'Anonymous'; // Necesario para SVG

        bgImg.onload = function() {
            // Escalar imagen manteniendo proporción
            const scale = Math.min(
                stage.width() / bgImg.width,
                stage.height() / bgImg.height
            );
            
            const imgNode = new Konva.Image({
                x: (stage.width() - bgImg.width * scale) / 2,
                y: (stage.height() - bgImg.height * scale) / 2,
                image: bgImg,
                width: bgImg.width * scale,
                height: bgImg.height * scale,
                listening: false // Deshabilitar interacción
            });
            layer.add(imgNode);

            // 3. Dibujar stands (si existen)
            <?php if (!empty($stands)): ?>
                <?php foreach ($stands as $stand): ?>
                    const standGroup = new Konva.Group({
                        x: <?= $stand['x'] ?? 0 ?>,
                        y: <?= $stand['y'] ?? 0 ?>,
                    });

                    // Rectángulo del stand
                    standGroup.add(new Konva.Rect({
                        width: <?= $stand['width'] ?? 100 ?>,
                        height: <?= $stand['height'] ?? 50 ?>,
                        fill: 'rgba(52, 152, 219, 0.7)',
                        stroke: 'black',
                        strokeWidth: <?= $stand['stroke_width'] ?? 1 ?>,
                        cornerRadius: 5,
                        listening: false // Deshabilitar interacción
                    }));

                    // Texto del stand
                    standGroup.add(new Konva.Text({
                        text: '<?= $stand['numero'] ?? '' ?>',
                        fontSize: 14,
                        fontFamily: 'Arial',
                        fill: 'white',
                        width: <?= $stand['width'] ?? 100 ?>,
                        align: 'center',
                        verticalAlign: 'middle',
                        listening: false
                    }));

                    layer.add(standGroup);
                <?php endforeach; ?>
            <?php endif; ?>

            layer.draw();
        };

        bgImg.onerror = function() {
            console.error("Error al cargar:", this.src);
            alert('No se pudo cargar el plano');
        };

        // Forzar recarga (evitar caché)
        bgImg.src = '<?= $imagen_plano ?>?t=' + Date.now();

        // 4. Configurar zoom con rueda del mouse
        stage.on('wheel', (e) => {
            e.evt.preventDefault();
            const oldScale = stage.scaleX();
            const pointer = stage.getPointerPosition();
            
            const newScale = e.evt.deltaY > 0 ? 
                oldScale * 0.9 : // Zoom out
                oldScale * 1.1;  // Zoom in

            // Limitar zoom
            const scale = Math.max(0.5, Math.min(newScale, 3));
            
            stage.scale({ x: scale, y: scale });
            
            // Ajustar posición para zoom centrado
            const newPos = {
                x: pointer.x - (pointer.x - stage.x()) * (scale / oldScale),
                y: pointer.y - (pointer.y - stage.y()) * (scale / oldScale)
            };
            
            stage.position(newPos);
            stage.batchDraw();
        });

        // 5. Ajustar al redimensionar ventana
        window.addEventListener('resize', function() {
            stage.width(window.innerWidth * 0.95);
            stage.height(window.innerHeight * 0.8);
            layer.draw();
        });
    </script>
</body>
</html>