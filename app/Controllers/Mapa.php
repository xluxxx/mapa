<?php

namespace App\Controllers;
use App\Models\EventModel; // Importar el modelo de eventos
use CodeIgniter\API\ResponseTrait;

class Mapa extends BaseController
{
    use ResponseTrait;

    public function index(): string
    {
        return view('layouts/mapa');
    }
    
    public function guardar_posiciones($id_evento){
        
        // Obtener los datos enviados desde el frontend
        $json = $this->request->getJSON(); // Obtener el cuerpo de la solicitud como JSON
        $shapes = $json->shapes; // Acceder al array de shapes

        return $this->respond([
            'success' => true,
            'message' => $shapes
        ]);

        // Validar que shapes no esté vacío
        if (empty($shapes)) {
            return $this->respond([
                'success' => false,
                'message' => 'No se recibieron figuras para guardar.'
            ]);
        }

        // Instanciar el modelo (si es necesario)
        $eventModel = new EventModel();

        // Procesar y guardar cada figura en la base de datos
        foreach ($shapes as $shape) {
            // Aquí puedes validar y guardar cada figura en la base de datos
            // Ejemplo:
            $data = [
                'stand' => $shape->stand,
                'empresa' => $shape->empresa,
                'paginaweb' => $shape->paginaweb,
                'logo_url' => $shape->logoURL,
                'id_evento' => $shape->idEvento,
                'shape_index' => $shape->shapeIndex,
                'info' => json_encode($shape->info) // Si info es un objeto, lo convertimos a JSON
            ];

            // Guardar en la base de datos
            $eventModel->insert($data);
        }

        // Responder con un mensaje de éxito
        return $this->respond([
            'success' => true,
            'message' => 'Figuras guardadas correctamente.'
        ]);
    }
}