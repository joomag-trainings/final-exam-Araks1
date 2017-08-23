<?php

namespace Model;

use Psr\Container\ContainerInterface;
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
        $sel = $this->db->table($this->table)->where('email', $params['email'])->count();
        if ($sel === 0) {
            try {

                $insert = $this->db->table($this->table)->insert($params);
                return AuthService::insert($insert);
            } catch (\Exception $exception) {
                $code = $exception->errorInfo[1];
                if ($code === 1062) {
                    return 1;
                } else {
                    return $exception->getMessage();
                }
            }
        } else {
            return 0;
        }
    }

    public function select($hash)
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