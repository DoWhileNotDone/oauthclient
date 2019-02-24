<?php declare(strict_types=1);

use Slim\Http\Request;
use Slim\Http\Response;

// Define named route
$app->get('/', function (Request $request, Response $response, array $args): Response {
    return $this->view->render($response, 'index.html', $args);
});
