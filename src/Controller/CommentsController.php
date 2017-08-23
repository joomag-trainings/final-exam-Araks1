<?php

namespace Controller;

use Model\CommentsModel;
use Slim\Http\Request;
use Psr\Container\ContainerInterface;
use Slim\Http\Response;

class CommentsController
{
    protected $db = '';
    private $container = '';

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->db = $this->container->get(CommentsModel::class);
    }

    public function addComment(Request $request, Response $response)
    {
        $commentText = $_POST["input"];
        $id = $_POST["id"];
        if ($commentText !== "") {
            session_start();
            $addComment = $this->db->create([
                "discussion_id" => $id,
                "user_id" => $_SESSION['id'],
                "comment" => $commentText,
                "created_at" => date("Y-m-d H:i:s")
            ]);
            if ($addComment === true) {
                $showComment = $this->db->select($id, $_SESSION['id']);
                echo $showComment;
            }
        }
    }

    public function deleteComment(Request $request, Response $response)
    {
        if (isset($_GET['cid'])) {
            $commentId = $_GET['cid'];
            $id = $_GET['id'];
            $deleteComment = $this->db->deleteComment($commentId);
            if ($deleteComment) {
                return $response->withRedirect("/forum/public/index.php/discussion?id=$id");
            }
        }
    }

    public function markBestAnswer(Request $request, Response $response)
    {
        $id = $_GET['id'];
        $discussionId = $_GET["desc"];
        $res = $this->db->markBest($id);
        return $response->withRedirect("/forum/public/index.php/discussion?id=$discussionId");
    }
}