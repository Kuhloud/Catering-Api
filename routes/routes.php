<?php

/** @var Bramus\Router\Router $router */

// Define routes here
$router->get('/test', App\Controllers\IndexController::class . '@test');
$router->get('/', App\Controllers\IndexController::class . '@test');
$router->post('/facility', App\Controllers\FacilityController::class . '@createFacility');
$router->get('/facility/{\d+}', App\Controllers\FacilityController::class . '@readFacility');
$router->get('/facilities', App\Controllers\FacilityController::class . '@readFacilities');
$router->put('/facility/{\d+}', App\Controllers\FacilityController::class . '@updateFacility');
$router->delete('/facility/{\d+}', App\Controllers\FacilityController::class . '@deleteFacility');

error_log("Registered Routes:\n" . print_r([
        'POST /facility' => isset($router->routes['POST']['/facility']),
        'DELETE /facility/{d}' => isset($router->routes['DELETE']['/facility/{d}'])
    ], true));

//error_log("Route /createFacility registered: " .
//    (isset($router->routes['POST']['/createFacility']) ? 'YES' : 'NO'));
