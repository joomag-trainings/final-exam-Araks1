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
            $id = $all[$i]['id'];
        }
        if (password_verify($pwd, $password)) {
            return $id;
        } else {
            return 0;
        }
    }

    public static function discussions($sel)
    {
        $sel = json_decode($sel, true);
        $tit_arr = [];
        $id_arr = [];
        foreach ($sel as $k) {
            $title = $k["title"];
            $id = $k["id"];
            array_push($tit_arr, $title);
            array_push($id_arr, $id);

        }

    }
}