<?php
/**
 *
 * User: jt
 * Date: 2016/10/21 14:20
 */
class ListAction extends CAction{

    /**
     * @param string $id 区域
     * @param string $type 类型
     * @param string $kw 关键词
     */
    public function run($sort=0,$area='',$type='',$kw='',$limit='20',$street=''){
        $criteria = new CDbCriteria(array(
            'order'=>'t.recommend ,t.house_num desc,t.created desc',
            'with'=>array('areaInfo','plot')
        ));
        if($sort && $sort != 0){
            if($sort == 1)
                $criteria->order = 't.house_num asc';
            if($sort == 2)
                $criteria->order = 't.esf_num desc';
            if($sort == 3)
                $criteria->order = 't.esf_num asc';
        }
        if($area){
            $criteria->addCondition('t.area=:area');
            $criteria->params[':area'] = $area;
        }
        if($street){
            $criteria->addCondition('t.street=:street');
            $criteria->params[':street'] = $street;
        }
        if($kw){
            $kw=$this->controller->cleanXss($kw);
            $criteria->addSearchCondition('t.name',$kw);
        }
        if($type && array_key_exists($type,SchoolExt::$type)){
            $criteria->addCondition('t.type=:type');
            $criteria->params[':type'] = $type;
        }
        $dataProvider = SchoolExt::model()->normal()->getList($criteria,$limit);
        $response = array();
        foreach ($dataProvider->data as $item){
            $value = $item->getAPIAttributes(array('name','id','image'));
            $value['plotNum'] = $item->house_num;
            $value['esfNum'] = (int)($item->esf_num);
            $value['area'] = $item->areaInfo?$item->areaInfo->name:'暂无';
            $value['street'] = $item->streetInfo?$item->streetInfo->name:'暂无';
            $value['image'] = ImageTools::fixImage($value['image'],88,66);
            $response[] = $value;
        }
        return $this->getController()->frame['data'] = array(
            'list'=>$response,
            'totalCount'=>$dataProvider->totalItemCount,
            'page_count'=>$dataProvider->pagination->getPageCount()
        );
    }

}