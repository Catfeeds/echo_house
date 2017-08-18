<?php
/**
 * 更新密码处理
 * @author tivon
 * @version 2016年9月1日
 */
class HangjiaUc_ServerModel_UpdatePwd extends HangjiaUc_ServerModel
{
    public function run($username=null, $pwd=null, $oldPwd=null)
    {
        // var_dump($this->passport->updatePwd($username, $pwd, $oldPwd));die;
        // 目前只支持用户名
        $code = $this->passport->updatePwd($username, $pwd, $oldPwd);
        if($code>0) {
            return $this->render('更新成功');
        } else {
            return $this->error($code);
        }
    }

    public function getErrorCode($code)
    {
        $map = [
            0 => 3001,
            -1 => 3002,
            -4 => 3003,
            -5 => 3004,
            -6 => 3005,
            -7 => 3001,
            -8 => 3006,
        ];
        return isset($map[$code]) ? $map[$code] : $code;
    }
}
