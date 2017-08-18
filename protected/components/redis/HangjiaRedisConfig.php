<?php
/**
 * redis配置信息类
 * 主要用于{@see HangjiaRedisConnection}
 * 使用：
 * 一般需要写在config.php文件，从config.yaml文件获取配置的信息
 */
class HangjiaRedisConfig extends CApplicationComponent
{
    // public $hostname;
    // public $port;
    // public $password;
    // public $database;
    private $_configs = [
        'hostname' => '',
        'port' => '',
        'password' => '',
        'database' => ''
    ];

    /**
     * 防止设置不存在的属性而抛出异常
     */
    public function __set($name, $value)
    {
        if($this->hasConfig($name)) {
            $this->setConfig($name, $value);
        } else {
            try {
                return parent::__set($name, $value);
            } catch(Exception $e) {
                return $value;
            }
        }
    }

    public function hasConfig($name)
    {
        return isset($this->_configs[$name]);
    }

    public function setConfig($name, $value)
    {
        $this->_configs[$name] = $value;
    }

    public function getConfigs()
    {
        return $this->_configs;
    }
}
