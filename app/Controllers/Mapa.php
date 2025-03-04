<?php

namespace App\Controllers;
use App\Models\EventModel; // Importar el modelo de eventos
use App\Models\FigurasModel; // Importar el modelo de eventos
use App\Models\InfoFiguraModel; // Importar el modelo de eventos

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

        // Validar que shapes no esté vacío
        if (empty($shapes)) {
            return $this->respond([
                'success' => false,
                'message' => 'No se recibieron figuras para guardar.'
            ]);
        }
        
        return $this->respond([
            'success' => true,
            'message' => $shapes
        ]);

        // Procesar y guardar cada figura en la base de datos
        foreach ($shapes as $shape) {
            // Aquí puedes validar y guardar cada figura en la base de datos
            $dataFigura = [
                'type' => $shape->type,
                'x' => $shape->x,
                'y' => $shape->y,
                'width' => $shape->width,
                'heigth' => $shape->height,
                'radius' => $shape->radius,
                'stroke_width' => $shape->stroke_width,
                'id_evento' => $id_evento,
            ];

            // $figurasModel->insert($data);

            $info = $shape->info;

            $data = [
                'nombre'   => $info->stand,
                'numero'   => $info->stand,
                'estatus'  => 1,
                'contacto' => $info->stand,
                'id_figura' => 1,
            ];
            
            // $figurasModel->insert($data);

        }
        

        // Responder con un mensaje de éxito
        return $this->respond([
            'success' => true,
            'message' => "cool"
        ]);
    }

    public function generar_mapa(){

    }
}