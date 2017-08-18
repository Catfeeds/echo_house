<?php

/**
 * User: fanqi
 * Date: 2016/9/26
 * Time: 9:04
 */
class EsfListAction extends CAction
{
    public function run()
    {
        $recom = Yii::app()->request->getQuery('recom',0);
        $limit = Yii::app()->request->getQuery('limit',20);
        $kw = Yii::app()->request->getQuery('kw','');
        $pricetag = Yii::app()->request->getQuery('pricetag',0);
        $sort = Yii::app()->request->getQuery('sort',0);
        $miniprice = Yii::app()->request->getQuery('miniprice',0);
        $minsize = Yii::app()->request->getQuery('minsize',0);
        $maxsize = Yii::app()->request->getQuery('maxsize',0);
        $area = Yii::app()->request->getQuery('area',0);
        $street = Yii::app()->request->getQuery('street',0);
        $source = Yii::app()->request->getQuery('source',0);
        $bedroom = Yii::app()->request->getQuery('bedroom',0);
        $ts = Yii::app()->request->getQuery('ts',0);
        $maxprice = Yii::app()->request->getQuery('maxprice',0);
        $size = Yii::app()->request->getQuery('size',0);
        $school = Yii::app()->request->getQuery('school',0);
        $sale = Yii::app()->request->getQuery('sale',1);
        $type = Yii::app()->request->getQuery('type',1);
        $page = Yii::app()->request->getQuery('page',1);
        $hurry = Yii::app()->request->getQuery('hurry',0);
        $hid = Yii::app()->request->getQuery('hid',0);
        $uid = $this->controller->staff->uid;
        $esf = [];
        //有没有筛选条件
        $needFilter = 1;
        $pathInfo = Yii::app()->request->getPathInfo();

        $typeName = $type==1?'zz':($type==2?'sp':'xzl');
        $sortArr = ['1'=>['price'=>true],'2'=>['price'=>false],'3'=>['ave_price'=>true],'4'=>['ave_price'=>false],'5'=>['size'=>true],'6'=>['size'=>false]];

        // 如果recom为1 则只从推荐表中找
        // 根据请求的地址确定推荐位位置
        if($recom)
        {
            $needFilter = 0;
            $recomesfs = [];
            $recomPlace = ['resoldWapApi/esf/list'=>'wapsyjpesf'];
            $recoms = ResoldRecomExt::model()->getRecom($recomPlace[$pathInfo])->findAll(['limit'=>$limit,'order'=>'t.sort desc,t.created desc']);
            if($recoms)
                foreach ($recoms as $key => $value) {
                    $recomesfs[] = ResoldEsfExt::model()->findByPk($value->fid);
                }
            if($recomesfs)
                foreach ($recomesfs as $key => $value) {
                    $farea = AreaExt::model()->findByPk($value->area);
                    $esf[] = ['id'=>$value->id,'title'=>$value->title,'image'=>$value->image,'bedroom'=>$value->bedroom,'livingroom'=>$value->livingroom,'bathroom'=>$value->bathroom,'area'=>$farea->name,'price'=>$value->price];
                }
        }
        else
        {
            // 如果recom不为1且无排序 则先将所有房源找出 后把推荐的房源排在前面
            // 若有排序 按照排序规则
            // 若有学校 从学校模型中找楼盘
            $ids = [];
            $xs = Yii::app()->search->house_esf;
            $filterHasHid = 0;
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
            $xs->setFacets(array('status'), true);//分面统计
            
            // 特色标签筛选
            if($ts)
                $xs->setQuery('tag:'.$ts);
            
            $xs->addRange('status', 1, 1);
            $xs->addRange('deleted', 0, 0);
            // $xs->addRange('category', $type, $type);
            if($sale)
                $xs->addRange('sale_status', $sale, $sale);

            // 价格区间
            if($pricetag)
            {
                $priceTag = TagExt::model()->findByPk($pricetag);
                $xs->addRange('price', $priceTag->min, $priceTag->max);
            }
            $miniprice and $xs->addRange('price', $miniprice, null);
            $maxprice and $xs->addRange('price', null, $maxprice);
            $minsize and $xs->addRange('size', $minsize, null);
            $maxsize and $xs->addRange('size', null, $maxsize);
            // 各种判断
            $area and $xs->addRange('area', $area, $area);
            $street and $xs->addRange('street', $street, $street);
            $source and $xs->addRange('source', $source, $source);
            $area and $xs->addRange('area', $area, $area);
            if($size)
            {
                $sizeTag = TagExt::model()->findByPk($size);
                $xs->addRange('size', $sizeTag->min, $sizeTag->max);
            }
            $hurry and $xs->addRange('hurry', 0, null);
            $uid and $xs->addRange('uid', $uid, $uid);
            $bedroom && ( $bedroom>5 ? $xs->addRange('bedroom', $bedroom, null) : $xs->addRange('bedroom', $bedroom, $bedroom) );
            // 学区筛选
            if($school)
            {
                $rel = SchoolPlotRelExt::model()->findAll(['condition'=>'sid=:sid','params'=>[':sid'=>$school]]);
                $pid = '';
                if($rel)
                    foreach ($rel as $key => $value) {
                        $pid .= 'hid:'.$value->plot->id.' ';
                    }
                $xs->setFuzzy()->setQuery($pid);// 迅搜模糊搜索
            }

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
            $hidInfo = $noHidInfo = $esfs = [];
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
                $esfs[] = ResoldEsfExt::model()->findByPk($value);
            }
            if($esfs)
                foreach ($esfs as $key => $value) {
                    if(isset($value['image']) && $value['image'])
                        $value['image'] = ImageTools::fixImage($value['image'],88,66);
                    else
                        $value['image'] = ImageTools::fixImage(SM::resoldImageConfig()->resoldNoPic(),88,66);
                    $farea = $value->areaInfo?$value->areaInfo->name:'';
                    $fstreet = $value->streetInfo?$value->streetInfo->name:'';
                    $time = $value->updated ? $value->updated : $value->created;
                    $esf[] = ['id'=>$value->id,'title'=>$value->title,'image'=>$value['image'],'size'=>$value->size,'category'=>$value->category ,'bedroom'=>$value->bedroom,'livingroom'=>$value->livingroom,'bathroom'=>$value->bathroom,'area'=>$farea,'street'=>$fstreet,'price'=>$value->price,'hits'=>$value->hits,'created'=>date('Y.m.d H:i',$time),'sale_time'=>date('Y.m.d',$value->sale_time),'is_hurry'=>((time()-$value->hurry)<SM::resoldConfig()->resoldHurryTime->value*3600)?1:0,'is_expire'=>(time()-$value->expire_time)>0?1:0,'is_refresh'=>((time()-$value->refresh_time)<SM::resoldConfig()->resoldRefreshInterval->value*60)?1:0,'appoint_num'=>ResoldAppointExt::model()->count(['condition'=>'fid=:fid and type=1 and status=0  and uid=:uid','params'=>[':fid'=>$value->id,':uid'=>$this->controller->staff->uid]])];
                }
        }
        
        $trans = ['esf'=>$esf,'totalNum'=>$count,'page'=>$page,'page_count'=>$pager->pageCount];
        if($needFilter)
            $trans = array_merge($trans,['filters'=>['chosen_pricetag'=>$pricetag,'chosen_sort'=>$sort,'chosen_miniprice'=>$miniprice,'chosen_area'=>$area,'chosen_street'=>$street,'chosen_source'=>$source,'chosen_bedroom'=>$bedroom,'chosen_ts'=>$ts,'chosen_maxprice'=>$maxprice,'chosen_size'=>$size,'chosen_school'=>$school]]);

        $this->getController()->frame['data'] = $trans;
    }
}