<?php

namespace App\Models;

use CodeIgniter\Model;

class StandsModel extends Model
{
    protected $table = 'stands'; // Nombre de la tabla
    protected $primaryKey = 'id'; // Clave primaria
    protected $allowedFields = [
        'id', 'type', 'x', 'y', 'width', 'height', 'radius', 'stroke_width',
        'id_evento', 'id_konva', 'numero', 'map_id', 'stand_id', 'nombreEmpresa',
        'status', 'correo', 'paginaweb', 'tel', 'nombreRepresentante', 'logo','descripcion'
    ];
    
}