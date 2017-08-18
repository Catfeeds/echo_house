<?php

class m170814_175838_alter_tk extends CDbMigration
{
	
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
		$sql="ALTER TABLE `tk` MODIFY COLUMN `descpt`  longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `title`;";
		$this->execute($sql);
		$this->refreshTableSchema('tk');
	}

	public function safeDown()
	{
		return false;
	}
	
}