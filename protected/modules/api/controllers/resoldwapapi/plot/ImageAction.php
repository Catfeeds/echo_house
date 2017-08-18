<?php
/*
* 小区相册
* @author liyu
* @created 2016年10月27日09:14:28
*/
class ImageAction extends CAction{
    public function run(){
        $hid = Yii::app()->request->getQuery('hid',0);
        $data = [];
        $filter = PlotImgExt::model()->findAll(array(
            'select' => 'count(id) as count,type',
            'condition' => 'hid=:hid',
            'params' => array(':hid'=>$hid),
            'group' => 'type',
        ));
        $cates = array();
        foreach($filter as $v){
            if($v->count>0){
                $cates[] = $v->type;
            }
        }
        $criteria = new CDbCriteria();
        $criteria->addInCondition('id', $cates);
        $criteria->order = 'sort asc';
        $cates = TagExt::model()->getTagByCate('xcfl')->normal()->findAll($criteria);

        if(!$cates) throw new CHttpException(404,'相册分类不存在');
        $cate = [];
        foreach($cates as $k=>$v){
            $cate[] = [$v->id=>$v->name];
        }
        $data['type'] = $cate;
        $type = (int)Yii::app()->request->getQuery('type', $cates[0]['id']);

        //获取相册数据
        $list = PlotImgExt::model()->findAll(array(
            'condition'=>'type=:type and hid=:hid',
            'params'=>array(':type'=>$type,':hid'=>$hid),
        ));
        foreach($list as $k=>$v){
            $imgs[] = ImageTools::fixImage($v->url);
        }
        $data['img'] = $imgs;
        $this->controller->frame['data'] = $data;
    }
}
