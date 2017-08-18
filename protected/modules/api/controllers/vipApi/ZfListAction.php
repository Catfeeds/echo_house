<?php
/**
 * 租房列表接口
 * @author steven.allen
 * @date 2016.10.21
 */
class ZfListAction extends CAction
{
	public function run(){
        $pricetag = Yii::app()->request->getQuery('pricetag',0);
        //户型的tagid
        $hxtag = Yii::app()->request->getQuery('resoldhuxing',0);
        //指定价格区间
        $miniprice = Yii::app()->request->getQuery('miniprice',0);
        $maxprice = Yii::app()->request->getQuery('maxprice',0);
        $page = Yii::app()->request->getQuery('page',1);
        $sale = Yii::app()->request->getQuery('sale',1);
        $kw = Yii::app()->request->getQuery('kw','');//关键词
        $recom = Yii::app()->request->getQuery('recom',0);//推荐位
        $limit = Yii::app()->request->getQuery('limit',20);//每页显示记录数
        // $category = Yii::app()->request->getQuery('category',1);//默认是住宅
        $pathInfo = Yii::app()->request->getPathInfo();
        $sort = Yii::app()->request->getQuery('sort',0);
        $hurry = Yii::app()->request->getQuery('hurry',0);
        $hid = Yii::app()->request->getQuery('hid',0);
        $area = [];
        $hxvalue = 0;
        $area_tmp = Yii::app()->request->getQuery('area',0);
        $areas = AreaExt::getAllarea();
        $uid = $this->controller->staff->uid;
        $ids = $zf = [];
        $filterHasHid = 0;
        $sortArr = ['1'=>['price'=>true],'2'=>['price'=>false],'3'=>['ave_price'=>true],'4'=>['ave_price'=>false],'5'=>['size'=>true],'6'=>['size'=>false]];
        $sourceArr = Yii::app()->params['source'];

        //迅搜
        $xs = Yii::app()->search->house_zf;
        if($kw && $hid) {
                $xs->addQueryString('title:'.$kw, XS_CMD_QUERY_OP_OR);
                $xs->addQueryString('hid:'.$hid, XS_CMD_QUERY_OP_OR);
                $filterHasHid = 1;
            }else {
               if($kw)
                {
                    $xs->addQueryString('title:'.$kw, XS_CMD_QUERY_OP_OR);
                    $xs->addQueryString('plot_name:'.$kw, XS_CMD_QUERY_OP_OR);
                }
                else
                    $xs->setQuery($kw);
                $hid and $xs->addRange('hid', $hid, $hid);
            }
        $xs->setFacets(array('status'), true);
        $params = [];
        //传参
        foreach(['source','hurry'] as $v){
            if($value = Yii::app()->request->getQuery("$v",0)){
                $params[$v] = $value;
                $xs->addRange($v, $value, $value);
            }
        }
        if($area_tmp){
            if(in_array($area_tmp,array_keys($areas))){
                $area = $area_tmp;
                $params['area'] = $area_tmp;
                $xs->addRange('area',$area,$area);
            }else{
                $areas = $area_tmp;
                $xs->addRange('street',$areas,$areas);
                $params['area'] = $area_tmp;
            }
        }
        //户型的值
        if($hxtag){
            $hxtagmodel = TagExt::model()->findByPk($hxtag);
            $hxvalue = $hxtagmodel->max;
            $params['resoldhuxing'] = $hxtag;
            $xs->addRange('bedroom',$hxvalue,$hxvalue);
        }
        $xs->addRange('status', 1, 1);
        $xs->addRange('sale_status', $sale, $sale);
        $xs->addRange('deleted', 0, 0);
        // $xs->addRange('category',$category,$category);
        $ext = [];//更多
        /*
        * 写字楼出租 esfzfsptype
        */
        foreach(['zfmode','zfzzts','resoldface','resoldzx','esfzfsptype'] as $v){
            if($value = Yii::app()->request->getQuery("$v",0)){
                $ext[$v] = $value;
                $q = $v.':'.$value;
                $xs->addQueryString($q, XS_CMD_QUERY_OP_AND);
            }
        }
        $miniprice and $xs->addRange('price', $miniprice, null);
    	$maxprice and $xs->addRange('price', null, $maxprice);
        $uid and $xs->addRange('uid', $uid, $uid);
        //价格区间
        if($pricetag){
            $priceTag = TagExt::model()->findByPk($pricetag);
            $xs->addRange('price',$priceTag->min,$priceTag->max);
        }
        // 排序,规则：@self::sortArr>hurry>sort>refresh_time>sale_time>id
        // 中介后台发布时间在先
        if($sale == 1) {
            $defaultSort = $hurry?['hurry'=>false,'refresh_time'=>true,'sale_time'=>false,'sort'=>false,'id'=>false]:['refresh_time'=>true,'sale_time'=>false,'sort'=>false,'id'=>false];
        } else {
            $defaultSort = ['updated'=>false,'created'=>false];
        }
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
        $pager->pageSize = $limit;

        $xs->setLimit($limit, $limit*$pager->currentPage);
        $docs = $xs->search();
        // 如果同时有hid和kw 将hid优先排序
        $hidInfo = $noHidInfo = $zfs = [];
        if($docs)
            foreach ($docs as $key => $value) {
                if($filterHasHid)
                {
                    if($value->hid == $hid)
                        $hidInfo[] = $value->id;
                    else
                        $noHidInfo[] = $value->id;
                }
                else
                    $ids[] = $value->id;
            }
        $filterHasHid && $ids = array_merge($hidInfo,$noHidInfo);
        foreach ($ids as $key => $value) {
            $zfs[] = ResoldZfExt::model()->findByPk($value);
        }
        if($zfs){
            foreach ($zfs as $key => $value) {
                if(isset($value['image']) && $value['image'])
                    $value['image'] = ImageTools::fixImage($value['image'],88,66);
                else
                    $value['image'] = ImageTools::fixImage(SM::resoldImageConfig()->resoldNoPic(),88,66);
                $farea = $value->areaInfo?$value->areaInfo->name:'';
                $fstreet = $value->streetInfo?$value->streetInfo->name:'';
                $time = $value->updated ? $value->updated : $value->created;
				$zf[] = ['id'=>$value->id,'title'=>$value->title,'image'=>$value['image'],'area'=>$farea,'street'=>$fstreet,'category'=>$value->category ,'source'=>$sourceArr[$value->source],'bedroom'=>$value->bedroom,'livingroom'=>$value->livingroom,'bathroom'=>$value->bathroom,'price'=>$value->price,'size'=>$value->size,'hits'=>$value->hits,'created'=>date('Y.m.d H:i',$time),'sale_time'=>date('Y.m.d',$value->sale_time),'is_hurry'=>((time()-$value->hurry)<SM::resoldConfig()->resoldHurryTime->value*3600)?1:0,'is_refresh'=>((time()-$value->refresh_time)<SM::resoldConfig()->resoldRefreshInterval->value*60)?1:0,'appoint_num'=>ResoldAppointExt::model()->count(['condition'=>'fid=:fid  and status=0 and type=2 and uid=:uid','params'=>[':fid'=>$value->id,':uid'=>$this->controller->staff->uid]]),'is_expire'=>(time()-$value->expire_time)>0?1:0,'refresh_time'=>date('Y.m.d H:i',$value->refresh_time)];
        	}
        }
        //传值
        $filters = [];
        if($params){
            foreach($params as $k=>$v)
                $filters['chosen_'.$k] = $v;
        }
        if($ext){
            foreach($ext as $k=>$v)
                $filters['chosen_'.$k] = $v;
        }

        if($kw){
            $filters['chosen_kw'] = $kw;
        }

        $trans = ['zf'=>$zf,'totalNum'=>$count,'page'=>$page,'page_count'=>$pager->pageCount];
        $trans = array_merge($trans,['filters'=>$filters]);
        $this->controller->frame['data'] = $trans;
    }
}