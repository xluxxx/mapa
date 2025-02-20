<?php

namespace App\Controllers;

use App\Models\EventModel;

class Eventos extends BaseController
{
    public function index()
    {
        return view('layouts/eventos'); // Cargar la vista del formulario
    }

    public function save()
    {
        // Cargar el modelo
        $eventModel = new EventModel();

        // Definir reglas de validación
        $rules = [
            'event_name' => [
                'label'  => 'event_name',
                'rules'  => 'required|min_length[3]',
                'errors' => [
                    'required' => 'El campo {field} es requerido',
                    'min_length' => 'El campo {field} debe tener más de 3 caracteres',
                ],
            ],
            'description' => [
                'label'  => 'description',
                'rules'  => 'required|min_length[5]',
                'errors' => [
                    'required' => 'El campo {field} es requerido',
                    'min_length' => 'El campo {field} debe tener más de 5 caracteres',
                ],
            ],
            'event_date' => [
                'label'  => 'event_date',
                'rules'  => 'required',
                'errors' => [
                    'required' => 'El campo {field} es requerido',
                ],
            ],
            'event_place' => [
                'label'  => 'event_place',
                'rules'  => 'required',
                'errors' => [
                    'required' => 'El campo {field} es requerido',
                ],
            ]
        ];

        // Validar los datos
        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                "result" => $this->validator->getErrors(),
                "success" => false
            ]);
        }

        // Manejo del archivo
        $file = $this->request->getFile('name_file');
       $nombre = $this->request->getPost('event_name');
       $descripcion = $this->request->getPost('description');
       $fecha = $this->request->getPost('event_date');
       $lugar = $this->request->getPost('event_place');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $mimeType = $file->getMimeType();
            $fileName = $file->getClientName();
            $allowedTypes = ['image/svg+xml', 'image/jpeg', 'image/png'];

            if (!in_array($mimeType, $allowedTypes)) {
                return $this->response->setJSON([
                    "result" => "Solo se permiten archivos en formato SVG, JPG o PNG.",
                    "success" => false
                ]);
            }

            // Generar un nuevo nombre único para el archivo
            $newName = $file->getRandomName();
            $file->move(ROOTPATH . 'public/uploads/planos', $newName); // Guarda en /public/uploads/
            // Guardar el nombre del archivo en la base de datos
           // $data['name_file'] = $newName;

        }
        // Recibir los datos del formulario
        $data = [
            'event_name' => $nombre,
            'description' => $descripcion,
            'event_date' => $fecha,
            'event_place' => $lugar,
            'name_file' => $newName
        ];
        // Insertar los datos en la base de datos
        $guardar = $eventModel->insert($data);

        if ($guardar) {
            return $this->response->setJSON([
                "result"    => "Evento guardado correctamente.",
                "success"   => true,
                "file_path" => isset($guardar),
            ]);
        } else {
            return $this->response->setJSON([
                "result"  => "Error al guardar el evento.",
                "success" => false
            ]);
        }
    }

    public function getEventos()
    {
        $eventModel = new EventModel();
        $eventos = $eventModel->findAll();

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
                                    onclick="window.location.href='<?= base_url('Eventos/obtenerEvento/' . esc($evento['id'], 'url')) ?>'">
                                    Plano
                                </button>
                            </center>',

                "acciones" => '<center>
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary btn-editar" data-id="' . $evento['id'] . '">Editar</button>
                        <button type="button" class="btn btn-primary btn-eliminar" data-id="' . $evento['id'] . '">Eliminar</button>
                    </div>
                </center>'
            ];
        }

        return $this->response->setJSON(['data' => $data]);
    }

    public function eliminarEvento()
    {
        $id = $this->request->getPost('id');
        if (!$id) {
            return $this->response->setStatusCode(400, 'ID requerido');
        }

        $model = new EventModel();
        if ($model->delete($id)) {
            return $this->response->setJSON(['status' => 'success']);
        } else {
            return $this->response->setStatusCode(500, 'Error al eliminar el registro');
        }
    }

    public function actualizarEvento()
    {
        $request = $this->request->getJSON();
        $model = new EventModel();
        $evento = $model->find($request->id);

        if ($evento) {
            $data = [
                'event_name' => $request->nombre,
                'description' => $request->descripcion,
                'event_date' => $request->fecha,
                'event_place' => $request->lugar,
            ];

            $model->update($request->id, $data);
            return $this->response->setJSON(['message' => 'Evento actualizado correctamente']);
        } else {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Evento no encontrado']);
        }
    }

    public function obtenerEvento(){

        $id = $this->request->getPost('id');
        if (!$id) {
            return $this->response->setStatusCode(400, 'ID requerido');
        }
        $model = new EventModel();
        $evento = $model->find($id);

        return $this->response->setJSON(['message' => $evento]);

    }
}
