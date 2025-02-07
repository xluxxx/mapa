<?php

namespace App\Models;

use CodeIgniter\Model;

class EventModel extends Model
{
    protected $table = 'events';  // Nombre de la tabla
    protected $primaryKey = 'id'; // Clave primaria
    protected $allowedFields = ['event_name', 'description', 'event_date', 'event_place']; // Campos permitidos

    protected $useTimestamps = false; // Si usas timestamps automáticos
}
