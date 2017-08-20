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

    public function create(Request $request, Response $response)
    {
        $inp = $_POST["input"];
        $id = $_POST["id"];
        if ($inp !== "") {
            session_start();
            $res = $this->db->create([
                "discussion_id" => $id,
                "user_id" => $_SESSION['id'],
                "comment" => $inp,
                "created_at" => date("Y-m-d H:i:s")
            ]);
            if ($res === true) {
                $show = $this->db->select($id, $_SESSION['id']);
                echo $show;
            }
        }
    }

    public function delete(Request $request, Response $response)
    {
        if (isset($_GET['cid'])) {
            $commentId = $_GET['cid'];
            $id = $_GET['id'];
            $resp = $this->db->delete($commentId);
            if ($resp) {
                return $response->withRedirect("single?id=$id");
            }
        }
    }

    public function markBestAnswer(Request $request, Response $response)
    {
        $id = $_GET['id'];
        $desc = $_GET["desc"];
        $res = $this->db->markBest($id);
        return $response->withRedirect("single?id=$desc");
    }
}