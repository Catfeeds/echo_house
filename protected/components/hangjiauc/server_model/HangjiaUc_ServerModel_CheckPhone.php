<?php
/**
 * 注册处理
 * @author tivon
 * @version 2016年9月1日
 */
class HangjiaUc_ServerModel_CheckPhone extends HangjiaUc_ServerModel
{
    public function run($phone='', $uid=0)
    {
        $user = UcUserExt::findByPhone($phone);
        if($user) {
            return intval($uid)>0 && $uid==$user->uid ? $this->error(3009) : $this->error(3007);
        } else {
            return $this->error(3015);
        }
    }
}
