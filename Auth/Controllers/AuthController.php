<?php

require_once __DIR__."/../Models/User.php";


class AuthController
{
    function getRoleFromUser($user){
        if ($user != null){
            return $user['role'];
        }
        return null;
    }

    function getIdFromUser($user){
        if ($user != null){
            return $user['id'];
        }
        return null;
    }

    function getLoginFromUser($user){
        if ($user != null){
            return $user["login"];
        }
        return null;
    }

    function verifyPasswordFromUser($user, $password){
        if ($user != null){
            if (password_verify($password, $user["password"])) {
                return true;
            }
        }
        return false;

    }
}