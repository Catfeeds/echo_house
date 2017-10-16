<?php 
use Qiniu\Auth;
/**
 * 图片相关接口
 */
class ImageController extends ApiController
{   
    /**
     * 二维码接口
     * @return png图片
     */
    public function actionQrCode($data,$size=10)
    {
        $errorCorrectionLevel = 'L';
        $data = urldecode($data);       
        QRcode::png($data, false, $errorCorrectionLevel, $size, 0);
    }

    /**
     * [actionQnUpload 七牛图片上传]
     * @return [type] [description]
     */
    public function actionQnUpload()
    {
        $auth = new Auth(Yii::app()->file->accessKey,Yii::app()->file->secretKey);
        $policy = array(
            'mimeLimit'=>'image/*',
            'fsizeLimit'=>10000000,
            'saveKey'=>Yii::app()->file->createQiniuKey(),
        );
        $token = $auth->uploadToken(Yii::app()->file->bucket,null,3600,$policy);
        echo CJSON::encode( array('uptoken'=>$token));
        Yii::app()->end();
    }
}
?>
