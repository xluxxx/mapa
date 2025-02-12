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

        // Definir reglas de validación como array
     /* $rules = [
        'event_name'  => 'required|min_length[3]',
        'description' => 'required|min_length[5]',
        'event_date'  => 'required',
        'event_place' => 'required',
      ];*/

      $rules =([
        'event_name' => [
            'label'  => 'event_name',
            'rules'  => 'required|min_length[3]',
            'errors' => [
                'required' => 'el campo {field} es requerido',
                'min_length' => 'el campo {field} debe tener mas de 3 caracteres',

            ],
        ],
        'description' => [
            'label'  => 'description',
            'rules'  => 'required|min_length[5]',
            'errors' => [
                'required' => 'el campo {field} es requerido',
                'min_length' => 'el campo {field} debe tener mas de 5 caracteres',

            ],
        ],
        'event_date' => [
            'label'  => 'event_date',
            'rules'  => 'required',
            'errors' => [
                'required' => 'el campo {field} es requerido',

            ],
        ],
        'event_place' => [
            'label'  => 'event_place',
            'rules'  => 'required',
            'errors' => [
                'required' => 'el campo {field} es requerido',

            ],
        ],

    ]);

       // Verificar si los datos son válidos
    if (!$this->validate($rules)) {
        return $this->response->setJSON(["result" => $this->validator->getErrors(),"success" => false]);
       // return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Recibir los datos del formulario
        $data = [
            'event_name' => $this->request->getPost('event_name'),
            'description' => $this->request->getPost('description'),
            'event_date' => $this->request->getPost('event_date'),
            'event_place' => $this->request->getPost('event_place'),
        ];

        // Insertar los datos en la base de datos
        $guardar = $eventModel->save($data);

        if ($guardar){
			return $this->response->setJSON(["result" => true,"success" => true]);
		} else {
			return $this->response->setJSON(["result" => false,"success" => false]);
		}

        // Redirigir con un mensaje de éxito

    }
    public function getEventos()
{
    $eventModel = new EventModel();
    $eventos = $eventModel->findAll(); // Obtiene todos los eventos

    $data = [];
    foreach ($eventos as $evento) {
        $data[] = [
            "id" => $evento['id'],
            "event_name" => $evento['event_name'],
            "description" => $evento['description'],
            "event_date" => $evento['event_date'],
            "event_place" => $evento['event_place'],
            "acciones" => '
    <div class="dropdown mb-auto">
        <div class="btn-link" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10 11.9999C10 13.1045 10.8954 13.9999 12 13.9999C13.1046 13.9999 14 13.1045 14 11.9999C14 10.8954 13.1046 9.99994 12 9.99994C10.8954 9.99994 10 10.8954 10 11.9999Z" fill="black"></path>
                <path d="M10 4.00006C10 5.10463 10.8954 6.00006 12 6.00006C13.1046 6.00006 14 5.10463 14 4.00006C14 2.89549 13.1046 2.00006 12 2.00006C10.8954 2.00006 10 2.89549 10 4.00006Z" fill="black"></path>
                <path d="M10 20C10 21.1046 10.8954 22 12 22C13.1046 22 14 21.1046 14 20C14 18.8954 13.1046 18 12 18C10.8954 18 10 18.8954 10 20Z" fill="black"></path>
            </svg>
        </div>
        <div class="dropdown-menu dropdown-menu-end">
            <a class="dropdown-item" href="javascript:void(0)" data-id="' . $evento['id'] . '">Editar</a>
            <a class="dropdown-item" href="javascript:void(0)" data-id="' . $evento['id'] . '">Eliminar</a>
        </div>
    </div>
'

        ];
    }

    return $this->response->setJSON(['data' => $data]);
}

}
