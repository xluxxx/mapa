// Crear el escenario principal (canvas de Konva)
const stage = new Konva.Stage({
    container: 'container', // ID del contenedor HTML
    width: window.innerWidth,
    height: 500,
});

// Crear una capa donde se dibujarán los elementos
const layer = new Konva.Layer();
stage.add(layer);

let isDrawing = false;
let rectangle;

// Agregar evento de clic al escenario
stage.on('mousedown', (e) => {
    isDrawing = true;

    const pos = stage.getPointerPosition();
    rectangle = new Konva.Rect({
        x: pos.x,
        y: pos.y,
        width: 0,
        height: 0,
        fill: 'rgba(0, 150, 255, 0.5)', // Color semi-transparente
        stroke: 'blue',
        strokeWidth: 2,
    });

    layer.add(rectangle);
});

stage.on('mousemove', (e) => {
    if (!isDrawing) return;

    const pos = stage.getPointerPosition();
    const newWidth = pos.x - rectangle.x();
    const newHeight = pos.y - rectangle.y();

    rectangle.width(newWidth);
    rectangle.height(newHeight);
    layer.batchDraw(); // Redibuja la capa
});

stage.on('mouseup', () => {
    isDrawing = false;

    // Hacer que el rectángulo sea arrastrable después de ser creado
    if (rectangle) {
        rectangle.draggable(true); // Hacer que el cuadro sea arrastrable
    }
});

// Evento de clic en el rectángulo
rectangle.on('click', () => {
    const info = `Posición: (${rectangle.x()}, ${rectangle.y()})<br>Tamaño: ${rectangle.width()} x ${rectangle.height()}`;
    document.getElementById('rectangle-info').innerHTML = info;
    document.getElementById('modal').style.display = 'block'; // Mostrar el modal
    document.getElementById('modal-overlay').style.display = 'block'; // Mostrar el overlay
});

// Evento para eliminar el rectángulo
document.getElementById('delete-rectangle').addEventListener('click', () => {
    if (rectangle) {
        rectangle.destroy(); // Eliminar el rectángulo
        rectangle = null; // Limpiar la referencia
        layer.batchDraw(); // Redibuja la capa
    }
    document.getElementById('modal').style.display = 'none'; // Ocultar el modal
    document.getElementById('modal-overlay').style.display = 'none'; // Ocultar el overlay
});

// Evento para cerrar el modal
document.getElementById('close-modal').addEventListener('click', () => {
    document.getElementById('modal').style.display = 'none'; // Ocultar el modal
    document.getElementById('modal-overlay').style.display = 'none'; // Ocultar el overlay
});

// Evento para cerrar el modal al hacer clic en el overlay
document.getElementById('modal-overlay').addEventListener('click', () => {
    document.getElementById('modal').style.display = 'none'; // Ocultar el modal
    document.getElementById('modal-overlay').style.display = 'none'; // Ocultar el overlay
});