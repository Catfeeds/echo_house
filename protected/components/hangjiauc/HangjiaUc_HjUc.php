<?php
/**
 * 航加用户体系函数接口组件
 * 类名使用了伪命名空间（yii1蛋疼之处），HjUc会冲突
 * @author tivon
 * @version 2016-08-29
 */
 Yii::setPathOfAlias('hangjiauc', __DIR__);
class HangjiaUc_HjUc extends CApplicationComponent
{
    /**
     * 是否通过curl请求
     * URM项目请将此值设置为false，其他垂直平台请设置为true，可通过main.php中配置该组
     * 件时设置curlConnect为指定的值
     * @var boolean
     */
    private $_curlConnect = true;
    /**
     * 错误提示，所有通过通过接口函数请求后返回的错误信息都存在该属性中
     * @var string
     */
    private $_error = '';
    /**
     * 错误码
     * @var integer
     */
    private $_errorCode = 0;
    /**
     * URM网址
     * 当curl请求时，指定URM地址
     * @var string
     */
    private $_urmHost = '';
    /**
     * 业务处理模型类
     * 当非curl请求时，内部指定模型类的路径别名，类似"application.components.*"
     * @var HangjiaUc_ServerModel
     */
    private $_modelPath = 'hangjiauc.server_model.*';
    /**
     * HjPassport组件名称
     * @var string
     */
    private $_passportComponentName = 'passport';
    /**
     * CWebUser组件名称
     * 涉及到航加用户体系的（关于Ucenter）用户登录的，统一使用单独的user组件，避免和admin后台、
     * vip后台登混淆，因此这里需要单独配置一个组件名称，该组件名称需要在main.php文件中已经配置
     * 格式如：
     * [
     *  'components' => [
     *      'ucUser' => [
     *          'class' => 'CWebUser',
     *          'stateKeyPrefix' => '_hangjiauc'
     *      ]
     *  ]
     * ]
     * @var string CWebUser组件名称
     */
    private $_userComponentName = 'ucUser';
    /**
     * CWebUser组件名称
     * @var [type]
     */
    public $user;
    /**
     * URL构造器
     * @var HangjiaUc_HjUc_UrlConstructor
     */
    private $_urlConstructor;

    /**
     * init
     */
    public function init()
    {
        parent::init();
        $this->user = Yii::app()->{$this->_userComponentName};
        $this->resetError();
    }

    /**
     * 配置请求方式
     * 在URM中配置为false，其他垂直平台需要配置为true
     * @param boolean $value true or false
     */
    public function setCurlConnect($value)
    {
        $this->_curlConnect = (bool)$value;
    }

    /**
     * 配置URM域名
     * @param string $url url
     */
    public function setUrmHost($url)
    {
        if(is_callable($url)) {
            $url = call_user_func($url);
        }
        if(strpos($url, 'http://')===false){
            $url = 'http://' . $url;
        }
        $this->_urmHost = trim($url,'\/');
    }

    /**
     * 配置业务模型路径别名
     * @param string $alias 路径别名
     */
    public function setModelPath($alias)
    {
        $this->_modelPath = $alias;
    }

    /**
     * 配置passport组件名称
     * @param string $name 组件名称
     */
    public function setPassportComponentName($name)
    {
        $this->_passportComponentName = $name;
    }

    /**
     * 配置user组件名称
     * @param string $name CWebUser组件名称
     */
    public function setUserComponentName($name)
    {
        $this->_userComponentName = $name;
    }

    /**
     * 获取curl
     */
    public function getCurl()
    {
        Yii::import('hangjiauc.curl.*');
        return new App_Components_HangjiaUc_Curl_Curl();
    }

    /**
     * 获取URL ROUTE
     * 在CURL请求方式下，获得到的字符串作为请求URL的URI部分
     * 在非CURL请求方式下，获得到的字符串作为yii框架执行的route，执行对应的action
     * @return
     */
    private function getUrlPath($name)
    {
        $path = 'hangjiauc.server_model.';
        $urlRouteMap = [
            'register' => [
                'uri' => '/uc/server/register',
                'path' => $path.'HangjiaUc_ServerModel_Register',
            ],
            'login' => [
                'uri' => '/uc/server/login',
                'path' => $path.'HangjiaUc_ServerModel_Login',
            ],
            'logout' => [
                'uri' => '/uc/server/logout',
                'path' => $path.'HangjiaUc_ServerModel_Logout',
            ],
            'getUser' => [
                'uri' => '/uc/server/getUser',
                'path' => $path.'HangjiaUc_ServerModel_GetUser'
            ],
            'updatePhone' => [
                'uri' => '/uc/server/updatePhone',
                'path' => $path.'HangjiaUc_ServerModel_UpdatePhone',
            ],
            'updateOpenid' => [
                'uri' => '/uc/server/updateOpenid',
                'path' => $path.'HangjiaUc_ServerModel_UpdateOpenid',
            ],
            'updateUnionid' => [
                'uri' => '/uc/server/updateUnionid',
                'path' => $path.'HangjiaUc_ServerModel_UpdateUnionid',
            ],
            'updatePwd' => [
                'uri' => '/uc/server/updatePwd',
                'path' => $path.'HangjiaUc_ServerModel_UpdatePwd'
            ],
            'checkPhone' => [
                'uri' => '/uc/server/checkPhone',
                'path' => $path.'HangjiaUc_ServerModel_CheckPhone',
            ],
            'synLogin' => [
                'uri' => '/uc/server/synLogin',
                'path' => $path.'HangjiaUc_ServerModel_SynLogin',
            ],
            'getPhoneByUids' => [
                'uri' => '/uc/server/getPhoneByUids',
                'path' => $path.'HangjiaUc_ServerModel_GetPhoneByUids',
            ]
        ];

        $route = isset($urlRouteMap[$name]) ? $urlRouteMap[$name] : $name;
        if($this->_curlConnect) {
            return $this->_urmHost . $route['uri'];
        } else {
            return $route['path'];
        }
    }

    /**
     * __call
     */
    public function __call($name, $args)
    {
        if(method_exists($this->getUrlConstructor(), $name)) {
            return call_user_func_array([$this->getUrlConstructor(), $name], $args);
        }elseif($this->_curlConnect && method_exists($this, $name)) {
            return call_user_func_array([$this, $name], $args);
        } elseif(!$this->_curlConnect) {
            Yii::import($this->_modelPath);
            $model = Yii::createComponent(['class'=>$this->getUrlPath($name)], Yii::app()->{$this->_passportComponentName});
            $result = call_user_func_array([$model, 'run'], $args);
            $this->parseError($result);
            return $result;
        } else {
            return parent::__call($name, $args);
        }
    }

    /**
     * __get
     */
    public function __get($name)
    {
        if(isset($this->getUrlConstructor()->$name)) {
            return $this->getUrlConstructor()->$name;
        } else {
            return parent::__get($name);
        }
    }

    /**
     * 解析错误信息
     * @param  array $data 接口函数或者远程请求或得到的数组结果
     * @return void
     */
    private function parseError($data)
    {
        $this->resetError();
        if(isset($data['code']) && $data['code']>0 && isset($data['error'])) {
            $this->addError($data['code'], $data['error']);
        }
    }

    /**
     * 恢复错误状态
     */
    private function resetError()
    {
        $this->_errorCode = 0;
        $this->_error = '';
    }

    /**
     * 添加错误信息
     */
    public function addError($code, $error)
    {
        $this->_errorCode = $code;
        $this->_error = $error;
    }

    /**
     * 获取错误信息
     */
    public function getError()
    {
        return $this->_error;
    }

    /**
     * 获取错误码
     */
    public function getErrorCode()
    {
        return $this->_errorCode;
    }

    /**
     * 是否有错误
     * @return boolean
     */
    public function hasError()
    {
        return intval($this->_errorCode) > 0;
    }

    //***************************接口函数***************************
    //***********各个函数使用参数说明见URM项目的wiki文档**************
    //********http://hjgit.shangxiaban.cn/mzl/hj_urm/wiki**********

    /**
     * 用户登录
     */
    protected function login($username, $password)
    {
        $response = $this->getCurl()->post($this->getUrlPath('login'), [
            'username' => $username,
            'password' => $password,
        ]);
        $result = CJSON::decode($response->body);
        $this->parseError($result);
        return $result;
    }

    /**
     * 用户退出
     */
    protected function logout()
    {
        $response = $this->getCurl()->get($this->getUrlPath('logout'));
        $result = CJSON::decode($response->body);
        $this->parseError($result);
        return $result;
    }

    /**
     * 获取用户信息
     */
    protected function getUser($uid,$type=1)
    {
        $response = $this->getCurl()->get($this->getUrlPath('getUser'), ['uids'=>$uid, 'type'=>$type]);
        $result = CJSON::decode($response->body);
        $this->parseError($result);
        return $result;
    }

    /**
     * 修改用户手机号码
     * $phone=null时为解除绑定操作
     */
    protected function updatePhone($uid, $phone=null)
    {
        $response = $this->getCurl()->post($this->getUrlPath('updatePhone'), [
            'uid' => $uid,
            'phone' => $phone==null ? '' : $phone,
        ]);
        $result = CJSON::decode($response->body);
        $this->parseError($result);
        return $result;
    }

    /**
     * 修改openid
     */
    protected function updateOpenid($uid, $openId)
    {
        $response = $this->getCurl()->post($this->getUrlPath('updateOpenid'), [
            'uid' => $uid,
            'openid' => $openId,
        ]);
        $result = CJSON::decode($response->body);
        $this->parseError($result);
        return $result;
    }

    /**
     * 修改unionId
     */
    protected function updateUnionid($uid, $unionId)
    {
        $response = $this->getCurl()->post($this->getUrlPath('updateUnionId'), [
            'uid' => $uid,
            'unionid' => $unionId,
        ]);
        $result = CJSON::decode($response->body);
        $this->parseError($result);
        return $result;
    }

    /**
     * 修改用户密码
     */
    protected function updatePwd($uid, $newPwd)
    {
        $response = $this->getCurl()->post($this->getUrlPath('updatePwd'), [
            'uid' => $uid,
            'pwd' => $newPwd,
        ]);
        $result = CJSON::decode($response->body);
        $this->parseError($result);
        return $result;
    }

    protected function register($username, $password)
    {
        $response = $this->getCurl()->post($this->getUrlPath('register'), [
            'username' => $username,
            'password' => $password
        ]);
        $result = CJSON::decode($response->body);
        $this->parseError($result);
        return $result;
    }

    protected function checkPhone($phone)
    {
        $response = $this->getCurl()->get($this->getUrlPath('checkPhone'), [
            'phone' => $phone,
        ]);
        $result = CJSON::decode($response->body);
        $this->parseError($result);
        return $result;
    }

    protected function getPhoneByUids($uids)
    {
        $response = $this->getCurl()->get($this->getUrlPath('getPhoneByUids'), [
            'uids' => $uids,
        ]);
        $result = CJSON::decode($response->body);
        $this->parseError($result);
        return $result;
    }

    /**
     * 获取URL构造器
     * @return HangjiaUc_HjUc_UrlConstructor
     */
    public function getUrlConstructor()
    {
        if($this->_urlConstructor===null) {
            $this->_urlConstructor = new HangjiaUc_HjUc_UrlConstructor($this->_urmHost);
        }
        return $this->_urlConstructor;
    }
}


/**
 * URL构造器
 * 职责：
 * 1. 负责为垂直平台构造URM用户体系页面链接
 * 2. 负责为URM平台转发器页面解析请求参数
 * 参数定义规则（构造URL以及解析URL所需要的参数）：
 * {@param operate}指定操作类型
 * {@param project}来源项目
 */
class HangjiaUc_HjUc_UrlConstructor extends CComponent
{
    /**
     * URM页面中转器的URI
     * 该配置需要随URM中uc模块page控制器下的redirector而变动
     * @var string
     */
     private $_pageRedirector = '/uc/page/redirect';
     /**
      * URM地址
      * @var string
      */
     private $_urmHost = '';
     private $_map = [
         //type=>[wapurl,pcurl]
     ];
     /**
      * 查询参数
      * @var array
      */
     private $_queryParams = [
         'operate' => '',
         'project' => '',
         'returnUrl' => '',
     ];


     /**
      * constructor
      */
     public function __construct($urmHost)
     {
         $this->_urmHost = $urmHost;
         $this->parseUrl();
     }

     /**
      * 获取URM页面路由转发器绝对地址
      * @return string 绝对地址
      */
     private function getPageRedirector()
     {
         return rtrim($this->_urmHost, '\/') . '/' . trim($this->_pageRedirector, '\/');
     }

     /**
      * 获取构造的URL
      * @return string
      */
     public function createUrl()
     {
         $this->setProject(Yii::app()->name);
         return $this->getPageRedirector() . '?' . http_build_query($this->_queryParams);
     }

     //******************************接口函数******************************
     /**
      * 登录页
      */
     public function getLoginPageUrl($returnUrl='')
     {
         $this->returnUrl = $returnUrl;
         $this->operate = 'login';
         return $this->createUrl();
     }

     /**
      * 退出页|一般情况下直接使用项目自己的退出页面就行
      * 如果需要，可以走URM的退出页
      */
     public function getLogoutPageUrl($returnUrl='')
     {
         $this->returnUrl = $returnUrl;
        $this->operate = 'logout';
        return $this->createUrl();
     }

     /**
      * 注册页
      */
     public function getRegisterPageUrl($returnUrl='')
     {
         $this->returnUrl = $returnUrl;
         $this->operate = 'register';
         return $this->createUrl();
     }

     /**
      * 修改密码页
      */
     public function getUpdatePwdPageUrl($returnUrl='')
     {
         $this->returnUrl = $returnUrl;
         $this->operate = 'updatePwd';
         return $this->createUrl();
     }

     /**
      * 重置密码页
      */
     public function getResetPwdPageUrl($returnUrl='')
     {
         $this->returnUrl = $returnUrl;
         $this->operate = 'resetPwd';
         return $this->createUrl();
     }

     /**
      * 绑定手机页
      */
     public function getUpdatePhonePageUrl($returnUrl='')
     {
         $this->returnUrl = $returnUrl;
         $this->operate = 'bindPhone';
         return $this->createUrl();
     }

     /**
      * 解析URL参数
      */
     public function parseUrl()
     {
         $this->setOperate(htmlspecialchars(Yii::app()->request->getQuery('operate', $this->getOperate())));
         $this->setProject(htmlspecialchars(Yii::app()->request->getQuery('project', $this->getProject())));
         $this->setReturnUrl(htmlspecialchars(Yii::app()->request->getQuery('returnUrl', $this->getReturnUrl())));
     }



     //**************************对查询参数的setter/getter操作******************
     public function setOperate($value)
     {
         $this->_queryParams['operate'] = $value;
     }

     public function getOperate()
     {
         return $this->_queryParams['operate'];
     }

     public function setProject($value)
     {
         $this->_queryParams['project'] = $value;
     }

     public function getProject()
     {
         return $this->_queryParams['project'];
     }

     public function setReturnUrl($value)
     {
         $this->_queryParams['returnUrl'] = $value;
     }

     public function getReturnUrl()
     {
         return $this->_queryParams['returnUrl'];
     }
}
