<?php

namespace Fuel\Migrations;

class Create_clothing_seasons
{
    public function up()
    {
        \DBUtil::create_table('clothing_seasons', array(
            'id'          => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true),
            'clothing_id' => array('type' => 'int', 'constraint' => 11),
            'season'      => array('type' => 'varchar', 'constraint' => 20),
        ), array('id'), true, 'InnoDB', null, array(
            array(
                'constraint' => 'fk_clothing_seasons_clothing_id',
                'key'        => 'clothing_id',
                'reference'  => array('table' => 'clothes', 'column' => 'id'),
                'on_update'  => 'CASCADE',
                'on_delete'  => 'CASCADE',
            ),
        ));

        \DBUtil::create_index(
            'clothing_seasons',
            array('clothing_id', 'season'),
            'idx_clothing_seasons_unique',
            'UNIQUE'
        );
    }

    public function down()
    {
        \DBUtil::drop_table('clothing_seasons');
    }
}