<?php
/**
 *
 * User: jt
 * Date: 2016/10/12 9:10
 */
class ListAction extends CAction{

    //wap版目前只有二手房和租房收藏
    public function run($house_type,$limit='20'){
        if($house_type != 2 && $house_type != 3){
            return $this->controller->returnError('参数异常');
        }
        $with = ResoldUserCollectionExt::$house_type_relations[$house_type];
        if(!$with){
            $with = ResoldUserCollectionExt::$house_type_relations[ResoldUserCollectionExt::HOUSE_TYPE_XF];
        }
        $criteria = new CDbCriteria(array(
            'condition'=>'t.uid=:uid',
            'params'=>array(':uid'=>$this->getController()->uid),
            'order'=>'t.created desc',
            'with'=>array($with),
        ));
        $dataProvider = ResoldUserCollectionExt::model()->getList($criteria,$limit);
        $response = array();
        if($dataProvider->data)
            foreach ($dataProvider->data as $collection){
                $info = $collection->$with->attributes;
                $info['favid'] = $collection->id;
                $info['image'] = ImageTools::fixImage($info['image']);
                $info['area'] = $collection->$with->areaInfo ? $collection->$with->areaInfo->name : '';
                $info['street'] = $collection->$with->streetInfo ? $collection->$with->streetInfo->name : '';
                $response[] = $info;
            }
        // $response['page_count'] = $dataProvider->pagination->getPageCount();
        $esf_num = ResoldUserCollectionExt::model()->conditionUid($this->controller->uid)->count("house_type=2");
        $zf_num = ResoldUserCollectionExt::model()->conditionUid($this->controller->uid)->count("house_type=3");
        return $this->controller->frame['data'] = ['data'=>$response,'num'=>['esf'=>$esf_num,'zf_num'=>$zf_num]];
    }

}