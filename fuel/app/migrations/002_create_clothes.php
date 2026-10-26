<?php

namespace Fuel\Migrations;

class Create_clothes
{
	public function up()
	{
		\DBUtil::create_table('clothes', array(
			'id' => array('type' => 'int', 'constraint' => 11, 'type' => 'int', 'auto_increment' => true),
			'user_id' => array('type' => 'int','constraint' => 11),
			'name'          => array('type' => 'varchar', 'constraint' => 100),
            'category'      => array('type' => 'varchar', 'constraint' => 50),
            'brand'         => array('type' => 'varchar', 'constraint' => 100, 'null' => true),
            'color'         => array('type' => 'varchar', 'constraint' => 30),
            'image_path'    => array('type' => 'varchar', 'constraint' => 255, 'null' => true),
            'purchase_date' => array('type' => 'date', 'null' => true),
            'memo'          => array('type' => 'varchar', 'constraint' => 500, 'null' => true),
            'created_at'    => array('type' => 'datetime', 'default' => \DB::expr('CURRENT_TIMESTAMP')),
            'updated_at'    => array('type' => 'datetime', 'default' => \DB::expr('CURRENT_TIMESTAMP')),
            'deleted_at'    => array('type' => 'datetime', 'null' => true),
		), array('id'), true, 'InnoDB', null, array(
			array(
				'constraint' => 'fk_clothes_user_id',
                'key'        => 'user_id',
                'reference'  => array('table' => 'users', 'column' => 'id'),
                'on_update'  => 'CASCADE',
                'on_delete'  => 'RESTRICT',
			)
		));
	}

	public function down()
	{
		\DBUtil::drop_table('clothes');
	}
}