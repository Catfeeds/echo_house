<?php
/**
 * 配置文件
 * @author tivon
 * @date 2015-09-07 13:35:55
 */
if (!function_exists('fatallog')) {
    function fatallog($event)
    {
        $error = error_get_last();
        $errorsToHandle = E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING;
        if (!empty($error) && ($error['type'] & $errorsToHandle)) {
            $message = 'FATAL ERROR: ' . $error['message'];
            if (!empty($error['file'])) {
                $message .= ' (' . $error['file'] . ' :' . $error['line'] . ')';
            }
            if (isset($_SERVER['REQUEST_URI'])) {
                $message .= 'REQUEST_URI=' . $_SERVER['REQUEST_URI'];
            }

            Yii::log($message, CLogger::LEVEL_ERROR, 'php');
            Yii::getLogger()->flush(true);

            Yii::app()->handleError($error['type'], 'Fatal error: ' . $error['message'], $error['file'], $error['line']);
        }
    }
}

$config1 = array(
    'onEndRequest' => 'fatallog',
    'onBeginRequest' => ['DiyRules', 'generateNewRules'],
    'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
    'language' => 'zh_cn',
    'name'=>'house',
    'id'=>'qbs',
    'timeZone' => 'Asia/Shanghai',
    'defaultController' => 'site',
    'preload'=>array('log'),
    'import'=>array(
        'application.extensions.*',
        'application.extensions.validator.*',
        'application.models.*',
        'application.models_ext.*',
        'application.models_ext.siteSetting.*',
        'application.components.*',
        'application.widgets.*',
        'application.models_ext.siteSetting.*',
    ),
    'modules'=>array(
        'gii'=>array(
            'class'=>'system.gii.GiiModule',
            'password'=>'123',
            'ipFilters'=>array('127.0.0.1','::1'),
        ),
        'admin',
        'home',
        'vip',
        'api',
        'wap',
        'rest',
        'urm',
        'hangjia',
        'resoldhome',
        'vipie'
    ),
    'controllerMap' => array(
        'ueditor' => array(
            'class' => 'ext.ueditor.UeditorController',
            'config' => array(),
            'thumbnail' => false,
            'watermark' => '',
            'locate' => 9,
        // 'useQiniu'=>true
        ),
        'ucapi' => array(
			'class'=>'application.components.hjpassport.ucenter.HjUcenterController'
		),
		'pwapi' => array(
			'class' => 'application.components.hjpassport.phpwind.HjPhpwindController',
		)
    ),
    'components'=>array(
        //新版登录机制通行证组件
		'passport' => array(
			'class' => 'application.components.hjpassport.HjPassport',
			'type' => function(){
					return SM::ucConfig()->ucType();
				},
			'options' => function(){
                return [
                    'ucServer' => SM::ucConfig()->ucServer,
                    'appId' => SM::ucConfig()->ucAppid,
                    'key' => SM::ucConfig()->ucKey,
                    'charset' => SM::urmConfig()->bbsCode,
                ];
				}
		),
        //uc体系组件
		'uc' => array(
			'class' => 'application.components.hangjiauc.HangjiaUc_HjUc',
			'curlConnect' => true,
			'passportComponentName' => 'passport',
			'userComponentName' => 'ucUser',
			'urmHost' => function(){
                return Yii::app()->params['urmHost'];
            },
		),
		//uc体系使用的user组件
		'ucUser' => array(
			'class' => 'CWebUser',
			'stateKeyPrefix' => '_hangjiauc',
            'allowAutoLogin' => true,
		),
        'request' => array(
            'enableCsrfValidation' => false,
            'csrfTokenName' => 'CSRF_TOKEN'
        ),
        'user'=>array(
            'allowAutoLogin'=>true,
        ),
        'urlManager' => array(
            'urlFormat' => 'path',
            'showScriptName' => false,
            'rules'=>array(
                'resoldhome'=>'resoldhome/index/index',
                'pwapi' => 'pwapi',//pw客户端接收接口地址
                'api/ucapi' => 'ucapi',//dz、uc客户端接收接口地址
                'iframe/houselabel'=>'home/xsy/three',
                'pindao' => 'home/xsy/one',
                'gii' => 'gii',
                // 二手房模块
                'vipApi/<a:\w+>'=>'api/vipApi/<a>',
                'api/vipapi/<a:\w+>'=>'api/vipApi/<a>',
                'userApi/<a:\w+>'=>'api/userApi/<a>',
                'api/userapi/<a:\w+>'=>'api/userApi/<a>',
                'api/resoldwapapi/<a:\w+>'=>'api/resoldWapApi/<a>',
                'resoldWapApi/<a:\w+>/<b:\w+>'=>'api/resoldWapApi/<a><b>',
                //二手房pc
                'resoldhome/<c:\w+>/<a:\w+>' => 'resoldhome/<c>/<a>',
                // 详细页路由转换
                'resoldhome/esf/info/<id:\d+>'=>'resoldhome/esf/info?id=<id>',
                'resoldhome/zf/info/<id:\d+>'=>'resoldhome/zf/info?id=<id>',
                'resoldhome/qg/detail/<id:\d+>/<type:\w+>'=>'resoldhome/qg/detail?id=<id>&type=<type>',
                'resoldhome/qz/detail/<id:\d+>/<type:\w+>'=>'resoldhome/qz/detail?id=<id>&type=<type>',
                //后台
                'admin' => 'admin/common/index',
                'admin/<_a:(login|logout)>' => 'admin/common/<_a>',
                'admin/ajaxCheckFields' => 'admin/ajaxCheckFields',
                //中介ie版
                'vipie/<c:\w+>/<a:\w+>' => 'vipie/<c>/<a>',
                
                //中介
                'vip/zf/zfEdit' =>'vip/zf/publish',
                'vip/<c:\w+>/<a:\w+>' => 'vip/<c>/<a>',
                'wap/<c:\w+>/<a:\w+>' => 'wap/<c>/<a>',
                'vip' => 'vip/common/index',
                //前台
                'fz-<fenzhan:\w+>' => 'home/index/index',//分站主页
                'fzactive-<fenzhan:\w+>' => 'home/index/active',//分站动态
                '/' => 'home/index/index',
                'plot/<place:\d+>/<ext:\w+>' => 'home/plot/list',
                'plot/<place:\d+>/' => 'home/plot/list',
                'plot/<ext:\w+>' => 'home/plot/list',
                'plot' => 'home/plot/list',
                'ditu' => 'home/map/index',

                //知识库
                'baike' => 'home/baike/index',
                'baike/l<cid:\d+>' =>'home/baike/list',
                'baike/<id:\d+>.html' => 'home/baike/detail',
                'home/baike/<a:\w+>' => 'home/baike/<a>',
                'home/rent/list' => 'home/product/list/cate/50',
                //买房顾问
                'maifangguwen' => 'home/adviser/index',
                //前台资讯
                'news/<id:\d+>.html'=>'home/news/info',
                'news/<cid:\w+>'=>'home/news/list',
                'tag/<tag:\w+>'=>'home/news/list',
                'news'=>'home/news/list',
                'match'=>'home/match/index',
                'data'=>'home/data/index',
                //前台资讯
                'video/<id:\d+>.html'=>'home/video/info',
                'videos/<cid:\d+>'=>'home/video/list',
                'videos'=>'home/video/list',
                'image'=>'home/album/list',
                'image/<cid:\w+>'=>'home/album/list',
                'image/<id:\d+>.html'=>'home/album/info',
                'user'=>'home/user/index',
                'login'=>'home/user/login',
                'regis'=>'home/user/regis',
                'findpwd'=>'home/user/findpwd',
                'addOne'=>'home/user/addOne',
                'setPraise'=>'home/news/setPraise',
                'checkPhone'=>'home/user/checkPhone',
                'checkCode'=>'home/user/checkCode',
                'checkName'=>'home/user/checkName',
                'checkOld'=>'home/user/checkOld',
                'add'=>'home/comment/add',
                'alltag'=>'home/news/alltag',
                //百度编辑器
                'ueditor/<a:\w+>' => 'ueditor/<a>',

                 //restful
                array('rest/index/<_c>',  'pattern' => 'rest/<_c:\w+>', 'verb' => 'GET', 'parsingOnly' => true),
                array('rest/create/<_c>', 'pattern' => 'rest/<_c:\w+>', 'verb' => 'POST', 'parsingOnly' => true),
                array('rest/<_c>/view',   'pattern' => 'rest/<_c:\w+>/<id>', 'verb' => 'GET', 'parsingOnly' => true),
                array('rest/<_c>/update', 'pattern' => 'rest/<_c:\w+>/<id>', 'verb' => 'PUT', 'parsingOnly' => true),
                array('rest/<_c>/delete', 'pattern' => 'rest/<_c:\w+>/<id>', 'verb' => 'DELETE', 'parsingOnly' => true),

                //wap楼盘
                'wap/<py:[a-z0-9]+>' => 'wap/plot/index',
                'wap/<py:[a-z0-9]+>/huxing/<id:\d+>' => 'wap/plot/huxingDetail',
                'wap/<py:[a-z0-9]+>/huxing' => 'wap/plot/huxingList',
                'wap/<py:[a-z0-9]+>/pingce' => 'wap/plot/evaluate',
                'wap/<py:[a-z0-9]+>/map' => 'wap/plot/around',
                'wap/<py:[a-z0-9]+>/price' => 'wap/plot/index',//v2临时改造
                'wap/<py:[a-z0-9]+>/news' => 'wap/news/index',//v2临时改造
                'wap/<py:[a-z0-9]+>/faq' => 'wap/plot/index',//v2临时改造
                'wap/<py:[a-z0-9]+>/dianping' => 'wap/plot/comment',//v2临时改造
                'wap/<py:[a-z0-9]+>/<_a:\w+>' => 'wap/plot/<_a>',
                '<_m:\w+>/<_c:\w+>/<_a:\w+>' => '<_m>/<_c>/<_a>',

                // //PC楼盘
                // '<py:[a-z0-9]+>' => 'home/plot/index',
                // '<py:[a-z0-9]+>/album/<t:\d+>' => 'home/plot/album',
                // '<py:[a-z0-9]+>/huxing/<t:\d+>' => 'home/plot/huxing',
                // '<py:[a-z0-9]+>/img/<pid:\d+>_<offset:\d+>' => 'home/plot/image',
                // '<py:[a-z0-9]+>/img/<pid:\d+>' => 'home/plot/image',
                // '<py:[a-z0-9]+>/hx/<pid:\d+>_<offset:\d+>' => 'home/plot/hximg',
                // '<py:[a-z0-9]+>/hx/<pid:\d+>' => 'home/plot/hximg',
                // '<py:[a-z0-9]+>/pingce' => 'home/plot/evaluate',
                // '<py:[a-z0-9]+>/dianping' => 'home/plot/comment',
                // '<py:[a-z0-9]+>/<_a:\w+>' => 'home/plot/<_a>',
                // '<_m:\w+>/<_c:\w+>/<_a:\w+>' => '<_m>/<_c>/<_a>',
            ),
        ),
        //迅搜
        'search' => array(
            'class' => 'application.extensions.EXunSearch',
            'xsRoot' => 'application.vendor', // xunsearch 安装目录
            'charset' => 'utf-8', // 您当前使用的字符集（索引、搜索结果）
        ),
        'errorHandler'=>array(
            // use 'site/error' action to display errors
            'errorAction'=>'home/error/error',
        ),
        'log'=>array(
            'class'=>'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, info',
                ),
                 array(
                  'class' => 'CWebLogRoute',
                  'levels' => 'profile,trace',
                  'categories' => 'system.db.*',
                  'showInFireBug' => true,
                  ),
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning',
                    'logFile' => 'db.log',
                    'categories' => 'exception.cdbexception'
                ),
                //记录AR类中主要错误
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'info',
                    'logFile' => 'model_ext.log',
                    'categories' => 'application.models_ext.*'
                ),
                //v2升级用
                array(
                    'class' => 'CFileLogRoute',
                    'logFile' => 'v2upgrade.log',
                    'categories' => 'v2'
                ),
            ),
        ),
        'session' => array(
            'autoStart' => false,
            'cookieMode' => 'allow',
            'sessionName' => 'hj_house',
        ),
    ),
);
return require_once(__DIR__ . '/config.php');
