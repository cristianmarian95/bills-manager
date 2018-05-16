<?php

namespace App\Controllers;

class MainController extends \App\App
{
    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function index($request, $response, $args){
        return $this->view->render($response, 'user/index.twig');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function login($request, $response, $args){
        return $this->view->render($response, 'user/login.twig');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function register($request, $response, $args){
        return $this->view->render($response, 'user/register.twig');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function recover($request, $response, $args){
        return $this->view->render($response, 'user/recover.twig');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function changePassword($request, $response, $args){
        if(!isset($args['key'])){
            $this->flash->addMessage('danger', 'Invalid link');
            return $this->redirect('recover');
        }
        $recover = $this->db->table('recovers')->where('recover_key', $args['key'])->where('status', 0)->first();
        if($recover){
            if(date('Y-m-d H:i:s') > date('Y-m-d H:i:s', strtotime($recover->expire_at))) {
                $this->flash->addMessage('danger', 'The link is expired. Please request a new one');
                return $this->redirect('recover');
            } else {
                return $this->view->render($response, 'user/updatepassword.twig', ['key' => $recover->recover_key]);
            }
        }
        $this->flash->addMessage('danger', 'Invalid link');
        return $this->redirect('recover');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function billingPay($request, $response, $args){
        if(!isset($args['number'])){
            return $this->redirect('index');
        }
        if($this->function->getBillingDetails($args['number'])->status == 1){
            return $response->withRedirect($this->router->pathFor('billingDetails', ['number' => $args['number']]));
        }
        return $this->view->render($response, 'user/billingPay.twig', ['billing_number' => $args['number']]);
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function billingDetails($request, $response, $args) {
        if(!isset($args['number'])){
            return $this->redirect('index');
        }
        $billing = $this->db->table('billings')->where('user_id', $this->session->get('uid'))->where('number', $args['number'])->first();
        if(!$billing){
            return $this->redirect('index');
        }
        return $this->view->render($response, 'user/billingDetails.twig', ['billing_number' => $args['number']]);
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function myTransactions($request, $response, $args){
        return $this->view->render($response, 'user/userTransactions.twig');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function getReading($request, $response, $args){
        return $this->view->render($response, 'user/reading.twig');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function myProfile($request, $response, $args){
        return $this->view->render($response, 'user/profile.twig');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function logout($request, $response, $args){
        $this->session->delete('uid');
        return $this->redirect('login');
    }
}