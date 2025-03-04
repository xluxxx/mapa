<?php

namespace App\Models;

use CodeIgniter\Model;

class InfoFiguraModel extends Model
{
    protected $table      = 'info_figura'; // Nombre de la tabla
    protected $primaryKey = 'id'; // Clave primaria de la tabla

    // Campos que se pueden insertar o actualizar
    protected $allowedFields = ['nombre', 'numero', 'estatus', 'img', 'contacto', 'id_figura', 'created_at'];

    // Si deseas que CodeIgniter maneje automáticamente las fechas de creación y actualización
    protected $useTimestamps = true;
    protected $createdField  = 'created_at'; // Campo de fecha de creación
    protected $updatedField  = ''; // No hay campo de actualización en la tabla

    // Si deseas usar la validación
    protected $validationRules    = [
        'nombre'    => 'required|max_length[50]',
        'numero'    => 'required|max_length[10]',
        'estatus'   => 'required|integer',
        'contacto'  => 'required|max_length[50]',
        'id_figura' => 'permit_empty|integer',
    ];

    /*
    protected $validationMessages = [
        'nombre' => [
            'required'   => 'El campo nombre es obligatorio.',
            'max_length' => 'El campo nombre no puede exceder los 50 caracteres.',
        ],
        'numero' => [
            'required'   => 'El campo número es obligatorio.',
            'max_length' => 'El campo número no puede exceder los 10 caracteres.',
        ],
        'estatus' => [
            'required' => 'El campo estatus es obligatorio.',
            'integer'  => 'El campo estatus debe ser un número entero.',
        ],
        'contacto' => [
            'required'   => 'El campo contacto es obligatorio.',
            'max_length' => 'El campo contacto no puede exceder los 50 caracteres.',
        ],
        'id_figura' => [
            'integer' => 'El campo id_figura debe ser un número entero.',
        ],
    ];*/

    // Si deseas usar la devolución de datos como objetos en lugar de arrays
    protected $returnType = 'object';
}