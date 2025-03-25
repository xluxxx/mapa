<?php

namespace App\Models;

use CodeIgniter\Model;

class EventModel extends Model
{
    protected $table = 'events';  // Nombre de la tabla
    protected $primaryKey = 'id'; // Clave primaria
    protected $allowedFields = ['clave','event_name', 'description', 'event_date', 'event_place', 'name_file']; // Campos permitidos
    protected $useTimestamps = false; // Si usas timestamps automáticos
    protected $validationRules = [
        'clave' => 'required|is_unique[events.clave]|regex_match[/^[A-Z0-9\-]+$/]',
    ];
}
