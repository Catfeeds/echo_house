<?php
define('S_DIR', __DIR__);
define('S_CHARSET', 'utf-8');//客户端编码
define('P_W', 'admincp');
define('R_P', S_DIR.'/');
define('D_P', R_P);
class HjPhpwindController extends CExtController
{
    public $componentName = 'passport';

    public $fromCharset = 'utf-8';
    public $apiClient;
    public $requestId = 0;

    /**
     * code解密后的数据放这
     * @var array
     */
    public $data = array();

    public function init()
    {
        Yii::app()->user->setStateKeyPrefix('_ppt');
        Yii::app()->{$this->componentName};
        $this->requestId = date('ndHis');//生成此次请求编号，以检测后面该编号请求的去处
        Yii::log('请求编号No:'.$this->requestId, 'error');
    }

    /**
     * 扫描actions目录构建数组
     * @return array
     */
    public function actions()
    {
        $dir = rtrim(Yii::getPathOfAlias('hjpassport.phpwind'),'\/\\') . DIRECTORY_SEPARATOR . 'actions';
        Yii::import('hjpassport.phpwind.actions.UcReceiverAction');
        $dirArr = array();
        if(($handle = opendir($dir)) !== false){
            while(($fileName = readdir($handle)) !== false){
                //扫描目录获得所有action的ID并构建对应路径别名，ClientAction是基类
                if(($pos = strpos($fileName, 'Action.php'))>0 && $fileName!=='UcReceiverAction.php'){
                    $actionId = strtolower(substr($fileName, 0, $pos));
                    $dirArr[$actionId] = 'hjpassport.phpwind.actions.'.ucfirst($actionId).'Action';
                }
            }
        }
        return $dirArr;
    }

    /**
     * @param  string $code [description]
     * @return [type]       [description]
     */
    public function actionIndex()
    {
        $params = $_POST+$_GET;
        if(isset($params['charset'])&&$params['charset']=='gbk'){
            $this->fromCharset = 'gbk';
        }
        Yii::log(CJSON::encode($params),'error');
        Yii::import('hjpassport.phpwind.client.*');
        $uc = Yii::app()->{$this->componentName}->getUc();
        $this->apiClient = new api_client($uc->key, $uc->appId);
        $response = $this->apiClient->run($_POST+$_GET);

        Yii::log(serialize($response),'error');

        if(is_array($response)&&isset($response['action'])){
            if(isset($response['params'])) $this->data = $response['params'];
            $this->run($response['action']);
        }
    }

    /**
     * 收到用户同步通知时登陆解码
     * @param  [type] $string [description]
     * @param  [type] $encode [description]
     * @return [type]         [description]
     */
    public function strcode($string, $encode = true)
    {
		!$encode && $string = base64_decode($string);
		$code = '';
		$key  = substr(md5($_SERVER['HTTP_USER_AGENT'] . Yii::app()->{$this->componentName}->getUc()->key),8,18);
		$keylen = strlen($key);
		$strlen = strlen($string);
		for ($i = 0; $i < $strlen; $i++) {
			$k		= $i % $keylen;
			$code  .= $string[$i] ^ $key[$k];
		}
        $str = ($encode ? base64_encode($code) : $code);
        return $this->apiClient->pwConvert($str, S_CHARSET, $this->fromCharset);
	}
}
