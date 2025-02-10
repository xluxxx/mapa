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
}
