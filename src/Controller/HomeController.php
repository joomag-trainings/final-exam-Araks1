<?php

namespace Controller;

use Model\DiscussionModel;
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
        $this->db = $this->container->get(DiscussionModel::class);

    }

    public function showHomePage(Request $request, Response $response)
    {
        $sel = $this->db->selectActiveDiscussions();
        session_start();
        if (isset($_SESSION["id"])) {
            $list = $this->db->selectMyDiscussions($_SESSION["id"]);
            $viewRenderer = $this->container->get('view');
            $response = $viewRenderer->render($response, "HomePageView.phtml", ["args" => $sel, "list" => $list]);

        } else {
            $viewRenderer = $this->container->get('view');
            $response = $viewRenderer->render($response, "HomePageView.phtml", ["args" => $sel]);
        }

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

    public function showCreatePage(Request $request, Response $response)
    {
        session_start();
        if (!isset($_SESSION["id"])) {
            return $response->withRedirect("login");
        }
        $viewRenderer = $this->container->get('view');
        $response = $viewRenderer->render($response, "CreateNewDiscussion.phtml");
    }

    public function createNewDiscussion(Request $request, Response $response)
    {
        if (isset($_POST['create'])) {
            $title = $_POST['title'];
            $desc = $_POST['desc'];

            if ($title !== "" && $desc !== "") {
                session_start();
                $ins = $this->db->create([
                    "user_id" => $_SESSION['id'],
                    "title" => $title,
                    "description" => $desc,
                    "open" => 1,
                    "created_at" => date("Y-m-d H:i:s"),
                ]);
                if ($ins) {

                } else {

                }
            }
        }
    }

    public function showEachDiscussionPage(Request $request, Response $response)
    {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $info = $this->db->eachDiscussion($id);
            $viewRenderer = $this->container->get('view');
            $response = $viewRenderer->render($response, "EachDiscussion.phtml", ["info" => $info]);
        }

    }

}