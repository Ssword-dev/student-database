<?php
require_once __DIR__ . '/libs/autoload.php';
require_once __DIR__ .'/routes/index.php';

use function App\Routes\Entry\attatchRoutes as attatchRoutes;
use EdgeFramework\Routing\EdgeRouter;

$router = new EdgeRouter(__DIR__);

// attatch routes.
attatchRoutes($router);

// serve content.
$router->serve();