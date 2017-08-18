<?php
/**
 * 求租详情页
 * User: jt
 * Date: 2016/10/14 15:09
 */

class InfoAction extends CAction{

    public function run($id){
        $resold_qz = ResoldQzExt::model()->with('areaInfo')->enabled()->undeleted()->findByPk($id);
        if(!$resold_qz){
            return $this->getController()->returnError('找不到求租信息');
        }
        //最近浏览
        $this->controller->addViewRecord('qz',['id'=>$resold_qz->id,'category'=>$resold_qz->category,'title'=>$resold_qz->title]);
        $resold_qz->hits = $resold_qz->hits + 1;
        $resold_qz->save();

        $response = array();
        $resold_qz->created = date('Y-m-d',$resold_qz->created);;
        $response = $resold_qz->attributes;
        $plot_id_array = json_decode($resold_qz->hid,true);
        $data_conf = json_decode($resold_qz->data_conf,true);
        $all_tag = TagExt::getAllByCate();
        if($data_conf) {
            foreach ($data_conf as $key => $value) {
                if(isset($all_tag[$key]) && $tags = $all_tag[$key]){
                    foreach ($tags as $tag){
                        if(is_array($value)) {
                            if (in_array($tag['id'], $value)) {
                                $response[$key][] = $tag['name'];
                            }
                        }else{
                            if($tag['id'] == $value){
                                $response[$key][] = $tag['name'];
                            }
                        }
                    }
                }
            }
        }
        unset($response['hid'],$response['street'],$response['data_conf']);
        if($plot_id_array) {
            $plots = PlotExt::model()->findAllByAttributes(array('id' => $plot_id_array));
            foreach ($plots as $plot) {
                $response['plot'][] = $plot->title;
            }
        }
        $zx = '暂无';
        foreach ($all_tag['resoldzx'] as $el){
            if($el['id'] == $response['decoration'])
                $zx = $el['name'];
        }
        $response['decoration'] = $zx;
        $rent_type = '不限';
        foreach ($all_tag['zfmode'] as $mode){
            if($mode['id'] == $response['rent_type'])
                $rent_type = $mode['name'];
        }
        $response['rent_type'] = $rent_type;
        $response['areaInfo']['name'] = $resold_qz->areaInfo ? $resold_qz->areaInfo->name : '';
        $response['streetInfo']['name'] = $resold_qz->streetInfo ? $resold_qz->streetInfo->name : '';
        $response['pc_url'] = $this->controller->createUrl("/resoldhome/qz/detail/id/$id/type/".$resold_qz->category);
        return $this->getController()->frame['data']  = $response;
    }

}