<?php

namespace App\Controllers;

class ActionController extends \App\App
{
    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function actionLogin($request, $response, $args) {
        $data = $request->getParsedBody();
        $this->validate->addRuleMessages(['required' => 'You need to enter a {field} to continue']);
        $this->validate->validate([
            'username' => [$data['username'], 'required'],
            'password' =>[$data['password'], 'required']
        ]);
        if(!$this->validate->passes()){
            $this->flash->addMessage('danger', $this->validate->errors()->first());
            return $this->redirect('login');
        }
        $user = $this->db->table('users')->where('username', $data['username'])->first();
        if($user){
            if(password_verify($data['password'], $user->password)) {
                $this->session->createSession('uid', $user->id);
                return $this->redirect('index');
            }else{
                $this->flash->addMessage('danger', 'The password is incorrect');
                return $this->redirect('login');
            }
        }else{
            $this->flash->addMessage('danger', 'The user ' . $data['username'] . ' do not exist');
            return $this->redirect('login');
        }
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function actionRegister($request, $response, $args){
        $data = $request->getParsedBody();
        $this->validate->addRuleMessages([
            'required' => 'You need to enter a {field} to continue',
            'email' => 'The {value} it\'s not a valid email',
            'matches' => 'The password\'s do not matches',
            'min' => 'Invalid phone number'
        ]);
        $this->validate->validate([
            'username' => [$data['username'], 'required'],
            'password' => [$data['password'], 'required'],
            'conf_password|confirm password' => [$data['conf_password'], 'required|matches(password)'],
            'email' => [$data['email'], 'required|email'],
            'name' => [$data['name'], 'required'],
            'last_name' => [$data['last_name'], 'required'],
            'phone' => [$data['phone'], 'required|min(8)'],
            'floor' => [$data['floor'], 'required'],
            'number' => [$data['number'], 'required'],
            'number_of_persons|number of persons' => [$data['number_of_persons'], 'required'],
            'number_of_pets|number of pets' => [$data['number_of_pets'], 'required']
        ]);
        if(!$this->validate->passes()){
            $this->flash->addMessage('danger', $this->validate->errors()->first());
            return $this->redirect('register');
        }
        $username = $this->db->table('users')->where('username', $data['username'])->first();
        if($username){
            $this->flash->addMessage('danger', 'The username already exists. Recover your account');
            return $this->redirect('register');
        }
        $email = $this->db->table('users')->where('email', $data['email'])->first();
        if($email){
            $this->flash->addMessage('danger', 'The email already exists. Recover your account');
            return $this->redirect('register');
        }
        $number = $this->db->table('users')->where('number', $data['number'])->first();
        if($number){
            $this->flash->addMessage('danger', 'There is already an account on this number');
            return $this->redirect('register');
        }
        $this->db->table('users')->insert([
            'username' => $data['username'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'email' => $data['email'],
            'phone' => $data['phone'],
            'name' => $data['name'],
            'last_name' => $data['last_name'],
            'floor' => $data['floor'],
            'number' => $data['number'],
            'number_of_persons' => $data['number_of_persons'],
            'number_of_pets' => $data['number_of_pets'],
            'status' => 0,
            'access' => 0,
            'created_at' => date('Y-m-d h:i:sa'),
            'updated_at' => date('Y-m-d h:i:sa')
        ]);
        $this->flash->addMessage('success', 'The account was successfully registered');
        $this->flash->addMessage('info', 'The administrator will review your account for activation');
        return $this->redirect('register');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function actionRecover($request, $response, $args){
        $data = $request->getParsedBody();
        $this->validate->addRuleMessages(['required' => 'You need to enter a {field} to continue']);
        $this->validate->validate(['username' => [$data['username'], 'required']]);
        if(!$this->validate->passes()){
            $this->flash->addMessage('danger', $this->validate->errors()->first());
            return $this->redirect('recover');
        }
        $user = $this->db->table('users')->where('username', $data['username'])->first();
        if(!$user){
            $this->flash->addMessage('danger', 'The user ' . $data['username'] . ' do not exist');
            return $this->redirect('recover');
        }
        if($user->status != 1){
            $this->flash->addMessage('danger', 'The account is not active');
            return $this->redirect('recover');
        }
        $recover = $this->db->table('recovers')->where('user_id', $user->id)->where('status', 0)->first();
        if($recover){
            if(date('Y-m-d H:i:s') < date('Y-m-d H:i:s', strtotime($recover->expire_at))){
                $this->flash->addMessage('danger', 'You can send a recovery email every 30 minutes');
                return $this->redirect('recover');
            } else {
                $this->db->table('recovers')->where('user_id', $user->id)->delete();
            }
        }
        $key = md5(date('Y-m-d H:i:sa'));
        $this->mail->recover($user->email, $key);
        $this->db->table('recovers')->insert([
            'user_id' => $user->id,
            'recover_key' => $key,
            'status' => 0,
            'expire_at' => date('Y-m-d H:i:s', strtotime("+30 minutes")),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        $this->flash->addMessage('success', 'Check your email account for information');
        $this->flash->addMessage('info', 'You can send a recovery email every 30 minutes');
        $this->flash->addMessage('info', 'The link will expire in 30 minutes');
        return $this->redirect('recover');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function actionChangePassword($request, $response, $args){
        $data = $request->getParsedBody();
        $this->validate->addRuleMessages([
            'required' => 'You need to enter a {field} to continue',
            'matches' => 'The password\'s do not matches',
        ]);
        $this->validate->validate([
            'key' => [$data['key'], 'required'],
            'password' => [$data['password'], 'required'],
            'conf_password|confirm password' => [$data['conf_password'], 'required|matches(password)'],
        ]);
        $recover = $this->db->table('recovers')->where('recover_key', $data['key'])->where('status', 0)->first();
        if($recover){
            if(date('Y-m-d H:i:s') > date('Y-m-d H:i:s', strtotime($recover->expire_at))){
                $this->flash->addMessage('danger', 'The link is expired. Please request a new one');
                return $this->redirect('recover');
            } else {
                if(!$this->validate->passes()){
                    $this->flash->addMessage('danger', $this->validate->errors()->first());
                    return $response->withRedirect($this->router->pathFor('changePassword', ['key' => $recover->recover_key]));
                }
                $user = $this->db->table('users')->where('id', $recover->user_id)->first();
                if($user){
                    $this->db->table('users')->where('id', $recover->user_id)->update([
                        'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    $this->db->table('recovers')->where('recover_key', $recover->recover_key)->update([
                        'status' => 1,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    $this->flash->addMessage('success', 'Your password successfully changed');
                    return $this->redirect('login');
                } else {
                    $this->flash->addMessage('danger', 'The user do not exist');
                    return $this->redirect('recover');
                }
            }
        }else{
            $this->flash->addMessage('danger', 'The link is not valid');
            return $this->redirect('recover');
        }

    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function makePayment($request, $response, $args)
    {

        $data = $request->getParsedBody();
        $this->validate->addRuleMessages([
            'required' => 'You need to fill in all spaces to continue',
            'number' => 'Invalid card data'
        ]);
        $this->validate->validate([
            'card_number|Card number' => [$data['card_number'], 'required|number|max(16)|min(16)'],
            'year' => [$data['year'], 'required|number'],
            'month' => [$data['month'], 'required|number'],
            'cvv' => [$data['cvv'], 'required|number'],
            'billing_number|billing number' => [$data['billing_number'], 'required'],
        ]);
        if(!$this->validate->passes()){
            $this->flash->addMessage('danger', $this->validate->errors()->first());
            return $response->withRedirect($this->router->pathFor('billingPay', ['number' => $data['billing_number']]));
        }
        if($this->function->getBillingDetails($data['billing_number'])->status == 1){
            return $response->withRedirect($this->router->pathFor('billingDetails', ['number' => $data['billing_number']]));
        }
        $transaction_mid = 'card_' .  md5($data['billing_number']);
        $card_number = 'xxxx xxxx xxxx ' . substr($data['card_number'], -4);
        $this->db->table('transactions')->insert([
            'billing_number' => $data['billing_number'],
            'user_id' => $this->session->get('uid'),
            'transaction_mid' => $transaction_mid,
            'card_number' => $card_number,
            'total_pay' => $this->function->getBillingDetails($data['billing_number'])->total_amount,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        $this->db->table('billings')->where('number', $data['billing_number'])->where('user_id',$this->session->get('uid'))->update(['status' => 1]);
        return $response->withRedirect($this->router->pathFor('billingDetails', ['number' => $data['billing_number']]));
    }


    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function makeRead($request, $response, $args){
        $data = $request->getParsedBody();
        $this->validate->addRuleMessages([
            'required' => 'You need to fill in all spaces to continue',
            'number' => 'Invalid card data'
        ]);
        $this->validate->validate([
            'cold_water' => [$data['cold_water'], 'number'],
            'hot_water' => [$data['hot_water'], 'number'],
            'gas' => [$data['gas'], 'number'],
            'heat' => [$data['heat'], 'number']
        ]);
        if(!$this->validate->passes()){
            $this->flash->addMessage('danger', $this->validate->errors()->first());
            return $this->redirect('getReading');
        }
        $readings = $this->db->table('readings')->where('user_id', $this->session->get('uid'))->first();
        if($readings){
            if($readings->cold_water_new > $data['cold_water'] || $readings->hot_water_new >  $data['hot_water'] || $readings->gas_new > $data['gas'] || $readings->heat_new > $data['heat']){
                $this->flash->addMessage('danger', 'Your readings are to low');
                return $this->redirect('getReading');
            }
            if(date('Y-m-d') < date('Y-m-d', strtotime($readings->new_reading_data))){
                $this->flash->addMessage('danger', 'You can read your used utility every 25 days');
                return $this->redirect('getReading');
            }
            $this->db->table('readings')->where('id', $readings->id)->where('user_id', $this->session->get('uid'))->update([
                'cold_water_old' => $readings->cold_water_new,
                'cold_water_new' => $data['cold_water'],
                'hot_water_old' => $readings->hot_water_new,
                'hot_water_new' => $data['hot_water'],
                'gas_old' => $readings->gas_new,
                'gas_new' => $data['gas'],
                'heat_old' => $readings->heat_new,
                'heat_new' => $data['heat'],
                'created_at' => date('Y-m-d'),
                'new_reading_data' => date('Y-m-d', strtotime("+25 days")),
            ]);

            /** Auto generate bill */
            $price = $this->db->table('prices')->where('id',1)->first();
            $utilities = $this->db->table('readings')->where('user_id', $this->session->get('uid'))->first();
            $user = $this->db->table('users')->where('id', $this->session->get('uid'))->first();

            /* Cold water */
            $cold_water = $utilities->cold_water_new - $utilities->cold_water_old;
            $cold_water_price = $cold_water * $price->cold_water_price;

            /* Hot water */
            $hot_water = $utilities->hot_water_new - $utilities->hot_water_old;
            $hot_water_price = $hot_water * $price->hot_water_price;

            /* Gas */
            $gas = $utilities->gas_new - $utilities->gas_old;
            $gas_price = $gas * $price->gas_price;

            /* Trash */
            $trash_price = $price->trash_price * $user->number_of_persons;

            /* Pets */
            if($user->number_of_pets == 'no'){
                $pets_price = 0 * $price->pets_price;
            }else{
                $pets_price = $user->number_of_pets * $price->pets_price;
            }

            /* Heat price */
            $heat = $utilities->heat_new - $utilities->heat_old;
            $heat_price = $heat * $price->heat_price;

            /* total */
            $total = $cold_water_price + $hot_water_price + $gas_price + $trash_price + $pets_price + $price->electricity_price + $price->sewage_price + $price->cleaning_price;

            $this->db->table('billings')->insert([
                'user_id' => $this->session->get('uid'),
                'number' => $this->function->getNumber(),
                'status' => 0,
                'gas' => $gas_price,
                'gas_amount' => $gas,
                'hot_water' => $hot_water_price,
                'hot_water_amount' => $hot_water,
                'cold_water' => $cold_water_price,
                'cold_water_amount' => $cold_water,
                'sewage' => $price->sewage_price,
                'common_electricity' => $price->electricity_price,
                'trash' => $trash_price,
                'pets' => $pets_price,
                'cleaning' => $price->cleaning_price,
                'heat_amount' => $heat,
                'heating' => $heat_price,
                'total_amount' => $total,
                'due_date' => date('Y-m-d H:i:s', strtotime("+5 days")),
                'created_at' => date('Y-m-d H:i:s')
            ]);

            $this->flash->addMessage('success', 'Your readings have been register');
            return $this->redirect('getReading');
        }
        $this->db->table('readings')->insert([
           'user_id' => $this->session->get('uid'),
            'cold_water_old' => 0,
            'cold_water_new' => $data['cold_water'],
            'hot_water_old' => 0,
            'hot_water_new' => $data['hot_water'],
            'gas_old' => 0,
            'gas_new' => $data['gas'],
            'heat_old' => 0,
            'heat_new' => $data['heat'],
            'created_at' => date('Y-m-d'),
            'new_reading_data' => date('Y-m-d', strtotime("+25 days")),
        ]);

        /** Auto generate bill */
        $price = $this->db->table('prices')->where('id',1)->first();
        $utilities = $this->db->table('readings')->where('user_id', $this->session->get('uid'))->first();
        $user = $this->db->table('users')->where('id', $this->session->get('uid'))->first();

        /* Cold water */
        $cold_water = $utilities->cold_water_new - $utilities->cold_water_old;
        $cold_water_price = $cold_water * $price->cold_water_price;

        /* Hot water */
        $hot_water = $utilities->hot_water_new - $utilities->hot_water_old;
        $hot_water_price = $hot_water * $price->hot_water_price;

        /* Gas */
        $gas = $utilities->gas_new - $utilities->gas_old;
        $gas_price = $gas * $price->gas_price;

        /* Trash */
        $trash_price = $price->trash_price * $user->number_of_persons;

        /* Pets */
        if($user->number_of_pets == 'no'){
            $pets_price = 0 * $price->pets_price;
        }else{
            $pets_price = $user->number_of_pets * $price->pets_price;
        }

        /* Heat price */
        $heat = $utilities->heat_new - $utilities->heat_old;
        $heat_price = $heat * $price->heat_price;

        /* total */
        $total = $cold_water_price + $hot_water_price + $gas_price + $trash_price + $pets_price + $price->electricity_price + $price->sewage_price + $price->cleaning_price;

        $this->db->table('billings')->insert([
            'user_id' => $this->session->get('uid'),
            'number' => $this->function->getNumber(),
            'status' => 0,
            'gas' => $gas_price,
            'gas_amount' => $gas,
            'hot_water' => $hot_water_price,
            'hot_water_amount' => $hot_water,
            'cold_water' => $cold_water_price,
            'cold_water_amount' => $cold_water,
            'sewage' => $price->sewage_price,
            'common_electricity' => $price->electricity_price,
            'trash' => $trash_price,
            'pets' => $pets_price,
            'cleaning' => $price->cleaning_price,
            'heat_amount' => $heat,
            'heating' => $heat_price,
            'total_amount' => $total,
            'due_date' => date('Y-m-d H:i:s', strtotime("+5 days")),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $this->flash->addMessage('success', 'Your readings have been register');
        return $this->redirect('getReading');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function changeProfile($request, $response, $args){
        $data = $request->getParsedBody();
        $this->validate->addRuleMessages([
            'required' => 'You need to fill in all spaces to continue',
            'email' => 'The {value} it\'s not a valid email',
            'matches' => 'The password\'s do not matches',
        ]);
        $this->validate->validate([
            'email' => [$data['email'], 'required|email'],
            'phone' => [$data['phone'], 'required|min(8)'],
            'floor' => [$data['floor'], 'required'],
            'number' => [$data['number'], 'required'],
            'number_of_persons|number of persons' => [$data['number_of_persons'], 'required'],
            'number_of_pets|number of pets' => [$data['number_of_pets'], 'required']
        ]);
        if(!$this->validate->passes()){
            $this->flash->addMessage('danger', $this->validate->errors()->first());
            return $this->redirect('myProfile');
        }
        if(!$this->validate->passes()){
            $this->flash->addMessage('danger', $this->validate->errors()->first());
            return $this->redirect('myProfile');
        }
        $this->db->table('users')->where('id',$this->session->get('uid'))->update([
            'email' => $data['email'],
            'phone' => $data['phone'],
            'floor' => $data['floor'],
            'number' => $data['number'],
            'number_of_persons' => $data['number_of_persons'],
            'number_of_pets' => $data['number_of_pets']
        ]);
        $this->flash->addMessage('success', 'Your profile information have been updated');
        return $this->redirect('myProfile');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function changeProfilePassword($request, $response, $args){
        $data = $request->getParsedBody();
        $this->validate->addRuleMessages([
            'required' => 'You need to fill in all spaces to continue',
            'matches' => 'The password\'s do not matches',
        ]);
        $this->validate->validate([
            'old_password' => [$data['old_password'], 'required'],
            'new_password' => [$data['new_password'], 'required'],
            'conf_password|confirm password' => [$data['conf_password'], 'required|matches(new_password)'],
        ]);
        if(!$this->validate->passes()){
            $this->flash->addMessage('danger', $this->validate->errors()->first());
            return $this->redirect('myProfile');
        }
        $user = $this->db->table('users')->where('id', $this->session->get('uid'))->first();
        if(!password_verify($data['old_password'], $user->password)){
            $this->flash->addMessage('danger', 'The old password do not matches');
            return $this->redirect('myProfile');
        }
        $this->db->table('users')->where('id', $user->id)->update([
            'password' => password_hash($data['new_password'], PASSWORD_DEFAULT)
        ]);
        $this->flash->addMessage('success', 'Your password have been updated');
        return $this->redirect('myProfile');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function updatePrices($request, $response, $args){
        $data = $request->getParsedBody();
        $this->validate->addRuleMessages([
            'required' => 'You need to fill in all spaces to continue',
            'number' => 'Please enter only numbers',
        ]);
        $this->validate->validate([
            'cold_water' => [$data['cold_water'], 'required'],
            'hot_water' => [$data['hot_water'], 'required'],
            'gas' => [$data['gas'], 'required'],
            'heat' => [$data['heat'], 'required'],
            'electricity' => [$data['electricity'], 'required'],
            'trash' => [$data['electricity'], 'required'],
            'pets' => [$data['pets'], 'required'],
            'sewage' => [$data['sewage'], 'required'],
            'cleaning' => [$data['cleaning'], 'required']
        ]);
        if(!$this->validate->passes()){
            $this->flash->addMessage('danger', $this->validate->errors()->first());
            return $this->redirect('getPrices');
        }
        $this->db->table('prices')->where('id',1)->update([
            'electricity_price' => $data['electricity'],
            'trash_price' => $data['trash'],
            'cleaning_price' => $data['cleaning'],
            'cold_water_price' => $data['cold_water'],
            'hot_water_price' => $data['hot_water'],
            'gas_price' => $data['gas'],
            'sewage_price' => $data['sewage'],
            'pets_price' => $data['pets'],
            'heat_price' => $data['heat']
        ]);
        $this->flash->addMessage('success', 'The prices have been updated');
        return $this->redirect('getPrices');
    }
}