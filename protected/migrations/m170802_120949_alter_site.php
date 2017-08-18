<?php

class m170802_120949_alter_site extends CDbMigration
{
	
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
		$sql="ALTER TABLE `site` MODIFY COLUMN `value`  longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `name`;";
		$this->execute($sql);
		$this->refreshTableSchema('site');
	}

	public function safeDown()
	{
		return false;
	}
	
}