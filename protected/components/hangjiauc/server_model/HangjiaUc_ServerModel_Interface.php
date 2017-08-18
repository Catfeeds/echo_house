<?php
Interface HangjiaUc_ServerModel_Interface
{
    /**
     * 运行具体业务逻辑
     * @return array 见文档
     */
    public function run();
    /**
     * 获取错误代码
     * @return integer
     */
    public function getErrorCode($code);
}
