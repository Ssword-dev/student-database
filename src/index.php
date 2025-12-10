<?php

use EdgeFramework\Routing\Configurations\RouterConfiguration;
use EdgeFramework\Routing\Enums\OutputKind;
require_once dirname(__DIR__) . "/vendor/autoload.php";
require_once __DIR__ .'/routes/index.php';

use function App\Routes\Entry\attatchRoutes as attatchRoutes;
use EdgeFramework\Routing\EdgeRouter;

$config = new RouterConfiguration(
    projectName: 'student-database',
    outputKind: OutputKind::Streamed
);
$router = new EdgeRouter($config);

// attatch routes.
attatchRoutes($router);

// serve content.
$router->serve();