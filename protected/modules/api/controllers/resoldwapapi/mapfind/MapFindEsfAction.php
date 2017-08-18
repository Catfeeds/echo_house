<?php 
/**
 * PC版地图接口
 * @author steven allen <[<email address>]>
 * @date(2016.11.4)
 */
class MapFindEsfAction extends CAction
{
	public function run()
	{
		$allTags = ['esfzzprice','resoldhuxing','esfzzsize','esfzzts','esfzfzztype','resoldage','resoldface','resoldfloor','esfzzpt','resoldzx','zfzzprice','zfzzts','zfzzpt','kw','type','page','hid','sort','source'];
		foreach ($allTags as $key => $value) {
			$$value = Yii::app()->request->getQuery($value,'');
		}
		if(!$type || !$hid)
			return $this->controller->returnError('参数错误');
		$esfdirect = ['resoldface'=>'towards','resoldzx'=>'decoration','source'=>'source',];
		$esfbetween = ['esfzzprice'=>'price','resoldhuxing'=>'bedroom','esfzzsize'=>'size','resoldage'=>'age','resoldfloor'=>'floor',];
		$esftags = ['esfzzts','esfzfzztype','esfzzpt'];

		$zfdirect = ['resoldface'=>'towards','resoldzx'=>'decoration','source'=>'source','esfzfzztype'=>'esfzfzztype'];
		$zfbetween = ['zfzzprice'=>'price','resoldhuxing'=>'bedroom','esfzzsize'=>'size','resoldage'=>'age','resoldfloor'=>'floor',];
		$zftags = ['zfzzts','zfzzpt'];

		$sortArr = [
			1=>['sale_time'=>false],
			2=>['price'=>false],
			3=>['price'=>true]
		];

		$xs = $type==1?Yii::app()->search->house_esf:Yii::app()->search->house_zf;
		$xs->setQuery($kw);
		$xs->setFacets(array('status'), true);//分面统计
		$xs->addRange('deleted',0,0);
		$xs->addRange('status',1,1);
		$xs->addRange('category',1,1);
		
        $xs->addRange('expire_time',time(),null);
		$xs->addRange('sale_status',1,1);
		if($type==1) {
			foreach ($esfdirect as $key => $value) {
				if($$key)
				{
					$xs->addRange($value,$$key,$$key);
				}
				
			}
			foreach ($esfbetween as $key => $value) {
				if($$key)
				{
					$tag = TagExt::model()->findByPk($$key);
					$xs->addRange($value,$tag->min,$tag->max);
				}
			}
		} elseif($type==2) {
			foreach ($zfdirect as $key => $value) {
				if($$key)
				{
					$xs->addRange($value,$$key,$$key);
				}
				
			}
			foreach ($zfbetween as $key => $value) {
				if($$key)
				{
					$tag = TagExt::model()->findByPk($$key);
					$xs->addRange($value,$tag->min,$tag->max);
				}
			}
		}
		$source && $xs->addRange('source',$source,$source);
		$xs->addRange('hid',$hid,$hid);
		$resoldzx && $xs->addRange('decoration',$resoldzx,$resoldzx);
		$resoldface && $xs->addRange('towards',$resoldface,$resoldface);
		if($resoldhuxing)
		{
			$bedroomtag = TagExt::model()->normal()->findByPk($resoldhuxing);
			$bedroomtag && $xs->addRange('bedroom',$bedroomtag->min,$bedroomtag->max);
		}
		if($type==1)
		{
			$filters = [$esfzzts,$esfzzpt,$esfzfzztype];
			foreach ($filters as $key => $value) {
				$value && $xs->addQueryString('tag:'.$value,XS_CMD_QUERY_OP_AND);
			}
		}
		else
		{
			$filters = ['zfzzts'=>$zfzzts,'zfzzpt'=>$zfzzpt];
			foreach ($filters as $key => $value) {
				$value && $xs->addQueryString($key.':'.$value,XS_CMD_QUERY_OP_AND);
			}
		}
		// 排序,规则：@self::sortArr>hurry>sort>refresh_time>sale_time>id
		$defaultSort = ['sort'=>false,'refresh_time'=>false,'sale_time'=>false,'id'=>false];

        if($sort)
        	$xs->setMultiSort(array_merge($sortArr[$sort],$defaultSort));
        else
        	$xs->setMultiSort($defaultSort);

		// 增加排序条件需要放在Count统计完数量之后
        $count = 0;
        $xs->search();//count放在search之后才能应用排序条件
        $count = array_sum($xs->getFacets('status'));//通过获取分面搜索值能得到精准数量

        // 分页
        $pager = new CPagination($count);
        $pager->pageSize = 20;

        $xs->setLimit(20, 20*$pager->currentPage);
        $docs = $xs->search();
        $infos = $infoArr = [];
        if($docs)
	        foreach ($docs as $key => $value) {
	        	$infos[] = $type==1?ResoldEsfExt::model()->findByPk($value->id):ResoldZfExt::model()->findByPk($value->id);
	        }
		if($infos)
		{
			foreach ($infos as $key => $info) {
				if($info) 
				{
					$floorword = '暂无';
					if($info->total_floor && $floorcate = $info->floor/$info->total_floor)
						if($floorcate<0)
							$floorword = '地下室';
						else
							$floorword = $floorcate<1/3?'低层':($floorcate>2/3?'高层':'中层');
					$infoArr[] = ['id'=>$info->id,'price'=>$info->price,'bedroom'=>$info->bedroom,'livingroom'=>$info->livingroom,'bathroom'=>$info->bathroom,'size'=>$info->size,'ave_price'=>isset($info->ave_price)?$info->ave_price:'','image'=>ImageTools::fixImage($info->image,80,60),'title'=>$info->title,'floor'=>$info->floor,'total_floor'=>$info->total_floor,'floorcate'=>$floorword,'url'=>$this->controller->createUrl('/resoldhome'.($type==1?'/esf/info':'/zf/info'),['id'=>$info->id])];
				}
			}
		}
		// $plotResold = PlotResoldDailyExt::getLastInfoByHid($hid);
		$lastEsfPrice = 0;
		if($type==1)
			$lastEsfPrice = Yii::app()->db->createCommand('select price from resold_plot_price where hid='.$hid.' and price>0 order by new_time desc limit 1')->queryScalar();
		$esf_num = $zf_num = $esf_price = $zf_price = 0;
		
		$esf_num = $count;
		$zf_num = $count;
		$esf_price = $lastEsfPrice;
		$zf_price = 0;
		
		$plot = PlotExt::model()->findByPk($hid);

		$page_count = $pager->pageCount;
		$this->controller->frame['data'] = ['data'=>$infoArr,'esf_num'=>$esf_num,'zf_num'=>$zf_num,'page_count'=>$page_count,'esf_price'=>$esf_price,'zf_price'=>$zf_price,'plot_name'=>$plot->title,'area'=>$plot->areaInfo?$plot->areaInfo->name:'','street'=>$plot->streetInfo?$plot->streetInfo->name:''];

	}

	/**
	 * [getInfoByHid 根据hid获取房源信息]
	 * @param  [type] $hid  [description]
	 * @param  [type] $sort [0默认 1最新 2价格升序]
	 * @return [type]       [description]
	 */
	public function getInfoByHid($hid,$sort,$type,$page)
	{
		$plot = PlotExt::model()->findByPk($hid);
		$criteria = new CDbCriteria;
		$defaultSort = 'recommend desc,refresh_time desc,sale_time desc,id desc';
		$sort && $defaultSort = $sort==1?'refresh_time desc,recommend desc,sale_time desc,id desc':('price asc,'.$defaultSort);
		$criteria->order = $defaultSort;
		$criteria->addCondition('category=1');
		$criteria->addCondition('hid=:hid');
		$criteria->params[':hid'] = $hid;
		$esfs = ResoldEsfExt::model()->undeleted()->saling()->findAll($criteria);
		$zfs = ResoldZfExt::model()->undeleted()->saling()->findAll($criteria);
		$infos = $type==1 ? ResoldEsfExt::model()->undeleted()->saling()->getList($criteria,10) : ResoldZfExt::model()->undeleted()->saling()->getList($criteria,10);
		$plotResold = PlotResoldDailyExt::getLastInfoByHid($hid,1);
		$data = [];
		$data['info_count'] = $data['zf_count'] = $data['zf_count'] = $data['ave_price'] = 0;
		$plotResold && $type==1 && $data['ave_price'] = $plotResold->esf_price;
		$plotResold && $type==2 && $data['ave_price'] = $plotResold->zf_price;
		$plotResold && $type==1 && ($data['info_count'] = $data['esf_count'] = $plotResold->esf_num) && $data['zf_count'] = $plotResold->zf_num;
		$plotResold && $type==2 && ($data['info_count'] = $data['zf_count'] = $plotResold->zf_num) && $data['esf_count'] = $plotResold->esf_num;

		if($infos->data)
		{
			$infoArr = [];
			foreach ($infos->data as $key => $info) {
				$infoArr[] = ['id'=>$info->id,'price'=>$info->price,'bedroom'=>$info->bedroom,'livingroom'=>$info->livingroom,'bathroom'=>$info->bathroom,'size'=>$info->size,'ave_price'=>isset($info->ave_price)?$info->ave_price:'','image'=>ImageTools::fixImage($info->image,80,60),'title'=>$info->title];
			}
			$data['info'] = $infoArr;
		}
		$data['area'] = $plot->areaInfo?$plot->areaInfo->name:'';
		$data['street'] = $plot->streetInfo?$plot->streetInfo->name:'';
		return $this->controller->frame['data'] = $data;
	}
}