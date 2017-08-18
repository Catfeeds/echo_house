<?php
/**
 * 替换资讯楼盘名称的api
 */
class PlotController extends ApiController
{
    /**
     * [actionExport 导出固定格式接口]
     * @param  string $value [description]
     * @return [type]        [description]
     */
    public function actionExport($page='',$type='')
    {
        $pageOff = ($page-1)*100;
        
        
        $data = [];

        // is_new=>1 status=>1 is_coop=>1
        if($type=='plot') {
            $sql = "select m.* from plot m where m.deleted=0 order by m.id asc limit $pageOff,100";
            $criteria = new CDbCriteria();
            $criteria->order = 'id asc';
            $count = PlotExt::model()->undeleted()->count($criteria);
            if($page>($count/100)+1)
                return false;
            $houseInfo = Yii::app()->db->createCommand($sql)->queryAll();
            foreach ($houseInfo as $key => $value) {
                $data_conf = json_decode($value['data_conf'],true);
                // va
                unset($value['data_conf']);
                unset($value['deleted']);
                unset($value['created']);
                unset($value['updated']);
                // $tmp = array_merge($value,$data_conf);
                // var_dump($tmp);exit;
                foreach (array_merge($value,$data_conf) as $k => $v) {
                    if($k!='transit'&&$k!='peripheral'&&$k!='content')
                        $tmp[$k] = $this->unicode_decode($v); 
                    else
                        $tmp[$k] = str_replace(['\n','\t','\r'], '', $v);
                }

                $tmp['is_new'] = $tmp['status'] = $tmp['is_coop'] = 1;

                $areas = Yii::app()->params['area'];
                $jzlbs = Yii::app()->params['jzlb'];
                $zxzts = Yii::app()->params['zxzt'];
                $xmtss = Yii::app()->params['xmts'];
                $wylxs = Yii::app()->params['wylx'];
                $xszts = Yii::app()->params['xszt'];
                // foreach ($areas as $k => $v) {
                //     if(strstr($value['area'],$k))
                //         $tmp['area'] = $v;
                //     if(strstr($value['street'],$k))
                //         $tmp['street'] = $v;
                // }
                foreach ($jzlbs as $k => $v) {
                    if(isset($tmp['jzlb'])&&strstr($tmp['jzlb'],$k))
                        $tmp_jzlb[] = $v;
                }
                foreach ($zxzts as $k => $v) {
                    if(isset($tmp['zxzt'])&&strstr($tmp['zxzt'],$k))
                        $tmp_zxzt[] = $v;
                }
                foreach ($xmtss as $k => $v) {
                    if(isset($tmp['xmts'])&&strstr($tmp['xmts'],$k))
                        $tmp_xmts[] = $v;
                }
                foreach ($wylxs as $k => $v) {
                    if(isset($tmp['wylx'])&&strstr($tmp['wylx'],$k))
                        $tmp_wylx[] = $v;
                }
                foreach ($xszts as $k => $v) {
                    if(isset($tmp['xszt'])&&strstr($tmp['xszt'],$k))
                        $tmp['xszt'] = $v;
                }
                if(strstr($tmp['image'],'http')){
                    $tmp['image'] = $this->sfImage($tmp['image'],$tmp['image']);
                }
                $tmp['image'] && $tmp['image'] = ImageTools::fixImage($tmp['image']).'?imageMogr2/auto-orient/gravity/NorthWest/crop/!800x500-10-10/blur/1x0/quality/75';
                // if(!is_numeric($tmp['area']))
                //     continue;
                if(!isset($tmp_jzlb))
                    $tmp['jzlb'] = [];
                else
                    $tmp['jzlb'] = $tmp_jzlb;
                if(!isset($tmp_zxzt))
                    $tmp['zxzt'] = [];
                else
                    $tmp['zxzt'] = $tmp_zxzt;
                if(!isset($tmp_xmts))
                    $tmp['xmts'] = [];
                else
                    $tmp['xmts'] = $tmp_xmts;
                if(!isset($tmp_wylx))
                    $tmp['wylx'] = [];
                else
                    $tmp['wylx'] = $tmp_wylx;
                if(!isset($tmp_xszt))
                    $tmp['sale_status'] = 2;
                else
                    $tmp['sale_status'] = $tmp_xszt;
                unset($tmp_jzlb);
                unset($tmp_zxzt);
                unset($tmp_xmts);
                unset($tmp_wylx);
                unset($tmp_xszt);

                if($tmp['price']) {
                    if(strstr($tmp['price'],'套')){
                        $tmp['unit'] = 2;
                    } else {
                        $tmp['unit'] = 1;
                    }
                    preg_match_all('/[0-9|.]+/', $tmp['price'], $pricefs);
                    if(isset($pricefs[0][0]) && $tmp['price'] = intval($pricefs[0][0])) ;
                }

                $data[] = $tmp;
            }
        } elseif($type=='hx') {
            $sql = "select m.* from plot_hx m where m.deleted=0 and image!='' order by m.id asc limit $pageOff,100";
            $criteria = new CDbCriteria();
            $criteria->order = 'id asc';
            $count = PlotHxExt::model()->undeleted()->count($criteria);
            if($page>($count/100)+1)
                return false;
            $houseInfo = Yii::app()->db->createCommand($sql)->queryAll();
            foreach ($houseInfo as $key => $value) {
                
                if(strstr($value['image'],'http')) {
                    continue;
                }
                $tmp['image'] = ImageTools::fixImage($value['image']);
                $tmp['bedroom'] = $value['bedroom'];
                $tmp['livingroom'] = $value['livingroom'];
                $tmp['bathroom'] = $value['bathroom'];
                $tmp['size'] = $value['size'];
                if($sat = $value['sale_status']) {
                    if($sat == '在售') {
                        $tmp['sale_status'] = 1;
                    } elseif($sat == '售完') {
                        $tmp['sale_status'] = 0;
                    } elseif($sat == '待售') {
                        $tmp['sale_status'] = 2;
                    }
                }
                $tmp['hid'] = $value['hid'];

                $tmp['title'] = $value['title'];
                $data[] = $tmp;
            }
        } elseif($type=='image') {
            $sql = "select m.* from plot_image m where m.deleted=0 and url!='' order by m.id asc limit $pageOff,100";
            $criteria = new CDbCriteria();
            $criteria->order = 'id asc';
            $count = PlotImageExt::model()->undeleted()->count($criteria);
            if($page>($count/100)+1)
                return false;
            $houseInfo = Yii::app()->db->createCommand($sql)->queryAll();
            foreach ($houseInfo as $key => $value) {
                
                if(strstr($value['url'],'http')) {
                    continue;
                }
                $tmp['url'] = ImageTools::fixImage($value['url']).'?imageMogr2/auto-orient/gravity/NorthWest/crop/!800x500-10-10/blur/1x0/quality/75';
                $tmp['type'] = 18;
                $tmp['hid'] = $value['hid'];

                $tmp['title'] = $value['title'];
                $data[] = $tmp;
            }
        }
        echo json_encode($data);
    }
}
