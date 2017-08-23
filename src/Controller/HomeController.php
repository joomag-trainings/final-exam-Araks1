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
        $activeDiscussions = $this->db->selectActiveDiscussions();
        $archiveDiscussions = $this->db->selectArchiveDiscussions();
        session_start();
        if (isset($_SESSION["id"])) {
            $myDiscussions = $this->db->selectMyDiscussions($_SESSION["id"]);
            $viewRenderer = $this->container->get('view');
            $response = $viewRenderer->render($response, "HomePageView.phtml",
                [
                    "activeDiscussions" => $activeDiscussions,
                    "archive" => $archiveDiscussions,
                    "myDiscussions" => $myDiscussions
                ]);
        } else {
            $viewRenderer = $this->container->get('view');
            $response = $viewRenderer->render($response, "HomePageView.phtml",
                ["activeDiscussions" => $activeDiscussions, "archiveDiscussions" => $archiveDiscussions]);
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

    public function showDiscussionCreatePage(Request $request, Response $response)
    {
        session_start();
        if (!isset($_SESSION["id"])) {
            return $response->withRedirect("login");
        }
        $viewRenderer = $this->container->get('view');
        $response = $viewRenderer->render($response, "CreateNewDiscussion.phtml", ["error" => $this->errorMessage]);
    }

    public function createNewDiscussion(Request $request, Response $response)
    {
        if (isset($_POST['create'])) {
            $title = $_POST['title'];
            $description = $_POST['desc'];
            if ($title !== "" && $description !== "") {
                session_start();
                $insert = $this->db->create([
                    "user_id" => $_SESSION['id'],
                    "title" => $title,
                    "description" => $description,
                    "open" => 1,
                    "created_at" => date("Y-m-d H:i:s"),
                ]);
                if ($insert) {
                    $last = $this->db->selectLastDiscussion();
                    return $response->withRedirect("/forum/public/index.php/discussion?id=$last");
                } else {
                    $this->setErrorMessage("Something went wrong,Please try again");
                    $viewRenderer = $this->container->get('view');
                    $response = $viewRenderer->render($response, "CreateNewDiscussion.phtml",
                        ["error" => $this->errorMessage]);
                }
            } else {
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
            $eachDiscussion = $this->db->eachDiscussion($id);
            $bestDiscussion = $this->db->selectBestDiscussions($id);
            if ($eachDiscussion === []) {
                return $response = $viewRenderer->render($response, "404.phtml")->withStatus(404);
            }
            $comments = $this->db->selectComments($id);
            $viewRenderer = $this->container->get('view');
            $response = $viewRenderer->render($response, "EachDiscussion.phtml",
                ["eachDiscussion" => $eachDiscussion, "comments" => $comments, "bestDiscussion" => $bestDiscussion]);
        }
    }

    public function editDiscussion(Request $request, Response $response)
    {
        $viewRenderer = $this->container->get('view');
        $title = $_POST['title'];
        $description = $_POST['modal-desc'];
        $id = $_POST['id'];
        $update = $this->db->editDiscussion($id, ["title" => $title, "description" => $description]);
        if ($update) {
            return $response->withRedirect("/forum/public/index.php/discussion?id=$id");
        }
    }

    public function archiveDiscussion(Request $request, Response $response)
    {
        $id = $_POST['id'];
        $archiveDiscussion = $this->db->archiveDiscussion($id);
        if ($archiveDiscussion) {
            return $response->withRedirect("/forum/public/index.php/discussion?id=$id");
        }
    }
}