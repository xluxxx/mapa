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
            'stand' => [
                'label' => 'Número de Stand',
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => 'El número de stand es obligatorio',
                    'numeric' => 'El stand debe ser un número válido'
                ]
            ],
            'empresa' => [
                'label' => 'Nombre de la empresa',
                'rules' => 'required|string|min_length[3]|max_length[100]',
                'errors' => [
                    'required' => 'El nombre de la empresa es obligatorio',
                    'string' => 'El nombre debe ser texto válido',
                    'min_length' => 'El nombre debe tener al menos 3 caracteres',
                    'max_length' => 'El nombre no puede exceder 100 caracteres'
                ]
            ],
            'pagina' => [
                'label' => 'Página Web',
                'rules' => 'required|valid_url|max_length[200]',
                'errors' => [
                    'required' => 'La página web es obligatoria',
                    'valid_url' => 'Ingrese una URL válida (ej: https://ejemplo.com)',
                    'max_length' => 'La URL no puede exceder 200 caracteres'
                ]
            ],
            'correo' => [
                'label' => 'Correo electrónico',
                'rules' => 'required|valid_email|max_length[100]',
                'errors' => [
                    'required' => 'El correo electrónico es obligatorio',
                    'valid_email' => 'Ingrese un correo electrónico válido',
                    'max_length' => 'El correo no puede exceder 100 caracteres'
                ]
            ],
            'tel' => [
                'label' => 'Teléfono',
                'rules' => 'required|numeric|min_length[7]|max_length[15]',
                'errors' => [
                    'required' => 'El teléfono es obligatorio',
                    'numeric' => 'El teléfono debe contener solo números',
                    'min_length' => 'El teléfono debe tener al menos 7 dígitos',
                    'max_length' => 'El teléfono no puede exceder 15 dígitos'
                ]
            ],
            'nombre' => [
                'label' => 'Nombre del representante',
                'rules' => 'required|string|min_length[3]|max_length[100]',
                'errors' => [
                    'required' => 'El nombre del representante es obligatorio',
                    'string' => 'El nombre debe ser texto válido',
                    'min_length' => 'El nombre debe tener al menos 3 caracteres',
                    'max_length' => 'El nombre no puede exceder 100 caracteres'
                ]
            ],
            'id_evento' => [
                'label' => 'ID del Evento',
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => 'Se requiere el ID del evento',
                    'numeric' => 'ID de evento inválido'
                ]
            ],
            'id_konva' => [
                'label' => 'ID del Elemento',
                'rules' => 'required|string',
                'errors' => [
                    'required' => 'Se requiere el ID del elemento',
                    'string' => 'ID de elemento inválido'
                ]
            ],
            'logo' => [
                'label' => 'Logo',
                'rules' => 'is_image[logo]|max_size[logo,2048]|mime_in[logo,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'is_image' => 'El logo debe ser una imagen válida (JPG, PNG)',
                    'max_size' => 'El tamaño máximo del logo es 2MB',
                    'mime_in' => 'Solo se permiten formatos JPG, JPEG o PNG para el logo'
                ]
            ],
            'render' => [
                'label' => 'Render',
                'rules' => 'is_image[render]|max_size[render,2048]|mime_in[render,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'is_image' => 'El render debe ser una imagen válida (JPG, PNG)',
                    'max_size' => 'El tamaño máximo del render es 2MB',
                    'mime_in' => 'Solo se permiten formatos JPG, JPEG o PNG para el render'
                ]
            ],
            'descripcion' => [
                'label' => 'Descripción',
                'rules' => 'required|string|max_length[500]',
                'errors' => [
                    'required' => 'La descripción es obligatoria',
                    'string' => 'La descripción debe ser texto válido',
                    'max_length' => 'La descripción no debe exceder los 500 caracteres'
                ]
            ]
        ];
    
        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors(),
                'message' => 'Error de validación'
            ]);
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
    try {
        // Verifica si el usuario está logueado
        if (!is_logged_in()) {
            return redirect()->to('auth/login');
        }

        // Cargar el modelo de eventos
        $eventModel = new EventModel();

        // Definir reglas de validación
        $rules = [
            'event_name' => [
                'label' => 'Nombre del Evento',
                'rules' => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required' => 'El nombre del evento es obligatorio',
                    'min_length' => 'El nombre debe tener al menos 3 caracteres',
                    'max_length' => 'El nombre no puede exceder 100 caracteres'
                ],
            ],
            'event_date' => [
                'label' => 'Fecha del Evento',
                'rules' => 'required|valid_date',
                'errors' => [
                    'required' => 'La fecha del evento es obligatoria',
                    'valid_date' => 'La fecha proporcionada no es válida'
                ],
            ],
            'event_place' => [
                'label' => 'Lugar del Evento',
                'rules' => 'required|min_length[3]|max_length[200]',
                'errors' => [
                    'required' => 'El lugar del evento es obligatorio',
                    'min_length' => 'El lugar debe tener al menos 3 caracteres',
                    'max_length' => 'El lugar no puede exceder 200 caracteres'
                ],
            ],
            'description' => [
                'label' => 'Descripción',
                'rules' => 'required|permit_empty|max_length[500]',
                'errors' => [
                    'required' => 'La descripción es obligatoria',
                    'max_length' => 'La descripción no puede exceder 500 caracteres'
                ],
            ],
            'name_file' => [
                'label' => 'Archivo',
                'rules' => 'uploaded[name_file]|max_size[name_file,2048]|mime_in[name_file,image/svg+xml,image/jpeg,image/png]',
                'errors' => [
                    'uploaded' => 'Debe seleccionar un archivo',
                    'max_size' => 'El tamaño máximo permitido es 2MB',
                    'mime_in' => 'Solo se permiten archivos SVG, JPG o PNG'
                ]
            ]
        ];

        // Validar los datos del formulario
        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            return $this->response->setJSON([
                "success" => false,
                "errors" => $errors,
                "message" => "Por favor corrija los errores en el formulario"
            ]);
        }

        // Procesar el archivo subido
        $file = $this->request->getFile('name_file');
        $newName = null;

        if ($file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(ROOTPATH . 'public/uploads/planos', $newName);
        }

        // Preparar datos para guardar
        $data = [
            'event_name' => $this->request->getPost('event_name'),
            'description' => $this->request->getPost('description'),
            'event_date' => $this->request->getPost('event_date'),
            'event_place' => $this->request->getPost('event_place'),
            'name_file' => $newName,
            'clave' => $this->request->getPost('clave'),
        ];

        // Insertar en la base de datos
        $guardar = $eventModel->insert($data);
        
        if (!$guardar) {
            throw new \RuntimeException('Error al guardar en la base de datos');
        }

        return $this->response->setJSON([
            "success" => true,
            "message" => "Evento guardado correctamente",
            "data" => [
                "event_id" => $guardar,
                "file_path" => $newName ? base_url('uploads/planos/'.$newName) : null
            ]
        ]);

    } catch (\Exception $e) {
        return $this->response->setJSON([
            "success" => false,
            "message" => "Error en el servidor: " . $e->getMessage(),
            "error" => $e->getTraceAsString()
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

    private function generarUUIDv4() {
        // Generar 16 bytes (128 bits) aleatorios
        $data = random_bytes(16);

        // Establecer la versión a 0100 (UUID v4)
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);

        // Establecer los bits del "variant" a 10xx
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

        // Convertir los bytes en una cadena con formato UUID
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

}
