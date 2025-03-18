<?php

namespace App\Controllers;

use IonAuth\Libraries\IonAuth;

class Panel extends BaseController
{
    protected $ionAuth;

    public function __construct(){
        $this->ionAuth = new IonAuth();
    }

    public function Home()
    {
        if (!is_logged_in()) {
            return redirect()->to('auth/login');
        }
        return view('layouts/panel');
    }
}
