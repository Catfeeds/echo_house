<?php
/**
 * redis连接组件
 * 使用：
 * 1. 项目yaml文件只需配置[hostname]、[port]、[database]、[password(可选)]连接参数
 * 2. 在config.php文件中加入HangjiaRedisConfig组件获取yaml中的配置
 * 3. 使用者按需求在main.php文件中添加组件即可，组建名称自定义，传入HangjiaRedisConfig组件
 * 4. 配置{@property $redisPrefix}定义该连接的前缀
 * {@see HangjiaRedisConfig} redis配置信息类，需要传入到本类中来，以配置redis连接
 */
class HangjiaRedisConnection extends CApplicationComponent
{
    /**
     * redis实例
     * @var Redis
     */
    private $_client;
    /**
     * redis连接地址
     * @var string
     */
    public $hostname;
    /**
     * redis端口号
     * @var string
     */
    public $port = 6379;
    /**
     * redis配置前缀
     * @var string
     */
    private $_redisPrefix = 'f';
    /**
     * redis数据库
     * @var integer
     */
    public $database = 1;
    public $password;
    public $timeout = 0;

    public function __construct()
    {
        $obj = $this->getClient();
        return $obj;
        Yii::app()->end();
    }

    public function setClient(Redis $redis)
    {
        $this->_client = $redis;
    }

    public function getClient()
    {
        if($this->_client===null) {
            $this->_client = new Redis;
            $this->_client->connect($this->hostname, $this->port, $this->timeout);
            if (!empty($this->password) && $this->_client->auth($this->password) === false) {
                throw new CException('Redis authentication failed!');
            }
            if(empty($this->redisPrefix)) {
                throw new CException('Redis prefix not set');
            }
            $this->_client->setOption(Redis::OPT_PREFIX, $this->redisPrefix);
            if($this->database===null) {
                throw new CException('Redis database not select');
            }
			$this->_client->select($this->database);
        }
        return $this->_client;
    }

    /**
     * 批量配置redis
     * @param array|HangjiaRedisConfig $config 配置对象或数组
     */
    public function setRedisConfig($config)
    {
        if(is_callable($config)) {
            $config = call_user_func($config);
        }
        if($config instanceof HangjiaRedisConfig) {
            $config = $config->getConfigs();
        }
        if(is_array($config)) {
            foreach($config as $name=>$value) {
                if(property_exists($this, $name)) $this->$name = $value;
            }
        }
    }

    public function setRedisPrefix($value)
    {
        if(is_callable($value)) {
            $value = call_user_func($value);
        }
        $this->_redisPrefix = $value;
    }

    public function getRedisPrefix()
    {
        return $this->_redisPrefix;
    }

    // public function __set($name,$value)
	// {
	// 	$setter='set'.$name;
	// 	if (property_exists($this->getClient(),$name)) {
	// 		return $this->getClient()->{$name} = $value;
	// 	}
	// 	elseif(method_exists($this->getClient(),$setter)) {
	// 		return $this->getClient()->{$setter}($value);
	// 	}
	// 	return parent::__set($name,$value);
	// }
    //
    // public function __get($name) {
	// 	$getter='get'.$name;
	// 	if (property_exists($this->getClient(),$name)) {
	// 		return $this->getClient()->{$name};
	// 	}
	// 	elseif(method_exists($this->getClient(),$getter)) {
	// 		return $this->$getter();
	// 	}
	// 	return parent::__get($name);
	// }

    /**
	 * Checks if a property value is null.
	 * Do not call this method. This is a PHP magic method that we override
	 * to allow using isset() to detect if a component property is set or not.
	 * @param string $name the property name
	 * @return boolean
	 */
	// public function __isset($name)
	// {
	// 	$getter='get'.$name;
	// 	if (property_exists($this->getClient(),$name)) {
	// 		return true;
	// 	}
	// 	elseif (method_exists($this->getClient(),$getter)) {
	// 		return true;
	// 	}
	// 	return parent::__isset($name);
	// }

	/**
	 * Sets a component property to be null.
	 * Do not call this method. This is a PHP magic method that we override
	 * to allow using unset() to set a component property to be null.
	 * @param string $name the property name or the event name
	 * @throws CException if the property is read only.
	 * @return mixed
	 */
	// public function __unset($name)
	// {
	// 	$setter='set'.$name;
	// 	if (property_exists($this->getClient(),$name)) {
	// 		$this->getClient()->{$name} = null;
	// 	}
	// 	elseif(method_exists($this,$setter)) {
	// 		$this->$setter(null);
	// 	}
	// 	else {
	// 		parent::__unset($name);
	// 	}
	// }
	/**
	 * Calls a method on the redis client with the given name.
	 * Do not call this method. This is a PHP magic method that we override to
	 * allow a facade in front of the redis object.
	 * @param string $name the name of the method to call
	 * @param array $parameters the parameters to pass to the method
	 * @return mixed the response from the redis client
	 */
	// public function __call($name, $parameters) {
	// 	return call_user_func_array(array($this->getClient(),$name),$parameters);
	// }
}
