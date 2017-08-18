<?php
/**
 * console控制台配置
 */
if(!function_exists('fatallog'))
{
	function fatallog($event) {
		$error = error_get_last();
		$errorsToHandle = E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING;
		if (!empty($error) && ($error['type'] & $errorsToHandle))
		{
			$message = 'FATAL ERROR: ' . $error['message'];
			if (!empty($error['file']))
				$message .= ' (' . $error['file'] . ' :' . $error['line'] . ')';
			if (isset($_SERVER['REQUEST_URI']))
				$message .= 'REQUEST_URI=' . $_SERVER['REQUEST_URI'];

			Yii::log($message, CLogger::LEVEL_ERROR, 'php');
			Yii::getLogger()->flush(true);

			Yii::app()->handleError($error['type'], 'Fatal error: ' . $error['message'], $error['file'], $error['line']);
		}
	}
}

$config1 = array(
		'onEndRequest' => 'fatallog',
		'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
		'name' => 'house',
        'timeZone' => 'Asia/Shanghai',
        'preload' => array('log'),
		'import' => array(
			'application.extensions.*',
			'application.extensions.validator.*',
			'application.models.*',
			'application.models_ext.*',
			'application.models_ext.siteSetting.*',
			'application.components.*',
		),
		'components' => array(
			'db2' => array(//70测试库，用于同步推荐位等数据
				'class' => 'CDbConnectionExt',
				'connectionString' => 'mysql:host=61.160.251.70;dbname=hxfamily', //主数据库 写
				'emulatePrepare' => true,
				'username' => 'hangjia',
				'password' => 'hangjia2015',
				'charset' => 'utf8',
				'schemaCachingDuration' => 3600,
				'enableParamLogging' => false,
				'enableProfiling' => false,
				'enableSlave' => false, //从数据库启用
				'masterRead' => false, //紧急情况 从数据库无法连接 启用主数据库 读功能
			),
            //航加统一七牛静态文件服务器配置
            'staticFile' => [
                'class' => 'application.components.FileComponent',
                'enableCloudStorage' => true,
                'accessKey' => 'ucBKr7BxOc2PFVMl6w47-H4qvOwJofNsoPUKBotA',
                'secretKey' => '4W7QPR9U3x7gNJ3Z_Rgunw3Ny5QrL274cYqW6NqH',
                'bucket' => 'hangjia-static',
                'host' => 'http://static.hangjiayun.com'
            ],
			'log' => array(
				'class' => 'CLogRouter',
				'routes' => array(
					array(
							'class' => 'CFileLogRoute',
							'levels' => 'error, info',
					),
					array(
							'class' => 'CFileLogRoute',
							'levels' => 'error, warning',
							'logFile' => 'db.log',
							'categories' => 'exception.cdbexception'
					),
				),
			),
			//迅搜
	        'search' => array(
	            'class' => 'ext.EXunSearch',
	            'xsRoot' => 'application.vendor', // xunsearch 安装目录
	            // 'project' => 'demo', // 搜索项目名称或对应的 ini 文件路径
	            'charset' => 'utf-8', // 您当前使用的字符集（索引、搜索结果）
	        ),
			'authManager' => array(
	            'class' => 'CDbAuthManagerExt',
	            'connectionID' => 'db',
	            'itemTable' => 'auth_item',
	            'itemChildTable' => 'auth_item_child',
	            'assignmentTable' => 'auth_assignment',
	        ),
		),
);
return require_once(__DIR__ . '/config.php');
