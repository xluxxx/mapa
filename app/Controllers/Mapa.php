<?php

namespace App\Controllers;

use App\Models\StandsModel; // Importar el modelo de stands
use CodeIgniter\API\ResponseTrait;

class Mapa extends BaseController
{
    use ResponseTrait;

    public function index(): string
    {
        return view('layouts/mapa');
    }

    public function guardar_posiciones($id_evento)
    {
        // Obtener los datos enviados desde el frontend
        $json = $this->request->getJSON(); // Obtener el cuerpo de la solicitud como JSON
        $shapes = $json->stdx_shapes; // Acceder al array de shapes

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
						// var_dump($shape);
						// continue;
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
								'id_konva' => $shape->id_konva,
                'nombre' => $shape->info->stand ?? null, // Información adicional
                'numero' => $shape->info->stand ?? null,
                'estatus' => 1, // Estatus por defecto
                'contacto' => $shape->info->stand ?? null,
            ];
						// busca el objecto en la base de datos con el id_evento y el id_konva
						$fshape = $standsModel->where('id_evento', $id_evento)
													->where('id_konva', $shape->id_konva)
													->first();
						// si existe el objeto, actualiza los datos
						if ($fshape) {
							$standsModel->where('id_evento', $id_evento)
													->where('id_konva', $fshape["id_konva"])
													->set($dataStand)
													->update();
						} else {
						// si no existe el objeto, lo inserta
							$standsModel->insert($dataStand);
						}
				}

				// Responder con un mensaje de éxito
				return $this->respond([
						'success' => true,
						'message' => 'Stands guardados correctamente.'
				]);
							
    }

		/**
		 * Busca los stands de un evento
		 * 		@param int $id_evento
		 * 		@return array
		 */
		public function buscar_stands($id_evento)
		{
			// Cargar el modelo de stands
			$standsModel = new StandsModel();

			// Buscar los stands del evento
			$stands = $standsModel->where('id_evento', $id_evento)->findAll();

			// Responder con los stands encontrados
			return $this->respond($stands);
		}
			

    public function generar_mapa()
    {
        // Lógica para generar el mapa
    }
}