<?php

namespace Model;

use Psr\Container\ContainerInterface;
use Service\AuthService;

class DiscussionModel
{
    protected $table = '';
    protected $db = '';

    public function __construct(ContainerInterface $container)
    {
        $this->db = $container->get('db');
        $this->setTable('discussions');

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

    public function create($params)
    {
        $insert = $this->db->table($this->table)->insert($params);
        return $insert;
    }

    public function selectActiveDiscussions()
    {
        $selected = $this->db->table($this->table)->where("open", 1)->get();
        $selected = json_decode($selected, true);
        return $selected;

    }

    public function selectMyDiscussions($id)
    {
        $selected = $this->db->table($this->table)->where(["open" => 1, "user_id" => $id])->get();
        $selected = json_decode($selected, true);
        return $selected;
    }

    public function eachDiscussion($id)
    {

        $selected = $this->db->table($this->table)->join('users', 'user_id', '=',
            'users.id')->select('users.first_name', 'users.last_name', 'discussions.*')->where('discussions.id',
            $id)->get();
        $selected = json_decode($selected, true);
        return $selected;
    }

    public function selectComments($discussion_id)
    {

        $selected = $this->db->table('comments')->join('users', 'user_id', '=',
            'users.id')->select('users.first_name', 'users.last_name',
            'comments.*')->where(['comments.discussion_id' => $discussion_id])->orderBy('id', 'desc')->get();
        return json_decode($selected, true);
    }
}
