<?php

namespace Fuel\Migrations;
class Create_users
{
    public function up()
    {
        \DBUtil::create_table('users', array(
            'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true),
            'name' => array('type' => 'varchar', 'constraint' => 50),
            'email' => array('type' => 'varchar', 'constraint' => 255),
            'password' => array('type' => 'varchar', 'constraint' => 255),
            'created_at' => array('type' => 'datetime', 'default' => \DB::expr('CURRENT_TIMESTAMP')),
            'updated_at' => array('type' => 'datetime', 'default' => \DB::expr('CURRENT_TIMESTAMP')),
        ),array('id'), true, 'InnoDB');

        \DBUtil::create_index('users', 'email', 'idx_users_email', 'UNIQUE');
    }

    public function down()
    {
        \DBUtil::drop_table('users');
    }
}