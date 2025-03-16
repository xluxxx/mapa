	const id_evento = Number("<?php echo $evento['id'] ?>");
	// Inicializar el escenario y las capas
	var container = document.getElementById('container');
	const formulario_edicion = 1;
	const formulario_nuevo = 2;
	var estilo_form;

	// History stack for undo/redo
	var historial = [];
	var historialStep = -1;
	var maxHistorialSteps = 50; // Maximum number of steps to store

	let standForm = null;
	let _sts_typeSelect = null;

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
	function preAgregarRectangulo() {
		estilo_form = formulario_nuevo;
		document.querySelector('doby').classList.add('nuevo_rectangulo');
	}

	function agregarRectangulo() {
		// validar si se tiene la información necesaria antes de agregar un rectángulo

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
					strokeScaleEnabled: false, // Evitar que el borde se escale con el rectángulo	
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
					strokeScaleEnabled: false, // Evitar que el borde se escale con el círculo
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
			
			// Save state for undo/redo
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

		// JSON variable for type dropdown
	let _sts_typeOptions = [
		{ value: "Rect", label: "Rectangle" },
		{ value: "Circle", label: "Circle" },
		{ value: "Legend", label: "Legend" }
	];

	function fnIniciarForm() {
		_sts_typeSelect = document.getElementById('_sts_type');

		// Populate type dropdown
		_sts_typeOptions.forEach(option => {
				const optionElement = document.createElement('option');
				optionElement.value = option.value;
				optionElement.textContent = option.label;
				_sts_typeSelect.appendChild(optionElement);
		});
		
		// Add event listeners
		document.getElementById('_sts_resetBtn').addEventListener('click', _sts_resetForm);
		document.getElementById('_sts_submitBtn').addEventListener('click', _sts_validateAndSubmit);
		
		// Add change event to type dropdown to show/hide relevant fields
		document.getElementById('_sts_type').addEventListener('change', _sts_toggleFieldsBasedOnType);
		
		// Initial toggle of fields
		_sts_toggleFieldsBasedOnType();

	}

	function sts_mostrarForm() {
		standForm.classList.add('shown');
	}

	function sts_ocultarForm() {
		standForm.classList.remove('shown');
	}

	// Function to toggle fields based on selected type
	function _sts_toggleFieldsBasedOnType() {
			const _sts_type = document.getElementById('_sts_type').value;
			const _sts_widthHeightContainer = document.getElementById('_sts_width').closest('.row');
			const _sts_widthInput = document.getElementById('_sts_width');
			const _sts_heightInput = document.getElementById('_sts_height');
			const _sts_radiusInput = document.getElementById('_sts_radius');
			
			if (_sts_type === 'Circle') {
					_sts_radiusInput.required = true;
					_sts_radiusInput.parentElement.style.display = 'block';
					_sts_widthInput.required = false;
					_sts_heightInput.required = false;
					_sts_widthInput.parentElement.style.display = 'none';
					_sts_heightInput.parentElement.style.display = 'none';
			} else {
					_sts_radiusInput.required = false;
					_sts_radiusInput.parentElement.style.display = 'none';
					_sts_widthInput.required = true;
					_sts_heightInput.required = true;
					_sts_widthInput.parentElement.style.display = 'block';
					_sts_heightInput.parentElement.style.display = 'block';
			}
	}

	// Function to reset form values
	function _sts_resetForm() {
			const _sts_form = document.getElementById('_sts_standForm');
			_sts_form.reset();
			
			// Set default values
			document.getElementById('_sts_id').value = "-1";
			document.getElementById('_sts_id_evento').value = "-1";
			
			// Reset validation classes
			const _sts_formElements = _sts_form.elements;
			for (let i = 0; i < _sts_formElements.length; i++) {
					_sts_formElements[i].classList.remove('is-invalid');
					_sts_formElements[i].classList.remove('is-valid');
			}
			
			// Toggle fields based on type
			_sts_toggleFieldsBasedOnType();
	}

	// Async fetch function to get stand data
	async function _sts_fetchStandData(id_evento, id_stand) {
			try {
					// Create request body data
					const _sts_requestData = {
							id_evento: id_evento,
							id_stand: id_stand
					};
					
					// Send POST request with data in the body
					const _sts_response = await fetch(_sts_apiGetUrl, {
							method: 'POST',
							headers: {
									'Content-Type': 'application/json'
							},
							body: JSON.stringify(_sts_requestData)
					});
					
					const _sts_data = await _sts_response.json();
					
					if (_sts_data && _sts_data.result === "success") {
							_sts_populateForm(_sts_data.data);
					} else {
							alert("Error: Failed to retrieve stand data. " + (_sts_data.message || ""));
					}
			} catch (error) {
					alert("Error: An unexpected error occurred while fetching stand data. " + error.message);
					console.error("Fetch error:", error);
			}
	}

	// Function to populate form with fetched data
	function _sts_populateForm(data) {
			if (!data) return;
			
			// Helper function to safely set form values
			function _sts_setFormValue(fieldId, value) {
					const _sts_field = document.getElementById(fieldId);
					if (_sts_field) {
							if (_sts_field.type === 'checkbox') {
									_sts_field.checked = !!value;
							} else {
									_sts_field.value = value !== undefined && value !== null ? value : '';
							}
					}
			}
			
			// Set values for each field
			_sts_setFormValue('_sts_id', data.id);
			_sts_setFormValue('_sts_type', data.type);
			_sts_setFormValue('_sts_x', data.x);
			_sts_setFormValue('_sts_y', data.y);
			_sts_setFormValue('_sts_width', data.width);
			_sts_setFormValue('_sts_height', data.height);
			_sts_setFormValue('_sts_radius', data.radius);
			_sts_setFormValue('_sts_stroke_width', data.stroke_width);
			_sts_setFormValue('_sts_id_evento', data.id_evento);
			_sts_setFormValue('_sts_nombre', data.nombre);
			_sts_setFormValue('_sts_numero', data.numero);
			_sts_setFormValue('_sts_status', data.status);
			_sts_setFormValue('_sts_contacto', data.contacto);
			
			// Toggle fields based on type
			_sts_toggleFieldsBasedOnType();
	}

	// Function to validate and submit form
	async function _sts_validateAndSubmit() {

			// estilo_form = formulario_nuevo;
			const _sts_form = document.getElementById('_sts_standForm');
			let _sts_isValid = true;
			
			// Reset validation classes
			const _sts_formElements = _sts_form.elements;
			for (let i = 0; i < _sts_formElements.length; i++) {
					_sts_formElements[i].classList.remove('is-invalid');
					_sts_formElements[i].classList.remove('is-valid');
			}
			
			// Validate id (integer, default -1)
			const _sts_id = document.getElementById('_sts_id');
			if (!Number.isInteger(Number(_sts_id.value))) {
					_sts_id.classList.add('is-invalid');
					_sts_isValid = false;
			} else {
					_sts_id.classList.add('is-valid');
			}
			
			// Validate type (must be selected)
			const _sts_type = document.getElementById('_sts_type');
			if (!_sts_type.value) {
					_sts_type.classList.add('is-invalid');
					_sts_isValid = false;
			} else {
					_sts_type.classList.add('is-valid');
			}
			
			// Validate x and y (float, >= 0)
			const _sts_x = document.getElementById('_sts_x');
			if (isNaN(parseFloat(_sts_x.value)) || parseFloat(_sts_x.value) < 0) {
					_sts_x.classList.add('is-invalid');
					_sts_isValid = false;
			} else {
					_sts_x.classList.add('is-valid');
			}
			
			const _sts_y = document.getElementById('_sts_y');
			if (isNaN(parseFloat(_sts_y.value)) || parseFloat(_sts_y.value) < 0) {
					_sts_y.classList.add('is-invalid');
					_sts_isValid = false;
			} else {
					_sts_y.classList.add('is-valid');
			}
			
			// Validate width, height, radius based on type
			if (_sts_type.value === 'Circle') {
					const _sts_radius = document.getElementById('_sts_radius');
					if (!Number.isInteger(Number(_sts_radius.value)) || Number(_sts_radius.value) < 0 || Number(_sts_radius.value) > 999) {
							_sts_radius.classList.add('is-invalid');
							_sts_isValid = false;
					} else {
							_sts_radius.classList.add('is-valid');
					}
			} else {
					const _sts_width = document.getElementById('_sts_width');
					if (!Number.isInteger(Number(_sts_width.value)) || Number(_sts_width.value) < 0 || Number(_sts_width.value) > 999) {
							_sts_width.classList.add('is-invalid');
							_sts_isValid = false;
					} else {
							_sts_width.classList.add('is-valid');
					}
					
					const _sts_height = document.getElementById('_sts_height');
					if (!Number.isInteger(Number(_sts_height.value)) || Number(_sts_height.value) < 0 || Number(_sts_height.value) > 999) {
							_sts_height.classList.add('is-invalid');
							_sts_isValid = false;
					} else {
							_sts_height.classList.add('is-valid');
					}
			}
			
			// Validate stroke_width (integer, 0-100)
			const _sts_stroke_width = document.getElementById('_sts_stroke_width');
			if (!Number.isInteger(Number(_sts_stroke_width.value)) || Number(_sts_stroke_width.value) < 0 || Number(_sts_stroke_width.value) > 100) {
					_sts_stroke_width.classList.add('is-invalid');
					_sts_isValid = false;
			} else {
					_sts_stroke_width.classList.add('is-valid');
			}
			
			// Validate nombre (required, max 200 chars)
			const _sts_nombre = document.getElementById('_sts_nombre');
			if (!_sts_nombre.value.trim() || _sts_nombre.value.length > 200) {
					_sts_nombre.classList.add('is-invalid');
					_sts_isValid = false;
			} else {
					_sts_nombre.classList.add('is-valid');
			}
			
			// Validate numero (required, max 200 chars)
			const _sts_numero = document.getElementById('_sts_numero');
			if (!_sts_numero.value.trim() || _sts_numero.value.length > 200) {
					_sts_numero.classList.add('is-invalid');
					_sts_isValid = false;
			} else {
					_sts_numero.classList.add('is-valid');
			}
			
			// Validate contacto (optional, max 300 chars)
			const _sts_contacto = document.getElementById('_sts_contacto');
			if (_sts_contacto.value.length > 300) {
					_sts_contacto.classList.add('is-invalid');
					_sts_isValid = false;
			} else if (_sts_contacto.value.trim()) {
					_sts_contacto.classList.add('is-valid');
			}
			
			// If form is valid, submit data
			if (_sts_isValid) {
					// Prepare form data
					const _sts_formData = {
							id: parseInt(_sts_id.value),
							type: _sts_type.value,
							x: parseFloat(_sts_x.value),
							y: parseFloat(_sts_y.value),
							width: _sts_type.value !== 'Circle' ? parseInt(_sts_width.value) : null,
							height: _sts_type.value !== 'Circle' ? parseInt(_sts_height.value) : null,
							radius: _sts_type.value === 'Circle' ? parseInt(document.getElementById('_sts_radius').value) : null,
							stroke_width: parseInt(_sts_stroke_width.value),
							id_evento: parseInt(document.getElementById('_sts_id_evento').value),
							nombre: _sts_nombre.value.trim(),
							numero: _sts_numero.value.trim(),
							status: document.getElementById('_sts_status').checked,
							contacto: _sts_contacto.value.trim()
					};
					
					try {
							const _sts_response = await fetch(_sts_apiSubmitUrl, {
									method: 'POST',
									headers: {
											'Content-Type': 'application/json'
									},
									body: JSON.stringify(_sts_formData)
							});
							
							const _sts_result = await _sts_response.json();
							
							if (_sts_result && _sts_result.result === "success") {
									alert("Success: Stand information has been saved successfully!");
									// Optionally reset the form after successful submission
									// _sts_resetForm();
							} else {
									alert("Error: Failed to save stand information. " + (_sts_result.message || ""));
							}
					} catch (error) {
							alert("Error: An unexpected error occurred while submitting the form. " + error.message);
							console.error("Submit error:", error);
					}
			} else {
					alert("Please correct the errors in the form before submitting.");
			}
	}