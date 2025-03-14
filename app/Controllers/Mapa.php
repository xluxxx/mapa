<?php

namespace App\Controllers;

use IonAuth\Libraries\IonAuth;
use App\Models\StandsModel; // Importar el modelo de stands
use CodeIgniter\API\ResponseTrait;

class Mapa extends BaseController
{
    use ResponseTrait;

    protected $ionAuth;

    public function __construct()
    {
        $this->ionAuth = new IonAuth(); // Instancia de IonAuth
    }

    public function index(): string
    {
        // Verifica si el usuario está logueado
        if (!is_logged_in()) {
            return redirect()->to('auth/login');
        }

        // Cargar la vista del mapa
        return view('layouts/mapa');
    }

    public function guardar_posiciones($id_evento)
    {
        // Verifica si el usuario está logueado
        if (!is_logged_in()) {
            return redirect()->to('auth/login');
        }

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

        // Cargar el modelo de stands
        $standsModel = new StandsModel();

        // Procesar y guardar cada figura en la base de datos
        foreach ($shapes as $shape) {
            // Preparar los datos para la tabla de stands
            $dataStand = [
                'type' => $shape->type,
                'x' => $shape->x,
                'y' => $shape->y,
                'width' => $shape->width,
                'height' => $shape->height, // Corregido: 'heigth' -> 'height'
                'radius' => $shape->radius ?? null, // Solo para círculos
                'stroke_width' => $shape->stroke_width,
                'id_evento' => $id_evento,
                'nombre' => $shape->info->stand ?? null, // Información adicional
                'numero' => $shape->info->stand ?? null,
                'estatus' => 1, // Estatus por defecto
                'contacto' => $shape->info->stand ?? null,
            ];

            // Insertar el stand en la base de datos
            $standsModel->insert($dataStand);
        }

        // Responder con un mensaje de éxito
        return $this->respond([
            'success' => true,
            'message' => 'Stands guardados correctamente.'
        ]);
    }

    public function generar_mapa()
    {
        // Verifica si el usuario está logueado
        if (!is_logged_in()) {
            return redirect()->to('auth/login');
        }

        // Lógica para generar el mapa
    }
}