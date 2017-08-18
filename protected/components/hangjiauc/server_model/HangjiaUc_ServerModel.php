<?php
/**
 * 航加用户体系服务端业务逻辑处理类的基类
 * 1.该类只在URM端使用，与HangjiaUc_HjUc类有区别，该类处理业务逻辑，HangjiaUc_HjUc类是接口函数类；
 * 2.该类需要依赖passport组件类
 * 3. 由该类定义用户体系错误码
 * @author webaqiu
 * @version 2016-09-01
 */
abstract class HangjiaUc_ServerModel implements HangjiaUc_ServerModel_Interface
{
    CONST UNKNOW_ERROR_CODE = 0001;

    /**
     * passport组件
     * @var HjPassport
     */
    protected $passport;

    /**
     * 构造函数
     * 需要传入依赖的HjPassport类
     */
    public function __construct(HjPassport $passport)
    {
        $this->passport = $passport;
    }

    /**
     * 定义错误代码对照表
     * @var array
     */
    protected $errorCode = [
        0001 => '未知错误',
        //1开头，用户注册
        1001 => '用户名不合法',
        1002 => '包含不允许注册的词语',
        1003 => '用户名已经存在',
        1004 => 'Email格式有误',
        1005 => 'Email不允许注册',
        1006 => 'Email已被注册',
        1007 => '该手机号已被占用',
        1008 => '该手机号格式错误',
        //2开头，用户登录
        2001 => '用户不存在，或者被删除',
        2002 => '密码错误',
        2003 => '安全提示错误',
        //3开头，更新用户资料
        3001 => '没有做任何修改',
        3002 => '旧密码不正确',
        3003 => 'Email格式有误',
        3004 => 'Email不允许注册',
        3005 => '该Email已被注册',
        3006 => '该用户受保护无权限修改',
        3007 => '该手机号已绑定其他帐号',
        3008 => '该手机号格式错误',
        3009 => '该手机号已绑定给定帐号',
        3010 => '该帐号已经绑有手机号',
        3011 => '无效的openid',
        3012 => '无效的unionid',
        3013 => '该帐号已经绑定openid',
        3014 => '该帐号已经绑定unionid',
        3015 => '该手机未绑定任何帐号',
    ];

    /**
     * 输出结果通用模板，当出错或者输出指定内容时会用到
     * @var array
     */
    protected $commonTpl = [
        'code' => '{code}',
        'error' => '{error}'
    ];

    /**
     * 渲染结果
     * @param  string|array $content 渲染的结果，可以为数组，将被直接转成json输出；也可以为字符串，将以一定格式输出
     * @return void
     */
    public function render( $content )
    {
        if(is_array($content)) {
            return $content;
        } elseif(is_string($content)) {
            return array_replace($this->commonTpl, [
                'code' => 0,
                'error' => $content
            ]);
            // $json = CJSON::encode($this->commonTpl);
            // return strtr($json, ['{code}'=>0, '{error}'=>$content]);
        } else {
            return $this->error();
        }
    }

    /**
     * 输出错误信息
     * @param  [type] $code [description]
     * @return [type]       [description]
     */
    public function error( $code='' )
    {
        $code = $this->getErrorCode($code);
        if(!isset($this->errorCode[$code])){
            $code = self::UNKNOW_ERROR_CODE;
        }
        // $json = CJSON::encode($this->commonTpl);
        return array_replace($this->commonTpl, [
            'code' => $code,
            'error' => $this->errorCode[$code]
        ]);
        // return strtr($json, ['{code}'=>$code, '{error}'=>$this->errorCode[$code]]);
    }

    public function getErrorCode($code)
    {
        return $code;
    }

    protected function log($msg, $data = [])
    {
        if($data) {
            $msg .= "\n";
            $msg .= "data：\n";
            $msg .= CJSON::encode($data) . "\n";
        }
        Yii::log($msg, CLogger::LEVEL_ERROR, 'uc');
    }
}
