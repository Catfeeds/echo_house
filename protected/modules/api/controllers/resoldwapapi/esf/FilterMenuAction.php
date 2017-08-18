<?php
/**
 * 二手房筛选菜单
 * @author steven.allen
 * @date 2016.09.18
 */
class FilterMenuAction extends CAction
{
	
	public function run()
	{
		//区域和学区
		$allArea = AreaExt::getAllarea();
		$allStreet = AreaExt::getAllStreet();
		$allSchool = $priceTag = $sizeTag = [];
		// var_dump($allStreet);exit;
		foreach ($allArea as $key => $v) {
			// var_dump($key);exit;
			$schools = SchoolExt::model()->normal()->findAll(['condition'=>'area=:area','params'=>[':area'=>$key]]);
			if($schools)
			{
				$school = [];
				foreach ($schools as $v1) {
					$allSchool[$key][$v1->id] = $v1->name;
				}
			}
			
		}

		// 价格标签
		$pricetags = TagExt::model()->normal()->getTagByCate('esfzzprice')->findAll();
		if($pricetags)
		{
			foreach ($pricetags as $v1) {
				$priceTag[$v1['id']] = $v1['name'];
			}
		}

		// 户型
		$houseType = ['1'=>'一居','2'=>'二居','3'=>'三居','4'=>'四居','5'=>'五居','6'=>'五居以上'];

		//来源
		$sourceArr = ['1'=>'中介','2'=>'个人','3'=>'加急'];

		//住宅面积
		$sizetags = TagExt::model()->normal()->getTagByCate('esfzzsize')->findAll();
		if($sizetags)
		{
			foreach ($sizetags as $v1) {
				$sizeTag[$v1['id']] = $v1['name'];
			}
		}

		// 住宅特色
		$tstags = TagExt::model()->normal()->getTagByCate('esfzzts')->findAll();
		if($tstags)
		{
			foreach ($tstags as $v1) {
				$tsTag[$v1['id']] = $v1['name'];
			}
		}

		// 排序
		$sortArr = ['1'=>'按总价从低到高','2'=>'按总价从高到低','3'=>'按单价从低到高','4'=>'按单价从高到低','5'=>'按面积从小到大','6'=>'按面积从大到小'];

		// var_dump($tstags);exit;
		$this->controller->frame['data'] = [
		'area'=>$allArea,
		'street'=>$allStreet,
		'school'=>$allSchool,
		'pricetag'=>$priceTag,
		'bedroom'=>$houseType,
		'source'=>$sourceArr,
		'size'=>$sizeTag,
		'ts'=>$tsTag,
		'sort'=>$sortArr,
		];

	}
}