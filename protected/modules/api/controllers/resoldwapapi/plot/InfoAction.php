<?php
/*
* 小区详情页
* @author liyu
* @created 2016年10月24日14:40:29
*/
class InfoAction extends CAction{
    public function run($id){
        $plot = PlotExt::model()->with('avg_esf')->findByPk($id);
        if(empty($plot)){
            $this->controller->frame['status'] = 'error';
			$this->controller->frame['msg'] = '房源不存在';
        }else{
            $data = $plot->getAttributes();
            $streetid = $plot->street;
            $areaid = $plot->area;
            //区域

            $data['area'] = $plot->areaInfo?$plot->areaInfo->name:'';
            $data['street'] = $plot->streetInfo?$plot->streetInfo->name:'';
            $data['image'] = $data['image']?$data['image']:SM::resoldImageConfig()->resoldNoPic();
            $data['image'] = ImageTools::fixImage($data['image'],640,400);
            $data['age'] = $plot->open_time?date('Y',$plot->open_time):'未知';
            //物业费data_conf manage_fee
            //年代
            //绿化 data_conf green
            //住宅类型
            $jzlb = [];
            foreach($plot->jzlb as $k=>$v){
                $jzlb[] = $v->name;
            }
            $wylx = [];
            foreach($plot->wylx as $k=>$v){
                $wylx[] = $v->name;
            }
            //容积率 data_conf capacity
            //物业公司 data_conf manage_company
            //开发商 data_conf developer
            //周边配套 data_conf transit peripheral
            //特色
            $xmts = [];
            foreach($plot->xmts as $k=>$v){
                $xmts[] = $v->name;
            }
            $daily = PlotResoldDailyExt::getLastInfoByHid($id);
            //'esfcount'=>$daily->esf_num,'esf_price'=>$daily->esf_num,'zfcount'=>$daily->zf_num,'zf_price'=>$daily->zf_price
            $esfcount = 0;$esfprice = 0;$zfcount = 0;$zfprice=0;
            if($daily){
                $esfcount = $daily->esf_num;
                $esfprice = $plot->avg_esf?$plot->avg_esf->price:0;
                $zfcount = $daily->zf_num;
                $zfprice = $daily->zf_price;
            }
            $unit = '元/㎡';

            $data['content'] = $data['data_conf']['content'];
            $data['content'] = str_replace('<br>', '', $data['content']);//bug remaining
            $data['jzlb'] = $jzlb;// 住宅类别
            $data['xmts'] = $xmts;
            $data['wylx'] = $wylx;// 房屋类型
            $data['unit'] = $unit;

            $data['price'] = $plot->avg_esf?$plot->avg_esf->price:0;

            $data = array_merge($data,['zbpt'=>$data['data_conf']['peripheral']]);
            $trans = ['plot'=>$data,'esfcount'=>$esfcount,'esf_price'=>$esfprice,'zfcount'=>$zfcount,'zf_price'=>$zfprice,'streetid'=>$streetid,'areaid'=>$areaid,'transit'=>$data['data_conf']['transit']];
            $this->controller->frame['data'] = $trans;
        }

    }
}
