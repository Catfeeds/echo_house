<?php

class VideoController extends ApiController
{
    public function actionNotify()
    {
        //获取回调的body信息
        $callbackBody =json_decode(file_get_contents('php://input'));

        //验证回调是否为七牛发起
        $isQiniu=$this->isQiniu($callbackBody);
        if($isQiniu['status']){
            $video=$isQiniu['video'];
            $video->qiniuPro($callbackBody);
            if($video->transcoded==0){
                $video->setStatusOpen();
            }
            echo json_encode(array('ret' => 'success'));
        }
    }

    /**
     * 根据七牛文档规定的回调内容格式，判断当前回调的发起方是否为七牛
     * @param $body
     * @return array
     */
    private function isQiniu($body)
    {
        $result=array('status'=>false,'video'=>null);
        if(is_object($body)){
            $bucket=$body->inputBucket;
            $persistentId=$body->id;
            $video=PlotVideoExt::model()->find('persistent_id=:persistentId',array(':persistentId'=>$persistentId));
            if($bucket===Yii::app()->file->bucket&&$video){
                $result['status']=true;
                $result['video']=$video;
            }
        }
        return $result;
    }
}