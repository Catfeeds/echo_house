<?php
/**
 * 同步登录处理
 * @author tivon
 * @version 2016年9月1日
 */
class HangjiaUc_ServerModel_SynLogin extends HangjiaUc_ServerModel
{
    /**
     * 同步登录代码
     */
    public function run($uid=0, $returnHtml=true)
    {
        $data = [];
        if($uid>0){
            $html = $this->passport->synLogin($uid);
            if(!empty($html)) {
                if($returnHtml) {
                    $data = [$html];
                } else {
                    $pattern = '/src=[\'\"](http:\/\/[A-Z0-9][A-Z0-9_-]*(\.[A-Z0-9][A-Z0-9_-]*)+.*)[\'\"]/iU';
                    preg_match_all($pattern, $html, $urls);
                    if(isset($urls[1])) {
                        $data = $urls[1];
                    }
                }
            }
        }
        return $this->render($data);
    }
}
