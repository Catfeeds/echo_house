<?php
/**
 * 根据用户uid获取关联的手机号码
 * @author tivon
 * @version 2016-11-22
 */
class HangjiaUc_ServerModel_GetPhoneByUids extends HangjiaUc_ServerModel
{
    public function run($uids='')
    {
        $data = [];
        $uids = trim($uids, ',');
        //只支持一次性查询50个
        if($uids) {
            $uids = array_slice(explode(',',$uids),0,50);
            $users = UcUserExt::findAllByUids($uids);
            foreach($uids as $uid) {
                $data[$uid] = isset($users[$uid]) ? $users[$uid]->phone : '';
            }
        }
        return $this->render($data);
    }
}
