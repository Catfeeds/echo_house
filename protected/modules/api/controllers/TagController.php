<?php
class TagController extends ApiController{
	public function actionIndex($cate='')
	{
		if($cate == 'wzlm') {
			$this->frame['data'] = CacheExt::gas('tag_wzlm','AreaExt',0,'顶部标签缓存',function (){
		            return Yii::app()->db->createCommand("select id,name from tag where status=1 and cate='wzlm' order by sort asc")->queryAll();
		        });
		}
		elseif($cate) {
			$this->frame['data'] = Yii::app()->db->createCommand("select id,name from tag where status=1 and cate='$cate' order by sort asc")->queryAll();
		}
	}
	public function actionArea()
	{
		$this->frame['data'] = CacheExt::gas('wap_all_area','AreaExt',0,'wap区域缓存',function (){
		            $areas = AreaExt::model()->normal()->findAll(['condition'=>'parent=0','order'=>'sort asc']);
		            $areas[0]['childArea'] = $areas[0]->childArea;
		            return $this->addChild($areas);
		        });
	}
	public function actionPublishTags()
	{
		$areas = CacheExt::gas('wap_all_area','AreaExt',0,'wap区域缓存',function (){
		            $areas = AreaExt::model()->normal()->findAll(['condition'=>'parent=0','order'=>'sort asc']);
		            $areas[0]['childArea'] = $areas[0]->childArea;
		            return $this->addChild($areas);
		            });
		$tags = CacheExt::gas('wap_publish_tags','AreaExt',0,'wap发布房源标签',function (){
					$wylx['list'] = Yii::app()->db->createCommand("select id,name from tag where status=1 and cate='wylx' order by sort asc")->queryAll();
					$wylx['name'] = 'wylx';

					$zxzt['list'] = Yii::app()->db->createCommand("select id,name from tag where status=1 and cate='zxzt' order by sort asc")->queryAll();
					$zxzt['name'] = 'zxzt';
					$sfprice['list'] = Yii::app()->db->createCommand("select id,name from tag where status=1 and cate='sfprice' order by sort asc")->queryAll();
					$sfprice['name'] = 'sfprice';
					return [$wylx,$zxzt,$sfprice];
		});
		$tags[] = ['name'=>'area','list'=>$areas];
		$tags[] = ['name'=>'mode','list'=>Yii::app()->params['dllx']];
		$this->frame['data'] = $tags;

	}
	public function actionList($cate='')
	{
		switch ($cate) {
			case 'plotFilter':
				$area = [];
				$area['name'] = '区域';
				$area['filed'] = 'area';
				$areas = CacheExt::gas('wap_all_area','AreaExt',0,'wap区域缓存',function (){
		            $areas = AreaExt::model()->normal()->findAll(['condition'=>'parent=0','order'=>'sort asc']);
		            $areas[0]['childArea'] = $areas[0]->childArea;
		            return $this->addChild($areas);
		        });
            	$area['list'] = $areas;
            	$ots = CacheExt::gas('wap_all_filters','AreaExt',0,'wap筛选标签缓存',function (){
	            	$aveprice = [];
					$aveprice['name'] = '均价';
					$aveprice['filed'] = 'aveprice';
					$aveprice['list'] = Yii::app()->db->createCommand("select id,name from tag where status=1 and cate='price' order by sort asc")->queryAll();

					$sfprice = [];
					$sfprice['name'] = '首付';
					$sfprice['filed'] = 'sfprice';
					$sfprice['list'] = Yii::app()->db->createCommand("select id,name from tag where status=1 and cate='sfprice' order by sort asc")->queryAll();

					$sort = [];
					$sort['name'] = '排序';
					$sort['filed'] = 'sort';
					$sort['list'] = [
						['id'=>1,'name'=>'均价从高到低'],
						['id'=>2,'name'=>'均价从低到高'],
						['id'=>3,'name'=>'位置从近到远'],
					];

					$wylx = [];
					$wylx['name'] = '物业类型';
					$wylx['filed'] = 'wylx';
					$wylx['list'] = Yii::app()->db->createCommand("select id,name from tag where status=1 and cate='wylx' order by sort asc")->queryAll();

					$zxzt = [];
					$zxzt['name'] = '装修状态';
					$zxzt['filed'] = 'zxzt';
					$zxzt['list'] = Yii::app()->db->createCommand("select id,name from tag where status=1 and cate='zxzt' order by sort asc")->queryAll();

					$more = [];
					$more['name'] = '更多';
					$more['list'] = [$sort,$wylx,$zxzt];
					return [$aveprice,$sfprice,$more];
				});
				// var_dump($ots);exit;
				array_unshift($ots,$area);
            	$this->frame['data'] = $ots;
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