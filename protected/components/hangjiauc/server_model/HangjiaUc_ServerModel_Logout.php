<?php
/**
 * 退出处理
 * @author tivon
 * @version 2016年9月1日
 */
class HangjiaUc_ServerModel_Logout extends HangjiaUc_ServerModel
{
    public function run($returnHtml=true)
    {
        $data = [];
        $html = $this->passport->synLogout();
        if($returnHtml) {
            $data = [$html];
        } else {
            $pattern = '/src=[\'\"](http:\/\/[A-Z0-9][A-Z0-9_-]*(\.[A-Z0-9][A-Z0-9_-]*)+.*)[\'\"]/iU';
            preg_match_all($pattern, $html, $urls);
            if(isset($urls[1])) {
                $data = $urls[1];
            }
        }
        return $this->render($data);
    }
}
