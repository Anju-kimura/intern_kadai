<?php

namespace Fuel\Migrations;

class Create_users
{
    public function up()
    {
        \DBUtil::create_table('users', array(
            'id'             => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true),
            'username'       => array('type' => 'varchar', 'constraint' => 50),
            'password'       => array('type' => 'varchar', 'constraint' => 255),
            'group'          => array('type' => 'int', 'constraint' => 11, 'default' => 1),
            'email'          => array('type' => 'varchar', 'constraint' => 255),
            'last_login'     => array('type' => 'varchar', 'constraint' => 25),
            'login_hash'     => array('type' => 'varchar', 'constraint' => 255),
            'profile_fields' => array('type' => 'text'),
            'created_at'     => array('type' => 'int', 'constraint' => 11, 'default' => 0),
            'updated_at'     => array('type' => 'int', 'constraint' => 11, 'default' => 0),
        ), array('id'), true, 'InnoDB');

        \DBUtil::create_index('users', 'username', 'idx_users_username', 'UNIQUE');
        \DBUtil::create_index('users', 'email', 'idx_users_email', 'UNIQUE');
    }

    public function down()
    {
        \DBUtil::drop_table('users');
    }
}