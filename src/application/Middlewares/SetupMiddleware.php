<?php

namespace App\Middlewares;

class SetupMiddleware extends \App\App
{
    public function setup($request, $response, $next){
        if(!$this->db->schema()->hasTable('users')){
            $this->db->schema()->create('users', function($table){
                $table->increments('id');
                $table->string('username', 256);
                $table->string('password', 256);
                $table->string('email', 256);
                $table->string('phone');
                $table->string('name', 256);
                $table->string('last_name', 256);;
                $table->string('floor');
                $table->string('number');
                $table->string('number_of_persons');
                $table->string('number_of_pets');
                $table->boolean('status');
                $table->integer('access');
                $table->timestamps();
            });
        }

        if(!$this->db->schema()->hasTable('billings')){
            $this->db->schema()->create('billings', function($table){
                $table->increments('id');
                $table->string('number', 256);
                $table->integer('user_id');
                $table->boolean('status');
                $table->float('gas', 8, 2);
                $table->integer('gas_amount');
                $table->float('hot_water', 8, 2);
                $table->integer('hot_water_amount');
                $table->float('cold_water', 8, 2);
                $table->integer('cold_water_amount');
                $table->float('sewage', 8, 2);
                $table->float('common_electricity', 8, 2);
                $table->float('trash', 8, 2);
                $table->float('pets', 8, 2);
                $table->float('cleaning', 8, 2);
                $table->integer('heat_amount');
                $table->float('heating', 8, 2);
                $table->float('total_amount', 8, 2);
                $table->dateTime('due_date');
                $table->timestamps();

            });
        }

        if(!$this->db->schema()->hasTable('transactions')){
            $this->db->schema()->create('transactions', function($table){
                $table->increments('id');
                $table->string('billing_number');
                $table->string('card_number');
                $table->integer('user_id');
                $table->string('transaction_mid');
                $table->float('total_pay', 8, 2);
                $table->timestamps();
            });
        }

        if(!$this->db->schema()->hasTable('readings')) {
            $this->db->schema()->create('readings', function ($table) {
                $table->increments('id');
                $table->integer('user_id');
                $table->integer('cold_water_old')->default(0);
                $table->integer('cold_water_new');
                $table->integer('hot_water_old')->default(0);
                $table->integer('hot_water_new');
                $table->integer('gas_old')->default(0);
                $table->integer('gas_new');
                $table->integer('heat_old')->default(0);
                $table->integer('heat_new');
                $table->dateTime('created_at');
                $table->dateTime('new_reading_data');
            });
        }

        if(!$this->db->schema()->hasTable('recovers')){
            $this->db->schema()->create('recovers', function ($table) {
               $table->increments('id');
               $table->integer('user_id');
               $table->string('recover_key', 256);
               $table->string('expire_at', 256);
               $table->boolean('status');
               $table->timestamps();
            });
        }

        if(!$this->db->schema()->hasTable('prices')){
            $this->db->schema()->create('prices', function($table){
                $table->increments('id');
                $table->float('electricity_price', 8, 2);
                $table->float('trash_price', 8, 2);
                $table->float('cleaning_price', 8, 2);
                $table->float('cold_water_price', 8, 2);
                $table->float('hot_water_price', 8, 2);
                $table->float('gas_price', 8, 2);
                $table->float('sewage_price', 8, 2);
                $table->float('pets_price', 8, 2);
                $table->float('heat_price', 8, 2);
            });
        }

        $result = $this->db->table('users')->where('id',1)->first();
        if(!$result) {
            $this->db->table('users')->insert([
                'username' => 'admin',
                'password' => password_hash('admin', PASSWORD_DEFAULT),
                'email' => 'admin@exemple.com',
                'phone' => '0000000000',
                'name' => 'Admin',
                'last_name' => 'Admin',
                'floor' => 'none',
                'number' => 'none',
                'number_of_persons' => 'none',
                'number_of_pets' => 'none',
                'status' => 1,
                'access' => 1
            ]);
        }
        $prices = $this->db->table('prices')->where('id',1)->first();
        if(!$prices){
            $this->db->table('prices')->insert([
                'id' => 1,
                'electricity_price' => 0,
                'trash_price' => 0,
                'cleaning_price' => 0,
                'cold_water_price' => 0,
                'hot_water_price' => 0,
                'gas_price' => 0,
                'sewage_price' => 0,
                'pets_price' => 0,
                'heat_price' => 0
            ]);
        }
        $response = $next($request, $response);
        return $response;
    }
}