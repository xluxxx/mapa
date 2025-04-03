<?php

namespace App\Controllers;

use App\Models\EventModel; // Importar el modelo de eventos
use App\Models\StandsModel;
use IonAuth\Libraries\IonAuth;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class Eventos extends BaseController
{
    protected $ionAuth;
    use ResponseTrait; // Agregar esta línea para poder usar failValidationErrors()

    public function __construct()
    {
        $this->ionAuth = new IonAuth(); // Instancia de IonAuth
    }
    public function guardarInformacionStand()
    {
        $validation = \Config\Services::validation();
    
        $rules = [
            'stand'     => 'required|numeric',
            'empresa'   => 'required|string',
            'pagina' => 'required|valid_url',
            'correo'    => 'required|valid_email',
            'tel'       => 'required|numeric',
            'nombre'    => 'required|string',
            'id_evento' => 'required|numeric',
            'id_konva'  => 'required|string',
            'logo'      => 'is_image[logo]|max_size[logo,2048]',
            'render'      => 'is_image[render]|max_size[render,2048]',
            'descripcion'   => 'required|string|max_length[500]'
        ];
    
        if (!$this->validate($rules)) {
            return $this->failValidationErrors($validation->getErrors());
        }
    
        // Obtener los datos
        $stand     = $this->request->getPost('stand');
        $empresa   = $this->request->getPost('empresa');
        $paginaweb = $this->request->getPost('pagina');
        $correo    = $this->request->getPost('correo');
        $tel       = $this->request->getPost('tel');
        $nombre    = $this->request->getPost('nombre');
        $descripcion= $this->request->getPost('descripcion');
        $id_evento = $this->request->getPost('id_evento');
        $id_konva  = $this->request->getPost('id_konva');
    
        // Manejo de archivo (opcional)
        $logoURL = null;
        $renderURL = null;
        $logo = $this->request->getFile('logo');
        $render = $this->request->getFile('render');

        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            $newName = $logo->getRandomName();
            $logo->move(WRITEPATH . 'uploads/logosEmpresasExpositoras', $newName);
            $logoURL = base_url('uploads/logosEmpresasExpositoras/' . $newName);
        }
        if ($render && $render->isValid() && !$render->hasMoved()) {
            $newName = $render->getRandomName();
            $render->move(WRITEPATH . 'uploads/renders', $newName);
            $renderURL = base_url('uploads/renders/' . $newName);
        }
        // Cargar el modelo
        $standModel = new \App\Models\StandsModel();
    
        // Verificar si el stand ya existe en el evento
        $standExistente = $standModel->where('id_konva', $id_konva)
                                     ->where('id_evento', $id_evento)
                                     ->first();
    
        if ($standExistente) {
            // Actualizar el registro existente
            $dataUpdate = [
                'numero'     => $stand,
                'nombreEmpresa'   => $empresa,
                'paginaweb' => $paginaweb,
                'correo'    => $correo,
                'tel'       => $tel,
                'nombreRepresentante' => $nombre,
                'descripcion'       => $descripcion,
                'id_konva'  => $id_konva,
                'status' => 3,
                'logo' => $newName
            ];
    
            if ($logoURL) {
                $dataUpdate['logo'] = $logoURL;
            }
            if ($renderURL) {
                $dataUpdate['render'] = $renderURL;
            }
            $standModel->update($standExistente['id'], $dataUpdate);
    
            return $this->respond(['message' => 'Registro actualizado con éxito', 'logoURL' => $logoURL], 200);
        } else {
            // Insertar nuevo registro
            $dataInsert = [
                'numero'     => $stand,
                'nombreEmpresa'  => $empresa,
                'paginaweb' => $paginaweb,
                'correo'    => $correo,
                'tel'       => $tel,
                'nombreRepresentante' => $nombre,
                'descripcion'       => $descripcion,
                'id_evento' => $id_evento,
                'id_konva'  => $id_konva,
                'status' => 2,
                'logo' => $newName

            ];
    
            if ($logoURL) {
                $dataInsert['logo'] = $logoURL;
            }
            if ($renderURL) {
                $dataInsert['render'] = $renderURL;
            }
            $standModel->insert($dataInsert);
    
            return $this->respond(['message' => 'Nuevo registro guardado con éxito', 'logoURL' => $logoURL], 201);
        }
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
        $eventos = $eventModel->orderBy('id', 'DESC')->findAll(); // Obtener eventos ordenados por fecha descendente

        // Preparar los datos para la vista (con botones de editar y eliminar)
        $data = [];
        foreach ($eventos as $evento) {
            $data[] = [
                "id" => $evento['id'],
                "clave" => $evento['clave'],
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

    public function verPorClave($clave)
    {
        // 1. Buscar solo datos esenciales del evento
        $evento = (new EventModel())
            ->select('id, clave, name_file')
            ->where('clave', $clave)
            ->first();
    
        if (!$evento) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    
        // 2. Verificar que exista el archivo del plano
        $imagenRelativa = 'public/uploads/planos/' . $evento['name_file'];
        $imagenAbsoluta = FCPATH . $imagenRelativa;

        if (!file_exists($imagenAbsoluta)) {
            // Debug: Verifica la ruta física
            die("El archivo no existe en: " . $imagenAbsoluta);
        }
        return view('layouts/plano_konva_view', [
            'evento' => $evento,
            'imagen_plano' => base_url($imagenRelativa), // URL pública
            'imagen_path' => $imagenAbsoluta, // Ruta física (para debug)
            'stands' => (new StandsModel())->where('id_evento', $evento['id'])->findAll()
        ]);
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