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
        $archive = $this->db->selectArchiveDiscussions();
        session_start();
        if (isset($_SESSION["id"])) {
            $list = $this->db->selectMyDiscussions($_SESSION["id"]);
            $viewRenderer = $this->container->get('view');
            $response = $viewRenderer->render($response, "HomePageView.phtml",
                ["args" => $sel, "archive" => $archive, "list" => $list]);
        } else {
            $viewRenderer = $this->container->get('view');
            $response = $viewRenderer->render($response, "HomePageView.phtml", ["args" => $sel, "archive" => $archive]);
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
        $response = $viewRenderer->render($response, "CreateNewDiscussion.phtml", ["error" => $this->errorMessage]);
    }

    public function createNewDiscussion(Request $request,Response $response)
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
                    $last = $this->db->selectLastDiscussion();
                    return $response->withRedirect("single?id=$last");
                } else {
                    $this->setErrorMessage("Something went wrong,Please try again");
                    $viewRenderer = $this->container->get('view');
                    $response = $viewRenderer->render($response, "CreateNewDiscussion.phtml",
                        ["error" => $this->errorMessage]);
                }
            }
            else{
                $this->setErrorMessage("Fill all fields");
                $viewRenderer = $this->container->get('view');
                $response = $viewRenderer->render($response, "CreateNewDiscussion.phtml",
                    ["error" => $this->errorMessage]);
                return $response;
            }
        }
    }

    public function showEachDiscussionPage(Request $request, Response $response)
    {
        $viewRenderer = $this->container->get('view');
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $info = $this->db->eachDiscussion($id);
            $ifBest = $this->db->ifBest($id);
            if ($info === []) {
                return $response = $viewRenderer->render($response, "404.phtml")->withStatus(404);
            }
            $comments = $this->db->selectComments($id);
            $viewRenderer = $this->container->get('view');
            $response = $viewRenderer->render($response, "EachDiscussion.phtml",
                ["info" => $info, "comments" => $comments, "if" => $ifBest]);
        }
    }

    public function edit(Request $request, Response $response)
    {
        $viewRenderer = $this->container->get('view');
        $title = $_POST['title'];
        $desc = $_POST['modal-desc'];
        $id = $_POST['id'];
        $update = $this->db->edit($id, ["title" => $title, "description" => $desc]);
        if ($update) {
            return $response->withRedirect("single?id=$id");
        }
    }

    public function archive(Request $request, Response $response)
    {
        $id = $_POST['id'];
        $update = $this->db->archive($id);
        if ($update) {
            return $response->withRedirect("single?id=$id");
        }
    }
}