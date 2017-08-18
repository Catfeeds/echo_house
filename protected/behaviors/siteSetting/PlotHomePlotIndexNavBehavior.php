<?php
class PlotHomePlotIndexNavBehavior extends CBehavior
{
    public $plot;

    public function getMenu(PlotExt $plot)
    {
        $this->plot = $plot;
        $menu = [];
        $owner = $this->owner;
        $value = $owner();
        if(is_array($value)) {
            foreach($value['title'] as $k=>$title) {
                if($this->checkIsEnable($value['url'][$k])){
                    $menu[] = [
                        'name' => $title,
                        'url' => $this->createUrl($value['url'][$k]),
                        'blank' => $value['blank'][$k],
                        'active' => isset($this->owner->data['active'][$value['url'][$k]]) && in_array(Yii::app()->controller->action->id, $this->owner->data['active'][$value['url'][$k]]),
                    ];
                }
            }
        }
        return $menu;
    }

    private function createUrl($url)
    {
        switch($url) {
            case '{esf}':
                if(isset($this->plot->data_conf['esfUrl'])&&$this->plot->data_conf['esfUrl']) {
                    return $this->plot->data_conf['esfUrl'];
                } elseif($this->plot->old_id&&SM::pageUrlConfig()->homePlotIndexEsfListUrl()) {
                    return str_replace('{id}',$this->plot->old_id,SM::pageUrlConfig()->homePlotIndexEsfListUrl());
                } else {
                    return Yii::app()->controller->createUrl('/resoldhome/plot/pesflist',['py'=>$this->plot->pinyin]);
                }
                break;
            case '{zf}':
                if(isset($this->plot->data_conf['zfUrl'])&&$this->plot->data_conf['zfUrl']) {
                    return $this->plot->data_conf['zfUrl'];
                } elseif($this->plot->old_id&&SM::pageUrlConfig()->homePlotIndexZfListUrl()) {
                    return str_replace('{id}',$this->plot->old_id,SM::pageUrlConfig()->homePlotIndexZfListUrl());
                } else {
                    return Yii::app()->controller->createUrl('/resoldhome/plot/pzflist',['py'=>$this->plot->pinyin]);
                }
                break;
            case '{bbs}':
                if(SM::urmConfig()->bbsTagPageUrl()&&$this->plot->tag_id) {
                    return str_replace('{tagid}',$this->plot->tag_id, SM::urmConfig()->bbsTagPageUrl());
                } else {
                    return '';
                }
                break;
        }

        if(strpos($url,'{')!==false && strpos($url,'}')!==false) {
            $actionId = trim($url, '{}');
            return Yii::app()->controller->createUrl('/home/plot/'.$actionId,['py'=>$this->plot->pinyin]);
        } else {
            return $url;
        }
    }

    /**
     * 检查内置项是否启用
     * @param  string $url 配置的url
     * @return boolean
     */
    public function checkIsEnable($url)
    {
        switch($url) {
            case '{evaluate}':
                return (bool)(SM::adviserConfig()->enable() && $this->plot->evaluate && $this->plot->evaluate->getIsEnabled() && SM::PlotEvaluateConfig()->enable());
                break;
            case '{comment}':
                return (bool)(SM::adviserConfig()->enable() && $this->plot->hasComments());
                break;
            default:
                return true;
        }
    }
}
