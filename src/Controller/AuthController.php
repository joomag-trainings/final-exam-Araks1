<?php

namespace Controller;

use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Service\MailService;
use Model\AuthModel;


class AuthController
{
    public $errorMessage = '';
    protected $db = '';
    private $container = '';
    public $successMessage = '';
    public $response = '';

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->db = $this->container->get(AuthModel::class);

    }

    /**
     * @param $param
     * @return string
     */
    static function sanitize($param)
    {
        $param = htmlspecialchars($param);
        $param = trim($param);
        $param = stripslashes($param);
        return $param;
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


    /**
     * @param Request $request
     * @param Response $response
     * @return Response|static
     */
    public function showRegPage(Request $request, Response $response)
    {
        session_start();
        if (isset($_SESSION['id'])) {

            return $response->withRedirect('/forum/public/index.php/home');
        }

        $viewRenderer = $this->container->get('view');
        $response = $viewRenderer->render($response, "RegView.phtml", ["error" => $this->errorMessage]);
        return $response;
    }

    public function registerUsers(Request $request, Response $response)
    {
        if ($request->isPost()) {
            $firstName = self::sanitize($_POST['first_name']);
            $lastName = self::sanitize($_POST['last_name']);
            $userName = self::sanitize($_POST['user_name']);
            $email = self::sanitize($_POST['email']);
            $password = self::sanitize($_POST['password']);
            $confirmPassword = self::sanitize($_POST['conf_password']);
            $accountVerifyHash = bin2hex(random_bytes(16));
            if ($firstName !== "" && $lastName !== "" && $userName !== "" && $email !== "" && $password !== "" && $confirmPassword !== "") {
                if ($password !== $confirmPassword) {
                    $this->setErrorMessage("Passwords don't match");
                } else {


                    $password = password_hash($password, PASSWORD_BCRYPT);
                    $valid = filter_var($email, FILTER_VALIDATE_EMAIL);
                    if ($valid) {
                        $this->db = $this->container->get(AuthModel::class);
                        $this->response = $this->db->insert([
                            "first_name" => $firstName,
                            "last_name" => $lastName,
                            "user_name" => $userName,
                            "email" => $email,
                            "password" => $password,
                            "hash" => $accountVerifyHash
                        ]);

                        if ($this->response === true) {
                            $this->setErrorMessage("Check your email");
                            MailService::sendMail($email, $accountVerifyHash);

                        } else {
                            if ($this->response === 0) {
                                $this->setErrorMessage("This email already exist");

                            } else {
                                if ($this->response === 1) {
                                    $this->setErrorMessage("This username is already in use");
                                } else {
                                    $this->setErrorMessage("Data is too long");
                                }
                            }
                        }
                    }


                }
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
        if (isset($_GET["hash"])) {
            $accountVerifyHash = $_GET["hash"];
            $responseForActivation = $this->db->select($accountVerifyHash);
            if ($responseForActivation === 1) {
                $this->setSuccessMessage("Registration is Done!!!Log in to your account");
            }
        }
        session_start();
        if (isset($_SESSION['id'])) {

            return $response->withRedirect('home');
        }

        $response = $viewRenderer->render($response, "LoginView.phtml", ["error" => $this->successMessage]);
        return $response;
    }


    public function loginUsers(Request $request, Response $response)
    {
        if ($request->isPost()) {
            $email = $_POST['email'];
            $password = $_POST['password'];
            if ($email !== '' && $password !== '') {
                $check = $this->db->checkLogin($email, $password);
                if ($check !== 0) {
                    session_start();
                    $_SESSION["id"] = $check;
                    return $response->withRedirect('home');
                } else {
                    $this->setErrorMessage("Wrong email or password");
                    $viewRenderer = $this->container->get('view');
                    $response = $viewRenderer->render($response, "LoginView.phtml",
                        ["error" => $this->errorMessage]);
                    return $response;
                }
            } else {
                $this->setErrorMessage("Fill all the fields");
                $viewRenderer = $this->container->get('view');
                $response = $viewRenderer->render($response, "LoginView.phtml",
                    ["error" => $this->errorMessage]);
                return $response;
            }
        }

    }

    public function signOut(Request $request, Response $response)
    {
        session_start();
        session_destroy();
        return $response->withRedirect('login');
    }
}