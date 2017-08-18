<?php
/*
* 出租列表页
* @author liyu
* @created 2016年10月13日16:41:43
*/
class ListAction extends CAction{
    public function run(){
        $pricetag = Yii::app()->request->getQuery('pricetag',0);
        //户型的tagid
        $hxtag = Yii::app()->request->getQuery('resoldhuxing',0);
        $zfmode = Yii::app()->request->getQuery('zfmode',0);
        $resoldface = Yii::app()->request->getQuery('resoldface',0);
        $resoldzx = Yii::app()->request->getQuery('resoldzx',0);
        //指定价格区间
        $miniprice = Yii::app()->request->getQuery('miniprice',0);
        $maxprice = Yii::app()->request->getQuery('maxprice',0);
        $page = Yii::app()->request->getQuery('page',1);
        $kw = Yii::app()->request->getQuery('kw','');//关键词
        $recom = Yii::app()->request->getQuery('recom',0);//推荐位
        $limit = Yii::app()->request->getQuery('limit',20);//每页显示记录数
        $category = Yii::app()->request->getQuery('category',1);//默认是住宅
        $fid = Yii::app()->request->getQuery('id',0);//过滤的id
        $pathInfo = Yii::app()->request->getPathInfo();
        $sort = Yii::app()->request->getQuery('sort',0);
        $hurry = Yii::app()->request->getQuery('hurry',0);
        $hid = Yii::app()->request->getQuery('hid',0);
        $source = Yii::app()->request->getQuery('source',0);
        $xzltype = Yii::app()->request->getQuery('xzltype',0);
        $size = Yii::app()->request->getQuery('size',0);
        $infoid = Yii::app()->request->getQuery('infoid',0);
        $area = [];
        $hxvalue = 0;
        $area = Yii::app()->request->getQuery('area',0);
        $street = Yii::app()->request->getQuery('street',0);

        $areas = AreaExt::getAllarea();
        //$uid = isset(Yii::app()->user->uid)?Yii::app()->user->uid:4;
        $ids = $zf = [];
        $filterHasHid = 0;
        $sortArr = ['1'=>['price'=>true],'2'=>['price'=>false],'3'=>['ave_price'=>true],'4'=>['ave_price'=>false],'5'=>['size'=>true],'6'=>['size'=>false]];
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
        //传参 hid 其他出租楼盘
        foreach(['hurry','hid'] as $v){
            if($$v = Yii::app()->request->getQuery("$v",0)){
                $params[$v] = $$v;
                $xs->addRange($v, $$v, $$v);
            }
        }
        if($source==4)
            $xs->addRange('hurry',1,null);
        elseif($source)
            $xs->addRange('source', $source, $source);
        $area && $xs->addRange('area',$area,$area);
        $xs->addRange('expire_time', time(), null);
        $street && $xs->addRange('street',$street,$street);
        // if($area_tmp){
        //     if(in_array($area_tmp,array_keys($areas))){
        //         $area = $area_tmp;
        //         $params['area'] = $area_tmp;
        //         $xs->addRange('area',$area,$area);
        //     }else{
        //         $areas = $area_tmp;
        //         $xs->addRange('street',$areas,$areas);
        //         $params['area'] = $area_tmp;
        //     }
        // }
        //户型的值
        if($hxtag){
            $hxtagmodel = TagExt::model()->findByPk($hxtag);
            $xs->addRange('bedroom',$hxtagmodel->min,$hxtagmodel->max?$hxtagmodel->max:null);
        }
        if($size){
            $sizetag = TagExt::model()->findByPk($size);
            $sizetag && $xs->addRange('size',$sizetag->min,$sizetag->max?$sizetag->max:null);
        }
        $xs->addRange('status', 1, 1);
        $xs->addRange('sale_status', 1, 1);
        $xs->addRange('deleted', 0, 0);
        $xs->addRange('category',$category,$category);
        $ext = [];//更多
        /*
        * 写字楼出租 esfzfsptype
        */
        foreach(['zfzzts','esfzfsptype','zfxzllevel'] as $v){
            if($value = Yii::app()->request->getQuery("$v",0)){
                $ext[$v] = $value;
                $q = $v.':'.$value;
                $xs->addQueryString($q, XS_CMD_QUERY_OP_AND);
            }
        }
        $xzltype and $xs->addRange('esfzfxzltype', $xzltype, $xzltype);
        $zfmode and $xs->addRange('rent_type', $zfmode, $zfmode);
        $resoldzx and $xs->addRange('decoration', $resoldzx, $resoldzx);
        $resoldface and $xs->addRange('towards', $resoldface, $resoldface);
        $miniprice and $xs->addRange('price', $miniprice, null);
    	$maxprice and $xs->addRange('price', null, $maxprice);
        //$uid and $xs->addRange('uid', $uid, $uid);
        //价格区间
        if($pricetag){
            $priceTag = TagExt::model()->findByPk($pricetag);
            $xs->addRange('price',$priceTag->min,$priceTag->max?$priceTag->max:null);
        }

        // 排序,规则：@self::sortArr>hurry>sort>refresh_time>sale_time>id
        $defaultSort = $hurry?['hurry'=>false,'sort'=>false,'refresh_time'=>false,'sale_time'=>false,'id'=>false]:['sort'=>false,'refresh_time'=>false,'sale_time'=>false,'id'=>false];
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
     /*   foreach ($ids as $key => $value) {
            $zfs[] = ResoldZfExt::model()->findByPk($value);
        }*/
        $criteria = new CDbCriteria();
        $criteriaSort = $hurry?'hurry desc,refresh_time desc,sale_time desc':'refresh_time desc,sale_time desc';

        if($sort)
            $criteriaSort = array_keys($sortArr[$sort])[0].(array_values($sortArr[$sort])[0]?' asc,':' desc,').$criteriaSort;
        $criteria->order = $criteriaSort;
        $criteria->addInCondition('id', $ids);
        $zfs = ResoldZfExt::model()->findAll($criteria);
        if($zfs){
            foreach ($zfs as $key => $value) {
                $tss = $value->getZfTag();
                // var_dump($tss);
                $ts = [];
                if($tss)
                    foreach ($tss as $k => $v) {
                        if(substr($k, -2 ,2) == 'ts') {
                            $ts = $v;
                            break;
                        }
                    }
                $value['image'] = ImageTools::fixImage($value['image'],88,66);
                $farea = $value->areaInfo?$value->areaInfo->name:'暂无';
                $street = $value->streetInfo?$value->streetInfo->name:'暂无';
				$zf[] = ['id'=>$value->id,'hid'=>$value->hid,'title'=>$value->title,'image'=>$value['image'],'area'=>$farea,'street'=>$street,'source'=>$value->source,'bedroom'=>$value->bedroom,'livingroom'=>$value->livingroom,'bathroom'=>$value->bathroom,'price'=>$value->price,'size'=>$value->size,'created'=>date('Y.m.d',$value->created),'sale_time'=>date('Y.m.d',$value->sale_time),'hurry'=>date('Y.m.d',$value->hurry),'ts'=>$ts];
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
