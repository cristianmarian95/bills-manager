<?php

namespace App\Helpers;


class SessionHelper
{
    /**
     * @param $name
     * @return bool
     */
    public function checkSession($name){
        if(isset($_SESSION[$name])){
            return true;
        }
        return false;
    }

    /**
     * @param $name
     * @param $value
     * @return mixed
     */
    public function createSession($name, $value){
        return $_SESSION[$name] = $value;
    }

    /**
     * @param $name
     */
    public function delete($name){
        if($this->checkSession($name)){
            unset($_SESSION[$name]);
        }
        return;
    }

    /**
     * @param $name
     * @param $value
     */
    public function updateSession($name, $value){
        if($this->checkSession($name)){
            $_SESSION[$name] = $value;
        }
        return;
    }

    /**
     * @param $name
     * @return null
     */
    public function get($name){
        if($this->checkSession($name)){
            return $_SESSION[$name];
        }
        return null;
    }
}