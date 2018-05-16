<?php


namespace App\Middlewares;

use \App\App;

class AccountMiddleware extends App
{
    /**
     * @param $request
     * @param $response
     * @param $next
     * @return mixed
     */
    public function isLog($request, $response, $next){
        if(!$this->session->checkSession('uid')){
            return $this->redirect('login');
        }
        $response = $next($request, $response);
        return $response;
    }

    /**
     * @param $request
     * @param $response
     * @param $next
     * @return mixed
     */
    public function isAdmin($request, $response, $next){
        $user = $this->db->table('users')->where('id', $this->session->get('uid'))->first();
        if($user->access != 1){
            return $this->redirect('index');
        }
        $response = $next($request, $response);
        return $response;
    }

    /**
     * @param $request
     * @param $response
     * @param $next
     * @return mixed
     */
    public function ifLog($request, $response, $next){
        if($this->session->checkSession('uid')){
            return $this->redirect('index');
        }
        $response = $next($request, $response);
        return $response;
    }
}