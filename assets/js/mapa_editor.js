let limagen = document.currentScript.getAttribute('imagen');
let lid_evento = document.currentScript.getAttribute('evento');
let lurl_guardado = document.currentScript.getAttribute('url_guardado');
let lurl_carga = document.currentScript.getAttribute('url_carga');
var konva_stage;
var konva_layer_bg;
var konva_layer_elem;
let stdx_shapes = [];
var konva_transformer;

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

const id_evento = Number(lid_evento);
// Inicializar el escenario y las capas
var lcontainer = document.getElementById('container');
const formulario_edicion = 1;
const formulario_nuevo = 2;
var estilo_form;

// History stack for undo/redo
var historial = [];
var historialStep = -1;
var maxHistorialSteps = 50; // Maximum number of steps to store

let standForm = null;
let _sts_typeSelect = null;

document.addEventListener('DOMContentLoaded', function () {
	// inicializarKonva();
	inicializarKonva(cargarFiguras);
	// cargarFiguras();
});

    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    const iframe = document.getElementById('mapFrame');
	
	//console.log('stdx_shape', stdx_shapes);

	// Evento para actualizar la lista de resultados en tiempo real
	searchInput.addEventListener('input', function () {
		const query = searchInput.value.toLowerCase();
		searchResults.innerHTML = ''; // Limpiar resultados anteriores

		// Filtrar stands según la búsqueda (usando stdx_shapes de la función cargarFigurasKonva)
		const filteredStands = stdx_shapes.filter(stand => 
			stand.empresa.toLowerCase().includes(query) // Buscar por nombre o título
		);

		// Generar la lista de resultados
		filteredStands.forEach(stand => {
			const li = document.createElement('li');
			li.textContent = `${stand.empresa} (Stand: ${stand.numeroStand})`;
			li.style.padding = "8px";
			li.style.cursor = "pointer";
			li.style.borderBottom = "1px solid #eee";
			
			// Evento al hacer clic en un resultado
			li.addEventListener('click', function () {
				seleccionarYResaltarFigura(stand.id_konva); // Resaltar y centrar en el mapa
			});

			searchResults.appendChild(li);
		});
	});


    // Controles de zoom
    document.getElementById('zoomIn').addEventListener('click', function () {
        iframe.style.transform = `scale(${getNewScale(1.2)})`;
    });

    document.getElementById('zoomOut').addEventListener('click', function () {
        iframe.style.transform = `scale(${getNewScale(1 / 1.2)})`;
    });

    document.getElementById('reset').addEventListener('click', function () {
        iframe.style.transform = 'scale(1)';
    });

	
   // Variable para almacenar la última figura seleccionada
   let figuraSeleccionada = null;

   // Función para actualizar la lista de búsqueda con eventos de selección
   function actualizarListaBusqueda(data) {
	   searchResults.innerHTML = ''; // Limpiar lista anterior
	   console.log(data);

	   data.forEach(stand => {
		   let li = document.createElement('li');
		   li.textContent = `${stand.nombreEmpresa} (Stand: ${stand.numero})`;
		   li.style.padding = "8px";
		   li.style.cursor = "pointer";
		   li.style.borderBottom = "1px solid #eee";

		   li.addEventListener('click', function () {
			   seleccionarYResaltarFigura(stand.id_konva);
		   });

		   searchResults.appendChild(li);
	   });
   }

   // Función para centrar y resaltar una figura en el mapa
   function seleccionarYResaltarFigura(id_konva) {
	   let stand = stdx_shapes.find(s => s.id_konva === id_konva);
	   if (!stand) return;

	   let stage = konva_layer_elem.getStage();

	   // Si hay una figura seleccionada previamente, quitar el resaltado
	   if (figuraSeleccionada) {
		   figuraSeleccionada.shape.stroke('black');  // Restaurar borde negro
		   figuraSeleccionada.shape.strokeWidth(2);
	   }

	   // Resaltar la nueva figura seleccionada
	   stand.shape.stroke('blue');
	   stand.shape.strokeWidth(4);
	   figuraSeleccionada = stand;

	   // Animación para centrar la vista en la figura seleccionada
	   stage.to({
		   x: -stand.x + stage.width() / 2 - stand.width / 2,
		   y: -stand.y + stage.height() / 2 - stand.height / 2,
		   scaleX: 1.5,
		   scaleY: 1.5,
		   duration: 0.5
	   });

	   konva_layer_elem.batchDraw();  // Redibujar la capa
   }

   // Modificación de cargarFiguras para actualizar la lista de búsqueda después de cargar
   const cargarFiguras = () => {	
	   fetch(lurl_carga + id_evento, {
		   method: 'POST',
		   headers: { 'Content-Type': 'application/json' }
	   })
	   .then(response => response.json())
	   .then(data => { 
		   cargarFigurasKonva(data);
		   actualizarListaBusqueda(data);  // Actualizar la lista de búsqueda después de cargar los stands
		   console.log("Cargado de BD:", data);
	   })
	   .catch(error => console.error("Error al cargar los stands: ", error));
   };
    function getNewScale(factor) {
        let currentScale = parseFloat(iframe.style.transform.replace('scale(', '').replace(')', '')) || 1;
        return Math.max(0.5, Math.min(2, currentScale * factor)); // Limita el zoom entre 0.5x y 2x
    }

	function inicializarKonva(afnCallback) {

		konva_stage = new Konva.Stage({
				container: lcontainer,
				width: lcontainer.offsetWidth,
				height: lcontainer.offsetHeight
		});

		// Crear una capa para el fondo
		konva_layer_bg = new Konva.Layer();
		konva_stage.add(konva_layer_bg);

		// Crear una capa para los elementos (rectángulos, círculos, etc.)
		konva_layer_elem = new Konva.Layer();
		konva_stage.add(konva_layer_elem);

		// Crear un Transformer para ajustar los rectángulos
		konva_transformer = new Konva.Transformer({
				nodes: [], // Inicialmente sin nodos asociados
				boundBoxFunc: (oldBox, newBox) => {
						// Limitar el tamaño de la figura para que no se salga del escenario
						const box = newBox;
						const isOut =
								box.x < 0 ||
								box.y < 0 ||
								box.x + box.width > konva_stage.width() ||
								box.y + box.height > konva_stage.height();

						if (isOut) {
								return oldBox; // Mantener el tamaño anterior si se sale del escenario
						}
						return newBox;
				},
		});

		konva_layer_elem.add(konva_transformer); // Añadir el Transformer a la capa

		// Evento para hacer zoom con la rueda del mouse
		lcontainer.addEventListener('wheel', function (e) {
				e.preventDefault(); // Evitar el comportamiento predeterminado del scroll

				var oldScale = scale; // Guardar la escala actual
				var pointer = konva_stage.getPointerPosition(); // Obtener la posición del mouse
				// console.log('oldScale:', oldScale);
				// Calcular la nueva escala
				if (e.deltaY < 0) {
						// Zoom in (acercar)
						scale = scale * scaleBy;
				} else {
						// Zoom out (alejar)
						scale = scale / scaleBy;
				}

				// Limitar el zoom mínimo y máximo (opcional)
				scale = Math.max(0.75, Math.min(scale, 5)); // Límites: 0.2x a 3x

				// Aplicar la nueva escala al escenario
				konva_stage.scale({ x: scale, y: scale });

				// Ajustar la posición del escenario para que el zoom se centre en el puntero del mouse
				var newPos = {
						x: pointer.x - (pointer.x - konva_stage.x()) * (scale / oldScale),
						y: pointer.y - (pointer.y - konva_stage.y()) * (scale / oldScale)
				};

				konva_stage.position(newPos);
				konva_stage.batchDraw(); // Redibujar el escenario
		});
		// ✅ Agregar un evento al `stage` para deseleccionar si se hace clic fuera de una figura
		konva_layer_elem.getStage().on('click', function (e) {
			// Si el clic no fue en una figura, quitar la selección
			if (!e.target || !e.target.getClassName || e.target.getClassName() !== 'Rect') {
				if (figuraSeleccionada) {
					figuraSeleccionada.shape.stroke('black');  // Restaurar borde negro
					figuraSeleccionada.shape.strokeWidth(2);
					figuraSeleccionada = null;
					konva_layer_elem.batchDraw();  // Redibujar
				}
			}
		});
		// Cargar la imagen de fondo
		var image = new Image();
		image.src = limagen; // Ruta de la imagen
		image.onload = function () {
				// Escalar la imagen para que se ajuste al contenedor
				var scaleFactor = Math.min(
						konva_stage.width() / image.width,
						konva_stage.height() / image.height
				);

				var width = image.width * scaleFactor;
				var height = image.height * scaleFactor;

				// Crear un objeto Konva.Image con la imagen cargada
				var konvaImage = new Konva.Image({
						x: (konva_stage.width() - width) / 2, // Centrar la imagen horizontalmente
						y: (konva_stage.height() - height) / 2, // Centrar la imagen verticalmente
						image: image, // Imagen cargada
						width: width, // Ancho escalado
						height: height, // Alto escalado
						name: 'background-image', // Add a name to identify it
						listening: false // Disable interactions with the background image
				});

				// Añadir la imagen a la capa de fondo
				konva_layer_bg.add(konvaImage);

				// Dibujar la capa de fondo
				konva_layer_bg.draw();
				
				// Save state for undo/redo
				saveState();
		};
		
		// Escuchar eventos de cambio en las formas
		konva_layer_elem.on('dragmove transform transformend', function (e) {
			const shape = e.target;

			if (shape instanceof Konva.Rect || shape instanceof Konva.Circle) {
        // Obtener el tamaño actual con escala aplicada
        const newWidth = shape.width() * shape.scaleX();
        const newHeight = shape.height() * shape.scaleY();

        //console.log("Nueva dimensión - Ancho:", newWidth, "Alto:", newHeight);
				updateShapeInfo(shape); // Llamar la función de actualización (si existe)
			}
		});

		// Funciones de zoom y reset
		function zoom(scale) {
			konva_stage.scale({ x: scale, y: scale });
			konva_stage.batchDraw();
		}

		function resetView() {
			konva_stage.scale({ x: 1, y: 1 });
			konva_stage.position({ x: 0, y: 0 });
			konva_stage.batchDraw();
		}
	
		// Eventos de los botones
		document.getElementById('zoomIn').addEventListener('click', function () {
			let currentScale = konva_stage.scaleX();
			zoom(currentScale * 1.2); // Aumentar el zoom
		});
	
		document.getElementById('zoomOut').addEventListener('click', function () {
			let currentScale = konva_stage.scaleX();
			zoom(currentScale / 1.2); // Reducir el zoom
		});
	
		document.getElementById('reset').addEventListener('click', resetView);


		konva_layer_elem.on('dblclick dbltap', function (e) {
			e.evt.preventDefault();
			
			const shape = e.target;
			if (!shape || !(shape instanceof Konva.Rect || shape instanceof Konva.Circle)) {
				return;
			}
		
			const idkonva = shape.attrs.id;
			console.log('Doble clic en:', idkonva);
		
			// Feedback visual
			shape.stroke('orange');
			shape.strokeWidth(3);
			konva_layer_elem.batchDraw();
		
			verificarStandRegistrado(idkonva)
				.then((registrado) => {
					// Restaurar apariencia
					shape.stroke('black');
					shape.strokeWidth(1);
					konva_layer_elem.batchDraw();
		
					if (registrado === true) {
						console.log('Stand registrado - mostrando info');
						verEmpresa(shape);
					} else if (registrado === false) {
						console.log('Stand no registrado - mostrando formulario');
						abrirFormulario(shape);
					} else {
						console.error('Respuesta inesperada');
						Swal.fire('Error', 'No se pudo determinar el estado del stand', 'error');
					}
				})
				.catch((error) => {
					console.error('Error:', error);
					shape.stroke('black');
					shape.strokeWidth(1);
					konva_layer_elem.batchDraw();
					Swal.fire('Error', 'Error al verificar el stand', 'error');
				});
		});

		// Seleccionar figuras
		konva_stage.on('click tap', function (e) {
				// Ignore clicks on the background image
				const shape = e.target;
				const botonEdit = document.querySelector(".editFigura"); // Selecciona el primer botón con la clase 'save'
				botonEdit.disabled = true; // Habilita el botón

				const botonDelete = document.querySelector(".delete"); // Selecciona el primer botón con la clase 'save'
				botonDelete.disabled = true; // Habilita el botón

				if (shape instanceof Konva.Rect || shape instanceof Konva.Circle) {
				const botonEdit = document.querySelector(".editFigura"); // Selecciona el primer botón con la clase 'save'
				botonEdit.disabled = false; // Habilita el botón

				const botonDelete = document.querySelector(".delete"); // Selecciona el primer botón con la clase 'save'
				botonDelete.disabled = false; // Habilita el botón
	
				// Obtener el tamaño actual con escala aplicada
				const newWidth = shape.width() * shape.scaleX();
				const newHeight = shape.height() * shape.scaleY();
		
				console.log("Nueva dimensión - Ancho:", newWidth, "Alto:", newHeight);
					updateShapeInfo(shape); // Llamar la función de actualización (si existe)
				}
				if (e.target.name() === 'background-image') {
						console.log('Click en la imagen de fondo');
						return;
				}
				console.log('click sobre la figura', e.target);
				if (e.target === konva_stage) {
						konva_transformer.nodes([]);
						konva_layer_elem.batchDraw();
				} else {
						konva_transformer.nodes([e.target]);
						konva_layer_elem.batchDraw();
				}
		});

		// Eventos para el movimiento (pan) con el botón izquierdo del mouse
		konva_stage.on('mousedown', function (e) {
				if (e.evt.button === 0 && e.target === konva_stage) { // Botón izquierdo y clic en el escenario (no en un objeto)
						isDragging = true;
						lastPointerPosition = konva_stage.getPointerPosition();
				}
		});

		konva_stage.on('mousemove', function (e) {
				if (isDragging) {
						var pos = konva_stage.getPointerPosition();
						var dx = pos.x - lastPointerPosition.x;
						var dy = pos.y - lastPointerPosition.y;

						// Mover el escenario
						konva_stage.x(konva_stage.x() + dx);
						konva_stage.y(konva_stage.y() + dy);

						// Actualizar la última posición
						lastPointerPosition = pos;

						// Redibujar el escenario
						konva_stage.batchDraw();
				} else if (isDrawing) {
						var pos = konva_stage.getPointerPosition();
						// Ajustar las coordenadas actuales teniendo en cuenta la escala y la posición del escenario
						var currentX = (pos.x - konva_stage.x()) / scale;
						var currentY = (pos.y - konva_stage.y()) / scale;

						// Ajustar el tamaño del rectángulo
						rect.width(currentX - startX);
						rect.height(currentY - startY);

						// Redibujar la capa
						konva_layer_elem.batchDraw();
				}
		});

		konva_stage.on('mouseup', function (e) {
				isDragging = false;
				isDrawing = false;
		});

		setTimeout(() => {
			if(afnCallback) {
				console.log('enviando a cargar las figuras de bd');
				afnCallback();
			} else {
				console.log('no hay callback');
			}
		}, 250);
	}
	
	// Función para hacer la petición AJAX a CodeIgniter 4
	function verificarStandRegistrado(idkonva) {
		return new Promise((resolve, reject) => {
			$.ajax({
				url: '../../Mapa/verificarRegistro',
				type: 'POST',
				data: { 
					id_konva: idkonva,
					id_evento: id_evento // Asegúrate de enviar el ID del evento
				},
				dataType: 'json',
				success: function (response) {
					if (!response.success) {
						// Stand no existe en absoluto
						resolve(false);
						return;
					}
	
					// Stand existe, ahora verificamos si está registrado (tiene info)
					if (response.registrado !== undefined) {
						resolve(response.registrado);
					} else {
						// Para compatibilidad con versiones anteriores
						resolve(response.success);
					}
				},
				error: function (xhr, status, error) {
					console.error('Error verificando stand:', error);
					reject(error);
				}
			});
		});
	}
	
	// Función alternativa para verificar el registro
	function verificarRegistroAlternativo(idkonva) {
		return new Promise((resolve, reject) => {
			$.ajax({
				url: '../../Mapa/obtenerEmpresa',
				type: 'POST',
				data: {
					id_konva: idkonva,
					id_evento: id_evento
				},
				dataType: 'json',
				success: function(response) {
					// Si obtiene datos de empresa, está registrado
					resolve(response.success && response.data !== null);
				},
				error: function() {
					resolve(false);
				}
			});
		});
	}
	/**
	 * Funcion para cargar las figuras desde la base de datos
	 * y dibujarlas en el escenario konva
	 * @param {Array} shapes - Arreglo de figuras a cargar
	 */
	const verEmpresa = (shape) => {
		var id_konva = shape.attrs.id; // Obtener el ID de la figura seleccionada
		var id_evento = lid_evento; // Asegúrate de definir esta variable con el evento actual
	
		// Realizar una petición AJAX para obtener los datos de la empresa
		$.ajax({
			url: '../../Mapa/obtenerEmpresa', // Ruta en el backend
			type: 'POST',
			data: { id_konva: id_konva, id_evento: id_evento },
			dataType: 'json',
			success: function(response) {
				if (response.success) {
					// Rellenar los campos del modal con los datos obtenidos
					$('#modalEmpresa #empresaNombre').text(response.data.nombreEmpresa);
					$('#modalEmpresa #empresaCorreo').text(response.data.correo);
					$('#modalEmpresa #empresaPagina').html('<a href="' + response.data.paginaweb + '" target="_blank">' + response.data.paginaweb + '</a>');
					$('#modalEmpresa #empresaTel').text(response.data.tel);
					$('#modalEmpresa #empresaStand').text(response.data.numero);
					$('#modalEmpresa #empresaRepresentante').text(response.data.nombreRepresentante);
					$('#modalEmpresa #empresaDescripcion').text(response.data.descripcion); 
		
					// Si tiene un logo, mostrarlo
					if (response.data.logo) {
						$('#modalEmpresa #empresaLogo').attr('src', '/mapa/files/uploads/'+response.data.logo).show();
					} else {
						$('#modalEmpresa #empresaLogo').hide(); // Corregido el ID aquí
					}
		
					// Abrir el modal
					$('#modalEmpresa').modal('show');
				} else {
					Swal.fire('Error', 'No se encontraron datos de la empresa.', 'error');
				}
			},
			error: function() {
				Swal.fire('Error', 'No se pudo obtener la información.', 'error');
			}
		});
	}
	
	function cargarFigurasKonva(arg_shapes) {
		console.log('Cargando figuras:', arg_shapes);
	
		let lsigue_buscando = true;
		codeRGB = false;
	
		while (lsigue_buscando) {
			arg_shapes.forEach(shape => {
				// Definir color según el estado
				if (shape.status === 'reserved') {
					codeRGB = "rgba(255, 115, 0, 0.49)";
				} else if (shape.status === 'available') {
					codeRGB = "rgba(51, 255, 0, 0.50)";
				} else if (shape.status === 'occupied') {
					codeRGB = "rgba(255, 0, 0, 0.50)";
					
				}
	
				let newShape = {
					type: shape.type,
					id: shape.id_konva,
					id_konva: (shape.id_konva == null) ? "shape" + (stdx_shapes.length + 1) : shape.id_konva,
					x: parseFloat(shape.x),
					y: parseFloat(shape.y),
					width: parseInt(shape.width),
					height: parseInt(shape.height),
					color: codeRGB,
					fill: codeRGB,
					stroke: "black",
					stroke_width: 2,
					shape: null,
					shapeIndex: null,
					info: null,
					empresa: shape.nombreEmpresa,
					numeroStand: shape.numero,
					title: shape.title || "Figura " + (stdx_shapes.length + 1)  // Título predeterminado
				};
	
				// Dibujar el rectángulo
				let lrect = fnDibujarNuevoRectangulo(newShape);
				newShape.shape = lrect;
				newShape.shapeIndex = lrect.index;
				newShape.info = null;
	
				// Añadir la forma al array
				stdx_shapes.push(newShape);
	
				// Crear el texto
				let titleText = new Konva.Text({
					text: shape.numero,
					fontSize: Math.max(10, newShape.height * 0.15),  // Ajustar tamaño del texto al 15% de la figura
					fontFamily: 'Arial',
					fill: 'white',
					align: 'center'
				});
	
				// Medir tamaño del texto
				let textWidth = titleText.measureSize(shape.numero).width;
				let textHeight = titleText.measureSize(shape.numero).height;
	
				// Centrar el texto en la figura
				titleText.position({
					x: newShape.x + (newShape.width / 2) - (textWidth / 2),
					y: newShape.y + (newShape.height / 2) - (textHeight / 2) + (newShape.height * 0.10)  // Espacio para el logo arriba
				});
	
				// Agregar la figura y el título al layer
				konva_layer_elem.add(lrect);
				konva_layer_elem.add(titleText);
	
				// Si la figura está reservada, agregar el logo
				if (shape.status === 'occupied') {
					let logoImage = new Image();
					logoImage.src = "/mapa/files/uploads/" + shape.logo;

					logoImage.onload = function () {
						// Calcular tamaño del logo proporcionalmente a la figura
						let logoWidth = Math.max(20, newShape.width * 0.3);  // 30% del ancho de la figura, mínimo 20px
						let logoHeight = Math.max(15, newShape.height * 0.2); // 20% de la altura de la figura, mínimo 15px
	
						let logo = new Konva.Image({
							image: logoImage,
							width: logoWidth,
							height: logoHeight,
							x: newShape.x + (newShape.width / 2) - (logoWidth / 2),  // Centrar en X
							y: titleText.y() - logoHeight - 5  // Justo arriba del texto con 5px de margen
						});
	
						konva_layer_elem.add(logo);
						konva_layer_elem.batchDraw();  // Redibujar la capa
					};
				}
			});
	
			saveState();
			lsigue_buscando = false;
		}
	}

	function editarFigura() {
		if (konva_transformer.nodes().length === 0) {
			Swal.fire('Error', 'Por favor selecciona una figura para editar', 'warning');
			return;
		}
	
		var selectedNode = konva_transformer.nodes()[0];
		var figuraId = selectedNode.attrs.id;
	
		$.ajax({
			url: '../../Mapa/obtenerFiguraPorIdKonva/' + figuraId + "/" + id_evento,
			type: 'GET',
			dataType: 'json',
			success: function(data) {
				Swal.fire({
					title: 'Editar Figura',
					html: `
					<div class="mb-3 col-md-12">
						<label class="form-label">Nombre de la empresa</label>
						<input type="text" id="editempresa" class="form-control" value="${data.nombreEmpresa || ''}">
					</div>
					<div class="mb-3 col-md-12">
						<label class="form-label">Correo de contacto</label>
						<input type="email" id="editcorreo" class="form-control" value="${data.correo || ''}">
					</div>
					<div class="mb-3 col-md-12">
						<label class="form-label">Numero de stand</label>
						<input type="number" id="editstand" class="form-control" value="${data.numero || ''}">
					</div>
					<div class="mb-3 col-md-12">
						<label class="form-label">Estado del stand</label>
						<div class="form-check">
							<input class="form-check-input" type="radio" name="estadoStand" id="reserved" value="reserved" ${data.status === 'reserved' ? 'checked' : ''}>
							<label class="form-check-label" for="reserved">
								Reservado
							</label>
						</div>
						<div class="form-check">
							<input class="form-check-input" type="radio" name="estadoStand" id="occupied" value="occupied" ${data.status === 'occupied' ? 'checked' : ''}>
							<label class="form-check-label" for="occupied">
								Ocupado
							</label>
						</div>
					</div>
					<div class="mb-3 col-md-12">
						<label class="form-label">Nombre completo del representante</label>
						<input type="text" id="editnombre" class="form-control" value="${data.nombreRepresentante || ''}">
					</div>
					<div class="mb-3 col-md-12">
						<label class="form-label">Telefóno</label>
						<input type="number" id="edittel" class="form-control" value="${data.tel || ''}">
					</div>
					<div class="mb-3 col-md-12">
						<label class="form-label">Pagina Web</label>
						<input type="text" id="editpagina" class="form-control" value="${data.paginaweb || ''}">
					</div>
					<div class="mb-3 col-md-12">
						<label class="form-label">Descripción</label>
						<textarea id="editdescripcion" class="form-control">${data.descripcion || ''}</textarea>
					</div>
					<div class="mb-3 col-md-12">
						<label class="form-label">Logo</label>
						<input type="file" id="editlogo" class="form-control">
					</div>
					`,
					showCancelButton: true,
					confirmButtonText: 'Guardar',
					cancelButtonText: 'Cancelar',
					focusConfirm: false,
					preConfirm: () => {
						const estadoStand = document.querySelector('input[name="estadoStand"]:checked').value;
						const formData = new FormData();
						
						// Agregar todos los campos al FormData
						formData.append("id_konva", figuraId);
						formData.append("id_evento", id_evento);
						formData.append("stand", document.getElementById('editstand').value);
						formData.append("empresa", document.getElementById('editempresa').value);
						formData.append("pagina", document.getElementById('editpagina').value);
						formData.append("correo", document.getElementById('editcorreo').value);
						formData.append("nombre", document.getElementById('editnombre').value);
						formData.append("tel", document.getElementById('edittel').value);
						formData.append("descripcion", document.getElementById('editdescripcion').value);
						formData.append("status", estadoStand);
						
						// Manejar el logo
						const logoInput = document.getElementById('editlogo');
						if (logoInput.files.length > 0) {
							formData.append("logo", logoInput.files[0]);
						}
	
						return fetch('../../Mapa/actualizarFigura/', {
							method: 'POST',
							body: formData
						})
						.then(response => {
							if (!response.ok) {
								throw new Error('Error en la respuesta del servidor');
							}
							return response.json();
						})
						.then(data => {
							if (!data.success) {
								throw new Error(data.error || 'Error al actualizar');
							}
							return data;
						});
					}
				}).then((result) => {
					if (result.isConfirmed) {

						// Obtener figuras actualizadas
						$.ajax({
							url: '../../Mapa/buscar_stands/' + id_evento,
							type: 'GET',
							success: function(response) {
								//cargarFigurasKonva(response);
								// Mostrar SweetAlert2 Toast de éxito
								Swal.fire({
									icon: 'success',
									title: 'Guardado exitoso',
									text: 'Figura actualizada.',
									toast: true,
									position: 'top-end',
									showConfirmButton: false,
									timer: 3000,
									timerProgressBar: true
								});
							},
							error: function() {
								Swal.fire('Error', 'No se pudieron cargar las figuras actualizadas', 'error');
							}
						});
					}
				}).catch(error => {
					Swal.fire('Error', error.message, 'error');
				});
			},
			error: function(xhr) {
				Swal.fire('Error', 'No se pudieron cargar los datos de la figura', 'error');
			}
		});
	}
	
	// funcion para que konvajs resetee el scale a x: 1 y y: 1
	function resetScale() {	
		konva_stage.scale({ x: 1, y: 1 });
		konva_stage.position({ x: 0, y: 0 });
		konva_stage.batchDraw();
	}

	// Funciones para agregar figuras desde el dashboard
	function preAgregarRectangulo() {
		estilo_form = formulario_nuevo;
		document.querySelector('doby').classList.add('nuevo_rectangulo');
	}

	function agregarRectangulo() {
		agregarNuevoRectangulo();
		return;
		// validar si se tiene la información necesaria antes de agregar un rectángulo

			const pos = konva_stage.getPointerPosition();
			if (!pos) return; // Verificar si hay una posición válida

			// Ajustar las coordenadas al zoom y desplazamiento
			const x = (pos.x - konva_stage.x()) / scale;
			const y = (pos.y - konva_stage.y()) / scale;

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

			konva_layer_elem.add(rect);
			konva_transformer.nodes([rect]);
			konva_layer_elem.batchDraw();

			// Añadir la forma al array stdx_shapes
			stdx_shapes.push({
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

	function agregarNuevoRectangulo() {
		// todo: guardar las figuras /stdx_shapes
		let newShape = {
			type: "Rect",
			id: "-1",
			id_konva: "shape" + (stdx_shapes.length + 1),
			x: Math.random() * konva_stage.width(),
			y: Math.random() * konva_stage.height(),
			width: 100,
			height: 100,
			color: "rgba(0, 0, 255, 0.3)",
			fill: "rgba(0, 0, 255, 0.3)",
			stroke: "blue",
			stroke_width: 2,
			shape: null,
			shapeIndex: null,
			info: null
			// title: "New Shape",
			// subtitle: "Added Shape",
			// url: "https://example.com/new",
		}
		let lrect = fnDibujarNuevoRectangulo(newShape);
		newShape.shape = lrect;
		newShape.shapeIndex = lrect.index;
		newShape.info = null;

		stdx_shapes.push(newShape)
		// Save state for undo/redo
		saveState();

	}

	function fnDibujarNuevoRectangulo(info) {
		const pos = konva_stage.getPointerPosition();
		let localx, localy;
		if (!pos) {
			localx = (info.x - konva_stage.x()) / scale;
			localy = (info.y - konva_stage.y()) / scale;
			// localx = info.x;
			// localy = info.y;
			// return; // Verificar si hay una posición válida
		} else {
			// Ajustar las coordenadas al zoom y desplazamiento
			localx = (pos.x - konva_stage.x()) / scale;
			localy = (pos.y - konva_stage.y()) / scale;
		}

		// Ajustar las coordenadas al zoom y desplazamiento
		// const x = (pos.x - konva_stage.x()) / scale;
		// const y = (pos.y - konva_stage.y()) / scale;

		// Crear un nuevo rectángulo
		const rect = new Konva.Rect({
				x: localx,
				y: localy,
				width: info.width,
				height: info.height,
				color: info.color,
				fill: info.fill,
				stroke: info.stroke,
				strokeWidth: info.stroke_width,
				strokeScaleEnabled: false, // Evitar que el borde se escale con el rectángulo	
				draggable: true,
				id: info.id
		});

		konva_layer_elem.add(rect);
		konva_transformer.nodes([rect]);
		konva_layer_elem.batchDraw();

		return rect;

	}

	function addCircle() {
			const pos = konva_stage.getPointerPosition();
			if (!pos) return; // Verificar si hay una posición válida

			// Ajustar las coordenadas al zoom y desplazamiento
			const x = (pos.x - konva_stage.x()) / scale;
			const y = (pos.y - konva_stage.y()) / scale;

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

			konva_layer_elem.add(circle);
			konva_transformer.nodes([circle]);
			konva_layer_elem.batchDraw();

			// Añadir la forma al array stdx_shapes
			stdx_shapes.push({
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

	// Función para eliminar la figura seleccionada
	function deleteSelectedShape() {
			var selectedNode = konva_transformer.nodes()[0]; // Obtener la figura seleccionada
			if (selectedNode) {
					// Find and remove the shape from the stdx_shapes array
					const index = stdx_shapes.findIndex(s => s.shape === selectedNode);
					if (index !== -1) {
							stdx_shapes.splice(index, 1);
					}
					
					selectedNode.destroy(); // Eliminar la figura
					konva_transformer.nodes([]); // Deseleccionar el Transformer
					konva_layer_elem.batchDraw(); // Redibujar la capa
					
					// Save state for undo/redo
					saveState();
			}
	}

	const abrirFormulario = (shape) => {

		Swal.fire({
			title: "Registrar Stand",
			html: `
					<div class="mb-3 col-md-12">
						<label class="form-label">Nombre de la empresa</label>
						<input type="text" id="empresa" class="form-control">
					</div>
					<div class="mb-3 col-md-12">
						<label class="form-label">Correo de contacto</label>
						<input type="email" id="correo" class="form-control">
					</div>
					<div class="mb-3 col-md-12">
						<label class="form-label">Numero de stand</label>
						<input type="number" id="stand" class="form-control">
					</div>
					<div class="mb-3 col-md-12">
						<label class="form-label">Nombre completo del representante</label>
						<input type="text" id="nombre" class="form-control">
					</div>
					<div class="mb-3 col-md-12">
						<label class="form-label">Telefóno</label>
						<input type="number" id="tel" class="form-control">
					</div>
					<div class="mb-3 col-md-12">
						<label class="form-label">Pagina Web</label>
						<input type="text" id="pagina" class="form-control">
					</div>
					<div class="mb-3 col-md-12">
						<label class="form-label">Descripción</label>
						<textarea id="descripcion" class="form-control" rows="3"></textarea>
					</div>
					<input type="file" id="logo" class="swal2-file">
			`,
			showCancelButton: true,
			confirmButtonText: "Guardar",
			preConfirm: () => {
				const stand = document.getElementById("stand").value;
				const empresa = document.getElementById("empresa").value;
				const pagina = document.getElementById("pagina").value;
				const logo = document.getElementById("logo");
				const correo = document.getElementById("correo").value;
				const nombre = document.getElementById("nombre").value;
				const tel = document.getElementById("tel").value;
				const descripcion = document.getElementById("descripcion").value;

	
				if (!stand || !empresa || !pagina) {
					Swal.showValidationMessage("Todos los campos son obligatorios");
					return false;
				}
	
				const formData = new FormData();
				formData.append("id_konva", shape.attrs.id);
				formData.append("id_evento", id_evento);
				formData.append("stand", stand);
				formData.append("empresa", empresa);
				formData.append("pagina", pagina);
				formData.append("correo", correo);
				formData.append("nombre", nombre);
				formData.append("tel", tel);
				formData.append("descripcion", descripcion);

				if (logo.files.length > 0) {
					formData.append("logo", logo.files[0]);
				}
	
				return fetch('../guardarInformacionStand', {
					method: "POST",
					body: formData
				})
				.then(response => response.json())
				.then(data => {
					if (data.message) {
						return data;
					} else {
						throw new Error("Error al guardar");
					}
				})
				.catch(error => {
					Swal.showValidationMessage(`Error: ${error.message}`);
					return false;
				});
			}
		}).then((result) => {
			if (result.isConfirmed) {
				Swal.fire({
					icon: 'success',
					title: 'Guardado exitoso',
					text: 'Información guardada correctamente',
					toast: true,
					position: 'top-end',
					showConfirmButton: false,
					timer: 3000,
					timerProgressBar: true
				});
			}
		});
	};
	
	function updateShapeInfo(shape) {
		const boundingBox = shape.getClientRect(); // Obtener tamaño real de la figura
		console.log("Ancho:", boundingBox.width, "Alto:", boundingBox.height);

			const index = stdx_shapes.findIndex(s => s.shape === shape);
			if (index !== -1) {
					stdx_shapes[index] = {
							...stdx_shapes[index],
							x: shape.x(),
							y: shape.y(),
							width: shape.width() * shape.scaleX(),
							height: shape.height() * shape.scaleY(),
							radius: shape.radius ? shape.radius() : null,
							fill: shape.fill(),
							stroke: shape.stroke(),
							stroke_width: shape.strokeWidth()
					};
			}
	}

	// Función para guardar las figuras
	const guardarFiguras = () => {
		console.log(stdx_shapes);
		fetch(lurl_guardado + id_evento, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json'
			},
			body: JSON.stringify({ stdx_shapes })
		})
		.then(response => response.json())
		.then(data => {
			console.log("Guardado en BD2:", data);
			// Mostrar SweetAlert2 Toast de éxito
			Swal.fire({
				icon: 'success',
				title: 'Guardado exitoso',
				text: 'Las figuras se guardaron correctamente',
				toast: true,
				position: 'top-end',
				showConfirmButton: false,
				timer: 3000,
				timerProgressBar: true
			});
			        // Recargar las figuras en el lienzo después de guardar
					//inicializarKonva();
		})
		.catch(error => {
			console.error("Error al guardar:", error);
			// Mostrar SweetAlert2 Toast de error
			Swal.fire({
				icon: 'error',
				title: 'Error',
				text: 'Hubo un problema al guardar las figuras',
				toast: true,
				position: 'top-end',
				showConfirmButton: false,
				timer: 3000,
				timerProgressBar: true
			});
		});
	};

	function saveState() {
			// Remove any future states if we're in the middle of the historial
			if (historialStep < historial.length - 1) {
					historial = historial.slice(0, historialStep + 1);
			}
			
			// Create a deep copy of the stdx_shapes array
			const shapesClone = stdx_shapes.map(shape => {
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
			// Clear the current stdx_shapes from the konva_layer_elem
			konva_layer_elem.destroyChildren();
			konva_layer_elem.add(konva_transformer); // Add back the transformer
			
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
							konva_layer_elem.add(shape);
							
							// Add the shape to the shapes array
							stdx_shapes.push({
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
			konva_transformer.nodes([]);
			
			// Redraw the konva_layer_elem
			konva_layer_elem.batchDraw();
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
	function descargarSVG() {
		const svgString = stage.toSVG(); // Genera SVG del lienzo

		// Crear un enlace de descarga
		const blob = new Blob([svgString], { type: "image/svg+xml" });
		const url = URL.createObjectURL(blob);
		const a = document.createElement("a");
		a.href = url;
		a.download = "dibujo.svg";
		document.body.appendChild(a);
		a.click();
		document.body.removeChild(a);
		URL.revokeObjectURL(url);
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