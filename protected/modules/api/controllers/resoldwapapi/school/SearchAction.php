<?php
/**
 *
 * User: jt
 * Date: 2016/10/24 10:18
 */
class SearchAction extends CAction{

    public function run($kw=''){
        $criteria = new CDbCriteria(array(
            'order'=>'t.recommend,t.created desc',
        ));
        if (preg_match("/^[a-zA-Z\s]+$/", $kw)) {
            $criteria->addSearchCondition('pinyin', $kw);
        } else {
            $criteria->addSearchCondition('name', $kw);
        }
        $school = SchoolExt::model()->normal()->findAll($criteria);
        $response = array();
        $plotNum = array();
        foreach ($school as $item){
            $result = $item->getAPIAttributes(array('id','name','pinyin','esf_num'));
            $result['plotNum'] = $plotNum[] = $item->esf_num;
            $response[] = $result;
        }
        array_multisort($plotNum,SORT_DESC,$response);
        if(count($response) > 10)
            $response = array_slice($response,0,10);
        return $this->getController()->frame['data'] = $response;
    }

}

