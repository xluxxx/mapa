<? if (strlen($evento['name_file']) <=0 || $evento['name_file'] == null): ?>
		<div class="alert alert-danger" role="alert">
				No se ha cargado el plano del evento
		</div>
<? endif;  ?>
		<?= $this->extend('layouts/header') ?>
		<?= $this->section('content') ?>

		<!-- Dashboard de figuras con iconos y nombres -->
		<div id="dashboard">
				<div class="icon rectangle btn btn-sm" onclick="addRectangle()">
						<i class="fas fa-square"></i> Rectángulo
				</div>
				<div class="icon circle btn btn-sm" onclick="addCircle()">
						<i class="fas fa-circle"></i> Círculo
				</div>
				<div class="icon delete btn btn-sm" onclick="deleteSelectedShape()">
						<i class="fas fa-trash"></i> Eliminar
				</div>
				<div class="icon save btn btn-sm" onclick="guardarFiguras()">
						<i class="fas fa-save"></i> Guardar Mapa
				</div>
				<div class="icon undo btn btn btn-sm" onclick="undo()">
						<i class="fas fa-undo"></i> Deshacer
				</div>
				<div class="icon redo btn btn-sm" onclick="redo()">
						<i class="fas fa-redo"></i> Rehacer
				</div>
				
		</div>

		<!-- Contenedor del lienzo -->
		<div id="container" style="border-style: solid; width: 100%; height: 85vh;"></div>

		<script src="<?= base_url('assets/mophy/vendor/toastr/js/toastr.min.js')?>"></script>
		<script>
				const id_evento = Number("<?php echo $evento['id'] ?>");
				// Inicializar el escenario y las capas
				var container = document.getElementById('container');

				// History stack for undo/redo
				var historial = [];
				var historialStep = -1;
				var maxHistorialSteps = 50; // Maximum number of steps to store

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
				
				// Modify the addRectangle function to save state after adding a rectangle
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
						
						// Save state for undo/redo
						saveState();
				}

				// Modify the addCircle function to save state after adding a circle
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
						
						// Save state for undo/redo
						saveState();
				}

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
								height: height, // Alto escalado
								name: 'background-image', // Add a name to identify it
								listening: false // Disable interactions with the background image
						});

						// Añadir la imagen a la capa de fondo
						backgroundLayer.add(konvaImage);

						// Dibujar la capa de fondo
						backgroundLayer.draw();
						
						// Initialize historial with the initial state (empty canvas)
						saveState();
				};

				// Función para eliminar la figura seleccionada
				function deleteSelectedShape() {
						var selectedNode = tr.nodes()[0]; // Obtener la figura seleccionada
						if (selectedNode) {
								// Find and remove the shape from the shapes array
								const index = shapes.findIndex(s => s.shape === selectedNode);
								if (index !== -1) {
										shapes.splice(index, 1);
								}
								
								selectedNode.destroy(); // Eliminar la figura
								tr.nodes([]); // Deseleccionar el Transformer
								layer.batchDraw(); // Redibujar la capa
								
								// Save state for undo/redo
								saveState();
						}
				}

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
												shape.fill('rgba(23, 148, 55, 0.5)');
												layer.batchDraw();
												
												// Save state for undo/redo
												saveState();
										}

										toastr.success('Información guardada correctamente', 'Éxito');
								}
						});
				}

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

				function saveState() {
						// Remove any future states if we're in the middle of the historial
						if (historialStep < historial.length - 1) {
								historial = historial.slice(0, historialStep + 1);
						}
						
						// Create a deep copy of the shapes array
						const shapesClone = shapes.map(shape => {
								// Create a new object without the shape reference (which can't be serialized)
								const { shape: shapeRef, ...rest } = shape;
								return { ...rest };
						});
						
						// Add the current state to historial
						historial.push(shapesClone);
						
						// Limit the historial size
						if (historial.length > maxHistorialSteps) {
								historial.shift();
						} else {
								historialStep++;
						}
						
						// Update button states
						updateUndoRedoButtons();	
				}

				// Function to update the undo/redo button states
				function updateUndoRedoButtons() {
						const undoButton = document.querySelector('.icon.undo');
						const redoButton = document.querySelector('.icon.redo');
						
						if (undoButton && redoButton) {
								undoButton.style.opacity = historialStep >= 0 ? '1' : '0.5';
								undoButton.style.pointerEvents = historialStep >= 0 ? 'auto' : 'none';
								
								redoButton.style.opacity = historialStep < historial.length - 1 ? '1' : '0.5';
								redoButton.style.pointerEvents = historialStep < historial.length - 1 ? 'auto' : 'none';
						}
				}

				// Function to restore a state from historial
				function restoreState(state) {
						// Clear the current shapes from the layer
						layer.destroyChildren();
						layer.add(tr); // Add back the transformer
						
						// Clear the shapes array
						shapes = [];
						
						// Recreate shapes from the saved state
						state.forEach(shapeData => {
								let shape;
								
								if (shapeData.type === 'Rect') {
										shape = new Konva.Rect({
												x: shapeData.x,
												y: shapeData.y,
												width: shapeData.width,
												height: shapeData.height,
												fill: shapeData.fill,
												stroke: shapeData.stroke,
												strokeWidth: shapeData.stroke_width,
												draggable: true
										});
								} else if (shapeData.type === 'Circle') {
										shape = new Konva.Circle({
												x: shapeData.x,
												y: shapeData.y,
												radius: shapeData.radius,
												fill: shapeData.fill,
												stroke: shapeData.stroke,
												strokeWidth: shapeData.stroke_width,
												draggable: true
										});
								}
								
								if (shape) {
										layer.add(shape);
										
										// Add the shape to the shapes array
										shapes.push({
												type: shapeData.type,
												x: shapeData.x,
												y: shapeData.y,
												width: shapeData.width,
												height: shapeData.height,
												radius: shapeData.radius,
												fill: shapeData.fill,
												stroke: shapeData.stroke,
												stroke_width: shapeData.stroke_width,
												shape: shape,
												shapeIndex: shape.index,
												info: shapeData.info
										});
								}
						});
						
						// Clear the transformer selection
						tr.nodes([]);
						
						// Redraw the layer
						layer.batchDraw();
				}

				// Undo function
				function undo() {
						if (historialStep > 0) {
								historialStep--;
								restoreState(historial[historialStep]);
								updateUndoRedoButtons();
						}
				}

				// Redo function
				function redo() {
						if (historialStep < historial.length - 1) {
								historialStep++;
								restoreState(historial[historialStep]);
								updateUndoRedoButtons();
						}
				}

				// Escuchar eventos de cambio en las formas
				layer.on('dragmove transform transformend', function (e) {
						const shape = e.target;
						if (shape instanceof Konva.Rect || shape instanceof Konva.Circle) {
								updateShapeInfo(shape);
						}
				});

				layer.on('dblclick dbltap', function (e) {
						let stage = e.target.getStage(); // Asegurar que tenemos el stage
						stage.setPointersPositions(e); // Registrar manualmente la posición del puntero

						let shape = e.target;
						if (shape instanceof Konva.Rect || shape instanceof Konva.Circle) {
								console.log('Doble clic en:', shape);
								abrirFormulario(shape);
						}
				});

				// Seleccionar figuras
				stage.on('click tap', function (e) {
						// Ignore clicks on the background image
						if (e.target.name() === 'background-image') {
							console.log('Click en la imagen de fondo');
							return;
						}
						if (e.target === stage) {
								tr.nodes([]);
								layer.batchDraw();
						} else {
								tr.nodes([e.target]);
								layer.batchDraw();
						}
				});

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

				// Add event listeners to save state after moving or transforming shapes
				layer.on('dragend', function(e) {
						const shape = e.target;
						if (shape instanceof Konva.Rect || shape instanceof Konva.Circle) {
								updateShapeInfo(shape);
								saveState();
						}
				});

				layer.on('transformend', function(e) {
						const shape = e.target;
						if (shape instanceof Konva.Rect || shape instanceof Konva.Circle) {
								updateShapeInfo(shape);
								saveState();
						}
				});

				updateUndoRedoButtons();
		</script>

		<?= $this->endSection() ?>
</body>
</html>