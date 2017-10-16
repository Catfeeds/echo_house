<?php
class WapAdWidget extends CWidget
{
	private $urmHost;
	public $render_type;
	public $position;
	public $ads;

	public function init()
	{
		$this->urmHost = Yii::app()->params['urmHost'];
        $this->render_type = (int)SM::advertConfig()->type();
        if ((int)$this->render_type === 2) {
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
        }
	}

	public function run()
	{
        if ((int)$this->render_type === 2) {
        	if (!$this->ads) {
        		return; 
        	}
        	$sizeName = array_keys($this->ads);
        	is_array($sizeName) and $sizeName = current($sizeName);
			
			$this->render('wap-ad', [
				'ads' => $this->ads,
				'sizeName' => $sizeName
			]);
		}
	}
}