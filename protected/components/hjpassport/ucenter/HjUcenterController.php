<?php
class HjUcenterController extends CExtController
{
    /**
     * 解码后的get参数
     * @var array
     */
    public $get = array();
    /**
     * 解码后的post参数
     * @var array
     */
    public $post = array();

    public $componentName = 'passport';

    public function init()
    {
        Yii::app()->user->setStateKeyPrefix('_ppt');
        Yii::app()->{$this->componentName};
        Yii::log('请求来了', 'error');
    }

    /**
     * 扫描actions目录构建数组
     * @return array
     */
    public function actions()
    {
        $dir = rtrim(Yii::getPathOfAlias('hjpassport.ucenter'),'\/\\') . DIRECTORY_SEPARATOR . 'actions';
        Yii::import('hjpassport.ucenter.actions.UcReceiverAction');
        $dirArr = array();
        if(($handle = opendir($dir)) !== false){
            while(($fileName = readdir($handle)) !== false){
                //扫描目录获得所有action的ID并构建对应路径别名，ClientAction是基类
                if(($pos = strpos($fileName, 'Action.php'))>0 && $fileName!=='UcReceiverAction.php'){
                    $actionId = strtolower(substr($fileName, 0, $pos));
                    $dirArr[$actionId] = 'hjpassport.ucenter.actions.'.ucfirst($actionId).'Action';
                }
            }
        }
        return $dirArr;
    }

    /**
     * 接收动作，解密code后转发action
     * @param  string $code [description]
     * @return [type]       [description]
     */
    public function actionIndex()
    {
        $code = Yii::app()->request->getParam('code', '');
        if(empty($code)){
            Yii::app()->end();
        }
        parse_str(Yii::app()->{$this->componentName}->getUc()->uc_authcode($code), $this->get);
        //参数验证
        if(empty($this->get)||!isset($this->get['time'])) {
    		Yii::app()->end('无效的请求参数');
    	}
        //验证参数有效期在1小时内
    	if(time() - $this->get['time'] > 3600) {
    		Yii::app()->end('验证参数已过期');
    	}

        $this->post = Yii::app()->{$this->componentName}->getUc()->uc_unserialize(file_get_contents('php://input'));

        //运行具体的处理动作
        $actionId = $this->get['action'];
        unset($this->get['action']);
        $this->run($actionId);
    }
}
