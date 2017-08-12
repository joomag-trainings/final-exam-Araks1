<?php

namespace Controller;

use Psr\Container\ContainerInterface;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class AuthController
{
    protected $table = '';
    public $errorMessage = '';
    protected $db = '';
    private $container = '';
    public $successMessage = '';

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->db = $container->get('db');
        $this->setTable('users');

    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param string $table
     */
    public function setTable($table)
    {
        $this->table = $table;
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

    public function showRegPage(Request $request, Response $response)
    {
        $viewRenderer = $this->container->get('view');
        $response = $viewRenderer->render($response, "RegView.phtml", ["error" => $this->errorMessage]);
    }

    public function getRegisterParams(Request $request, Response $response)
    {
        /* $table= $this->db->table($this->getTable());
         var_dump($table->get());*/
        if ($request->isPost()) {
            $firstname = $_POST['firstname'];
            $lastname = $_POST['lastname'];
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            if ($firstname !== "" && $lastname !== "" && $username !== "" && $email !== "" && $password !== "") {


            } else {
                $this->setErrorMessage("Please fill all fields");

            }
        }

        $viewRenderer = $this->container->get('view');
        $response = $viewRenderer->render($response, "RegView.phtml", ["error" => $this->errorMessage]);
        return $response;

    }

    public function showLoginPage(Request $request, Response $response)
    {
        $viewRenderer = $this->container->get('view');
        $response = $viewRenderer->render($response, "LoginView.phtml");
    }

}