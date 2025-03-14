<?php

use IonAuth\Libraries\IonAuth;

if (!function_exists('is_logged_in')) {
    /**
     * Verifica si el usuario está logueado.
     *
     * @return bool
     */
    function is_logged_in()
    {
        $ionAuth = new IonAuth(); // Instancia de IonAuth
        return $ionAuth->loggedIn(); // Retorna true si el usuario está logueado, false si no
    }
}

if (!function_exists('redirect_if_not_logged_in')) {
    /**
     * Redirige al usuario a la página de login si no está logueado.
     *
     * @return void
     */
    function redirect_if_not_logged_in()
    {
        if (!is_logged_in()) {
            return redirect()->to('auth/login'); // Redirige al login
        }
    }
}