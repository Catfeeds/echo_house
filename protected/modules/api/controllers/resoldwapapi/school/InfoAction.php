<?php
/**
 *
 * User: jt
 * Date: 2016/10/24 8:46
 */
class InfoAction extends CAction{

    public function run($id=0){
        $school =  SchoolExt::model()->with('plotSchool')->normal()->findByPk($id);
        if(!$school)
            return $this->getController()->returnError('找不到校区');
        $response = array();
        $response = $school->getAPIAttributes(array('name','phone','address','image','pic','description'));
        $ids= array();
        foreach($school->plotSchool as  $key=>$v){
            $ids[$key] = $v->hid;
        }

        $criteria = new CDbCriteria(array(
            'with'=>array('lastResoldData','avg_esf')
        ));
        $criteria->addInCondition('t.id',$ids);
        $data = PlotExt::model()->normal()->findAll($criteria);

        $response['totalCount'] = count($data);
        $response['image'] = ImageTools::fixImage($response['image'],640,400);
        if($response['pic']){
            foreach ($response['pic'] as $key => $item){
                $response['pic'][$key] = ImageTools::fixImage($item);
            }
        }
        if($data) {
            foreach ($data as $item) {
                $plots = $item->getAPIAttributes(array('id', 'title'));
                if($item->lastResoldData){
                    $plots['esfNum'] = $item->lastResoldData->esf_num ;
                    $plots['price'] = isset($item->avg_esf->price)?$item->avg_esf->price:$item->lastResoldData->esf_price;
                    $response['plot'][] = $plots;
                }
            }
            if(isset($response['plot']) && $response['plot']){
                usort($response['plot'],function ($v1,$v2){
                    return $v2['esfNum'] - $v1['esfNum'];
                });
            }
        }
        $response['pc_url'] = $this->controller->createUrl('/resoldhome/school/plot',['pinyin'=>$school->pinyin]);
       return $this->getController()->frame['data'] = $response;
    }

}