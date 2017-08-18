<?php

namespace Model;

use Psr\Container\ContainerInterface;
use Slim\Container;
use Service\AuthService;

class AuthModel
{
    protected $table = '';
    protected $db = '';
    public $error = '';

    public function __construct(ContainerInterface $container)
    {

        $this->db = $container->get('db');
        $this->setTable('users');

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

    public function insert($params)
    {
        try {
            $insert = $this->db->table($this->table)->insert($params);
            return AuthService::insert($insert);
        } catch (\Exception $exception) {
            $this->error = $exception->getMessage();
            return $this->error;
        }

    }

    public function sel($hash)
    {
        $sel = $this->db->table($this->table)->where(["hash" => $hash, "active" => 0])->count();
        $upd = $this->db->table($this->table)->where("hash", $hash)->update(["active" => 1, "hash" => 0]);
        return $sel;
    }

    public function checkLogin($email, $password)
    {
        $all = $this->db->table($this->table)->where(["email" => $email, "active" => 1])->get();
        return AuthService::checkLogin($all, $password);
    }
}