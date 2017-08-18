<?php
/**
 * 更新脚本
 * ========================更新记录====================
 * v20151223 合并资讯，一旦执行，无法逆操作
 * @author tivon
 * @date 2015-10-09
 */
class UpdateCommand extends CConsoleCommand
{
    public function actionHuxingtu($cateid)
    {
        $offset = $count = 0;
        begin:
        $criteria = new CDbCriteria(array(
            'limit' => 50,
            'offset' => $offset++ * 50,
            'condition' => 'type='.$cateid,
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
                if(PlotHouseTypeExt::model()->exists('hid=:hid and image=:url',[':hid'=>$row->hid,':url'=>$row->url])) {
                    continue;
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
                    echo $msg."\n";
                }
                $count++;
            }
            echo "处理完成".$count."条\n";
            goto begin;
        }
        echo "完成\n";
    }

    public function actionDingdan()
    {
        if($arr = Yii::app()->cache->get(CacheExt::CACHE_MAP_KEY)) {
            foreach($arr as $id=>$data) {
                Yii::app()->cache->delete($id);
            }
        }
        $page = 0;
        begin:
        $criteria = new CDbCriteria([
            'select' => 'id,spm_b,spm_c',
            'condition' => 'spm_c>0',
            'limit' => 30,
            'offset' => $page * 30,
            'order' => 'id asc',
        ]);
        $orders = OrderExt::model()->findAll($criteria);
        if($orders) {
            foreach($orders as $order) {
                foreach(OrderExt::$type as $modelName=>$types) {
                    if(in_array($order->spm_b, $types)) {
                        OrderExt::model()->updateByPk($order->id, ['spm_d'=>$modelName]);
                    }
                }
            }
            echo $page++."\n";
            goto begin;
        }
        echo "完成\n";
        Yii::app()->end();
    }

    public function actionRun($v)
    {
        if($v=='tuijianwei') {
            $map = [
                'syxfzxkp' => ['name' => '新盘-近期开盘(275x200)'],
                'syzxkpggw' => ['name' => '近期开盘广告位(226x90)'],
                'syzxesfggw' => ['name' => '新近二手房广告位(226x90)'],
            ];
            foreach($map as $pinyin => $v) {
                RecomCateExt::model()->updateAll($v, 'pinyin=:pinyin', [':pinyin'=>$pinyin]);
            }
            echo "完成\n";
            Yii::app()->end();
        }
        if($v=='shanchuhuxingtutupian') {
            $sql = "DELETE `t1` FROM `plot_img` `t1`,`plot_house_type` `t2` WHERE `t1`.`url`=`t2`.`image` and `t2`.`hid`=`t1`.`hid`;";
            $transaction = Yii::app()->db->beginTransaction();
            try{
                Yii::app()->db->createCommand($sql)->execute();
                $transaction->commit();
            }catch(Exception $e){
                $transaction->rollback();
                var_dump($e->getMessage());
            }
            echo "完成\n";
        }
        die;
        if($v=='shanchuhuxingtu') {
            $model = TagExt::model()->find('name="户型图" and cate="xcfl"');
            if($model) $model->delete();
            echo "\n操作成功\n";
        }
        die;
        if($v=='biaoqian') {
            $criteria = new CDbCriteria();
            $criteria->addInCondition('cate', ['xinfangjiage','xinfanghuxing']);
            TagExt::model()->deleteAll($criteria);
            //转移价格标签
            $prices = PlotPricetagExt::model()->findAll();

            $transaction = Yii::app()->db->beginTransaction();
            try
            {
                $hxs = [1=>'一居室',2=>'二居室',3=>'三居室',4=>'四居室',5=>'五居室',6=>'五居以上'];
                for($i=1; $i<=6; $i++) {
                    $model = new TagExt;
                    $model->name = $hxs[$i];
                    $model->cate = 'xinfanghuxing';
                    $model->min = $i;
                    $model->max = $i==6 ? 0 : $i;
                    $model->sort = $i;
                    $model->save();
                }
                foreach($prices as $price) {
                    $model = new TagExt;
                    $model->name = $price->title;
                    $model->cate = 'xinfangjiage';
                    $model->min = $price->min;
                    $model->max = $price->max;
                    $model->sort = $price->sort;
                    $model->save();
                }
                $transaction->commit();
                echo "\n操作成功\n";
            }
            catch(Exception $e)
            {
                $transaction->rollback();
                echo $e->getMessage();
            }
            die;
        }
        if($v=='guanjia'){
            Yii::app()->db->createCommand('DELETE FROM `staff_extra`;ALTER TABLE `staff_extra` AUTO_INCREMENT=1')->execute();
            $staffs = StaffExt::model()->findAll();
            foreach($staffs as $staff){
                $staff->save();
            }
            echo "finished\n";
        }
        die;
        if($v=='ceshi') {
            Yii::import('application.commands.V2');
            $v2 = new V2;
            $v2->convertHouseType();
        }
        die;
        if($v=='20160224')
        {
            $data = PlotPricetrendExt::model()->findAll();
            foreach($data as $v)
            {
                $year = $v->month==1 ? $v->year-1 : $v->year;
                $month = $v->month==1 ? 12 : $v->month-1;
                $time = strtotime($year.'-'.$month);
                PlotPricetrendExt::model()->updateByPk($v->id, array(
                    'year' => $year,
                    'month' => $month,
                    'time' => $time,
                ));
            }
            echo 'finished';die;
        }
        if($v=='schoolpic'){
            //拥有图片的表：article\plot\plot_img\school
            $page = 0;
            school:
            $schools = SchoolExt::model()->findAll(array(
                'limit' => 100,
                'offset' => $page*100,
                'order' => 'id asc',
            ));
            if(!empty($schools)){
                foreach($schools as $school){
                    //处理异常
                    try
                    {
                        //文章抠图抓取
                        $file = Yii::app()->file;
                        if(!empty($school->image)&&strpos($school->image,'http')!==false)
                        {
                            $url = $file->fetch($school->image);
                            if(empty($url))
                                $url = $school->image;
                            $school->image = $url;
                        }
                        $pics = array();
                        foreach($school->pic as $v){
                            if(!empty($v)&&strpos($v,'http')!==false){
                                $url = $file->fetch($v);
                                if(empty($url))
                                    $url = $v;
                                $v = $url;
                            }
                            $pics[] = $v;
                        }
                        $school->pic = $pics;
                        $school->save();
                        // print_r($article->errors);die;
                    }
                    catch(Exception $e)//抠图失败的话就跳过此篇文章抓取
                    {
                        continue;
                    }
                }
                echo $page++."\n";
                goto school;
            }
        }

        if($v=='plotimg'){
            //拥有图片的表：article\plot\plot_img\school
            $page = 0;
            plotimg:
            $imgs = PlotImgExt::model()->findAll(array(
                'limit' => 100,
                'offset' => $page*100,
                'order' => 'id asc',
            ));
            if(!empty($imgs)){
                foreach($imgs as $img){
                    //处理异常
                    try
                    {
                        //文章抠图抓取
                        $file = Yii::app()->file;
                        if(!empty($img->url)&&strpos($img->url,'http')!==false)
                        {
                            $imgurl = $file->fetch($img->url);
                            if(empty($imgurl))
                                $imgurl = $img->url;
                            $img->url = $imgurl;
                        }
                        $img->save();
                        // print_r($article->errors);die;
                    }
                    catch(Exception $e)//抠图失败的话就跳过此篇文章抓取
                    {
                        continue;
                    }
                }
                echo $page++."\n";
                goto plotimg;
            }
        }

        if($v=='plotpic'){
            //拥有图片的表：article\plot\plot_img\school
            $page = 0;
            plot:
            $plots = PlotExt::model()->findAll(array(
                'limit' => 100,
                'offset' => $page*100,
                'order' => 'id asc',
            ));
            if(!empty($plots)){
                foreach($plots as $plot){
                    //处理异常
                    try
                    {
                        //文章抠图抓取
                        $file = Yii::app()->file;
                        if(!empty($plot->image)&&strpos($plot->image,'http')!==false)
                        {
                            $img = $file->fetch($plot->image);
                            if(empty($img))
                                $img = $plot->image;
                            $plot->image = $img;
                        }
                        $plot->save();
                        // print_r($article->errors);die;
                    }
                    catch(Exception $e)//抠图失败的话就跳过此篇文章抓取
                    {
                        continue;
                    }
                }
                echo $page++."\n";
                goto plot;
            }
        }

        if($v=='plotspecialpic'){
            //拥有图片的表：article\plot\plot_img\school
            $page = 0;
            begin:
            $rows = PlotSpecialExt::model()->findAll(array(
                'limit' => 100,
                'offset' => $page*100,
                'order' => 'id asc',
            ));
            if(!empty($rows)){
                foreach($rows as $row){
                    //处理异常
                    try
                    {
                        //文章抠图抓取
                        $file = Yii::app()->file;
                        if(!empty($row->image)&&strpos($row->image,'http')!==false)
                        {
                            $img = $file->fetch($row->image);
                            if(empty($img))
                                $img = $row->image;
                            $row->image = $img;
                        }
                        $row->save();
                        // print_r($article->errors);die;
                    }
                    catch(Exception $e)//抠图失败的话就跳过此篇文章抓取
                    {
                        continue;
                    }
                }
                echo $page++."\n";
                goto begin;
            }
        }

        if($v=='articlepic'){
            //拥有图片的表：article\plot\plot_img\school
            $page = 0;
            article:
            $articles = ArticleExt::model()->findAll(array(
                'limit' => 100,
                'offset' => $page*100,
                'order' => 'id asc',
            ));
            if(!empty($articles)){
                foreach($articles as $article){
                    //处理异常
                    try
                    {
                        //文章抠图抓取
                        $file = Yii::app()->file;
                        preg_match_all("/\<img.*?src\=[\"\'](.*?)[\"\'][^>]*>/i", $article->content, $match);
                        $oldPics = $match[1];
                        $newPics = array();
                        if(is_array($oldPics))
                        {
                            foreach($oldPics as $k=>$picUrl)
                            {
                                if(strpos($picUrl,$file->host)!==false) continue;
                                $url = $file->fetch($picUrl);
                                if(!empty($url))
                                {
                                    $url = ImageTools::fixImage($url);
                                    // echo $picUrl.'---'.$url;die;
                                }
                                else
                                    $url = $picUrl;
                                $article->content = str_replace($picUrl, $url, $article->content);//将文章内的图片链接替换成新抓取的
                            }
                        }
                        if(!empty($article->image)&&strpos($article->image,'http')!==false)
                        {
                            $url = $file->fetch($article->image);
                            if(empty($url))
                                $url = $article->image;
                            $article->image = $url;
                        }
                        ArticleExt::model()->updateByPk($article->id, array(
                            'content' => $article->content
                        ));
                        // print_r($article->errors);die;
                    }
                    catch(Exception $e)//抠图失败的话就跳过此篇文章抓取
                    {
                        continue;
                    }
                }
                echo $page++."\n";
                goto article;
            }
            //article end;
        }
        if(false&&$v=='20151223'){
            /**
             * 今日聚焦1（热点关注17、娱乐地产5）
             * 楼盘动态3（楼市速递4、楼盘动态）
             * 新房导购6（每日成交21、土地成交20、精英访谈19、看房团18、探地报告15、买房技巧14、区域地段12、新房导购）
             * 探盘日记22（楼盘档案书16、景观报道13、工程进度11、最美样板间10、户型解析9、看房报告8、楼盘测评7、买房日记2）
             */
            $transaction = Yii::app()->db->beginTransaction();
            try{
                //合并到今日聚焦ID 1
                $sql1 = "update `article` set `cid`=1 where `cid`IN(17,5)";
                Yii::app()->db->createCommand($sql1)->execute();
                //合并到楼盘动态ID 3
                $sql2 = "update `article` set `cid`=3 where `cid`IN(4)";
                Yii::app()->db->createCommand($sql2)->execute();
                //合并到新房导购ID 6
                $sql3 = "update `article` set `cid`=6 where `cid`IN(21,20,19,18,15,14,12)";
                Yii::app()->db->createCommand($sql3)->execute();
                //合并到探盘日记22
                $sql4 = "update `article` set `cid`=22 where `cid`IN(16,13,11,10,9,8,7,2)";
                Yii::app()->db->createCommand($sql4)->execute();
                $transaction->commit();
            }catch(Exception $e){
                $transaction->rollback();
            }
        }
        if(false&&$v=='20151209')
        {
            CacheExt::delete('siteconfig');
        }
    }

    /**
     * 去除recommend，保留原来的展示顺序，不可多次执行
     */
    public function actionResetSort()
    {
        $criteria1 = new CDbCriteria(array(
            'limit'=>255,
            'order' => 'recommend desc,sort desc,open_time desc',
        ));
        $plots = PlotExt::model()->normal()->isNew()->findAll($criteria1);
        $plotSort=array();
        foreach ($plots as $k=>$plot){
            if($k>=255) break;
            $plotSort[$plot->id]=255-$k;
        }
        $ids = implode(',', array_keys($plotSort));
        $sql = "UPDATE plot SET sort = CASE id ";
        foreach ($plotSort as $id => $ordinal) {
            $sql .= sprintf("WHEN %d THEN %d ", $id, $ordinal);
        }
        $sql .= "END WHERE id IN ($ids)";
        $connection=Yii::app()->db;
        $command=$connection->createCommand($sql);
        $command->execute();
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
}
