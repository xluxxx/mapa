<?php

namespace App\Controllers;

class Panel extends BaseController
{
    public function Home()
    {
        return view('layouts/panel');
    }
}
