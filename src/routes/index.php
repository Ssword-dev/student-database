<?php
namespace App\Routes\Entry;

require_once __DIR__ . "/home.php";
require_once __DIR__ . "/_not-found.php";

use EdgeFramework\Routing\EdgeRouter;
use App\Routes\Home\Route as HomeRoute;
use NotFoundRoute;

function attatchRoutes(EdgeRouter $router){
    $routes = [
        new HomeRoute(),
        new NotFoundRoute(),
    ];

    foreach ($routes as $route) {
        $router->addRoute($route);
    }

    return $router;
}