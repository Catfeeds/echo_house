<?php
class TagController extends ApiController{
	public function actionIndex($cate='')
	{
		if($cate) {
			$this->frame['data'] = Yii::app()->db->createCommand("select id,name from tag where status=1 and cate='$cate'")->queryAll();
		}
	}
	public function actionList($cate='')
	{
		switch ($cate) {
			case 'plotFilter':
				$area = [];
				$area['name'] = '区域';
				$area['filed'] = 'area';
				$areas = AreaExt::model()->normal()->findAll(['condition'=>'parent=0','order'=>'sort asc']);
            	$areas[0]['childArea'] = $areas[0]->childArea;
            	$area['list'] = $this->addChild($areas);

            	$aveprice = [];
				$aveprice['name'] = '均价';
				$aveprice['filed'] = 'aveprice';
				$aveprice['list'] = Yii::app()->db->createCommand("select id,name from tag where status=1 and cate='price'")->queryAll();

				$sfprice = [];
				$sfprice['name'] = '首付';
				$sfprice['filed'] = 'sfprice';
				$sfprice['list'] = Yii::app()->db->createCommand("select id,name from tag where status=1 and cate='sfprice'")->queryAll();

				$sort = [];
				$sort['name'] = '排序';
				$sort['filed'] = 'sort';
				$sort['list'] = [
					['id'=>1,'name'=>'均价从高到低'],
					['id'=>2,'name'=>'均价从低到高'],
				];

				$wylx = [];
				$wylx['name'] = '物业类型';
				$wylx['filed'] = 'wylx';
				$wylx['list'] = Yii::app()->db->createCommand("select id,name from tag where status=1 and cate='wylx'")->queryAll();

				$more = [];
				$more['name'] = '更多';
				$more['list'] = [$sort,$wylx];
				
            	$this->frame['data'] = [$area,$aveprice,$sfprice,$more];
				break;
			
			default:
				# code...
				break;
		}
	}

	public function addChild($areas)
    {
        $count = count($areas);
        for ($i = 0;$i<$count;$i++){
            if($child = $areas[$i]->childArea){
                $child = $this->addChild($child);
            }
            //将对象转换成数组
            $areas[$i] = $areas[$i]->attributes;
            if($child){
                $areas[$i]['childAreas']=$child;
            }
        }
        return $areas;
    }
}