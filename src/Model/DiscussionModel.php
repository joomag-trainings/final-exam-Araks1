<?php

namespace Model;

use Psr\Container\ContainerInterface;

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

    public function selectLastDiscussion()
    {
        $selected = $this->db->table($this->table)->orderBy('id', 'DESC')->first();
        $selected = json_decode(json_encode($selected), true);
        $id = $selected['id'];
        return $id;
    }

    public function selectMyDiscussions($id)
    {
        $selected = $this->db->table($this->table)->where(["user_id" => $id])->get();
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
            'comments.*')->where(['comments.discussion_id' => $discussion_id])->orderByRaw('FIELD(best, "1") DESC')->orderBy('id',
            'DESC')->get();
        return json_decode($selected, true);
    }

    public function selectArchiveDiscussions()
    {
        $selected = $this->db->table($this->table)->where("open", 0)->get();
        $selected = json_decode($selected, true);
        return $selected;
    }

    public function ifBest($id)
    {
        $count = $this->db->table("comments")->where(["discussion_id" => $id, "best" => 1])->count();
        return $count;
    }

    public function edit($id, $params)
    {
        $upd = $this->db->table($this->table)->where("id", $id)->update($params);
        return $upd;
    }

    public function archive($id)
    {
        $upd = $this->db->table($this->table)->where("id", $id)->update(["open" => 0]);
        return $upd;
    }
}