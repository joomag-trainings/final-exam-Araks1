<?php

namespace Service;

class AuthService
{
    public static function insert($insert)
    {
        return $insert;
    }

    public static function checkLogin($all, $pwd)
    {

        $all = json_decode($all, true);

        if ($all == []) {
            return 0;
        }
        for ($i = 0; $i < count($all); $i++) {
            $password = $all[$i]['password'];
        }
        if (password_verify($pwd, $password)) {
            return 1;
        } else {
            return 0;
        }
    }
}