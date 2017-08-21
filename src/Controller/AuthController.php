<?php

namespace Controller;

use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use phpmailerException;
use Exception;
use Model\AuthModel;
use PHPMailer;

class AuthController
{

    public $errorMessage = '';
    protected $db = '';
    private $container = '';
    public $successMessage = '';
    public $response = '';
    private $table = '';

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
        session_start();
        if (isset($_SESSION['id'])) {

            return $response->withRedirect('home');
        }

        $viewRenderer = $this->container->get('view');
        $response = $viewRenderer->render($response, "RegView.phtml", ["error" => $this->errorMessage]);
        return $response;
    }

    public function getRegisterParams(Request $request, Response $response)
    {
        if ($request->isPost()) {
            $firstName = self::sanitize($_POST['first_name']);
            $lastName = self::sanitize($_POST['last_name']);
            $userName = self::sanitize($_POST['user_name']);
            $email = self::sanitize($_POST['email']);
            $password = self::sanitize($_POST['password']);
            $hash = bin2hex(random_bytes(16));
            if ($firstName !== "" && $lastName !== "" && $userName !== "" && $email !== "" && $password !== "") {
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
                        "hash" => $hash
                    ]);

                    if ($this->response === true) {
                        $this->setErrorMessage("Check your email");

                        $mail = new PHPMailer(true);
                        try {
                            $mail->isSMTP();
                            $mail->SMTPAuth = true;
                            $mail->SMTPSecure = "tls";
                            $mail->Host = "smtp.gmail.com";
                            $mail->Port = 587;
                            $mail->Username = "letstalkforum0@gmail.com";
                            $mail->Password = "letstalk1111";
                            $mail->setFrom('letstalkforum0@gmail.com', 'Forum');
                            $mail->addReplyTo('letstalkforum0@gmail.com', 'Forum');
                            $mail->AddAddress($email, $email);
                            $mail->Subject = 'Confirmation of account';
                            $mail->msgHTML(" <p>Hi,</p>
        <p>            
        Thanks for Registration.  We have received a request for a creating account associated with this email address.
        </p>
        <p>
        To confirm , please click <a href='http://localhost/forum/public/index.php/login?hash=$hash'>here</a>.  If you did not initiate this request,
        please disregard this message .
        </p >
        <p >
        If you have any questions about this email, you may contact us at letstalkforum0@gmail.com .
        </p >
        <p >
                            With regards,
        <br >
                            The Forum . com Team
                            </p > ");

                            $mail->send();
                        } catch (phpmailerException $e) {
                            echo $e->errorMessage();
                        } catch (Exception $e) {
                            echo $e->getMessage();
                        }

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
            $hash = $_GET["hash"];
            $res = $this->db->sel($hash);
            if ($res === 1) {
                $this->setSuccessMessage("Registration is Done!!!Log in to your account");
            }
        }
        session_start();
        if (isset($_SESSION['id'])) {

            return $response->withRedirect('home');
        }

        $response = $viewRenderer->render($response, "LoginView.phtml", ["error" => $this->successMessage]);
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

                }
            } else {
                $this->setErrorMessage("Fill all the fields");
                $viewRenderer = $this->container->get('view');
                $response = $viewRenderer->render($response, "LoginView.phtml",
                    ["error" => $this->errorMessage]);
            }
        }

    }

    public function signOut(Request $request,Response $response)
    {
        session_start();
        session_destroy();
        return $response->withRedirect('login');
    }
}