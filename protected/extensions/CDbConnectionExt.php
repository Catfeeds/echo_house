<?php

/**
 * 主数据库 写 从数据库（可多个）读
 * 实现主从数据库 读写分离 主服务器无法连接 从服务器可切换写功能
 * 从务器无法连接 主服务器可切换读功
 * by lmt
 * */
class CDbConnectionExt extends CDbConnection {
	public $timeout = 10; //连接超时时间
	public $markDeadSeconds = 10; //如果从数据库连接失败 10秒内不再连接 
	public $cacheID = 'cache';
	/**
	 * @var array $slaves .Slave database connection(Read) config array.
	 * 配置符合 CDbConnection.
	 * @example
	 * 'components'=>array(
	 *        'db'=>array(
	 *            'connectionString'=>'mysql://<master>',
	 *            'slaves'=>array(
	 *                array('connectionString'=>'mysql://<slave01>'),
	 *                array('connectionString'=>'mysql://<slave02>'),
	 *            )
	 *        )
	 * )
	 * */
	public $slaves = array();
	/**
	 *
	 * 从数据库状态 false 则只用主数据库
	 * @var bool $enableSlave
	 * */
	public $enableSlave = true;
	/**
	 * @var masterRead 紧急情况从主数据库无法连接 切换从住服务器（读写）.
	 */
	public $masterRead = false;
	/**
	 * @var _slave
	 */
	private $_slave;

	public function createCommand($sql = null) {
		if (is_string($sql))
		{
			if (self::isReadOperation($sql))
				$read = true;
			else
				$read = false;
		}
		else
			$read = false;
		if ($this->enableSlave && count($this->slaves) > 0 && $read && !$this->getCurrentTransaction())
		{
			//可以走从的情况
			if ($slave = $this->getSlave())
				return $slave->createCommand($sql);
			else if ($this->masterRead)
				return parent::createCommand($sql);
			else
				throw new CDbException("All slaves are down and masterRead is disabled");
		}
		else
			return parent::createCommand($sql);
	}

	/**
	 * 获得从服务器连接资源
	 * @return CDbConnection
	 * */
	public function getSlave() {
		if (!isset($this->_slave))
		{
			shuffle($this->slaves);
			foreach ($this->slaves as $slaveConfig)
			{
				if ($this->_isDeadServer($slaveConfig['connectionString']))
					continue;
				if (!isset($slaveConfig['class']))
					$slaveConfig['class'] = 'CDbConnection';
				$slaveConfig['autoConnect'] = false;
				try
				{
					if ($slave = Yii::createComponent($slaveConfig))
					{
						//Yii::app()->setComponent('dbslave', $slave);
						$slave->setAttribute(PDO::ATTR_TIMEOUT, $this->timeout);
						$slave->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
						$slave->setActive(true);
						$this->_slave = $slave;
						break;
					}
				} catch (Exception $e)
				{
					$this->_markDeadServer($slaveConfig['connectionString']);
					Yii::log("Slave database connection failed!\n\tConnection string:{$slaveConfig['connectionString']}",CLogger::LEVEL_WARNING,'db');
					continue;
				}
			}
			if (!isset($this->_slave))
			{
				$this->_slave = null;
				$this->enableSlave = false;
			}
		}

		return $this->_slave;
	}

	/**
	 * 检测读操作 sql 语句
	 *
	 * 关键字： SELECT,DECRIBE,SHOW ...
	 * 写操作:UPDATE,INSERT,DELETE ...
	 * */
	private static function isReadOperation($sql) {
		if ($sql)
		{
			$sql = substr(ltrim($sql), 0, 7);
			$sql = str_ireplace(array('SELECT', 'SHOW', 'PRAGMA', 'DESC'), '^O^', $sql); //^O^,magic smile
			return strpos($sql, '^O^') === 0;
		}

		return false;
	}

	/**
	 * 检测从服务器是否被标记 失败.
	 */
	private function _isDeadServer($c) {
		$cache = Yii::app()->{$this->cacheID};
		if ($cache && $cache->get('DeadServer::' . $c) == 1)
			return true;

		return false;
	}

	/**
	 * 标记失败的slaves.
	 */
	private function _markDeadServer($c) {
		$cache = Yii::app()->{$this->cacheID};
		if ($cache)
			$cache->set('DeadServer::' . $c, 1, $this->markDeadSeconds);
	}
}

?>
