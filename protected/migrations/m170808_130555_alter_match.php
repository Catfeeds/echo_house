<?php

class m170808_130555_alter_match extends CDbMigration
{
	
	public function safeUp()
	{
		$sql="ALTER TABLE `match` ADD COLUMN `old_id`  int(10) NOT NULL DEFAULT 0 AFTER `lid`;";
		$this->execute($sql);
		$this->refreshTableSchema('match');
	}

	public function safeDown()
	{
		return false;
	}
	
}