<?php

class m170802_084230_add_points extends CDbMigration
{
	
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
		$sql="ALTER TABLE `points` ADD COLUMN `old_id`  int(10) NOT NULL DEFAULT 0 COMMENT '' AFTER `status`;";
		$this->execute($sql);
		$this->refreshTableSchema('points');
	}

	public function safeDown()
	{
		return false;
	}
	
}