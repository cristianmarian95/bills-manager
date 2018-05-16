<?php


namespace App\Helpers;

use \App\App;

class Functions extends App
{
    /**
     * @param $uid
     * @return mixed
     */
    public function getUserInfo($uid){
        return $this->db->table('users')->where('id',$uid)->first();
    }

    /**
     * @param $uid
     * @return mixed
     */
    public function getUserAccess($uid){
        $user = $this->getUserInfo($uid);
        return $user->access;
    }

    /**
     * @param $uid
     * @return mixed
     */
    public function getUserBillings($uid){
        return $this->db->table('billings')->where('user_id',$uid)->orderBy('created_at', 'asc')->orderBy('status','asc')->get();
    }

    /**
     * @param $number
     * @return mixed
     */
    public function getBillingDetails($number){
        return $this->db->table('billings')->where('number',$number)->first();
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        $rand_char = null;
        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        for ($i = 0; $i < 6; $i++) {
            $rand_char .= $chars[rand(0, strlen($chars) - 1)];
        }
        $bill = $this->db->table('billings')->where('number', $rand_char)->first();
        if($bill){
            $rand_char = $this->getNumber();
        }
        return $rand_char;
    }

    /**
     * @return mixed
     */
    public function getPrice(){
        return $this->db->table('prices')->where('id',1)->first();
    }

    /**
     * @param $uid
     * @return mixed
     */
    public function myTransactions($uid){
        return $this->db->table('transactions')->where('user_id', $uid)->orderBy('created_at', 'asc')->get();
    }

    /**
     * @param $number
     * @return mixed
     */
    public function getTransaction($number){
        return $this->db->table('transactions')->where('billing_number', $number)->first();
    }

    /**
     * @param $uid
     * @return mixed
     */
    public function getMyLastRead($uid){
        return $this->db->table('readings')->where('user_id', $uid)->first();
    }

    /**
     * @return mixed
     */
    public function getInactiveUsers(){
        return $this->db->table('users')->where('status', 0)->orderBy('created_at', 'asc')->get();
    }

    /**
     * @return mixed
     */
    public function getUnpaidBills() {
        return $this->db->table('billings')->where('status',0)->orderBy('created_at', 'asc')->get();
    }

    /**
     * @return mixed
     */
    public function getUsers(){
        return $this->db->table('users')->orderBy('number', 'asc')->get();
    }
}