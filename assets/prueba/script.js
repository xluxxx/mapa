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

// // add click listener
// node.on('click', function() {
//     console.log('you clicked me!');
//   });
  
//   // get the target node
//   node.on('click', function(evt) {
//     console.log(evt.target);
//   });
  
//   // stop event propagation
//   node.on('click', function(evt) {
//     evt.cancelBubble = true;
//   });
  
//   // bind multiple listeners
//   node.on('click touchstart', function() {
//     console.log('you clicked/touched me!');
//   });
  
//   // namespace listener
//   node.on('click.foo', function() {
//     console.log('you clicked/touched me!');
//   });
  
//   // get the event type
//   node.on('click tap', function(evt) {
//     var eventType = evt.type;
//   });
  
//   // get native event object
//   node.on('click tap', function(evt) {
//     var nativeEvent = evt.evt;
//   });
  
//   // for change events, get the old and new val
//   node.on('xChange', function(evt) {
//     var oldVal = evt.oldVal;
//     var newVal = evt.newVal;
//   });
  
//   // get event targets
//   // with event delegations
//   layer.on('click', 'Group', function(evt) {
//     var shape = evt.target;
//     var group = evt.currentTarget;
//   });