<?php
/**
 * 通行证
 * main.php配置文件中必须配置该组件名称为"passport"
 */
Yii::setPathOfAlias('hjpassport', __DIR__);
class HjPassport extends CApplicationComponent
{
    /**
     * 用于应用后台配置Type的dropDownList选项参数
     * @var array
     */
    static $typeOptions = array(
        'ucenter' => 'Ucenter',
        'discuz' => 'Discuz',
        'discuzx' => 'DiscuzX',
        'phpwind' => 'PHPWind',
        'nextwind' => 'NextWind(pw9)'//PHP9以及后续版本
    );

    private $_type;
    private $_options;
    //具体处理同步登陆的实例类
    private $_instance;

    static $typeClassMap = array(
        'ucenter' => 'hjpassport.ucenter.HjUcenter',
        'phpwind' => 'hjpassport.phpwind.HjPhpwind',
    );

    public function init()
    {
        $classAlias = self::$typeClassMap[$this->_type];
        $this->loadTestConfig();
        $this->_instance = Yii::createComponent($classAlias, $this->_options);
    }

    /**
     * 调用测试配置文件，注意该文件不应该存进git版本库中
     * 测试配置文件放置于当前所在目录，文件名必须为config.php
     * 配置文件内容格式为：
     * `code`
     * <?php
     *  return [
     *      type' => '',
     *      'options' => [
     *          'ucServer' => '',
     *          'appId' => '',
     *          'key' => '',
     *          'charset' => '',
     *      ]
     *  ]
     * `code`
     * @return void
     */
    public function loadTestConfig()
    {
        $file = Yii::getPathOfAlias('hjpassport') . DIRECTORY_SEPARATOR . 'config.php';
        if(file_exists($file)) {
            $config = require($file);
            if(is_array($config)) {
                foreach($config as $attr=>$value) {
                    $this->$attr = $value;
                }
            }
        }
    }

    public function setType($type)
    {
        if(is_callable($type)){
            $type = call_user_func($type);
        }
        $type = (string)$type;
        if(!isset(self::$typeClassMap[$type])){
            throw new Exception('同步登陆论坛类型不正确');
        }
        $this->_type = $type;
    }

    /**
     * array(
     * 'ucServer'=>'',
     * 'type' => 'ucenter',
     * 'appId'=> 2,
     * 'key' => 'xxx'
     * )
     * @param [type] $options [description]
     */
    public function setOptions($options)
    {
        if(is_callable($options)){
            $options = (array)call_user_func($options);
        }
        $options = (array)$options;
        $this->_options = $options;
    }

    public function __call($name, $params)
    {
        $this->checkConfig();
        if(method_exists($this, $name)){
            return call_user_func_array(array($this, $name), $params);
        }elseif(method_exists($this->_instance, $name)){
            return call_user_func_array(array($this->_instance, $name), $params);
        }else{
            return parent::__call($name, $params);
        }
    }

    public function checkConfig()
    {
        if(is_array($this->_options)) {
            if(isset($this->_options['ucServer']) && empty($this->_options['ucServer'])) {
                Yii::log('ucServer未配置', 'error', 'passport');
            }
            if(isset($this->_options['appId']) && empty($this->_options['appId'])) {
                Yii::log('appId未配置', 'error', 'passport');
            }
            if(isset($this->_options['key']) && empty($this->_options['key'])) {
                Yii::log('key未配置', 'error', 'passport');
            }
        }
    }
}


abstract class Ppt extends CComponent
{
    private $_avatar = '';
    private $_errors = array();
    public $synloginHtml = '';

    /**
     * 登录函数
     * @param  [type] $username [description]
     * @param  [type] $password [description]
     * @return array 登录成功返回数组，包含用户uid\username\icon，失败uid返回0或者等于null的值
     */
    abstract function login($username, $password);

    abstract function synLogin($uid);

    abstract function synLogout();

    /**
     * 获得头像
     * @param  string $uids 用户uid，可通过逗号分割来指定多个用户
     * @return array  多个头像数组，数组key必须为用户uid,value为头像地址
     * array(
     * 		42 => 'http://xxxxx.jpg',
     * 		121 => 'http://aaaaa.jpg',
     * )
     */
    abstract function getAvatars($uids);

    /**
     * 根据uid或用户名获取用户信息
     * @param  string  $uids  uid或用户名 均以英文逗号分隔，如"31,24,121,325"或"admin,veitor,测试帐号"
     * @param  boolean $isUid 使用用户uid获取时为true，使用用户名获取时为false
     * @return array   [13=>['uid'=>13', 'username'=>'admin', 'avatar'=>'xxx.jpg']]
     */
    abstract function getUserInfoes($uids, $isUid = true);

    /**
     * 注册用户
     * @return integer 注册成功返回用户uid，否则返回一个<=0的数字
     */
    abstract function register($username, $password, $email);

    /**
     * 更新用户密码
     * @param  string $username 用户名（在Ucenter中需要使用用户名而不是uid）
     * @param  [type] $password 新密码
     * @return 修改成功返回1，修改失败返回小于1的值
     */
    abstract function updatePwd($username, $password);

    public function hasErrors()
    {
        return !empty($this->_errors);
    }

    public function addError($msg)
    {
        $this->_errors[] = $msg;
    }

    public function getErrors()
    {
        return $this->_errors;
    }
}
