<?php


namespace App\Controllers;

use \App\App;

class AdminController extends App
{
    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function index($request, $response, $args){
        return $this->view->render($response, 'admin/index.twig');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function getUsers($request, $response, $args){
        return $this->view->render($response, 'admin/users.twig');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function getUser($request, $response, $args){
        if(!isset($args['id'])){
            return $this->redirect('adminIndex');
        }
        return $this->view->render($response, 'admin/user.twig', ['user_id' => $args['id']]);
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function getBills($request, $response, $args){
        if(!isset($args['id'])){
            return $this->redirect('adminIndex');
        }
        return $this->view->render($response, 'admin/bills.twig', ['user_id' => $args['id']]);
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function getBill($request, $response, $args){
        if(!isset($args['id']) || !isset($args['number'])){
            return $this->redirect('adminIndex');
        }
        return $this->view->render($response, 'admin/bill.twig', ['user_id' => $args['id'], 'bill_number' => $args['number']]);
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function activeUser($request, $response, $args){
        if(!isset($args['id'])){
            return $this->redirect('adminIndex');
        }
        $user = $this->db->table('users')->where('id', $args['id'])->first();
        if($user){
            $this->db->table('users')->where('id',$user->id)->update(['status' => 1]);
            $this->flash->addMessage('success', 'The user ' . $user->username . ' have been actived');
            return $this->redirect('adminIndex');
        }
        $this->flash->addMessage('danger', 'The user not found');
        return $this->redirect('adminIndex');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function setAdmin($request, $response, $args){
        if(!isset($args['id'])){
            return $this->redirect('adminIndex');
        }
        $user = $this->db->table('users')->where('id', $args['id'])->first();
        if($user){
            $this->db->table('users')->where('id',$user->id)->update(['access' => 1]);
            $this->flash->addMessage('success', 'The user ' . $user->username . ' have been set to admin access');
            return $this->redirect('adminIndex');
        }
        $this->flash->addMessage('danger', 'The user not found');
        return $this->redirect('adminIndex');
    }


    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function delAdmin($request, $response, $args){
        if(!isset($args['id'])){
            return $this->redirect('adminIndex');
        }
        $user = $this->db->table('users')->where('id', $args['id'])->first();
        if($user){
            if($user->username == 'admin'){
                $this->flash->addMessage('danger', 'You can\'t downgrade the main admin');
                return $this->redirect('adminIndex');
            }
            $this->db->table('users')->where('id',$user->id)->update(['access' => 0]);
            $this->flash->addMessage('success', 'The user ' . $user->username . ' have been set to user access');
            return $this->redirect('adminIndex');
        }
        $this->flash->addMessage('danger', 'The user not found');
        return $this->redirect('adminIndex');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function deleteUser($request, $response, $args){
        if(!isset($args['id'])){
            return $this->redirect('adminIndex');
        }
        $user = $this->db->table('users')->where('id', $args['id'])->first();
        if($user){
            $this->db->table('users')->where('id',$user->id)->delete();
            $this->flash->addMessage('success', 'The user ' . $user->username . ' have been deleted');
            return $this->redirect('adminIndex');
        }
        $this->flash->addMessage('danger', 'The user not found');
        return $this->redirect('adminIndex');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function getPrices($request, $response, $args){
        return $this->view->render($response, 'admin/prices.twig');
    }

}