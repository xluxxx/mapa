<?php

namespace App\Controllers;

class Controlador extends BaseController
{
    public function index(): string
    {
        return view('welcome_message');
    }
    public function controllers(): string
    {
        return view('login');
    }
}
