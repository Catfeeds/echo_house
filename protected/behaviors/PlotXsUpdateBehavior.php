<?php
/**
 * 单条刷新迅搜plot
 */
class PlotXsUpdateBehavior extends CActiveRecordBehavior
{
    public function updateXs($plot)
    {
        $wylx = $xmts = $zxzt = $ditie = $jzlb = $bedroom = $schoolId = $schoolType = array();
        foreach($plot->tags as $v)
        {
            if($v->cate=='wylx') $wylx[] = $v->id;
            if($v->cate=='xmts') $xmts[] = $v->id;
            if($v->cate=='zxzt') $zxzt[] = $v->id;
            if($v->cate=='ditie') $ditie[] = $v->id;
            if($v->cate=='jzlb') $jzlb[] = $v->id;
        }
        //居室
        $brs = PlotHouseTypeExt::model()->enabled()->findAll(array(
            'select' => 'bedroom',
            'condition' => 'hid='.$plot->id.' and bedroom>0',
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
            'condition' => 'hid='.$plot->id,
        ));
        foreach($schools as $school) {
            if($school->school) $schoolType[$school->school->type] = $school->school->type;
            $schoolId[] = $school->sid;
        }
        //面积
        $size_arr = PlotHouseTypeExt::model()->enabled()->findAll(array(
            'select' => 'size',
            'condition' => 'hid='.$plot->id,
        ));
        $sizes=[];
        foreach($size_arr as $size){
            $sizes[]=$size->size;
        }

        $add = array(
            'id' => $plot->id,
            'title' => $plot->title,
            'pinyin' => $plot->pinyin,
            'image' => $plot->image,
            'area' => $plot->area,
            'street' => $plot->street,
            'is_new' => $plot->is_new,
            'sale_status' => $plot->sale_status,
            'wylx' => implode(',',$wylx),
            'xmts' => implode(',',$xmts),
            'zxzt' => implode(',',$zxzt),
            'ditie' => implode(',',$ditie),
            'jzlb' => implode(',',$jzlb),
            'price' => intval($plot->price),
            'unit' => $plot->unit,
            'open_time' => (int)$plot->open_time,
            'tuan' => $plot->tuan_id?1:0,
            'kan_id' => $plot->kan_id,
            'address' => $plot->address,
            'sale_tel' => $plot->sale_tel,
            'map_lng' => $plot->map_lng,
            'map_lat' => $plot->map_lat,
            'status' => $plot->status,
            'sort' => $plot->sort,
            'imagecount' => $plot->imgcount,
            'created' => (int)$plot->created,
            'updated' => (int)$plot->updated,
            'deleted' => (int)$plot->deleted,
            'recommend' => (int)$plot->recommend,
            'bedroom' => implode(',',$bedroom),
            'school_id' => implode(',',$schoolId),
            'school_type' => implode(',',array_keys($schoolType)),
            'price_mark' => $plot->price_mark,
            'record_name'=>$plot->data_conf['recordname'],
            'size_min' => count($sizes)>0?floor(min($sizes)):0,
            'size_max' => count($sizes)>0?ceil(max($sizes)):0,
        );
        if(Yii::app() instanceof CWebApplication)
        {
            $xs = Yii::app()->search->house_plot;
            $xs->update($add,$plot->getIsNewRecord());
            $xs->flushIndex();
        }
    }
}