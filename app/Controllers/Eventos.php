<?php

namespace App\Controllers;

class Eventos extends BaseController
{
    public function index()
    {
        return view('layouts/eventos');
    }
}