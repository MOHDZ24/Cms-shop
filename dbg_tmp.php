<?php
require __DIR__ . '/app/bootstrap.php';
header('Content-Type: text/plain');
echo "BASE_URL=["; var_export(BASE_URL); echo "]\n";
echo "SCRIPT_NAME=", $_SERVER['SCRIPT_NAME'] ?? '-', " SCRIPT_FILENAME=", $_SERVER['SCRIPT_FILENAME'] ?? '-', "\n";
echo "DOCROOT=", $_SERVER['DOCUMENT_ROOT'] ?? '-', "\n";
$router = new App\Core\Router();
$router->get('/lang/{code}', 'App\Controllers\LangController@switch');
$r = new ReflectionMethod(App\Core\Router::class, 'dispatch');
echo "--- dispatching /lang/fr ---\n";
$router->dispatch('GET', '/lang/fr');
