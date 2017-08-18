<?php
/**
 * 获取用户信息处理
 * @author tivon
 * @version 2016年9月1日
 */
class HangjiaUc_ServerModel_GetUser extends HangjiaUc_ServerModel
{
    public $result = [
        'uid' => '{uid}',
        'username' => '{username}',
        'icon' => '{icon}',
        'phone' => '{phone}',
        'nickname' => '{nickname}'
    ];

    /**
     * 通过用户名\uid\手机号获得用户信息
     * @param $uids 用户id，或用户名，或手机号。格式为字符串，以英文逗号分隔
     * @param $type 查找类别：1.根据用户名查找；2.根据用户uid查找；3.根据用户手机号查找；4.根据微信openid查找；5. 根据微信unionid查找；6.根据openid或unionid混合查找
    */
    public function run($uids='', $type=1)
    {
        if($uids!=='') {
            $isUid = false;
            switch($type) {
                case 1:
                    $isUid = false;
                    break;
                case 2:
                    $isUid = true;
                    break;
                case 3:
                    $phones = array_slice(explode(',',$uids),0,10);
                    $users = UcUserExt::findAllByPhones($phones);
                    if($users) {
                        $uids = [];
                        foreach($users as $user) {
                            $uids[] = $user->uid;
                        }
                        $uids = implode(',', $uids);
                    }
                    $isUid = true;
                    break;
                case 4:
                    $openids = array_slice(explode(',',$uids),0,10);
                    $users = UcUserExt::findAllByOpenIds($openids);
                    if($users) {
                        $uids = [];
                        foreach($users as $user) {
                            $uids[] = $user->uid;
                        }
                        $uids = implode(',', $uids);
                    }
                    $isUid = true;
                    break;
                case 5:
                    $unionids = array_slice(explode(',',$uids),0,10);
                    $users = UcUserExt::findAllByUnionIds($unionids);
                    if($users) {
                        $uids = [];
                        foreach($users as $user) {
                            $uids[] = $user->uid;
                        }
                        $uids = implode(',', $uids);
                    }
                    $isUid = true;
                    break;
                case 6:
                    $wechatIds = array_slice(explode(',',$uids),0,10);
                    $users = UcUserExt::findAllByWechatIds($wechatIds);
                    if($users) {
                        $uids = [];
                        foreach($users as $user) {
                            $uids[] = $user->uid;
                        }
                        $uids = implode(',', $uids);
                    }
                    $isUid = true;
                    break;
            }
            $infos = $this->passport->getUserInfoes($uids, $isUid);
            // var_dump($infos);die;
            $uids = $data = [];
            if(is_array($infos) ) {
                if($infos) {
                    foreach($infos as $v) {
                        $uids[] = $v['uid'];
                        $data[$v['uid']] = [
                            'uid' => $v['uid'],
                            'username' => $v['username'],
                            'icon' => $v['icon']
                        ];
                    }
                    $criteria = new CDbCriteria(['index'=> 'uid']);
                    $criteria->addInCondition('uid', $uids);
                    $ucUsers = UcUserExt::model()->findAll($criteria);
                    foreach($data as $uid => $v) {
                        $ucUser = isset($ucUsers[$uid]) ? $ucUsers[$uid] : null;
                        $data[$uid]['phone'] = $ucUser ? $ucUser->phone : '';
                        $data[$uid]['nickname'] = $ucUser&&$ucUser->nick_name ? $ucUser->nick_name : $v['username'];
                        $data[$uid]['openid'] = $ucUser ? $ucUser->openid : '';
                        $data[$uid]['unionid'] = $ucUser ? $ucUser->unionid : '';
                    }
                    return $this->render($data);
                } else {
                    return $this->error(2001);
                }
            }
        }
        return $this->error();
    }
}
