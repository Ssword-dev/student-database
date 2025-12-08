<?php
namespace App\Routes\Entry;

require_once __DIR__."/home.php";

use EdgeFramework\Routing\EdgeRouter;
use App\Routes\Home\Route as HomeRoute;

function attatchRoutes(EdgeRouter $router){
    $routes = [
        new HomeRoute(),
    ];

    foreach ($routes as $route) {
        $router->addRoute($route);
    }

    return $router;
}