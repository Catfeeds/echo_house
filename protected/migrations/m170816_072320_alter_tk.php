<?php

class m170816_072320_alter_tk extends CDbMigration
{

	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
		$sql="ALTER TABLE `tk` ADD COLUMN `hits`  int(10) NOT NULL DEFAULT 0 AFTER `cid`;";
		$this->execute($sql);
		$this->refreshTableSchema('tk');
	}

	public function safeDown()
	{
		return false;
	}
	
}