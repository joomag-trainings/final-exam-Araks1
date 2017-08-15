<?php

namespace Controller;

use Psr\Container\ContainerInterface;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Service\AuthService;
use Model\AuthModel;
use PHPMailer;

class AuthController
{

    public $errorMessage = '';
    protected $db = '';
    private $container = '';
    public $successMessage = '';

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->db = $this->container->get(AuthModel::class);

    }

    /**
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

        $viewRenderer = $this->container->get('view');
        $response = $viewRenderer->render($response, "RegView.phtml", ["error" => $this->errorMessage]);
    }

    public function getRegisterParams(Request $request, Response $response)
    {
        if ($request->isPost()) {
            $firstname = self::sanitize($_POST['first_name']);
            $lastname = self::sanitize($_POST['last_name']);
            $username = self::sanitize($_POST['user_name']);
            $email = self::sanitize($_POST['email']);
            $password = self::sanitize($_POST['password']);
            $hash = bin2hex(random_bytes(16));
            if ($firstname !== "" && $lastname !== "" && $username !== "" && $email !== "" && $password !== "") {
                $password = password_hash($password, PASSWORD_BCRYPT);
                $valid = filter_var($email, FILTER_VALIDATE_EMAIL);
                if ($valid) {
                    $this->db = $this->container->get(AuthModel::class);
                    $this->errorMessage = $this->db->insert([
                        "first_name" => $firstname,
                        "last_name" => $lastname,
                        "user_name" => $username,
                        "email" => $email,
                        "password" => $password,
                        "hash" => $hash
                    ]);
                    if ($this->errorMessage === true) {
                        $this->setErrorMessage("Check your email");

                        $mail = new PHPMailer(true);
                        try {
                            $mail->isSMTP();
                            $mail->SMTPDebug = true;
                            $mail->SMTPAuth = true;
                            $mail->SMTPSecure = "tls";
                            $mail->Host = "smtp.gmail.com";
                            $mail->Port = 587;
                            $mail->Username = "araqs.shahbazian@gmail.com";
                            $mail->Password = "araqssh1995";

                            $mail->setFrom('araqs.shahbazian@gmail.com', 'Forum');
                            $mail->addReplyTo('araqs.shahbazian@gmail.com', 'Forum');
                            $mail->AddAddress($email, $email);

                            $mail->Subject = 'Subject of the Email';
                            $mail->msgHTML(" <p>Hi,</p>
        <p>            
        Thanks for Registration.  We have received a request for a creating account associated with this email address.
        </p>
        <p>
        To confirm and reset your password, please click <a href='http://localhost/forum/public/index.php/login?hash=$hash'>here</a>.  If you did not initiate this request,
        please disregard this message .
        </p >
        <p >
        If you have any questions about this email, you may contact us at support@badger - dating . com .
        </p >
        <p >
                            With regards,
        <br >
                            The BadgerDating . com Team
                            </p > ");


                            $mail->send();
                        } catch (phpmailerException $e) {
                            echo $e->errorMessage();
                        } catch (Exception $e) {
                            echo $e->getMessage();
                        }

                    }
                } else {
                    $this->setErrorMessage("Invalid email");
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
        if (isset($_GET["hash"])) {
            $hash = $_GET["hash"];
            $res = $this->db->sel($hash);
            if ($res === 1) {
                $this->setSuccessMessage("Registration is Done!!!Log in to your account");
            }
        }
        $viewRenderer = $this->container->get('view');
        $response = $viewRenderer->render($response, "LoginView.phtml", ["success" => $this->successMessage]);
    }

    public function loginUsers(Request $request, Response $response)
    {
        if ($request->isPost()) {
            $email = $_POST['email'];
            $password = $_POST['password'];
            if ($email !== '' && $password !== '') {
                $check = $this->db->checkLogin($email, $password);
                if ($check === 1) {
                    //redirect
                } else {
                    $this->setSuccessMessage("Wrong email or password");
                    $viewRenderer = $this->container->get('view');
                    $response = $viewRenderer->render($response, "LoginView.phtml",
                        ["success" => $this->successMessage]);

                }
            }
        }

    }
}