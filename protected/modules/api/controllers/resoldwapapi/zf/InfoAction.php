<?php
/**
 * 租房详情
 * User: jt
 * Date: 2016/10/13 9:05
 */

class InfoAction extends CAction{

    public function run($id){
        $source = Yii::app()->params->source;
        $zf = ResoldZfExt::getZf($id);

        if(!$zf){
            //不存在
            $this->controller->frame['status'] = 'error';
			$this->controller->frame['msg'] = '房源不存在';
        }else{
            if($zf instanceof ResoldZfExt){
                //最近浏览
                $this->controller->addViewRecord('rent',['id'=>$zf->id,'category'=>$zf->category,'title'=>$zf->title]);
                $data = $zf->getAttributes();
                ResoldZfExt::model()->updateByPk($id,['hits'=>$data['hits']+1]);
                //扩展（类型 特色 ）
                $dataConf = CJSON::decode($data['data_conf']);
                //die(var_dump($dataConf));
                unset($data['data_conf']);
                $data['data_ext'] = $data_ext = [];
                if(!empty($dataConf)){
                    foreach($dataConf as $k=>$v){
                        $data_ext[$k] = TagExt::getNameByTag($v);
                    }
                }
                // 与二手房data_ext统一
                $arr = ['type'=>'','ts'=>[],'pt'=>[],'spkjyxm'=>[],'splevel'=>'','xzllevel'=>'','floorcate'=>''];
                if($data_ext)
                    foreach ($arr as $key => $value) {
                        foreach ($data_ext as $k => $v) {
                            if(strstr($k,$key))
                            {
                                $data['data_ext'][$key] = $v;
                                unset($data_ext[$k]);//sptype和配套冲突
                            }

                        }
                    }

                $data['image'] = ImageTools::fixImage($data['image'],640,400);
                //区域
                $data['area'] = $zf->areaInfo?$zf->areaInfo->name:'';
                //街道
                $data['street'] = $zf->streetInfo?$zf->streetInfo->name:'';
                //租房方式
                if(!empty($data['rent_type']) && $data['rent_type']){
                    $data['rent_type'] = TagExt::getNameByTag($data['rent_type']);
                }

                //装修程度
                if(!empty($data['decoration']) && $data['decoration']){
                    $data['decoration'] = TagExt::getNameByTag($data['decoration']);
                }

                //朝向
                if(!empty($data['towards']) && $data['towards']){
                    $data['towards'] = TagExt::getNameByTag($data['towards']);
                }

                //时间
                foreach(['created','refresh_time','updated','sale_time','expire_time'] as $v){
                    if($data[$v]){
                        $data[$v] = Tools::friendlyDate($data[$v]);
                    }
                }
                if($zf->source==2)
                {
                    $staff = ResoldStaffExt::model()->findStaffByUid($zf->uid);
                    $data['qq'] = $staff&&$staff->qq?$staff->qq:'';
                }
                //来源
                $data['source'] = $source[$data['source']];
                $data['pc_url'] = $this->controller->createUrl('/resoldhome/zf/info/id/'.$id);
                $data['source'] = $data['source']=='后台'?'个人':$data['source'];
                //交几付几
                $data['jiao'] = CJSON::decode($data['pay_type'])['jiao'];
                $data['ya'] = CJSON::decode($data['pay_type'])['ya'];
                //unset($data['pay_type']);
                $data['zbpt'] = Tools::export($zf->plot->data_conf['transit']).Tools::export($zf->plot->data_conf['peripheral'],'暂无');
                $data['map_lng'] = $zf->plot->map_lng;
    			$data['map_lat'] = $zf->plot->map_lat;
                $data['sstreet'] = $zf->street;
                $data['image'] = ImageTools::fixImage($zf->image);
                $data['pic_url'] = $this->controller->createUrl('/resoldhome/zf/info',['id'=>$id]);
                $this->controller->frame['data'] = $data;
            }

        }

    }

}
