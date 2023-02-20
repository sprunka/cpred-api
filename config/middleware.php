<?php

use CyberpunkRedAPI\Middleware\FormatInjector;
use Selective\BasePath\BasePathMiddleware;
use Slim\App;
use Slim\Middleware\ErrorMiddleware;

return function (App $app) {
    // Parse json, form data and xml
    $app->addBodyParsingMiddleware();

    // Add the Slim built-in routing middleware
    $app->addRoutingMiddleware();

    // Force-fix BasePath.
    $app->add(BasePathMiddleware::class);

    // Injector "format" from Headers
    $app->add(middleware: FormatInjector::class);

    // Catch exceptions and errors
    $app->add(ErrorMiddleware::class);

};
