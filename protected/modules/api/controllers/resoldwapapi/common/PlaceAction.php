<?php

/**
 * User: fanqi
 * Date: 2016/9/28
 * Time: 16:07
 *位置接口
 * ============================================
 * [response]响应参数：
 * array(object) area
 */
class PlaceAction extends CAction
{
    public function run()
    {
        $areas = CacheExt::gas('wap_all_area','AreaExt',0,'二手房wap区域缓存',function (){
            $areas = AreaExt::model()->findAll('parent = 0');
            $areas[0]['childArea'] = $areas[0]->childArea;
            return $this->addChild($areas);
        });
        $this->getController()->frame['data'] = [
            'area'=>$areas
        ];
    }

    public function addChild($areas)
    {
        $count = count($areas);
        for ($i = 0;$i<$count;$i++){
            if($child = $areas[$i]->childArea){
                $child = $this->addChild($child);
            }
            //将对象转换成数组
            $areas[$i] = $areas[$i]->attributes;
            if($child){
                array_unshift($child, ["id"=>0,"parent"=>$areas[$i]['id'],"name"=>'不限',"pinyin"=>0,"sort"=>100,"map_lng"=>0,"map_lat"=>0,"map_zoom"=>0,"deleted"=>0,"created"=>0,"updated"=>0,"old_id"=>0,"status"=>0]);
                $areas[$i]['childAreas']=$child;
            }
        }
        return $areas;
    }
}