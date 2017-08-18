<?php
/**
 * 更新手机处理
 * 更新绑定手机注意规则：
 * 1. 手机号若已绑定帐号，要给出提示，禁止操作，需要去绑定的帐号先解绑。
 * 2. 帐号已经绑定手机号，要给出提示，要先解绑旧手机号。
 * @author tivon
 * @version 2016年9月1日
 */
class HangjiaUc_ServerModel_UpdatePhone extends HangjiaUc_ServerModel
{
    /**
     * phone为null时，会清空$uid用户的手机号，进行解绑操作
     */
    public function run($uid=null, $phone=null)
    {
        if((int)$uid==0) {
            throw new Exception(__CLASS__.'::run()第一个参数不是一个有效的uid');
        }
        $u = Yii::app()->uc->getUser($uid,2);
        if(isset($u['code']) && $u['code']>0){
            return $u;
        }
        if($uid!==null) {
            $user = $this->loadModel($uid);
            if(!$user->getIsNewRecord() && $user->phone!='' && $phone!==null) {
                return $this->error(3010);
            }
            $user->phone = $phone===null ? '' : $phone;
            if($user->save()) {
                return $this->render('更新成功');
            } else {
                $errorCode = current(current($user->getErrors()));
                return $this->error($errorCode);
            }
        } else {
            return $this->error();
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
