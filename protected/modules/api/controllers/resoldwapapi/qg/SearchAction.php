<?php
/**
 * 求购求租搜索
 */
class SearchAction extends CAction
{
	public function run($kw='',$category=1)
	{
		$data = [];
        $xs = Yii::app()->search->house_qg;
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
            'order'=>'t.created desc',
            'limit'=>10
        ]);
        $criteria->addInCondition('id',$ids);
        $criteria->addCondition('category='.$category);
        $qgs = ResoldQgExt::model()->findAll($criteria);
        if($qgs)
            foreach ($qgs as $key => $v) {
                $data[] = ['hid'=>$v->id,'name'=>$v->title,'area'=>'','street'=>'','num'=>'1'];
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