<?php
use Qiniu\Storage\UploadManager;
use Qiniu\Auth;
/**
 * 定时清理中介预约记录
 * liyu
 * 没半个执行一次
 */
class AppointCommand extends CConsoleCommand{
    public function actionClearAppoint(){
        $time = strtotime('-15 day');//半月 15 天之前
        try{
            $criteria = new CDbCriteria;
            $criteria->addCondition('created<:created');
            $criteria->params[':created'] = $time;
            ResoldAppointExt::model()->enabled()->deleteAll($criteria);
        }catch(Exception $e) {
            echo $e->getMessage() . "\n";
            return false;
        }
    }
    public function actionUp()
    {
        $criteria = new CDbCriteria;
        $criteria->addSearchCondition('image','http');
        $plotImgs = PlotExt::model()->undeleted()->findAll($criteria);
        if($plotImgs){
            foreach ($plotImgs as $key => $value) {
                $value->image = $this->sfimage($value->image,$value->image);
                $value->save();
                if($hxs = $value->hxs){
                    foreach ($hxs as $hx) {
                        $hx->image = $this->sfimage($hx->image,$hx->image);
                        $hx->save();
                    }
                }
                if($imgs = $value->images){
                    foreach ($imgs as $img) {
                        $img->url = $this->sfimage($img->url,$img->url);
                        $img->save();
                    }
                }
            }
        }
    }

    /**
     * [actionQnUpload 七牛图片上传]
     * @return [type] [description]
     */
    public function createQnKey()
    {
        $auth = new Auth(Yii::app()->file->accessKey,Yii::app()->file->secretKey);
        $policy = array(
            'mimeLimit'=>'image/*',
            'fsizeLimit'=>10000000,
            'saveKey'=>Yii::app()->file->createQiniuKey(),
        );
        $token = $auth->uploadToken(Yii::app()->file->bucket,null,3600,$policy);
        return $token;
    }

    public function sfImage($img='',$refer = '')
    {
        $opt=array("http"=>array("header"=>"Referer: " . $refer)); 
        $context=stream_context_create($opt); 
        try{
            $file_contents = file_get_contents($img,false, $context);
        } catch(Exception $e){
            echo $e->getMessage();
            return '';
        }
        
        $name = str_replace('.', '', microtime(1)) . rand(100000,999999).'.jpg';
        $path = '/mnt/sfimages\/';
        if (! file_exists ( $path )) 
            mkdir ( "$path", 0777, true );
        file_put_contents($path.$name, $file_contents);
        $fileName = Yii::app()->file->getFilePath().str_replace('.', '', microtime(1)) . rand(100000,999999).'.jpg';

        $upManager = new UploadManager();
        try{
            list($ret, $error) = $upManager->putFile($this->createQnKey(),$fileName, $path.$name);
        } catch(Exception $e) {
            echo $e->getMessage();
            return '';
        }
        
        if(!$error){
            unlink($path.$name);
            return $ret['key'];
        }
        else
            return '';
    }
}
