<?php
/**
 * 迅搜索引管理脚本
 */
class XsIndexCommand extends CConsoleCommand
{
    /**
     * 清除指定项目索引
     * @param  string $project 项目名
     */
    public function actionClean($project)
    {
        $xs = Yii::app()->search->$project;
        if(!empty($xs))
        {
            try
            {
                begin:
                $xs->stopRebuild();
                $xs->clean();
                echo 'finished';
            }
            catch(Exception $e)
            {
                $xs->endRebuild();
                goto begin;
            }
        }
    }

    /**
     * 刷新索引缓存
     * @param  string $project 项目名
     */
    public function actionFlushIndex($project)
    {
        $xs = Yii::app()->search->$project;
        if(!empty($xs))
        {
            try
            {
                begin:
                $xs->stopRebuild();
                $xs->flushIndex();
                echo 'finished';
            }
            catch(Exception $e)
            {
                $xs->endRebuild();
                goto begin;
            }
        }
    }

    public function actionPlot($isNew=1){
        $n = 0;
        $count = PlotExt::model()->count();
        //$tag = CHtml::listData( TagExt::tagCache(),'id','name');
        $xs = Yii::app()->search->house_plot;
        $xs->stopRebuild();
        $xs->clean();
        $xs->beginRebuild();

        plotbegin:
        if($isNew)
            $plot = PlotExt::model()->normal()->isNew()->findAll(array(
                'order' => 'id desc',
                'limit' => 100,
                'offset' => $n*100
            ));
        else
            $plot = PlotExt::model()->normal()->findAll(array(
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
                $bedroom = $schoolId = $schoolType = $sizes = array();
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
                //面积
                $size_arr = PlotHouseTypeExt::model()->enabled()->findAll(array(
                    'select' => 'size',
                    'condition' => 'hid='.$val->id,
                ));
                foreach($size_arr as $size){
                    $sizes[]=$size->size;
                }

                $data = array('wylx'=>'','xmts'=>'','zxzt'=>'','tuan'=>0,'newDiscount'=>'','imagecount'=>0);
                $data['wylx'] = isset($tags['wylx'])?implode(',',$tags['wylx']):'';
                $data['xmts'] = isset($tags['xmts'])?implode(',',$tags['xmts']):'';
                $data['zxzt'] = isset($tags['zxzt'])?implode(',',$tags['zxzt']):'';
                $ditie = isset($tags['ditie']) ? implode(',',$tags['ditie']) : '';
                $jzlb = isset($tags['jzlb']) ? implode(',',$tags['jzlb']) : '';

                $data['tuan'] = $val->tuan_id?1:0;
                //$data['newDiscount'] = isset($val->newDiscount['title'])?$val->newDiscount['title']:'';
                $data['imagecount'] = PlotImgExt::model()->count('hid='.$val->id);

                $resoldPlot = PlotResoldDailyExt::getLastInfoByHid($val->id);

                $record_name=$val->data_conf['recordname'];
                $resold_sort=isset($val->data_conf['resold_sort'])?$val->data_conf['resold_sort']:0;
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
                    'ditie' => $ditie,
                    'jzlb' => $jzlb,
                    'price' => intval($val->price),
                    'unit' => $val->unit,
                    'open_time' => (int)$val->open_time,
                    'tuan' => $data['tuan'],
                    'kan_id' => $val->kan_id,
                    'address' => $val->address,
                    'sale_tel' => $val->sale_tel,
                    'map_lng' => $val->map_lng,
                    'map_lat' => $val->map_lat,
                    'status' => $val->status,
                    'sort' => (int)$val->sort,
                    'resold_sort' => $resold_sort,
                    'imagecount' => $data['imagecount'],
                    'deleted' => (int)$val->deleted,
                    'created' => (int)$val->created,
                    'updated' => (int)$val->updated,
                    'age'=> date('Y',(int)$val->open_time),
                    'esf_num'=>$resoldPlot?(int)$resoldPlot->esf_num:0,
                    'zf_num'=>$resoldPlot?(int)$resoldPlot->zf_num:0,
                    'esf_rate'=>PlotExt::PlotRate($val)['lastMouthP'],
                    'esf_price'=>$val->avg_esf?$val->avg_esf->price:0,
                    'recommend' => (int)$val->recommend,
                    'bedroom' => implode(',',$bedroom),
                    'school_id' => implode(',',$schoolId),
                    'school_type' => implode(',',array_keys($schoolType)),
                    'price_mark' => $val->price_mark,
                    'record_name' => $record_name,
                    'size_min' => count($sizes)>0?floor(min($sizes)):0,
                    'size_max' => count($sizes)>0?ceil(max($sizes)):0,
                ));
            }

            $xs->closeBuffer();
            echo $val->id;
            echo "-plot(finish:".(($n++)*100)."/".$count.")\n";
            goto plotbegin;
        }
        $xs->endRebuild();
        $xs->flushIndex();
        echo 'finished';
    }

    /**
     * 资讯文章
     */
    public function actionArticle()
    {
        $xs = Yii::app()->search->house_article;
        $xs->stopRebuild();
        $xs->clean();
        $xs->beginRebuild();
        $k = 0;
        article:
        $sql = 'select * from article where id>'.(100*$k).' order by id asc limit 100;';
        $articleArr = Yii::app()->db->createCommand($sql)->queryAll();
        $article = ArticleExt::model()->populateRecords($articleArr);

        /*
        $article = ArticleExt::model()->findAll(array(
            'order' => 'id desc',
            'limit' => 100,
            'offset' => $k*100,
        ));
        */

        if(!empty($article))
        {
            $xs->openBuffer();
            foreach($article as $v)
            {
                $xs->add($v->attributes);
            }
            $xs->closeBuffer();
            echo 'article(page:'.$k++.')';
            goto article;
        }
        $xs->endRebuild();
        $xs->flushIndex();
        echo "article end\n";
    }

     /**
     * 知识库
     */
    public function actionBaike()
    {
        $xs = Yii::app()->search->house_baike;
        $xs->stopRebuild();
        $xs->clean();
        $xs->beginRebuild();
        $k = 0;
        baike:
        $baike = BaikeExt::model()->enabled()->findAll(array(
            'order' => 'id desc',
            'limit' => 100,
            'offset' => $k*100,
        ));

        if(!empty($baike))
        {
            $xs->openBuffer();
            foreach($baike as $v)
            {
                $xs->add($v->attributes);
            }
            $xs->closeBuffer();
            echo 'baike(page:'.$k++.')';
            goto baike;
        }
        $xs->endRebuild();
        $xs->flushIndex();
        echo "baike end\n";
    }

    /**
     * 问答
     */
    public function actionAsk()
    {
        $xs = Yii::app()->search->house_ask;
        $xs->stopRebuild();
        $xs->clean();
        $xs->beginRebuild();
        $k = 0;
        ask:
        $ask = AskExt::model()->findAll(array(
            'order' => 'id desc',
            'limit' => 100,
            'offset' => $k*100,
        ));

        if(!empty($ask))
        {
            $xs->openBuffer();
            foreach($ask as $v)
            {
                $xs->add($v->attributes);
            } ;
            $xs->closeBuffer();
            echo 'ask(page:'.$k++.')';
            goto ask;
        }
        $xs->endRebuild();
        $xs->flushIndex();
        echo "ask end\n";
    }

    /**
     * 问答
     */
    public function actionQg()
    {
        $xs = Yii::app()->search->house_qg;
        $xs->stopRebuild();
        $xs->clean();
        $xs->beginRebuild();
        $k = 0;
        qg:
        $qg = ResoldQgExt::model()->findAll(array(
            'order' => 'id desc',
            'limit' => 100,
            'offset' => $k*100,
        ));

        if(!empty($qg))
        {
            $xs->openBuffer();
            foreach($qg as $v)
            {
                $xs->add($v->attributes);
            } ;
            $xs->closeBuffer();
            echo 'qg(page:'.$k++.')';
            goto qg;
        }
        $xs->endRebuild();
        $xs->flushIndex();
        echo "qg end\n";
    }

    /**
     * 问答
     */
    public function actionQz()
    {
        $xs = Yii::app()->search->house_qz;
        $xs->stopRebuild();
        $xs->clean();
        $xs->beginRebuild();
        $k = 0;
        qz:
        $qz = ResoldQzExt::model()->findAll(array(
            'order' => 'id desc',
            'limit' => 100,
            'offset' => $k*100,
        ));

        if(!empty($qz))
        {
            $xs->openBuffer();
            foreach($qz as $v)
            {
                $xs->add($v->attributes);
            } ;
            $xs->closeBuffer();
            echo 'qz(page:'.$k++.')';
            goto qz;
        }
        $xs->endRebuild();
        $xs->flushIndex();
        echo "ask end\n";
    }

    /**
     * 问答
     */
    public function actionStaff()
    {
        $xs = Yii::app()->search->house_staff;
        $xs->stopRebuild();
        $xs->clean();
        $xs->beginRebuild();
        $k = 0;
        qz:
        $t = $k * 100;
        $qz = Yii::app()->db->createCommand("select * from resold_staff where status=1 order by id desc limit $t,100")->queryAll();

        if(!empty($qz))
        {
            $xs->openBuffer();
            foreach($qz as $v)
            {
                $content = Yii::app()->db->createCommand('select p.content from resold_tariff_package p left join resold_staff_package s on s.pid=p.id where s.staff='.$v['id'])->queryScalar();
                $total_num = 10;
                if($content) {
                    $content = json_decode($content,true);
                    isset($content['total_num']) && $total_num = $content['total_num'];
                }
                $ar = [
                    'id'=>$v['id'],
                    'area'=>Yii::app()->db->createCommand('select area from resold_shop where id='.$v['sid'])->queryScalar(),
                    'street'=>Yii::app()->db->createCommand('select street from resold_shop where id='.$v['sid'])->queryScalar(),
                    'name'=>$v['name'],
                    'id_card'=>$v['id_card'],
                    'licence'=>$v['licence'],
                    'views'=>$v['views'],
                    'last_login'=>$v['last_login'],
                    'id_expire'=>$v['id_expire'],
                    'status'=>$v['status'],
                    'sid'=>$v['sid'],
                    'is_manager'=>$v['is_manager'],
                    'deleted'=>$v['deleted'],
                    'esf_num'=>Yii::app()->db->createCommand('select count(id) from resold_esf where sale_status=1 and deleted=0 and uid='.$v['uid'])->queryScalar(),
                    'zf_num'=>Yii::app()->db->createCommand('select count(id) from resold_zf where sale_status=1 and deleted=0 and uid='.$v['uid'])->queryScalar(),
                    'created'=>$v['created'],
                    'updated'=>$v['updated'],
                    'package_expire'=>Yii::app()->db->createCommand('select expire_time from resold_staff_package where staff='.$v['id'])->queryScalar(),
                    'package_num'=>$total_num,
                ];
                $xs->add($ar);
            } ;
            $xs->closeBuffer();
            echo 'qz(page:'.$k++.')';
            goto qz;
        }
        $xs->endRebuild();
        $xs->flushIndex();
        echo "ask end\n";
    }



    public function actionHouseToSid()
    {
        $k = 0;
        $allShops = Yii::app()->db->createCommand("select id,old_id from resold_shop")->queryAll();
        foreach ($allShops as $key => $value) {
            Yii::app()->db->createCommand("update resold_esf,house2sid set resold_esf.sid=".$value['id']." where house2sid.id>628848 and resold_esf.old_id=house2sid.id and house2sid.sid = ".$value['old_id'])->execute();
            // foreach (Yii::app()->db->createCommand("select id,sid from house2sid where id>628848 sid=".$value['old_id'])->queryAll() as $k => $v) {
            //      Yii::app()->db->createCommand("update resold_esf set sid=".$value['id']." where old_id=".$v['id'])->execute();
            // }
             echo '(page:'.$k++.')';
        }

        echo 'finished';

    }

    public function actionZfToSid()
    {
        $k = 0;
        $allShops = Yii::app()->db->createCommand("select id,old_id from resold_shop")->queryAll();
        foreach ($allShops as $key => $value) {
            Yii::app()->db->createCommand("update resold_zf,zf2sid set resold_zf.sid=".$value['id']." where zf2sid.id>628117 and resold_zf.old_id=zf2sid.id and zf2sid.sid = ".$value['old_id'])->execute();
            // foreach (Yii::app()->db->createCommand("select id,sid from house2sid where id>628848 sid=".$value['old_id'])->queryAll() as $k => $v) {
            //      Yii::app()->db->createCommand("update resold_esf set sid=".$value['id']." where old_id=".$v['id'])->execute();
            // }
             echo '(page:'.$k++.')';
        }

        echo 'finished';

    }

    /**
     * 问答
     */
    public function actionShop()
    {
        $xs = Yii::app()->search->house_shop;
        $xs->stopRebuild();
        $xs->clean();
        $xs->beginRebuild();
        $k = 0;
        qz:
        $t = $k * 100;
        $qz = Yii::app()->db->createCommand("select * from resold_shop where status=1 order by id desc limit $t,100")->queryAll();

        if(!empty($qz))
        {
            $xs->openBuffer();
            foreach($qz as $v)
            {
                $ar = [
                    'id'=>$v['id'],
                    'area'=>(int)$v['area'],
                    'street'=>(int)$v['street'],
                    'name'=>$v['name'],
                    'sort'=>$v['sort'],
                    'pinyin'=>$v['pinyin'],
                    'status'=>$v['status'],
                    'deleted'=>$v['deleted'],
                    'esf_num'=>Yii::app()->db->createCommand('select count(id) from resold_esf where sale_status=1 and expire_time>'.time().' and deleted=0 and sid='.$v['id'])->queryScalar(),
                    'zf_num'=>Yii::app()->db->createCommand('select count(id) from resold_zf where sale_status=1 and expire_time>'.time().' and deleted=0 and sid='.$v['id'])->queryScalar(),
                    'created'=>$v['created'],
                    'updated'=>$v['updated'],
                    'staff_num'=>Yii::app()->db->createCommand("select count(s.id) from resold_staff s left join resold_staff_package p on s.id=p.staff where s.status=1 and s.id_expire>".time()." and p.expire_time>".time()." and s.sid=".$v['id'])->queryScalar(),

                ];
                $xs->add($ar);
            } ;
            $xs->closeBuffer();
            echo 'qz(page:'.$k++.')';
            goto qz;
        }
        $xs->endRebuild();
        $xs->flushIndex();
        echo "ask end\n";
    }

    /**
     * 问答
     */
    public function actionEsf()
    {
        $xs = Yii::app()->search->house_esf;
        $xs->stopRebuild();
        $xs->clean();
        $xs->beginRebuild();
        $k = 0;
        esf:
        $t = $k * 100;
        $esf = Yii::app()->db->createCommand("select * from resold_esf where sale_status=1 and expire_time>".time()." and created>1420041600 order by id asc limit $t,100")->queryAll();

        if(!empty($esf)  && $k <= 200)
        {
            $xs->openBuffer();
            foreach($esf as $v)
            {
                $schoolrels = SchoolPlotRelExt::model()->findAll(['condition'=>'hid=:hid','params'=>[':hid'=>$v['hid']]]);
                $school = '';
                if($schoolrels) {
                    foreach ($schoolrels as $key => $value) {
                        $school .= $value->sid.',';
                    }
                    $school = trim($school,',');
                }
                $data_conf = CJSON::decode($v['data_conf'],true);

                $ar = [
                    'id'=>$v['id'],
                    'title'=>$v['title'],
                    'content'=>$v['content'],
                    'area'=>(int)$v['area'],
                    'street'=>(int)$v['street'],
                    'plot_name'=>$v['plot_name'],
                    'address'=>$v['address'],
                    'hid'=>(int)$v['hid'],
                    'image'=>$v['image'],
                    'image_count'=>(int)$v['image_count'],
                    'price'=>(int)$v['price'],
                    'ave_price'=>(int)$v['ave_price'],
                    'source'=>$v['source']==2?$v['source']:1,
                    'size'=>(int)$v['size'],
                    'age'=>$v['age'],
                    'floor'=>$v['floor'],
                    'total_floor'=>(int)$v['total_floor'],
                    'towards'=>$v['towards'],
                    'decoration'=>$v['decoration'],
                    'category'=>$v['category'],
                    'bedroom'=>$v['bedroom'],
                    'livingroom'=>$v['livingroom'],
                    'bathroom'=>$v['bathroom'],
                    'cookroom'=>$v['cookroom'],
                    'sort'=>(int)$v['sort'],
                    'sale_time'=>(int)$v['sale_time'],
                    'expire_time'=>(int)$v['expire_time'],
                    'status'=>$v['status'],
                    'contacted'=>$v['contacted'],
                    'sale_status'=>$v['sale_status'],
                    'top'=>$v['top'],
                    'hurry'=>(int)$v['hurry'],
                    'refresh_time'=>(int)$v['refresh_time'],
                    'hits'=>$v['hits'],
                    'ip'=>$v['ip'],
                    'sid'=>$v['sid'],
                    'uid'=>$v['uid'],
                    'account'=>$v['account'],
                    'username'=>$v['username'],
                    'phone'=>$v['phone'],
                    'year'=>$v['year'],
                    'month'=>$v['month'],
                    'day'=>$v['day'],
                    'appoint_time'=>$v['appoint_time'],
                    'wuye_fee'=>$v['wuye_fee'],
                    'sex'=>$v['sex'],
                    'deleted'=>$v['deleted'],
                    'updated'=>$v['updated'],
                    'created'=>$v['created'],
                    'school'=>$school,
                    'recommend'=>$v['recommend']?$v['recommend']:0,
                    'tag'=>$data_conf['tags']?implode(',',$data_conf['tags']):'',
                ];
                $xs->add($ar);
            } ;
            $xs->closeBuffer();
            echo 'esf(page:'.$k++.')';
            goto esf;
        }
        $xs->endRebuild();
        $xs->flushIndex();
        echo "esf end\n";
    }

    /**
     * 问答
     */
    public function actionZf()
    {
        $xs = Yii::app()->search->house_zf;
        $xs->stopRebuild();
        $xs->clean();
        $xs->beginRebuild();
        $k = 0;
        zf:
        $t = $k * 100;
        $zf = Yii::app()->db->createCommand("select * from resold_zf where sale_status=1 and expire_time>".time()." order by id asc limit $t,100")->queryAll();

        if(!empty($zf) && $k<=500)
        {
            $xs->openBuffer();
            foreach($zf as $v)
            {
                $schoolrels = SchoolPlotRelExt::model()->findAll(['condition'=>'hid=:hid','params'=>[':hid'=>$v['hid']]]);
                $school = '';
                if($schoolrels) {
                    foreach ($schoolrels as $key => $value) {
                        $school .= $value->sid.',';
                    }
                    $school = trim($school,',');
                }
                $data_conf = CJSON::decode($v['data_conf'],true);
                $ar = [
                    'id' => $v['id'],
                    'title' => $v['title'],
                    'area' => (int)$v['area'],
                    'street' => (int)$v['street'],
                    'hid' => (int)$v['hid'],
                    'plot_name' => $v['plot_name'],
                    'address' => $v['address'],
                    'image' => $v['image'],
                    'image_count' => (int)$v['image_count'],
                    'price' => (int)$v['price'],
                    'source'=>$v['source']==2?$v['source']:1,
                    'size' => (int)$v['size'],
                    'pay_type' => $v['pay_type'],
                    'rent_type' => $v['rent_type'],
                    'floor' => $v['floor'],
                    'total_floor' => (int)$v['total_floor'],
                    'towards' => $v['towards'],
                    'decoration' => $v['decoration'],
                    'category' => $v['category'],
                    'bedroom' => $v['bedroom'],
                    'livingroom' => $v['livingroom'],
                    'bathroom' => $v['bathroom'],
                    'cookroom' => $v['cookroom'],
                    'content' => $v['content'],
                    'sort' => (int)$v['sort'],
                    'sale_time' => (int)$v['sale_time'],
                    'expire_time' => (int)$v['expire_time'],
                    'status' => $v['status'],
                    'contacted' => $v['contacted'],
                    'sale_status' => $v['sale_status'],
                    'top' => $v['top'],
                    'recommend' => $v['recommend']?$v['recommend']:0,
                    'hurry' => $v['hurry'],
                    'hits' => $v['hits'],
                    'ip' => $v['ip'],
                    'sid' => $v['sid'],
                    'uid' => $v['uid'],
                    'account' => $v['account'],
                    'username' => $v['username'],
                    'phone' => $v['phone'],
                    'year' => $v['year'],
                    'month' => $v['month'],
                    'day' => $v['day'],
                    'appoint_time' => $v['appoint_time'],
                    'wuye_fee' => $v['wuye_fee'],
                    'sex' => $v['sex'],
                    'age' => $v['age'],
                    'deleted' => $v['deleted'],
                    'updated' => $v['updated'],
                    'created' => $v['created'],
                    'refresh_time' => $v['refresh_time'],
                    'school'=>$school,
                    'esfzfzztype' => isset($data_conf['esfzfzztype']) ? $data_conf['esfzfzztype'] : "",
                    'esfzfsptype' => isset($data_conf['esfzfsptype']) ? $data_conf['esfzfsptype'] : "",
                    'zfspkjyxm' => isset($data_conf['zfspkjyxm']) ? implode(",", $data_conf['zfspkjyxm']) : "",
                    'esfzfxzltype' => isset($data_conf['esfzfxzltype']) ? $data_conf['esfzfxzltype'] : "",
                    'esffloorcate' => isset($data_conf['esffloorcate']) ? $data_conf['esffloorcate'] : "",
                    'resoldface' => isset($data_conf['resoldface']) ? $data_conf['resoldface'] : "",
                    'zfzzpt' => isset($data_conf['zfzzpt']) && !empty($data_conf['zfzzpt']) ? implode(",", $data_conf['zfzzpt']) : "",
                    'zfzzts' => isset($data_conf['zfzzts']) && !empty($data_conf['zfzzts']) ? implode(",", $data_conf['zfzzts']) : "",
                    'zfsppt' => isset($data_conf['zfsppt']) && !empty($data_conf['zfsppt']) ? implode(",", $data_conf['zfsppt']) : "",
                    'zfspts' => isset($data_conf['zfspts']) && !empty($data_conf['zfspts']) ? implode(",", $data_conf['zfspts']) : "",
                    'zfxzlts' => isset($data_conf['zfxzlts']) && !empty($data_conf['zfxzlts']) ? implode(",", $data_conf['zfxzlts']) : "",
                    'zfxzlpt' => isset($data_conf['zfxzlpt']) && !empty($data_conf['zfxzlpt']) ? implode(",", $data_conf['zfxzlpt']) : "",
                    'zfxzllevel'=>isset($data_conf['zfxzllevel'])? $data_conf['zfxzllevel'] : ""
                ];
                $xs->add($ar);
            } ;
            $xs->closeBuffer();
            echo 'zf(page:'.$k++.')';
            goto zf;
        }
        $xs->endRebuild();
        $xs->flushIndex();
        echo "ask end\n";
    }

}
?>
