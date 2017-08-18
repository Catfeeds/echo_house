<?php
/**
 * 求购详情页
 * User: jt
 * Date: 2016/10/14 9:26
 */

class InfoAction extends CAction{

    public function run($id){
        $resold_qg = ResoldQgExt::model()->with('areaInfo')->enabled()->undeleted()->findByPk($id);
        if(!$resold_qg){
           return $this->getController()->returnError('找不到求购信息');
        }
        //最近浏览
        $this->controller->addViewRecord('qg',['id'=>$resold_qg->id,'category'=>$resold_qg->category,'title'=>$resold_qg->title]);
        $resold_qg->hits = $resold_qg->hits + 1;
        $resold_qg->save();

        $response = array();
        $resold_qg->created = date('Y-m-d',$resold_qg->created);
        $response = $resold_qg->getAPIAttributes(array('age','decoration','towards','title', 'username','size',
            'price','bedroom','content','livingroom','floor','phone','bathroom','created'),
            array('areaInfo'=>array('name'),'streetInfo'=>array('name')
        ));
        $all_tag = TagExt::getAllByCate();
        $zx = '暂无';
        foreach ($all_tag['resoldzx'] as $zx_item) {
            if($zx_item['id'] == $response['decoration']){
                $zx = $zx_item['name'];
            }
        }
        $towards = '暂无';
        foreach ($all_tag['resoldface'] as $face_item){
            if($face_item['id'] == $response['towards']){
                $towards = $face_item['name'];
            }
        }
        $response['decoration'] = $zx;
        $response['towards'] = $towards;
        $plot_id_array = json_decode($resold_qg->hid,true);
        $data_conf = json_decode($resold_qg->data_conf,true);
        if($data_conf['tags']) {
            $tags = TagExt::model()->findAllByAttributes(array('id' => $data_conf['tags']));
            foreach ($tags as $tag) {
                $response[$tag->cate][] = $tag->name;
            }
        }
        unset($response['hid'],$response['street'],$response['data_conf']);
        $plots = PlotExt::model()->findAllByAttributes(array('id'=>$plot_id_array));
        foreach ($plots as $plot){
            $response['plot'][] = $plot->title;
        }
        $response['pc_url'] = $this->controller->createUrl("/resoldhome/qg/detail/id/$id/type/".$resold_qg->category);
        return $this->getController()->frame['data']  = $response;
    }

}

