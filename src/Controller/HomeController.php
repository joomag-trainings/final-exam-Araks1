<?php

namespace Controller;

use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class HomeController
{
    public $errorMessage = '';
    protected $db = '';
    private $container = '';
    public $successMessage = '';

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        //$this->db = $this->container->get(AuthModel::class);

    }

    public function showHomePage(Request $request, Response $response)
    {
        $viewRenderer = $this->container->get('view');
        $response = $viewRenderer->render($response, "HomePageView.phtml");
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * @param string $errorMessage
     */
    public function setErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * @return string
     */
    public function getSuccessMessage()
    {
        return $this->successMessage;
    }

    /**
     * @param string $successMessage
     */
    public function setSuccessMessage($successMessage)
    {
        $this->successMessage = $successMessage;
    }

    public function createNewDiscussion(Request $request, Response $response)
    {
        session_start();
        if (!isset($_SESSION["id"])) {
            return $response->withRedirect("login");
        }
        $viewRenderer = $this->container->get('view');
        $response = $viewRenderer->render($response, "CreateNewDiscussion.phtml");
    }
}