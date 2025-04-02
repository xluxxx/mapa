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

    public function obtenerFiguraPorIdKonva($konva, $evento)
    {

        // Validar que el nombre no esté vacío
        if (empty($konva)) {
            return $this->response->setJSON(['error' => 'Id Konva no válido'], 400); // 400 Bad Request
        }
    
        // Consultar si el nombre existe en la base de datos
        $model = new StandsModel();
        $figura = $model->where('id_evento', $evento)->where('id_konva', $konva)->first();  // Buscar por nombre en la base de datos
    
        if (!$figura) {
            return $this->response->setJSON(['error' => 'Figura no encontrada'], 404); // 404 Not Found
        }
    
        // Si la figura existe, devolver los datos
        return $this->response->setJSON($figura);
    }
    public function verificarRegistro()
    {
        $idKonva = $this->request->getPost('id_konva');
        $idEvento = $this->request->getPost('id_evento'); // Asegúrate de enviar esto desde AJAX
    
        if (!$idKonva || !$idEvento) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Datos incompletos'
            ]);
        }
    
        $standModel = new StandsModel();
        $stand = $standModel->where('id_konva', $idKonva)
                           ->where('id_evento', $idEvento)
                           ->first();
    
        if ($stand) {
            // Verifica si el stand tiene información básica registrada
            $tieneInfo = !empty($stand['nombreEmpresa']) && !empty($stand['numero']);
            
            return $this->response->setJSON([
                'success' => true,
                'registrado' => $tieneInfo, // true si tiene info, false si solo existe
                'data' => $stand // Opcional: enviar datos si necesitas
            ]);
        }
    
        return $this->response->setJSON([
            'success' => false,
            'registrado' => false,
            'message' => 'Stand no existe'
        ]);
    }
    
    public function obtenerEmpresa()
    {
        $id_konva = $this->request->getPost('id_konva');
        $id_evento = $this->request->getPost('id_evento');

        $model = new StandsModel();
        
        // Buscar el registro donde coincidan id_konva y id_evento
        $empresa = $model->where('id_konva', $id_konva)
                        ->where('id_evento', $id_evento)
                        ->first();

        if ($empresa) {
            return $this->response->setJSON(['success' => true, 'data' => $empresa]);
        } else {
            return $this->response->setJSON(['success' => false, 'error' => 'Empresa no encontrada']);
        }
    }

    public function actualizarFigura()
    {
        // Validar que todos los campos requeridos estén presentes
        $required = ['id_konva', 'id_evento', 'stand', 'empresa', 'status'];
        foreach ($required as $field) {
            if (empty($this->request->getPost($field))) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => "El campo $field es requerido"
                ]);
            }
        }

        $model = new StandsModel();
        
        // Preparar datos para actualización
        $data = [
            'numero' => $this->request->getPost('stand'),
            'nombreEmpresa' => $this->request->getPost('empresa'),
            'paginaweb' => $this->request->getPost('pagina'),
            'correo' => $this->request->getPost('correo'),
            'nombreRepresentante' => $this->request->getPost('nombre'),
            'tel' => $this->request->getPost('tel'),
            'descripcion' => $this->request->getPost('descripcion'),
            'status' => $this->request->getPost('status') // Asegurar que el estado se guarda correctamente
        ];

        // Manejar el logo si se subió uno nuevo
        $logo = $this->request->getFile('logo');
        if ($logo && $logo->isValid()) {
            $newName = $logo->getRandomName();
            $logo->move(WRITEPATH . 'uploads/logosEmpresasExpositoras', $newName);
            $data['logo'] = $newName;
        }
        // Manejar el logo si se subió uno nuevo
        $render = $this->request->getFile('render');
        if ($render && $render->isValid()) {
            $newName = $render->getRandomName();
            $render->move(WRITEPATH . 'uploads/renders', $newName);
            $data['render'] = $newName;
        }
        // Actualizar el registro
        $updated = $model->where('id_konva', $this->request->getPost('id_konva'))
                        ->where('id_evento', $this->request->getPost('id_evento'))
                        ->set($data)
                        ->update();

        if ($updated) {
            // Devolver los datos actualizados
            $updatedData = $model->where('id_konva', $this->request->getPost('id_konva'))
                                ->where('id_evento', $this->request->getPost('id_evento'))
                                ->first();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $updatedData
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'error' => 'No se pudo actualizar la figura'
        ]);
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
                //'nombre' => $shape->info->stand ?? null, // Información adicional
                //'numero' => $shape->info->stand ?? null,
                'estatus' => 1, // Estatus por defecto
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
        // Verifica si el usuario está logueado
        if (!is_logged_in()) {
            return redirect()->to('auth/login');
        }

        // Lógica para generar el mapa
    }
}