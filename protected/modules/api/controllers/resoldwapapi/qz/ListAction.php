<?php
/**
 * 求租列表页
 * User: jt
 * Date: 2016/10/14 15:09
 */
class ListAction extends CAction{

    /**
     * @param string $page 分页
     * @param string $kw 搜索词
     * @param string $area 区域
     * @param string $price 租金
     * @param string $hx 户型
     * @param string $way 方式
     */
    public function run($category=1,$page='',$kw='',$area='',$street='',$price='',$hx='',$way='',$cate='',$size='',$minprice='',$maxprice='',$limit='20'){
        $kw=$this->controller->cleanXss($kw);
        $xs = Yii::app()->search->house_qz;
        $xs->setQuery($kw);
        $xs->setFacets(array('status'), true);//分面统计
        $xs->addRange('deleted',0,0); //是否删除
        $xs->addQueryString('status:1',XS_CMD_QUERY_OP_AND);
        $xs->addQueryString('category:'.$category,XS_CMD_QUERY_OP_AND);
//        $xs->addRange('category',$category,$category);
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
            if($price) {
                if(isset($all_tag[ResoldQzExt::$price_cate[$category]][$price])){
                    $price_tag = $all_tag[ResoldQzExt::$price_cate[$category]][$price];
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
            if (isset($all_tag['resoldhuxing'][$hx]) && $hx_tag = $all_tag['resoldhuxing'][$hx]) {
                $xs->addQueryString('resoldhuxing:'.$hx_tag['id'],XS_CMD_QUERY_OP_AND);
            }
        }
        //方式
        if($way && $category == 1){
            if(isset($all_tag['zfmode'][$way]) && $way_tag = $all_tag['zfmode'][$way]){
                $xs->addQueryString('rent_type:'.$way_tag['id'],XS_CMD_QUERY_OP_AND);
            }
        }
        //商铺类型
        if($cate && $category == 2){
            if(isset($all_tag['esfzfsptype'][$cate]) && $cate_tag = $all_tag['esfzfsptype'][$cate]){
                $xs->addQueryString('esfzfsptype:'.$cate_tag['id'],XS_CMD_QUERY_OP_AND);
            }
        }
        //写字楼面积
        if($size && ($category == 3 || $category == 2)){
            if(isset($all_tag[ResoldQzExt::$size_cate[$category]][$size]) && $size_tag = $all_tag[ResoldQzExt::$size_cate[$category]][$size]){
                if($size_tag['min'] > 0 && $size_tag['max'] > 0){
                    $size_tag['max'] >= $size_tag['min'] ? $xs->addRange('size',$size_tag['min'] , $size_tag['max']) : $xs->addRange('size',null,$size_tag['max']);
                }else{
                    $size_tag['min'] > 0 ? $xs->addRange('size',$size_tag['min'] , null) : $xs->addRange('size',null,$size_tag['max']);
                }
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
        $data = ResoldQzExt::model()->findAll($criteria);
        $response = array();
        foreach ($data as $item){
            $item->created =  Tools::friendlyDate($item->updated,'normal','Y-m-d H:i');
            $response[] = $item->getAPIAttributes(array('id','title','size','price','created'),array(
                'areaInfo'=>array('name'),'streetInfo'=>array('name')
            ));
        }
        return $this->getController()->frame['data'] = array(
            'list'=> $response,
            'totalCount'=>$count,
            'page_count'=>$pager->pageCount
        );
    }

}