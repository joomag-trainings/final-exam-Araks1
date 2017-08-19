<?php


namespace Model;

use Psr\Container\ContainerInterface;

class CommentsModel
{
    protected $table = '';
    protected $db = '';

    public function __construct(ContainerInterface $container)
    {
        $this->db = $container->get('db');
        $this->setTable('comments');

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

    public function select($discussion_id, $user_id)
    {
        $sel = $this->db->table($this->table)->orderBy('id', 'desc')->first();
        $sel = json_decode(json_encode($sel), true);
        $id = $sel['id'];

        $selected = $this->db->table($this->table)->join('users', 'user_id', '=',
            'users.id')->select('users.first_name', 'users.last_name', 'comments.*')->where('comments.id',
            $id)->get();
        return $selected;
    }

    public function delete($id)
    {
        $del = $this->db->table($this->table)->where('id', $id)->delete();
        return $del;
    }
}