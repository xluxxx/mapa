<?php

namespace App\Controllers;

use App\Models\EventModel; // Importar el modelo de eventos
use IonAuth\Libraries\IonAuth;

class Eventos extends BaseController
{
    protected $ionAuth;

    public function __construct()
    {
        $this->ionAuth = new IonAuth(); // Instancia de IonAuth
    }

    // Función que carga la vista principal del formulario
    public function index()
    {
        // Verifica si el usuario está logueado
        if (!is_logged_in()) {
            return redirect()->to('auth/login');
        }

        return view('layouts/eventos'); // Cargar la vista del formulario
    }

    // Función para guardar un nuevo evento
    public function save()
    {
        // Verifica si el usuario está logueado
        if (!is_logged_in()) {
            return redirect()->to('auth/login');
        }

        // Cargar el modelo de eventos para interactuar con la base de datos
        $eventModel = new EventModel();

        // Definir reglas de validación para los campos del formulario
        $rules = [
            'event_name' => [
                'label' => 'event_name',
                'rules' => 'required|min_length[3]',
                'errors' => [
                    'required' => 'El campo {field} es requerido',
                    'min_length' => 'El campo {field} debe tener más de 3 caracteres',
                ],
            ],
            'description' => [
                'label' => 'description',
                'rules' => 'required|min_length[5]',
                'errors' => [
                    'required' => 'El campo {field} es requerido',
                    'min_length' => 'El campo {field} debe tener más de 5 caracteres',
                ],
            ],
            'event_date' => [
                'label' => 'event_date',
                'rules' => 'required',
                'errors' => [
                    'required' => 'El campo {field} es requerido',
                ],
            ],
            'event_place' => [
                'label' => 'event_place',
                'rules' => 'required',
                'errors' => [
                    'required' => 'El campo {field} es requerido',
                ],
            ]
        ];

        // Validar los datos del formulario con las reglas definidas
        if (!$this->validate($rules)) {
            return $this->response->setJSON([ // Si no pasa la validación, retorna errores
                "result" => $this->validator->getErrors(),
                "success" => false
            ]);
        }

        // Obtener los datos del archivo subido y del formulario
        $file = $this->request->getFile('name_file');
        $nombre = $this->request->getPost('event_name');
        $descripcion = $this->request->getPost('description');
        $fecha = $this->request->getPost('event_date');
        $lugar = $this->request->getPost('event_place');

        // Verificar que el archivo sea válido y no se haya movido ya
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $mimeType = $file->getMimeType(); // Obtener el tipo MIME del archivo
            $fileName = $file->getClientName(); // Obtener el nombre original del archivo
            $allowedTypes = ['image/svg+xml', 'image/jpeg', 'image/png']; // Tipos de archivo permitidos

            // Verificar si el archivo tiene un tipo válido
            if (!in_array($mimeType, $allowedTypes)) {
                return $this->response->setJSON([ // Si el archivo no es válido, retornar un mensaje
                    "result" => "Solo se permiten archivos en formato SVG, JPG o PNG.",
                    "success" => false
                ]);
            }

            // Generar un nuevo nombre aleatorio para el archivo
            $newName = $file->getRandomName();
            $file->move(ROOTPATH . 'public/uploads/planos', $newName); // Mover el archivo a la carpeta de destino
        }

        // Preparar los datos para insertar en la base de datos
        $data = [
            'event_name' => $nombre,
            'description' => $descripcion,
            'event_date' => $fecha,
            'event_place' => $lugar,
            'name_file' => $newName // Incluir el nombre del archivo subido
        ];

        // Insertar los datos en la base de datos
        $guardar = $eventModel->insert($data);

        // Retornar una respuesta dependiendo del resultado de la inserción
        if ($guardar) {
            return $this->response->setJSON([
                "result" => "Evento guardado correctamente.",
                "success" => true,
                "file_path" => isset($guardar),
            ]);
        } else {
            return $this->response->setJSON([ // Si hay un error al guardar el evento
                "result" => "Error al guardar el evento.",
                "success" => false
            ]);
        }
    }

    // Función para obtener todos los eventos
    public function getEventos()
    {
        // Verifica si el usuario está logueado
        if (!is_logged_in()) {
            return redirect()->to('auth/login');
        }

        $eventModel = new EventModel();
        $eventos = $eventModel->findAll(); // Obtener todos los eventos

        // Preparar los datos para la vista (con botones de editar y eliminar)
        $data = [];
        foreach ($eventos as $evento) {
            $data[] = [
                "id" => $evento['id'],
                "event_name" => $evento['event_name'],
                "description" => $evento['description'],
                "event_date" => $evento['event_date'],
                "event_place" => $evento['event_place'],
                "plano" => '<center>
                                <button type="button" class="btn btn-primary btn-rounded" 
                                    onclick="window.location.href=\'' . base_url('Eventos/obtenerEvento/' . esc($evento['id'], 'url')) . '\'">
                                    Plano
                                </button>
                            </center>',
                "acciones" => '<center>
                    <div class="btn-group">
                        <button type="button" class="btn btn-snapchat btn-editar" data-id="' . $evento['id'] . '">Editar</button>
                        <button type="button" class="btn btn-danger btn-eliminar" data-id="' . $evento['id'] . '">Eliminar</button>
                    </div>
                </center>'
            ];
        }

        // Retornar los eventos en formato JSON
        return $this->response->setJSON(['data' => $data]);
    }

    // Función para eliminar un evento
    public function eliminarEvento()
    {
        // Verifica si el usuario está logueado
        if (!is_logged_in()) {
            return redirect()->to('auth/login');
        }

        $id = $this->request->getPost('id');
        if (!$id) {
            return $this->response->setStatusCode(400, 'ID requerido'); // Verificar si el ID fue proporcionado
        }

        $model = new EventModel();
        if ($model->delete($id)) { // Eliminar el evento de la base de datos
            return $this->response->setJSON(['status' => 'success']);
        } else {
            return $this->response->setStatusCode(500, 'Error al eliminar el registro');
        }
    }

    // Función para actualizar los datos de un evento
    public function actualizarEvento()
    {
        // Verifica si el usuario está logueado
        if (!is_logged_in()) {
            return redirect()->to('auth/login');
        }

        $request = $this->request->getJSON(); // Obtener los datos JSON enviados
        $model = new EventModel();
        $evento = $model->find($request->id); // Buscar el evento por su ID

        if ($evento) { // Si el evento existe, actualizarlo
            $data = [
                'event_name' => $request->nombre,
                'description' => $request->descripcion,
                'event_date' => $request->fecha,
                'event_place' => $request->lugar,
            ];

            $model->update($request->id, $data); // Actualizar los datos en la base de datos
            return $this->response->setJSON(['message' => 'Evento actualizado correctamente']);
        } else {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Evento no encontrado']);
        }
    }

    // Función para obtener los detalles de un evento específico
    public function obtenerEvento($id)
    {
        // Verifica si el usuario está logueado
        if (!is_logged_in()) {
            return redirect()->to('auth/login');
        }

        if (!$id) {
            return $this->response->setStatusCode(400, 'ID requerido'); // Verificar si el ID fue proporcionado
        }
        $model = new EventModel();
        $evento = $model->find($id); // Buscar el evento por su ID
        $data['evento'] = $evento; // Asignar el evento a los datos

        return view('layouts/mapa', $data); // Cargar la vista con los detalles del evento
    }
}