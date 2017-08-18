<?php

class m170816_075227_alter_match extends CDbMigration
{
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
		$sql="ALTER TABLE `match` MODIFY COLUMN `old_id`  bigint(100) NOT NULL AUTO_INCREMENT AFTER `lid` ;";
		$this->execute($sql);
		$this->refreshTableSchema('match');
	}

	public function safeDown()
	{
		return false;
	}
	
}