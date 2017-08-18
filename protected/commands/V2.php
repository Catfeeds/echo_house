<?php
/**
 * 房产v2升级数据转换脚本
 * @author tivon
 * @version 2016-06-22
 */
class V2 extends CComponent
{
    private $_errors = [];
    public function getIsSuccess()
    {
        return !$this->hasErrors();
    }

    public function hasErrors()
    {
        return !empty($this->_errors);
    }

    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * 记录升级日志
     */
    private function log($msg)
    {
        Yii::log($msg, 'info', 'v2');
    }

    /**
     * 执行转换
     */
    public function process()
    {
        if(Yii::app() instanceof CWebApplication){
            echo $this->registerJs();
        }
        //标记最新，因为yii1不能进行事务嵌套，所以拿前面来执行
        SiteConfigModel::model()->isLatest = 1;
        if(SiteConfigModel::model()->save()){
            $transaction = Yii::app()->db->beginTransaction();
            try {
                $this->showMsg('升级开始，请耐心等待，不要进行任何操作！');
                //转换户型
                $this->convertHouseType();
                //添加百科分类
                $this->initBaike();
                //修改推荐位尺寸标注
                $this->updateRecomCate();
                //重建迅搜
                $this->updateXs();

                $transaction->commit();
                $this->showMsg('升级完成....5秒后跳转首页');
                Yii::app()->user->setFlash('success','房产平台升级成功！');
                echo "<script>setTimeout(function(){parent.location.reload();},5000);</script>";
            } catch(Exception $e) {
                $transaction->rollback();
                $this->log($e->getMessage());
                SiteConfigModel::model()->isLatest = 0;
                SiteConfigModel::model()->save();
            }
        }
    }

    public function registerJs()
    {
        $js = '
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>升级中</title>
        <script type="text/javascript">
        function showmessage(message) {
            document.getElementById("notice").innerHTML += message + "<br />";
            document.getElementById("notice").scrollTop = 100000000;
        }
        </script>
        <style>
        #notice { overflow: hidden; margin: 20px; padding: 5px; height: 350px; border: 1px solid #B5CFD9; text-align: left; }
        </style>
        <div id="notice"></div>
        ';
        return $js;
    }

    /**
     * 输出信息
     */
    public function showMsg($msg)
    {
        $ms = explode('.',microtime(true));
        $message =  '['.date('Y-m-d H:i:s ').(isset($ms[1])?$ms[1]:'000').']'.$msg;
        if(Yii::app() instanceof CWebApplication){
            echo '<script type="text/javascript">'.str_repeat(" ",10240*512).'showmessage(\''.addslashes($message).' \');</script>'."\r\n";
            flush();
        	ob_flush();
        } else {
            echo $message;
        }
        $this->log($message);
    }

    public $baikeCates = [
        [
            'name'=>'买新房',
            'pinyin' => 'maixinfang',
            'items' => [
                [
                    'name' => '买房准备',
                    'pinyin' => 'xinfangmaifangzhunbei',
                    'belong' => 1,
                ],
                [
                    'name' => '看房选房',
                    'pinyin' => 'xinfangkanfangxuanfang',
                    'belong' => 1,
                ],
                [
                    'name' => '签约认购',
                    'pinyin' => 'qianyuerengou',
                    'belong' => 2,
                ],
                [
                    'name' => '贷款办理',
                    'pinyin' => 'daikuanbanli',
                    'belong' => 2,
                ],
                [
                    'name' => '缴税过户',
                    'pinyin' => 'xinfangjiaoshuiguohu',
                    'belong' => 3,
                ],
                [
                    'name' => '收房验房',
                    'pinyin' => 'shoufangyanfang',
                    'belong' => 3,
                ]
            ]
        ],
        [
            'name'=>'二手房',
            'pinyin' => 'ershoufang',
            'items' => [
                [
                    'name' => '卖房准备',
                    'pinyin' => 'ershoufangmaifangzhunbei',
                ],
                [
                    'name' => '房源核对',
                    'pinyin' => 'fangyuanhedui',
                ],
                [
                    'name' => '签订合同',
                    'pinyin' => 'qiandinghetong',
                ],
                [
                    'name' => '解抵押',
                    'pinyin' => 'jiediya',
                ],
                [
                    'name' => '缴税过户',
                    'pinyin' => 'ershoufangjiaoshuiguohu',
                ],
                [
                    'name' => '物业交割',
                    'pinyin' => 'wuyejiaoge',
                ]
            ]
        ],
        [
            'name'=>'租房',
            'pinyin' => 'zufang',
            'items' => [
                [
                    'name' => '租房准备',
                    'pinyin' => 'zufangzhunbei',
                ],
                [
                    'name' => '看房选房',
                    'pinyin' => 'zufangkanfangxuanfang',
                ],
                [
                    'name' => '签约入住',
                    'pinyin' => 'qianyueruzhu'
                ],
                [
                    'name' => '退房须知',
                    'pinyin' => 'tuifangxuzhi',
                ]
            ]
        ]
    ];

    /**
     * 添加百科分类
     */
    public function initBaike()
    {
        $this->importBaikeCateRecursive($this->baikeCates);
        $this->showMsg('知识宝典分类初始化完成');
    }


    private function importBaikeCateRecursive($cates=null, $parent=0)
    {
        if(is_array($cates) && isset($cates['name']) && isset($cates['pinyin']) && isset($cates['items'])) {//一级
            $childs = $cates['items'];
            unset($cates['items']);
            $model = new BaikeCateExt;
            $model->attributes = $cates;
            if(!$model->save()) {
                $msg = $model->hasErrors() ? current(current($model->getErrors())) : 'code:1';
                throw new Exception("百科分类添加失败,".$msg);
            }
            $this->showMsg('百科分类增加：'.$model->name);
            $this->importBaikeCateRecursive($childs, $model->id);

        }elseif(is_array($cates) && isset($cates['name']) && isset($cates['pinyin'])){//二级
            $model = new BaikeCateExt;
            $model->attributes = $cates;
            $model->parent = $parent;
            if(!$model->save()) {
                $msg = $model->hasErrors() ? current(current($model->getErrors())) : 'code:2';
                throw new Exception("百科分类添加失败,".$msg);
            }
            $this->showMsg('百科分类增加：'.$model->name);
        }elseif(is_array($cates)) {//最外部大循环
            foreach($cates as $v) {
                $this->importBaikeCateRecursive($v,$parent);
            }
        }
    }

    /**
     * 更新迅搜索引
     */
    public function updateXs()
    {
        $n = 0;
        $xs = Yii::app()->search->house_plot;
        $xs->stopRebuild();
        $xs->clean();
        $xs->beginRebuild();

        plotbegin:
        $plot = PlotExt::model()->isNew()->normal()->findAll(array(
            'order' => 'id desc',
            'limit' => 100,
            'offset' => $n*100
        ));

        if(!empty($plot)){
            $xs->openBuffer();
            foreach($plot as $key=>$val){
                $tag_arr = TagRelExt::model()->findAll(array(
                                    'select' => 'tag_id,cate',
                                    'condition' => 'hid = :hid',
                                    'params' => array(':hid'=>$val->id)
                ));

                $tags = array();
                if(!empty($tag_arr)){
                    foreach($tag_arr as $k=>$v){
                        $tags[$v->cate][] = $v->tag_id;
                    }
                }
                $bedroom = $schoolId = $schoolType = array();
                //居室
                $brs = PlotHouseTypeExt::model()->enabled()->findAll(array(
                    'select' => 'bedroom',
                    'condition' => 'hid='.$val->id.' and bedroom>0',
                    'group' => 'bedroom',
                    'order' => 'bedroom asc'
                ));
                $bedroom = [];
                foreach($brs as $k=>$br) {
                    if($k==0) {
                        $bedroom[] = $br->bedroom;//该户型数
                        $bedroom[] = $br->bedroom.'>';//该户型数以上
                    } elseif($k+1<count($brs)){
                        $bedroom[] = $br->bedroom.'>';//该户型数以上
                        $bedroom[] = $br->bedroom;//该户型数
                        $bedroom[] = '<'.$br->bedroom;//该户型数以下
                    } else {
                        $bedroom[] = '<'.$br->bedroom;//该户型数以下
                        $bedroom[] = $br->bedroom;////该户型数
                    }
                }
                //学校
                $schools = SchoolPlotRelExt::model()->with('school')->findAll(array(
                    'select' => 'sid',
                    'condition' => 'hid='.$val->id,
                ));
                foreach($schools as $school) {
                    if($school->school) $schoolType[$school->school->type] = $school->school->type;
                    $schoolId[] = $school->sid;
                }
                $data = array('wylx'=>'','xmts'=>'','zxzt'=>'','tuan'=>0,'newDiscount'=>'','imagecount'=>0);
                $data['wylx'] = isset($tags['wylx'])?implode(',',$tags['wylx']):'';
                $data['xmts'] = isset($tags['xmts'])?implode(',',$tags['xmts']):'';
                $data['zxzt'] = isset($tags['zxzt'])?implode(',',$tags['zxzt']):'';
                $data['tuan'] = $val->tuan_id?1:0;
                //$data['newDiscount'] = isset($val->newDiscount['title'])?$val->newDiscount['title']:'';
                $data['imagecount'] = PlotImgExt::model()->count('hid='.$val->id);
                $xs->add(array(
                    'id' => $val->id,
                    'title' => $val->title,
                    'pinyin' => $val->pinyin,
                    'image' => $val->image,
                    'area' => $val->area,
                    'street' => $val->street,
                    'is_new' => $val->is_new,
                    'sale_status' => $val->sale_status,
                    'wylx' => $data['wylx'],
                    'xmts' => $data['xmts'],
                    'zxzt' => $data['zxzt'],
                    'price' => intval($val->price),
                    'unit' => $val->unit,
                    'open_time' => (int)$val->open_time,
                    'tuan' => $data['tuan'],
                    'kan_id' => $val->kan_id,
                    'address' => $val->address,
                    'sale_tel' => $val->sale_tel,
                    'data_conf' => CJSON::encode($val->data_conf),
                    'map_lng' => $val->map_lng,
                    'map_lat' => $val->map_lat,
                    'status' => $val->status,
                    'sort' => (int)$val->sort,
                    'imagecount' => $data['imagecount'],
                    'deleted' => (int)$val->deleted,
                    'created' => (int)$val->created,
                    'updated' => (int)$val->updated,
                    'recommend' => (int)$val->recommend,
                    'bedroom' => implode(',',$bedroom),
                    'school_id' => implode(',',$schoolId),
                    'school_type' => implode(',',array_keys($schoolType)),
                ));
            }
            $xs->closeBuffer();
            $this->showMsg('搜索索引重建完成'.($n++*100).'条');
            goto plotbegin;
        }
        $xs->endRebuild();
        $xs->flushIndex();
        $this->showMsg('搜索索引重建完成');
    }

    /**
     * 转换户型图
     * @return [type] [description]
     */
    public function convertHouseType()
    {
        $offset = 0;
        $cate = $arr = TagExt::model()->find('name="户型图"');
        if(!$cate){
            throw new Exception('户型分类未找到');
            Yii::app()->end();
        }
        $cate->status =0 ;
        if($cate->save()){
            $this->showMsg('禁用相册户型图分类');
        }
        $count = 0;
        begin:
        $criteria = new CDbCriteria(array(
            'limit' => 100,
            'offset' => $offset++ * 100,
            'condition' => 'type='.$cate->id,
            'order' => 'id asc',
        ));
        $houseTypes = PlotImgExt::model()->findAll($criteria);
        if($houseTypes){
            foreach($houseTypes as $row){
                $arr = $this->findWord($row->title);
                if($row->size>0){
                    $arr['size'] = $row->size;
                }
                if($row->room>0){
                    $arr['room'] = $row->room;
                }
                $data = array(
                    'hid' => $row->hid,
                    'title' => $row->title ? $row->title : '-',
                    'image' => $row->url,
                    'bedroom' => (int)$arr['shi'],
                    'livingroom' => (int)$arr['ting'],
                    'bathroom' => (int)$arr['wei'],
                    'cookroom' => (int)$arr['chu'],
                    'size' => $arr['size'],
                    'is_cover' => $row->is_cover,
                    'sort' => $row->sort,
                    'created' => $row->created,
                    'updated' => $row->updated,
                );
                $model = new PlotHouseTypeExt;
                $model->attributes = $data;
                if(!$model->save()) {
                    $msg = '户型图转换出错，错误户型id：'.$row->id;
                    if($model->hasErrors()) {
                        $msg .= ' ' .current(current($model->getErrors()));
                    }
                    $this->showMsg($msg);
                }
                $count++;
            }
            $this->showMsg('户型数据转换完成:'.$count.'条');
            goto begin;
        }
        $this->showMsg('户型图数据转换完成');
    }

    /**
     * 抓取标题中参数
     * @return array
     */
    private function findWord($title)
    {
        $arr = ['shi'=>0,'ting'=>0,'wei'=>0,'chu'=>0,'size'=>0];
        $pattern = array(
            '/一/','/二/','/两/','/三/','/四/','/五/','/六/','/七/','/八/','/九/','/十/','/㎡/'
        );
        $num = array(
            '1','2','2','3','4','5','6','7','8','9','10','平'
        );
        if(!preg_match('/(\d+)房/', $title, $shi)) {//处理特殊情况"卢森堡 A7三房113.73㎡"，下同
            $title2 = preg_replace('/\d+/','',$title);
            $title2 = preg_replace($pattern, $num, $title2);
            preg_match('/(\d+)房/', $title2, $shi);
        }
        if(isset($shi[1])){
            $arr['shi'] = (int)$shi[1];
        }

        if(!preg_match('/(\d+)室/', $title, $shi)) {//处理特殊情况"卢森堡 A7三室113.73㎡"，下同原理
            $title2 = preg_replace('/\d+/','',$title);
            $title2 = preg_replace($pattern, $num, $title2);
            preg_match('/(\d+)室/', $title2, $shi);
        }
        if(isset($shi[1])){
            $arr['shi'] = (int)$shi[1];
        }

        if(!preg_match('/(\d+)厅/', $title, $ting)) {//处理特殊情况"卢森堡 A7三室113.73㎡"，下同原理
            $title2 = preg_replace('/\d+/','',$title);
            $title2 = preg_replace($pattern, $num, $title2);
            preg_match('/(\d+)厅/', $title2, $ting);
        }
        if(isset($ting[1])){
            $arr['ting'] = (int)$ting[1];
        }

        if(!preg_match('/(\d+)卫/', $title, $wei)) {//处理特殊情况"卢森堡 A7三室113.73㎡"，下同原理
            $title2 = preg_replace('/\d+/','',$title);
            $title2 = preg_replace($pattern, $num, $title2);
            preg_match('/(\d+)卫/', $title2, $wei);
        }
        if(isset($wei[1])){
            $arr['wei'] = (int)$wei[1];
        }

        if(!preg_match('/(\d+)厨/', $title, $chu)) {//处理特殊情况"卢森堡 A7三室113.73㎡"，下同原理
            $title2 = preg_replace('/\d+/','',$title);
            $title2 = preg_replace($pattern, $num, $title2);
            preg_match('/(\d+)厨/', $title2, $chu);
        }
        if(isset($chu[1])){
            $arr['chu'] = (int)$chu[1];
        }
        if(!preg_match('/(\d+(\.\d+)?)平/', $title, $size)) {
            preg_match('/(\d+(\.\d+)?)㎡/', $title, $size);
        }
        if(isset($size[1])){
            $arr['size'] = $size[1];
        }
        return $arr;
    }

    /**
     * 修改推荐位尺寸标注
     * @return [type] [description]
     */
    public function updateRecomCate()
    {
        $arr = [
            'wapsytwlh' => 'wap首页图文轮换(640x450)',
            'syxfxqf' => '新盘-学区房(275x200)',
            'syxfhftj' => '新盘-婚房推荐(275x200)',
            'syxfgxlp' => '新盘-刚需楼盘(275x200)',
            'syxfzxkp' => '新盘-最新开盘(275x200)',
            'syxfrmlp' => '新盘-热门楼盘(275x200)',
        ];
        $criteria = new CDbCriteria;
        $criteria->addInCondition('pinyin', array_keys($arr));
        $rows = RecomCateExt::model()->findAll($criteria);
        foreach($rows as $row){
            if(isset($arr[$row->pinyin])) {
                $row->name = $arr[$row->pinyin];
                $row->save();
            }
        }
        $this->showMsg('修改推荐位标注尺寸完成');
    }
}
