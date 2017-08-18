<?php
/**
 * 更新用户绑定微信的openid
 * @author tivon
 * @version 2016-09-19
 */
class HangjiaUc_ServerModel_UpdateUnionid extends HangjiaUc_ServerModel
{
    /**
     * {@see $unionId}默认为null，意味着进行解绑微信号
     */
    public function run($uid=null, $unionId=null)
    {
        if($uid===null || intval($uid)<=0) {
            throw new Exception(__CLASS__.'::run()第一个参数不是一个有效的uid');
        }
        $u = Yii::app()->uc->getUser($uid,2);
        if(Yii::app()->uc->hasError()){
            return $u;
        }

        $user = $this->loadModel($uid);
        if(!$user->getIsNewRecord() && $user->unionid!='' && $uniondId!==null) {
            return $this->error(3014);
        }
        $user->unionid = $unionId===null ? '' : $unionId;
        if($user->save()) {
            return $this->render('更新成功');
        } else {
            $errorCode = current(current($user->getErrors()));
            return $this->error($errorCode);
        }
    }

    protected function loadModel($uid)
    {
        if($user = UcUserExt::findByUid($uid)) {
            return $user;
        } else {
            $user = new UcUserExt;
            $user->uid = $uid;
            return $user;
        }
    }
}
