<?php

use App\Plugins\Di\Factory;

try {
    $di = Factory::getDi();
    $router = $di->getShared('router');
} catch (Exception $e) {

}

//$router->setBasePath('/web_backend_test_catering_api');
$router->setBasePath('/catering');

require_once '../routes/routes.php';

$router->set404(function () {
    throw new \App\Plugins\Http\Exceptions\NotFound(['error' => 'Route not defined', 'available_routes' => $GLOBALS['registered_routes'] ?? []]);
});

return $router;
