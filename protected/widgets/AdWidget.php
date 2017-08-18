<?php
/**
 * 百度广告小物件
 * 涉及{@see BaiduAdExt}模型类
 */
class AdWidget extends CWidget
{
    /**
     *  广告模式 1 百度 2 URM
     */
    public $render_type = 1;
    /**
     * 收集的广告id，一个页面中所有调用处的广告id收集起来
     * @var array
     */
    static $ids = array();
    /**
     * 将上面所有收集起来的广告id拼成js代码渲染在页面顶部，渲染的代码在home/index/index模板底部
     * @var string
     */
    static $js = '';
    /**
     * 广告位
     * @var string
     */
    public $position;
    /**
     * 分站站点id
     * @var integer
     */
    public $substationId = 0;

    /**
     * 广告实例
     * @var [type]
     */
    protected $ads;

    /**
     * 初始化
     */
    public function init()
    {
        $this->render_type = (int)SM::advertConfig()->type();

        if ($this->render_type === 2) {

            $data = CacheExt::get('urm-ad');

            if (!$data) {
                $data = HttpHelper::get(Yii::app()->params['urmHost'].'info/get?site='.strtolower(Yii::app()->name));
                $data = CJSON::decode($data['content']);
                CacheExt::set('urm-ad', $data, 7200, 'URM广告数据缓存');
            }

            if (!$data['status']) {
                return false;
            }
            $ads = Util::get($data, $this->position);
            if (!$ads) return false;
            $this->ads = $ads;
        } else {
            if($this->position===null)
                return;
            $this->ads = BaiduAdExt::model()->getByStationId($this->substationId)->position($this->position)->findAll();
        }
    }

    /**
     * 渲染百度广告js
     * 因为渲染的js需要放在页面顶部，并且是该页面中所有的广告id放进去，所以在最后一次调用widget后收集全id再渲染js
     * 渲染的js在home/index/index模板里
     * @return [type] [description]
     */
    private function renderAdJs()
    {
        foreach($this->ads as $ad)
        {
            if(!$ad->isFlash) self::$ids[] = '"'.$ad->bd_id.'"';

        }
        self::$js = 'BAIDU_CLB_preloadSlots('.implode(',',self::$ids).');';
    }

    /**
     * 生成flashHtml代码
     * @param  string $swfUrl swf地址
     * @return string
     */
    public function flash($swfUrl,$height=60)
    {
        $html = '<EMBED src="'.$swfUrl.'" quality=high bgcolor=#FFFFFF WIDTH=100% HEIGHT="'.$height.'" NAME="myMovieName" ALIGN="" TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer"></EMBED>';
        return $html;
    }

    public function run()
    {
        if ($this->render_type === 2) {
            $this->render('urm-ad', [
                'ads' => $this->ads
            ]);
        } else {
            if(empty($this->ads))
                return;
            $ads = array();
            foreach($this->ads as $v)
            {
                $ads[$v->size][] = $v;
            }
            Yii::app()->clientScript->registerScriptFile('http://cbjs.baidu.com/js/m.js');
            $this->renderAdJs();
            $this->render('advertisement', array(
                'ads' => $ads,
            ));
        }
    }
}
