<?php
/**
 * UC接口动作：接收更改用户名
 * @author tivon
 * @version 2016-01-13
 */
class RenameuserAction extends UcReceiverAction
{
    /**
     * 运行方法
     * $get['uid']用户uid
     * $get['oldusername']旧用户名
     * $get['newusername']新用户名
     */
    public function run()
    {
        if(!self::API_RENAMEUSER){
            echo self::API_RETURN_FORBIDDEN;
        }

        //更改本应用中用户名的逻辑

        echo self::API_RETURN_SUCCEED;
    }
}
