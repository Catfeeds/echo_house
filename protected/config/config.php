<?php
if(!file_exists(__DIR__.'/config.yaml'))
    throw new CException('config.yaml配置文件未找到');
require_once(dirname(__FILE__) .'/../vendor/mustangostang/spyc/Spyc.php');
$config = Spyc::YAMLLoad(__DIR__ . '/config.yaml');
//验证配置文件有效性
try
{
    $configRules = array(
        'db' => array('数据库',
            'host' => '地址',
            'username' => '用户名',
            'password' => '密码',
            'dbname' => '库名',
        ),
        'cache' => array('Memcache',
            'host' => '地址',
            'port' => '端口',
        ),
        'params' => array('系统参数',
            'urmHost' => 'URM地址',
        ),
        'file' => array('文件上传配置',
            'host' => '访问域名',
            'enableCloudStorage' => '启用云存储',
        ),
    );
    //若启用七牛则检查ak、sk、bucket
    if(isset($config['file']['enableCloudStorage']) && $config['file']['enableCloudStorage'])
    {
        $configRules['file'] = CMap::mergeArray($configRules['file'], array(
            'accessKey' => '七牛AK',
            'secretKey' => '七牛SK',
            'bucket' => '七牛bucket',
        ));
    }
    else
    {
        $configRules['file'] = CMap::mergeArray($configRules['file'], array(
            'root' => '文件存储目录',
        ));
    }
    // var_dump($configRules);die;
    foreach($configRules as $k=>$v)
    {
        $name = array_shift($v);
        foreach($v as $kk=>$vv)
        {
            if(!isset($config[$k][$kk])) throw new CException($name.$vv.'('.$kk.')未配置！');
        }
    }
}
catch(CException $e)
{
    header("Content-type: text/html; charset=utf-8");
    exit($e->getMessage());
}
// $spyc = new Spyc;
// var_dump($spyc->dump($config));die;


$params = require_once(__DIR__ . '/params.php');
$config2 = array(
    'components' => array(
        'db' => array(
            'class' => 'CDbConnectionExt',
            'connectionString' => 'mysql:host='.$config['db']['host'].';dbname='.$config['db']['dbname'], //主数据库 写
            'emulatePrepare' => true,
            'username' => $config['db']['username'],
            'password' => $config['db']['password'],
            'charset' => 'utf8',
            'schemaCachingDuration' => 3600,
            'enableParamLogging' => false,
            'enableProfiling' => false,
            'enableSlave' => false, //从数据库启用
            'masterRead' => false, //紧急情况 从数据库无法连接 启用主数据库 读功能
            'markDeadSeconds' => 10, //标记数据库失效时间
            'slaves' => array( //从数据库
                array( //slave1
                      'connectionString' => 'mysql:host=localhost;dbname=b',
                      'emulatePrepare' => true,
                      'username' => 'root',
                      'password' => '123',
                      'charset' => 'utf8',
                      'schemaCachingDuration' => 3600,
                      'enableParamLogging' => true,
                      'enableProfiling' => true,
                ),
            ),
        ),
        'file' => array('class' => 'application.components.FileComponent') + $config['file'],
        'mns'=>array(
            'class' => 'application.components.AliMns',
        ) + $config['mns'],
    ),
    'params' => CMap::mergeArray($params, $config['params'])
);

$cacheConfig['components']['cache'] = isset($config['cache']['host'])&&$config['cache']['host'] ? array('class'=>'CMemCache', 'servers'=>array($config['cache'])) : array('class'=>'CFileCache');
$config2 = CMap::mergeArray($config2, $cacheConfig);
$config = CMap::mergeArray($config1, $config2);
// var_dump($config);die;
return $config;
?>
