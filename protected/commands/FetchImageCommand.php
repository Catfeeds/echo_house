<?php
/**
 * 抓取图片上云存储
 * 两种形式：
 * 1. 导数据导入过来的是带http的完整地址，则直接抓取生成新的key。
 * 2. 导数据导入过来的只是相对地址，则作为七牛的key，配置七牛镜像源地址，把这些原图片都访问一遍则可以了。
 */
class FetchImageCommand extends CConsoleCommand
{
    /**
     * 访问图片
     * @param  string $url 相对地址
     */
    private function accessImage($url)
    {
        if(empty($url)) {
            echo "url empty";
            return false;
        }
        if(strpos($url, 'http')===false) {
            $url = ImageTools::fixImage($url);
        }
        try {
            $header = get_headers($url);
            return !(strpos($header[0],'200')===false);
        } catch(Exception $e) {
            echo $e->getMessage() . "\n";
            return false;
        }
    }

    public function actionPlot($relativeUrl=false)
    {
        $page = 0;
        begin:
        $criteria = new CDbCriteria([
            'select' => 'id,image',
            'order' => 'id asc',
            'limit' => 50,
            'offset' => $page * 50,
        ]);
        $plot = PlotExt::model()->findAll($criteria);
        if($plot) {
            foreach($plot as $v) {
                if($relativeUrl) {
                    echo $v->id . ($this->accessImage($v->image) ? 'ok' : 'faild') . "\n";
                }elseif(strpos($v->image, 'http')!==false) {
                    echo $v->id."抓取中\n";
                    $key = Yii::app()->file->fetch($v->image);
                    if($key) {
                        PlotExt::model()->updateByPk($v->id, ['image'=>$key]);
                    } else {
                        echo $v->id."抓取失败\n";
                    }
                }
            }
            echo $page++."---------------\n";
            goto begin;
        }
        echo "end\n";
    }

    public function actionArticle($relativeUrl=false)
    {
        $page = 0;
        article:
        $articles = ArticleExt::model()->findAll(array(
            'limit' => 50,
            'offset' => $page*50,
            'order' => 'id asc',
        ));
        if(!empty($articles)){
            foreach($articles as $k=>$article){
                if($relativeUrl) {
                    echo $article->id . ($this->accessImage($article->image) ? 'ok' : 'faild') . "\n";
                } else {
                    //处理异常
                    try
                    {
                        //文章抠图抓取
                        $file = Yii::app()->file;
                        preg_match_all("/\<img.*?src\=[\"\'](.*?)[\"\'][^>]*>/i", $article->content, $match);
                        $oldPics = $match[1];
                        $newPics = array();
                        $update = false;
                        if(is_array($oldPics))
                        {
                            foreach($oldPics as $k=>$picUrl)
                            {
                                if(strpos($picUrl,$file->host)!==false) continue;
                                $url = $file->fetch($picUrl);
                                if(!empty($url))
                                {
                                    $url = ImageTools::fixImage($url);
                                    $update = true;
                                    // echo $picUrl.'---'.$url;die;
                                }
                                else
                                    $url = $picUrl;
                                $article->content = str_replace($picUrl, $url, $article->content);//将文章内的图片链接替换成新抓取的
                            }
                        }
                        if(!empty($article->image)&&strpos($article->image,'http')!==false&&strpos($article->image,$file->host)===false)
                        {
                            $url = $file->fetch($article->image);
                            if(!empty($url) && is_string($url)) {
                                $article->image = $url;
                                $update = true;
                            }
                        }
                        if($update) {
                            ArticleExt::model()->updateByPk($article->id, array(
                                'image' => $article->image,
                                'content' => $article->content
                            ));
                        }
                        // print_r($article->errors);die;
                    }
                    catch(Exception $e)//抠图失败的话就跳过此篇文章抓取
                    {
                        echo "id:".$article->id."faild\n";
                    }
                }
                unset($article);
                unset($articles[$k]);
            }
            echo $page++."\n";
            unset($articles);
            goto article;
        }
        //article end;
        echo 'finished';
    }

    public function actionPlotImg($relativeUrl=false)
    {
        $page = 0;
        begin:
        $criteria = new CDbCriteria([
            'select' => 'id,url',
            'order' => 'id asc',
            'limit' => 50,
            'offset' => $page * 50,
        ]);
        $plotImgs = PlotImgExt::model()->findAll($criteria);
        if($plotImgs) {
            foreach($plotImgs as $v) {
                if($relativeUrl) {
                    echo $v->id . ($this->accessImage($v->url) ? 'ok' : 'faild') . "\n";
                }elseif(strpos($v->url, 'http')!==false) {
                    echo $v->id."抓取中\n";
                    $key = Yii::app()->file->fetch($v->url);
                    if($key) {
                        PlotImgExt::model()->updateByPk($v->id, ['url'=>$key]);
                    } else {
                        echo $v->id."抓取失败\n";
                    }
                }
            }
            echo $page++."---------------\n";
            goto begin;
        }
        echo "end\n";
    }

    public function actionSchool($relativeUrl = false)
    {
        $page = 0;
        begin:
        $criteria = new CDbCriteria([
            'select' => 'id,image,pic',
            'order' => 'id asc',
            'limit' => 50,
            'offset' => $page * 50,
        ]);
        $school = SchoolExt::model()->findAll($criteria);
        if($school) {
            foreach($school as $v) {
                if(is_array($v->pic)) {
                    foreach($v->pic as $k=>$pic) {
                        if($relativeUrl) {
                            echo $v->id . ($this->accessImage($pic) ? 'ok' : 'faild') . "\n";
                        }elseif(strpos($pic, 'http')!==false) {
                            $key = Yii::app()->file->fetch($pic);
                            if($key) {
                                $v->pic[$k] = $key;
                                if($pic==$v->image) {
                                    $v->image = $key;
                                }
                                $v->save();
                            }
                        }
                    }
                }


            }
            echo $page++."---------------\n";
            goto begin;
        }
        echo "end\n";
    }

    public function actionHouseType($relativeUrl=false)
    {
        $page = 0;
        begin:
        $criteria = new CDbCriteria([
            'select' => 'id,image',
            'order' => 'id asc',
            'limit' => 50,
            'offset' => $page * 50,
        ]);
        $houseTypes = PlotHouseTypeExt::model()->findAll($criteria);
        if($houseTypes) {
            foreach($houseTypes as $v) {
                if($relativeUrl) {
                    echo $v->id . ($this->accessImage($v->image) ? 'ok' : 'faild') . "\n";
                }elseif(strpos($v->image, 'http')!==false) {
                    echo $v->id."抓取中\n";
                    $key = Yii::app()->file->fetch($v->image);
                    if($key) {
                        PlotHouseTypeExt::model()->updateByPk($v->id, ['image'=>$key]);
                    } else {
                        echo $v->id."抓取失败\n";
                    }
                }
            }
            echo $page++."---------------\n";
            goto begin;
        }
        echo "end\n";
    }

    /**
     * [actionFetchResold 二手房图片访问]
     * @return [type] [description]
     */
    public function actionFetchResold()
    {
        $tables = ['resold_image'=>['url'],'resold_staff'=>['image','id_card','licence'],'resold_shop'=>['image'],'resold_esf'=>['image'],'resold_zf'=>['image'],'resold_shop_img'=>['url']];
        foreach ($tables as $key => $value) {
            $t = 0;
            $sql = "select id, ";
            $where = " where ";
            foreach ($value as $item) {
                $sql .= "$item,";
                $where .= " $item!='' and";
            }
            $sql = trim($sql,',');
            $sql .= " from $key ".$where;
            while($rows = Yii::app()->db->createCommand($sql." 1=1 limit $t,100")->queryAll()) {
                foreach ($rows as $row) {
                    foreach ($row as $k => $v) {
                        if($k != 'id') {
                            if(!$v) 
                                continue 2;
                            try{
                                if(strstr($v,'http') || strstr($v,'https')) {
                                    $newUrl = Yii::app()->file->fetch($v);
                                    $newUrl && Yii::app()->db->createCommand("update $key set $k='$newUrl' where id=".$row['id'])->execute();
                                } else {
                                    $this->accessImage($v);
                                }
                            }  catch(Exception $e) {
                                echo $e->getMessage() . "\n";
                            }
                                
                        }   
                    }  
                }
                $t += 100;
                echo ($t-100)."****************************\n";
            }
            echo $key." finished\n";
           
        }
        echo "end\n";
    }

    /**
     * [actionFetchResold 二手房图片访问]
     * @return [type] [description]
     */
    // public function actionFetchResold2()
    // {
    //     $tables = ['resold_staff'=>['image','id_card','licence'],'resold_shop'=>['image'],'resold_esf'=>['image'],'resold_zf'=>['image'],'resold_shop_img'=>['url']];
    //     foreach ($tables as $key => $value) {
    //         $t = 0;
    //         $sql = "select id, ";
    //         $where = " where ";
    //         foreach ($value as $item) {
    //             $sql .= "$item,";
    //             $where .= " $item!='' and";
    //         }
    //         $sql = trim($sql,',');
    //         $sql .= " from $key ".$where;
    //         while($rows = Yii::app()->db->createCommand($sql." 1=1 limit $t,100")->queryAll()) {
    //             foreach ($rows as $row) {
    //                 foreach ($row as $k => $v) {
    //                     if($k != 'id') {
    //                         if(!$v) 
    //                             continue 2;
    //                         try{
    //                             if(strstr($v,'http') || strstr($v,'https')) {
    //                                 $newUrl = Yii::app()->file->fetch($v);
    //                                 $newUrl && Yii::app()->db->createCommand("update $key set $k='$newUrl' where id=".$row['id'])->execute();
    //                             } else {
    //                                 $this->accessImage($v);
    //                             }
    //                         }  catch(Exception $e) {
    //                             echo $e->getMessage() . "\n";
    //                         }
    //                     }   
    //                 }  
    //             }
    //             $t += 100;
    //             echo ($t-100)."****************************\n";
    //         }
    //         echo $key." finished\n";
           
    //     }
    //     echo "end\n";
    // }
}
