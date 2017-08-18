<?php
/**
 * 求购列表页
 * User: jt
 * Date: 2016/10/14 9:25
 */

class ListAction extends CAction{

    /**
     * @param string $page 分页
     * @param string $kw 关键字
     * @param string $area 区域
     * @param string $price 价格
     * @param string $hx 户型
     * @param string $size 面积
     */
    public function run($category=1 , $page='',$kw='',$area='',$street='',$price='',$hx='',$size='',$cate='',$minprice='',$maxprice='',$limit=20){
        $category = in_array($category,array('1','2','3')) ? $category : 1;
        $kw=$this->controller->cleanXss($kw);

        $xs = Yii::app()->search->house_qg;
        $xs->setQuery($kw);
        $xs->setFacets(array('status'), true);//分面统计
        $xs->addRange('deleted',0,0); //是否删除

        $xs->addQueryString('status:1',XS_CMD_QUERY_OP_AND);
        $xs->addQueryString('category:'.$category,XS_CMD_QUERY_OP_AND);

        $tags = TagExt::getAllByCate();
        $all_tag = array();
        foreach ($tags as $key=>$tag){
            foreach ($tag as $el){
                $all_tag[$key][$el['id']] = $el;
            }
        }
        $area && $xs->addRange('area',$area,$area);
        $street && $xs->addRange('street',$street,$street);
        //价格
        if($price || $minprice || $maxprice){
            $price_tag = array();
            if($price){
                if (isset($all_tag[ResoldQgExt::$price_cate[$category]][$price])) {
                    $price_tag = $all_tag[ResoldQgExt::$price_cate[$category]][$price];
                }
            }else{
                $price_tag = array('min'=>$minprice ,'max'=>$maxprice);
            }
            if($price_tag){
                if($price_tag['min'] > 0 && $price_tag['max'] > 0){
                    $price_tag['max'] >= $price_tag['min'] ? $xs->addRange('price',$price_tag['min'] , $price_tag['max']) : $xs->addRange('price',null,$price_tag['max']);
                }else{
                    $price_tag['min'] > 0 ? $xs->addRange('price',$price_tag['min'] , null) : $xs->addRange('price',null,$price_tag['max']);
                }
            }
        }
        //户型
        if($hx && $category == 1) {
            if (isset($all_tag['resoldhuxing'][$hx]) && $hxTag = $all_tag['resoldhuxing'][$hx]) {
                if ($hxTag['min'] > 0 && $hxTag['max'] > 0) {//max和min都设置了数值
                    if ($hxTag['max'] - $hxTag['min'] >= 0) {//如：5-7之间户型
                        $xs->addRange('bedroom',$hxTag['min'] , $hxTag['max']);
                    } else {
                        $xs->addRange('bedroom',null,$hxTag['max']);
                    }
                } else {//有一个0
                    if ($hxTag->min > 0) {
                        $xs->addRange('bedroom',$hxTag['min'] , null);
                    } else {
                        $xs->addRange('bedroom',null, $hxTag['max']);
                    }
                }
            }
        }
        //住宅面积
        if($size){
            $size_tag = array();
            if (isset($all_tag[ResoldQgExt::$size_cate[$category]][$size])) {
                $size_tag = $all_tag[ResoldQgExt::$size_cate[$category]][$size];
            }
            if($size_tag){
                if($size_tag['min'] > 0 && $size_tag['max'] > 0){
                    $size_tag['max'] - $size_tag['min'] >= 0 ? $xs->addRange('size',$size_tag['min'] , $size_tag['max']) : $xs->addRange('size',null,$size_tag['max']);
                }else{
                    $size_tag['min'] > 0 ? $xs->addRange('size',$size_tag['min'] , null) : $xs->addRange('size',null,$size_tag['max']);
                }
            }
        }
        //商铺类型
        if($cate && $category == 2){
            if(isset($all_tag['esfzfsptype'][$cate]) && $cate_tag = $all_tag['esfzfsptype'][$cate]){
                $xs->addQueryString('tag:'.$cate_tag['id'],XS_CMD_QUERY_OP_AND);
            }
        }
        $xs->setSort('updated');
        $count = 0;
        $list = $xs->search();
        $count = array_sum($xs->getFacets('status'));//通过获取分面搜索值能得到精准数量
        $pager = new CPagination($count);
        $xs->setLimit($limit, $limit*$pager->currentPage);
        $docs = $xs->search();
        $ids = array();
        if($docs){
            foreach ($docs as $k=>$v) {
                $ids[] = $v->id;
            }
        }
        $criteria = new CDbCriteria(array(
            'order'=>'t.updated desc',
            'with'=>array('areaInfo')
        ));
        $criteria->addInCondition('t.id',$ids);
        $data = ResoldQgExt::model()->findAll($criteria);
        $response = array();
        foreach ($data as $item){
            $item->created = Tools::friendlyDate($item->updated,'normal','Y-m-d H:i');
            $response[] = $item->getAPIAttributes(array('id','title','size','price','created'),array(
                'areaInfo'=>array('name'),
                'streetInfo'=>array('name')
            ));
        }

        return $this->getController()->frame['data'] = array(
            'list'=> $response,
            'totalCount'=>$count,
            'page_count'=>$pager->pageCount
        );
    }
}