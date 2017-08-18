<?php
/*
* 二手房ajax搜索
* @author liyu
* @created 2016年10月19日11:50:50
*/
class SearchAjaxAction extends CAction{
    public function run($kw='',$category=1){
        $data = [];
        $xs = Yii::app()->search->house_plot;
        $xs->setFuzzy()->setQuery($kw);
        $xs->addRange('status',1,1);
        $xs->addRange('deleted',0,0);
        // $xs->addRange('is_new',1,1);
        $xs->setLimit(10,0);
        $docs = $xs->search();
        $ids = [];
        if($docs)
            foreach ($docs as $key => $value) {
                $ids[] = $value->id;
            }
        $criteria = new CDbCriteria([
            'order'=>'t.sort desc,t.created desc',
            'limit'=>10
        ]);
        $criteria->addInCondition('id',$ids);
        $plots = PlotExt::model()->findAll($criteria);
        if($plots)
            foreach ($plots as $key => $v) {
                $nums = ResoldEsfExt::model()->saling()->count(['condition'=>'hid=:hid and category=:cate','params'=>[':hid'=>$v->id,':cate'=>$category]]);
                $data[] = ['hid'=>$v->id,'name'=>$v->title,'area'=>$v->areaInfo?$v->areaInfo->name:'','street'=>$v->streetInfo?$v->streetInfo->name:'','num'=>$nums];
            }
        $criteria = new CDbCriteria([
            'order'=>'t.sort desc,t.created desc',
            'limit'=>15
        ]);
        // $esf = [];
        // if($kw){
        //     $criteria->addSearchCondition('title',$kw);
        //     $esfs = ResoldEsfExt::model()->undeleted()->saling()->enabled()->findAll($criteria);
        //     foreach($esfs as $k=>$v)
        //         $esf[] = ['id'=>$v->id,'title'=>$v->title,'sort'=>$v->sort,'created'=>$v->created];
        // }
        $this->controller->frame['data'] = $data;
    }
}
