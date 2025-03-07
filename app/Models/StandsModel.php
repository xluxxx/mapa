<?php

namespace App\Models;

use CodeIgniter\Model;

class StandsModel extends Model
{
    protected $table = 'stands'; // Nombre de la tabla
    protected $primaryKey = 'id'; // Clave primaria
    protected $allowedFields = [
        'type', 'x', 'y', 'width', 'height', 'radius', 'stroke_width', 
        'id_evento', 'nombre', 'numero', 'estatus', 'contacto'
    ]; // Campos permitidos para inserción
}