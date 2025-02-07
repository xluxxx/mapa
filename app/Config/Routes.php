<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::index');

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Auth');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();

$routes->setAutoRoute(true);


/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::index');
$routes->get('/panel', 'Panel::Home');
$routes->get('/eventos', 'Eventos::Event');
$routes->get('/eventos', 'Eventos::index'); // Mostrar formulario
$routes->post('/eventos/save', 'Eventos::save'); // Guardar los datos del formulario




