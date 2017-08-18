<?php
/**
 * 注册处理
 * @author tivon
 * @version 2016年9月1日
 */
class HangjiaUc_ServerModel_Register extends HangjiaUc_ServerModel
{
    public $result = [
        'uid' => '{uid}',
    ];

    public function run($username=null, $password=null)
    {
        if($username!==null && $password!==null) {
            $uid = $this->passport->register($username, $password);
            if($uid>0){
                return $this->render(array_replace($this->result, ['uid'=>$uid]));
            } else {
                $error = current($this->passport->getErrors());
                $this->log('注册失败', [
                    'errorCode' => $this->getErrorCode($uid),
                    'username' => $username,
                ]);
                return $this->error($uid);
            }
        } else {
            $this->log('注册失败，用户名或密码为null，请检查传参', [
                'username' => $username
            ]);
            return $this->error();
        }

    }

    /**
     * 将uc的错误码映射到我们规定的新的错误码
     * @return integer 新的错误码
     */
    public function getErrorCode($code)
    {
        $map = [
            -1 => 1001,
            -2 => 1002,
            -3 => 1003,
            -4 => 1004,
            -5 => 1005,
            -6 => 1006
        ];
        return isset($map[$code]) ? $map[$code] : $code;
    }
}
