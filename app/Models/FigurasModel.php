<?php

namespace App\Models;

use CodeIgniter\Model;

class FigurasModel extends Model
{
    protected $table      = 'figuras'; // Nombre de la tabla
    protected $primaryKey = 'id'; // Clave primaria de la tabla

    // Campos que se pueden insertar o actualizar
    protected $allowedFields = [
        'type', 'x', 'y', 'width', 'height', 'radius', 'stroke_width', 'id_evento', 'created_at'
    ];

    // Si deseas que CodeIgniter maneje automáticamente las fechas de creación y actualización
    protected $useTimestamps = true;
    protected $createdField  = 'created_at'; // Campo de fecha de creación
    protected $updatedField  = ''; // No hay campo de actualización en la tabla

    // Si deseas usar la validación
    protected $validationRules    = [
        'type'         => 'required|max_length[50]',
        'x'            => 'required|numeric',
        'y'            => 'required|numeric',
        'width'        => 'permit_empty|numeric',
        'height'       => 'permit_empty|numeric',
        'radius'       => 'permit_empty|numeric',
        'stroke_width' => 'permit_empty|numeric',
        'id_evento'    => 'permit_empty|integer',
    ];

/*
    protected $validationMessages = [
        'type' => [
            'required'   => 'El campo tipo es obligatorio.',
            'max_length' => 'El campo tipo no puede exceder los 50 caracteres.',
        ],
        'x' => [
            'required' => 'El campo x es obligatorio.',
            'numeric'  => 'El campo x debe ser un número.',
        ],
        'y' => [
            'required' => 'El campo y es obligatorio.',
            'numeric'  => 'El campo y debe ser un número.',
        ],
        'width' => [
            'numeric' => 'El campo width debe ser un número.',
        ],
        'height' => [
            'numeric' => 'El campo height debe ser un número.',
        ],
        'radius' => [
            'numeric' => 'El campo radius debe ser un número.',
        ],
        'stroke_width' => [
            'numeric' => 'El campo stroke_width debe ser un número.',
        ],
        'id_evento' => [
            'integer' => 'El campo id_evento debe ser un número entero.',
        ],
    ];
*/

    // Si deseas usar la devolución de datos como objetos en lugar de arrays
    protected $returnType = 'object';
}