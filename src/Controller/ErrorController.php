<?php

namespace Controller;

use Psr\Container\ContainerInterface;
use Slim\Handlers\NotFound;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class ErrorController extends NotFound
{
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        parent::__invoke($request, $response);
        $viewRenderer = $this->container->get('view');
        $response = $viewRenderer->render($response, "404.phtml");
        return $response->withStatus(404)->withHeader('Content-Type', 'text/html');
    }
}