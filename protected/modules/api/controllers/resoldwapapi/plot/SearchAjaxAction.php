<?php
/**
 * 搜索ajax请求接口
 */
class SearchAjaxAction extends CAction
{
	public function run()
    {
        $kw = Yii::app()->request->getQuery('kw','');
        $id = Yii::app()->request->getQuery('id','0');
        $category = Yii::app()->request->getQuery('category','1');
        $uid = Yii::app()->request->getQuery('uid','0');
        if($id)
        {
            $plot = PlotExt::model()->findByPk($id);

            $criteria = new CDbCriteria;

            $criteria->addCondition('hid=:hid');
            $criteria->params[':hid'] = $plot->id;
            if($uid)
            {
                $criteria->addCondition('uid=:uid');
                $criteria->params[':uid'] = $uid;
            }

            $data = array(
                'name' => $plot->title,
                'sale_status' => isset($plot->xszt->name)?$plot->xszt->name:'',
                'id' => $plot->id,
                'area' => $plot->areaInfo?$plot->areaInfo->name:'未知',
                'street' => $plot->streetInfo?$plot->streetInfo->name:'未知',
                'esf_num' => ResoldEsfExt::model()->saling()->count($criteria),
                'zf_num' => ResoldZfExt::model()->saling()->count($criteria),
            );
        }
        else
        {
            $criteria = new CDbCriteria(array(
            'limit' => 15
            ));
            if (preg_match("/^[a-zA-Z\s]+$/", $kw)) {
                $criteria->addSearchCondition('pinyin', $kw);
            } else {
                $criteria->addSearchCondition('title', $kw);
            }

            $plots = PlotExt::model()->normal()->findAll($criteria);
            if(!$plots)
                $plots = PlotExt::model()->normal()->findAll(['condition'=>'title=:title','params'=>[':title'=>'其他小区'],'order'=>'created desc','limit'=>1]);
            $data = array();
            if($plots)
            foreach($plots as $v)
            {
                $xs = Yii::app()->search->house_esf;
                $xs->setQuery('');
                $xs->setFacets(['sale_status'],true);
                $xs->addRange('sale_status',1,2);
                $xs->addRange('status',1,1);
                $xs->addRange('deleted',0,0);
                $xs->addRange('hid',$v->id,$v->id);
                $xs->addRange('expire_time',time(),null);
                $uid && $xs->addRange('uid',$uid,$uid);
                $category && $xs->addRange('category',$category,$category);
                
                $docs = $xs->search();
                $statuss = $xs->getFacets('sale_status');
                $saling_esf_num = isset($statuss[1])?$statuss[1]:0;
                $unsale_esf_num = isset($statuss[2])?$statuss[2]:0;

                $xs = Yii::app()->search->house_zf;
                $xs->setQuery('');
                $xs->setFacets(['sale_status'],true);
                $xs->addRange('sale_status',1,2);
                $xs->addRange('status',1,1);
                $xs->addRange('deleted',0,0);
                $xs->addRange('hid',$v->id,$v->id);
                $xs->addRange('expire_time',time(),null);
                $uid && $xs->addRange('uid',$uid,$uid);
                $category && $xs->addRange('category',$category,$category);
                
                $docs = $xs->search();
                $statuss = $xs->getFacets('sale_status');
                $saling_zf_num = isset($statuss[1])?$statuss[1]:0;
                $unsale_zf_num = isset($statuss[2])?$statuss[2]:0;
                // var_dump($criteria);
                // var_dump(ResoldEsfExt::model()->saling()->count($criteria));exit;
                $data[] = array(
                    'name' => $v->title,
                    'sale_status' => isset($v->xszt->name)?$v->xszt->name:'',
                    'id' => $v->id,
                    'pinyin' => $v->pinyin,
                    'area' => $v->areaInfo?$v->areaInfo->name:'未知',
                    'street' => $v->streetInfo?$v->streetInfo->name:'未知',
                    'saling_esf_num' => $saling_esf_num,
                    'unsaled_esf_num' => $unsale_esf_num,
                    'saling_zf_num' => $saling_zf_num,
                    'unsaled_zf_num' => $unsale_zf_num,
                );
                unset($xs);unset($statuss);
            }
        }
        $this->controller->frame['data'] = $data;
    }
}
