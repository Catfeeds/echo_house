<?php
/**
 * 数据迁移脚本
 * @author tivon
 * @date 2015-10-09
 */
class ImportCommand extends CConsoleCommand
{
    //用于计数的一些变量
    public $handled = 0;//已处理量
    public $failed = 0;//失败量
    //用于验证的一系列变量
    public $signature;
    public $nonce;
    public $timestamp;
    public $token;
    public $queryString;
    public $page = 1;
    public function init()
    {
        $this->token = md5(SM::urmConfig()->siteID());
        $this->timestamp = time();
        $this->nonce = rand(10000,99999);
        $tmpArr = array($this->timestamp, $this->nonce, $this->token);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $this->signature = sha1( $tmpStr );
        Yii::import('application.models_ext.siteSetting.*');

    }

    public function getQueryString()
    {
        return http_build_query(array('page'=>$this->page, 'signature'=>$this->signature, 'timestamp'=>$this->timestamp, 'nonce'=>$this->nonce, 'token'=>$this->token));
    }

    private function buildUrl($api)
    {
        return $api.(strpos($api,'?')===false ? '?' : '&').$this->getQueryString();
    }

    /**
     * 结尾信息
     */
    public function end()
    {
        echo "\n已处理".$this->handled."条,成功".($this->handled-$this->failed)."条,失败".$this->failed."条\n";
        echo "\nfinished\n";
    }

    /**
     * 区域分类（无翻页行为）
     */
    public function actionArea()
    {
        $api = SM::ImportConfig()->area();
        $r = HttpHelper::get($this->buildUrl($api));
        $content = $r['content'];
        $data = CJSON::decode($content);
        if(is_array($data) && !empty($data))
        {
            foreach($data as $row)
            {
                $this->handled++;
                if(!isset($row['id'])||!isset($row['parent'])||!isset($row['name']))
                {
                    $this->failed++;
                    echo "without must params:id,parent or name\n";
                    continue;
                }
                $row['old_id'] = $row['id'];
                unset($row['id']);
                $model = new AreaExt;
                $model->attributes = $row;
                $model->save();
                if($model->hasErrors())
                {
                    $this->failed++;
                    echo current(current($model->getErrors()));
                }
            }
            unset($data);
            //更新parent字段为新的id，若找不到新的父级则设自己为顶级
            $allData = AreaExt::model()->findAll('parent!=0');
            foreach($allData as $v)
            {
                $parent = AreaExt::model()->find('old_id=:parent', array(':parent'=>$v->parent));
                $v->parent = $parent ? $parent->id : 0;
                $v->save();
            }
        }
        $this->end();
    }

    /**
     * 导楼盘数据
     */
    public function actionPlot($savetag=false)
    {
        $api = SM::ImportConfig()->plot();
        begin:
        $r = HttpHelper::get($this->buildUrl($api));
        $content = $r['content'];
        $data = CJSON::decode($content);

        if(is_array($data) && !empty($data))
        {
            foreach($data as $row)
            {
                $this->handled++;
                if(!isset($row['id']) || !isset($row['title']))
                {
                    $this->failed++;
                    echo "without must params:id,title,area or street\n";
                    continue;
                }

                if(PlotExt::model()->exists('old_id='.$row['id'])){
                   continue;
                }

                $row['old_id'] = $row['id'];
                unset($row['id']);

                $area = AreaExt::model()->find('old_id=:id and parent=0', array(':id'=>$row['area']));
                if($area)
                    $row['area'] = $area->id;
                else
                {
                    echo $row['old_id']."new area not found\n";
                    $row['area'] = 0;
                    // continue;
                }
                $street = AreaExt::model()->find('old_id=:id and parent!=0', array(':id'=>$row['street']));
                if($street)
                    $row['street'] = $street->id;
                else
                {
                    // $this->failed++;
                    echo $row['old_id']."new street not found\n";
                    $row['street'] = 0;
                    // continue;
                }
                if(!isset($row['pinyin'])||empty($row['pinyin'])) $row['pinyin'] = Pinyin::get($row['title'])?Pinyin::get($row['title']):'nopinyin';

                $model = new PlotExt;
                $model->attributes = $row;
                $data_conf = array();
                foreach(PlotExt::$default_data_conf as $k=>$v)
                {
                    if(isset($row[$k]))
                    {
                        $data_conf[$k] = $row[$k];
                        unset($row[$k]);
                    }
                }
                $model->data_conf = $data_conf;
                $model->save();
                if($savetag) $model->saveTag();
                if($model->hasErrors())
                {
                    $this->failed++;
                    echo current(current($model->getErrors())).'id:'.$row['old_id']."\n";
                    continue;
                }

            }
            echo "\npage:{$this->page}\n";
            $this->page++;
            goto begin;
        }
        $this->end();
    }

    /**
     * 导楼盘交付时间，废弃
     */
    public function actionPlotDelivery()
    {
        $api = '';
        begin:
        $r = HttpHelper::get($this->buildUrl($api));
        $content = $r['content'];
        $data = CJSON::decode($content);

        if(is_array($data) && !empty($data))
        {
            foreach($data as $row)
            {
                $this->handled++;
                if(!isset($row['hid']) || !isset($row['delivery_time']))
                {
                    $this->failed++;
                    echo "without must params:hid or delivery_time\n";
                    continue;
                }
                $plot = PlotExt::model()->find('old_id=:hid', array(':hid'=>$row['hid']));
                if($plot)
                    $row['hid'] = $plot->id;
                else
                {
                    $this->failed++;
                    echo "new hid not found\n";
                    continue;
                }

                $model = new PlotDeliveryExt;
                $model->attributes = $row;
                $model->save();
                if($model->hasErrors())
                {
                    $this->failed++;
                    echo current(current($model->getErrors()))."\n";
                    continue;
                }
            }
            echo "\npage:{$this->page}\n";
            $this->page++;
            goto begin;
        }
        $this->end();
    }

    /**
     * 导楼盘优惠信息
     */
    public function actionPlotDiscount()
    {
        $api = SM::ImportConfig()->plotDiscount();
        begin:
        $r = HttpHelper::get($this->buildUrl($api));
        $content = $r['content'];
        $data = CJSON::decode($content);

        if( is_array($data) && !empty($data))
        {
            foreach($data as $row)
            {
                $this->handled++;
                if(!isset($row['hid']) || !isset($row['start']) || !isset($row['expire']))
                {
                    $this->failed++;
                    echo "without must params:hid,start or expire\n";
                    continue;
                }
                $plot = PlotExt::model()->find('old_id=:hid', array(':hid'=>$row['hid']));
                if($plot)
                    $row['hid'] = $plot->id;
                else
                {
                    $this->failed++;
                    echo "new hid not found\n";
                    continue;
                }

                $model = new PlotDiscountExt;
                $model->attributes = $row;
                $model->save();
                if($model->hasErrors())
                {
                    $this->failed++;
                    echo current(current($model->getErrors()))."\n";
                    continue;
                }
            }
            echo "\npage:{$this->page}\n";
            $this->page++;
            goto begin;
        }
        $this->end();
    }

    /**
     * 导楼盘图库
     */
    public function actionPlotImg()
    {
        $api = SM::ImportConfig()->plotImage();
        // echo $api;die;
        begin:
        $r = HttpHelper::get($this->buildUrl($api));
        $content = $r['content'];
        $data = CJSON::decode($content);

        if(is_array($data) && !empty($data))
        {
            foreach($data as $row)
            {
                $this->handled++;
                if(!isset($row['hid']) || !isset($row['type']) || !isset($row['url']))
                {
                    $this->failed++;
                    echo "without must params:hid,type or url\n";
                    continue;
                }
                $plot = PlotExt::model()->find('old_id=:hid', array(':hid'=>$row['hid']));
                if($plot)
                    $row['hid'] = $plot->id;
                else
                {
                    $this->failed++;
                    echo "new hid not found\n";
                    continue;
                }

                $model = new PlotImgExt;
                $model->attributes = $row;
                $model->save();
                if($model->hasErrors())
                {
                    $this->failed++;
                    echo current(current($model->getErrors()))."\n";
                    continue;
                }
            }
            echo "\npage:{$this->page}\n";
            $this->page++;
            goto begin;
        }
        $this->end();
    }

    public function actionPlotHouseType()
    {
        $api = SM::ImportConfig()->plotHouseType();
        begin:
        $r = HttpHelper::get($this->buildUrl($api));
        $content = $r['content'];
        $data = CJSON::decode($content);
        unset($content);
        if(is_array($data) && !empty($data))
        {
            foreach($data as $row)
            {
                $this->handled++;
                if(!isset($row['hid']) || !isset($row['image']))
                {
                    $this->failed++;
                    echo 'without must params:hid,image'."\n";
                    continue;
                }
                $plot = PlotExt::model()->find('old_id=:hid', array(':hid'=>$row['hid']));
                if($plot)
                    $row['hid'] = $plot->id;
                else
                {
                    $this->failed++;
                    echo "new hid not found\n";
                    continue;
                }
                if($row['title']=="")
                {
                    $row['title']="无";
                }
                $model = new PlotHouseTypeExt;
                $model->attributes = $row;
                $model->save();
                if($model->hasErrors())
                {
                    $this->failed++;
                    echo current(current($model->getErrors()))."\n";
                    continue;
                }
            }
            echo "\npage:{$this->page}\n";
            $this->page++;
            goto begin;
        }
        $this->end();
    }

    /**
     * 导入楼盘价格
     */
    public function actionPlotPrice()
    {
        $api = SM::ImportConfig()->plotPrice();
        begin:
        $r = HttpHelper::get($this->buildUrl($api));
        $content = $r['content'];
        $data = CJSON::decode($content);

        if(is_array($data) && !empty($data))
        {
            foreach($data as $row)
            {
                $this->handled++;
                if(!isset($row['hid']) || !isset($row['jglb']) || !isset($row['price']) || !isset($row['unit']))
                {
                    $this->failed++;
                    echo "without must params:hid,jglb,price or unit\n";
                    continue;
                }
                $plot = PlotExt::model()->find('old_id=:hid', array(':hid'=>$row['hid']));
                if($plot)
                    $row['hid'] = $plot->id;
                else
                {
                    $this->failed++;
                    echo "new hid not found\n";
                    continue;
                }

                $model = new PlotPriceExt;
                $model->attributes = $row;
                $model->save();
                if($model->hasErrors())
                {
                    $this->failed++;
                    echo current(current($model->getErrors()))."\n";
                    continue;
                }
            }
            echo "\npage:{$this->page}\n";
            $this->page++;
            goto begin;
        }
        $this->end();
    }

    /**
     * 导入特价房
     */
    public function actionPlotSpecial()
    {
        $api = SM::ImportConfig()->plotSpecial();
        begin:
        $r = HttpHelper::get($this->buildUrl($api));
        $content = $r['content'];
        $data = CJSON::decode($content);

        if(is_array($data) && !empty($data))
        {
            foreach($data as $row)
            {
                $this->handled++;
                if(!isset($row['hid']) || !isset($row['title']) || !isset($row['price_old']) || !isset($row['price_new'])  || !isset($row['image']))
                {
                    $this->failed++;
                    echo "without must params:hid,title,price_old,price_new or image\n";
                    continue;
                }
                $plot = PlotExt::model()->find('old_id=:hid', array(':hid'=>$row['hid']));
                if($plot)
                    $row['hid'] = $plot->id;
                else
                {
                    $this->failed++;
                    echo "new hid not found\n";
                    continue;
                }

                $model = new PlotSpecialExt;
                $model->attributes = $row;
                $model->save();
                if($model->hasErrors())
                {
                    $this->failed++;
                    echo current(current($model->getErrors()))."\n";
                    continue;
                }
            }
            echo "\npage:{$this->page}\n";
            $this->page++;
            goto begin;
        }
        $this->end();
    }

    /**
     * 导入特惠团|团购
     */
    public function actionPlotTuan()
    {
        $api = SM::ImportConfig()->plotTuan();
        begin:
        $r = HttpHelper::get($this->buildUrl($api));
        $content = $r['content'];
        $data = CJSON::decode($content);

        if(is_array($data) && !empty($data))
        {
            foreach($data as $row)
            {
                $this->handled++;
                if(!isset($row['hid']) || !isset($row['title']) )
                {
                    $this->failed++;
                    echo "without must params:hid or title\n";
                    continue;
                }
                $plot = PlotExt::model()->find('old_id=:hid', array(':hid'=>$row['hid']));
                if($plot)
                    $row['hid'] = $plot->id;
                else
                {
                    $this->failed++;
                    echo "new hid not found\n";
                    continue;
                }

                $model = new PlotTuanExt;
                $model->attributes = $row;
                $model->save();
                if($model->hasErrors())
                {
                    $this->failed++;
                    echo current(current($model->getErrors()))."\n";
                    continue;
                }
            }
            echo "\npage:{$this->page}\n";
            $this->page++;
            goto begin;
        }
        $this->end();
    }

    /**
     * 问答分类（无翻页行为）
     */
    public function actionAskCate()
    {
        $api = SM::ImportConfig()->askCate();
        $r = HttpHelper::get($this->buildUrl($api));
        $content = $r['content'];
        $data = CJSON::decode($content);

        if(is_array($data) && !empty($data))
        {
            foreach($data as $row)
            {
                $this->handled++;
                if(!isset($row['id'])||!isset($row['parent'])||!isset($row['name']))
                {
                    $this->failed++;
                    echo "without must params:id,parent or name\n";
                    continue;
                }
                $row['old_id'] = $row['id'];
                unset($row['id']);
                $model = new AskCateExt;
                $model->attributes = $row;
                $model->save();
                if($model->hasErrors())
                {
                    $this->failed++;
                    echo current(current($model->getErrors()));
                }
            }
            unset($data);
            //更新parent字段为新的id，若找不到新的父级则设自己为顶级
            $allData = AskCateExt::model()->findAll('parent!=0');
            foreach($allData as $v)
            {
                $parent = AskCateExt::model()->find('old_id=:parent', array(':parent'=>$v->parent));
                $v->parent = $parent ? $parent->id : 0;
                $v->save();
            }
        }
        $this->end();
    }

    /**
     * 导入问答内容
     */
    public function actionAsk()
    {
        $api = SM::ImportConfig()->ask();
        begin:
        $r = HttpHelper::get($this->buildUrl($api));
        $content = $r['content'];
        $data = CJSON::decode($content);

        if(is_array($data) && !empty($data))
        {
            foreach($data as $row)
            {
                $this->handled++;
                if(!isset($row['question']) || !isset($row['id']))
                {
                    $this->failed++;
                    echo "without must params:question or id\n";
                    continue;
                }
                $row['old_id'] = $row['id'];
                unset($row['id']);
                //关联新楼盘id
                if(isset($row['hid']))
                {
                    $plot = PlotExt::model()->find('old_id=:hid', array(':hid'=>$row['hid']));
                    if($plot)
                        $row['hid'] = $plot->id;
                    else
                    {
                        unset($row['hid']);
                        // $this->failed++;
                        //echo "new hid not found\n";
                        // continue;
                    }
                }
                //所属新分类
                $cate = AskCateExt::model()->find('old_id=:cid', array(':cid'=>$row['cid']));
                if($cate)
                    $row['cid'] = $cate->id;
                else
                {
                    $this->failed++;
                    echo "new cid not found\n";
                    continue;
                }
                if(!isset($row['phone'])) $row['phone'] = '';
                $model = new AskExt('move_data');
                $model->attributes = $row;
                $model->save();
                if($model->hasErrors())
                {
                    $this->failed++;
                    echo current(current($model->getErrors()))."\n";
                    continue;
                }
            }
            echo "\npage:{$this->page}\n";
            $this->page++;
            goto begin;
        }
        $this->end();
    }

    /**
     * 资讯分类（无翻页行为）
     */
    public function actionArticleCate()
    {
        $api = SM::ImportConfig()->articleCate();
        $r = HttpHelper::get($this->buildUrl($api));
        $content = $r['content'];
        $data = CJSON::decode($content);

        if(is_array($data) && !empty($data))
        {
            foreach($data as $row)
            {
                $this->handled++;
                if(!isset($row['id'])||!isset($row['name']))
                {
                    $this->failed++;
                    echo "without must params:id or name\n";
                    continue;
                }
                $row['old_id'] = $row['id'];
                unset($row['id']);
                $model = new ArticleCateExt;
                $model->attributes = $row;
                $model->save();
                if($model->hasErrors())
                {
                    $this->failed++;
                    echo current(current($model->getErrors()));
                }
            }
        }
        $this->end();
    }

    /**
     * 导入文章内容
     */
    public function actionArticle()
    {
        $api = SM::ImportConfig()->article();
        begin:
        $r = HttpHelper::get($this->buildUrl($api));
        $content = $r['content'];
        $data = CJSON::decode($content);

        if(is_array($data) && !empty($data))
        {
            foreach($data as $row)
            {
                $this->handled++;
                if(!isset($row['cid']) || !isset($row['id']) || !isset($row['title']) || !isset($row['content']))
                {
                    $this->failed++;
                    echo "without must params:cid,,id,title or content\n";
                    continue;
                }
                $row['old_id'] = $row['id'];
                unset($row['id']);
                //关联新楼盘id
                if(isset($row['hid'])&&is_array($row['hid'])&&$row['hid'])
                {
                    $criteria = new CDbCriteria(array(
                        'index'=>'id',
                    ));
                    $criteria->addInCondition('old_id', $row['hid']);
                    $plots = PlotExt::model()->findAll($criteria);
                    $row['hid'] = array_keys($plots);
                    if($row['hid'])
                    {
                        $addhid = true;
                        $row['plot'] = $row['hid'];
                    }
                    //下面这一段当时是针对化龙巷旧的资讯中关联id顺序与文章分页顺序有关
                    // foreach($row['hid'] as $taghid)
                    // {
                    //     $row['content'] = preg_replace('/\[hid\]\[\/hid\]/','[hid]'.$taghid.'[/hid]',$row['content'],1);
                    // }
                }
                if(isset($row['seo']))
                    $row['tag'] = $row['seo'];
                //所属新分类
                $cate = ArticleCateExt::model()->find('old_id=:cid', array(':cid'=>$row['cid']));
                if($cate)
                    $row['cid'] = $cate->id;
                else
                {
                    $this->failed++;
                    echo "new cid not found\n";
                    continue;
                }
                if(isset($row['source'])) $row['source'] = Tools::substr($row['source'],15);
                if($row['description']) $row['description'] = Tools::substr($row['description'],255);
                if($row['created']) $row['show_time'] = $row['created'];
                $model = new ArticleExt;
                $model->attributes = $row;
                $model->author_id = 1;
                $model->author = 'admin';
                $model->save();
                if(isset($addhid)&&$addhid)
                {
                    $model->addPlot();
                    unset($addhid);
                }
                if($model->hasErrors())
                {
                    $this->failed++;
                    echo current(current($model->getErrors()))."\n";
                    continue;
                }
            }
            echo "\npage:{$this->page}\n";
            $this->page++;
            goto begin;
        }
        $this->end();
    }

    /**
     * 导入集客订单
     */
    public function actionOrder()
    {
        $api = SM::ImportConfig()->order();
        begin:
        $r = HttpHelper::get($this->buildUrl($api));
        $content = $r['content'];
        $data = CJSON::decode($content);

        if(is_array($data) && !empty($data))
        {
            foreach($data as $row)
            {
                $this->handled++;
                if(!isset($row['phone']) || !isset($row['type']))
                {
                    $this->failed++;
                    echo "without must params:phone or type\n";
                    continue;
                }

                $row['spm_a'] = '旧数据导入';
                $row['spm_b'] = $row['type'];
                unset($row['type']);

                if(!isset($row['status']))
                    $row['status'] = 1;

                $model = new OrderExt;
                if(isset($row['created']))
                    $model->created_ymd = date('Ymd',$row['created']);
                $model->attributes = $row;
                $model->save();
                if($model->hasErrors())
                {
                    $this->failed++;
                    echo current(current($model->getErrors()))."\n";
                    continue;
                }
                if(isset($row['progress']))
                {
                    $model->user->progress = $row['progress'];
                    $model->user->save();
                }
            }
            echo "\npage:{$this->page}\n";
            $this->page++;
            goto begin;
        }
        $this->end();
    }

    /**
     * 导入集客用户
     */
    public function actionUser()
    {
        $api = SM::ImportConfig()->user();
        begin:
        $r = HttpHelper::get($this->buildUrl($api));
        $content = $r['content'];
        $data = CJSON::decode($content);

        if(is_array($data) && !empty($data))
        {
            foreach($data as $row)
            {
                $addYxlp = $addYxqy = false;
                $this->handled++;
                if(!isset($row['phone']))
                {
                    $this->failed++;
                    echo "without must params:phone\n";
                    continue;
                }
                if(isset($row['yxlp'])&&is_array($row['yxlp'])&&$row['yxlp'])
                {
                    $criteria = new CDbCriteria(array(
                        'index'=>'id',
                    ));
                    $criteria->addInCondition('old_id', $row['yxlp']);
                    $yxlpObj = PlotExt::model()->findAll($criteria);
                    if($yxlpObj)
                    {
                        $row['yxlp'] = array_keys($yxlpObj);
                        if($row['yxlp'])
                        {
                            $addYxlp = true;
                        }
                    }
                }
                elseif(isset($row['yxlp']))
                {
                    unset($row['yxlp']);
                }

                if(isset($row['yxqy'])&&is_array($row['yxqy'])&&$row['yxqy'])
                {
                    $criteria = new CDbCriteria(array(
                        'index'=>'id',
                    ));
                    $criteria->addInCondition('old_id', $row['yxqy']);
                    $yxqyObj = AreaExt::model()->findAll($criteria);
                    if($yxqyObj)
                    {
                        $row['yxqy'] =array_keys($yxqyObj);
                        if($row['yxqy'])//可能是空数组，会导致错误
                        {
                            $addYxqy = true;
                        }
                    }
                }
                elseif(isset($row['yxqy']))
                {
                    unset($row['yxqy']);
                }
                if(isset($row['created'])&&intval($row['created'])<=0)
                {
                    unset($row['created']);
                }

                $model = UserExt::model()->find('phone=:phone', array(':phone'=>$row['phone']));
                if(empty($model))
                    $model = new UserExt;
                $model->attributes = $row;
                $model->save();
                if($model->hasErrors())
                {
                    $this->failed++;
                    echo current(current($model->getErrors()))."\n";
                    continue;
                }
                if($addYxlp) $model->addYxlp();
                if($addYxqy) $model->addYxqy();
            }
            echo "\npage:{$this->page}\n";
            $this->page++;
            goto begin;
        }
        $this->end();
    }

    /**
     * 用户状态调整
     * 将用户最新订单时间更新
     * @return [type] [description]
     */
    public function actionUserTiaozheng()
    {

    }

    /**
     * 导入小编回访记录
     */
    public function actionAdminLog()
    {
        $api = SM::importConfig()->adminLog();
        begin:
        $r = HttpHelper::get($this->buildUrl($api));
        $content = $r['content'];
        $data = CJSON::decode($content);

        if(is_array($data) && !empty($data))
        {
            foreach($data as $row)
            {
                $this->handled++;
                if(!isset($row['phone']) || !isset($row['visit_status']))
                {
                    $this->failed++;
                    echo "without must params:phone or visit_status\n";
                    continue;
                }

                $model = new UserLogExt;
                $model->attributes = $row;
                if(!$model->validate())
                {
                    $this->failed++;
                    echo current(current($model->getErrors()))."\n";
                    continue;
                }
                if(!$model->user)
                {
                    $this->failed++;
                    echo "no related user record\n";
                    continue;
                }
                $model->admin_id = 1;
                $model->addAdminLog();
                $model->save();
                if($model->hasErrors())
                {
                    $this->failed++;
                    echo current(current($model->getErrors()))."\n";
                    continue;
                }
            }
            echo "\npage:{$this->page}\n";
            $this->page++;
            goto begin;
        }
        $this->end();
    }

    /**
     * 导入学校数据
     */
    public function actionSchool()
    {
        $api = SM::importConfig()->school();
        begin:
        $r = HttpHelper::get($this->buildUrl($api));
        $content = $r['content'];
        $data = CJSON::decode($content);

        if(is_array($data) && !empty($data))
        {
            foreach($data as $row)
            {
                $this->handled++;
                if(!isset($row['id']) || !isset($row['name']) || !isset($row['area']) || !isset($row['street']) || !isset($row['type']) || !isset($row['image']))
                {
                    $this->failed++;
                    echo "without must params:id,name,area,type or image\n";
                    continue;
                }
                $area = AreaExt::model()->find('old_id=:area', array(':area'=>$row['area']));
                if($area)
                    $row['area'] = $area->id;
                else
                {
                    // $this->failed++;
                    $row['area'] = 0;
                    echo "new area not found\n";
                    // continue;
                }
                $street = AreaExt::model()->find('old_id=:street', array(':street'=>$row['street']));
                if($street)
                    $row['street'] = $street->id;
                else
                {
                    // $this->failed++;
                    $row['street'] = 0;
                    echo "new street not found\n";
                    // continue;
                }
                $row['old_id'] = $row['id'];
                unset($row['id']);

                if(isset($row['image'])) {
                    $row['pic'][] = $row['image'];
                }

                $model = new SchoolExt('move_data');
                $model->attributes = $row;
                $model->save();
                if($model->hasErrors())
                {
                    $this->failed++;
                    echo current(current($model->getErrors()))."\n";
                    continue;
                }
            }
            echo "\npage:{$this->page}\n";
            $this->page++;
            goto begin;
        }
        $this->end();
    }

    /**
     * 导入学区房关联数据
     */
    public function actionSchoolPlotRel()
    {
        $api = SM::importConfig()->schoolPlotRel();
        begin:
        $r = HttpHelper::get($this->buildUrl($api));
        $content = $r['content'];
        $data = CJSON::decode($content);

        if(is_array($data) && !empty($data))
        {
            foreach($data as $row)
            {
                $this->handled++;
                if(!isset($row['sid']) || !isset($row['hid']))
                {
                    $this->failed++;
                    echo "without must params:sid or hid\n";
                    continue;
                }
                $school = SchoolExt::model()->find('old_id=:sid', array(':sid'=>$row['sid']));
                if($school)
                {
                    $row['sid'] = $school->id;
                    $row['sname'] = $school->name;
                    $row['area'] = $school->area;
                }
                else
                {
                    $this->failed++;
                    echo "new sid not found\n";
                    continue;
                }
                $plot = PlotExt::model()->find('old_id=:hid', array(':hid'=>$row['hid']));
                if($plot)
                {
                    $row['hid'] = $plot->id;
                    $row['hname'] = $plot->title;
                }
                else
                {
                    $this->failed++;
                    echo "new hid not found\n";
                    continue;
                }

                $model = new SchoolPlotRelExt;
                $model->school = $school;
                $model->plot = $plot;
                $model->save();
                if($model->hasErrors())
                {
                    $this->failed++;
                    echo current(current($model->getErrors()))."\n";
                    continue;
                }
            }
            echo "\npage:{$this->page}\n";
            $this->page++;
            goto begin;
        }
        $this->end();
    }

    /**
     * 导入看房团活动
     */
    public function actionPlotKan()
    {
        $api = SM::importConfig()->plotKan();
        begin:
        $r = HttpHelper::get($this->buildUrl($api));
        $content = $r['content'];
        $data = CJSON::decode($content);

        if(is_array($data) && !empty($data))
        {
            foreach($data as $row)
            {
                $this->handled++;
                if(!isset($row['hids']) || !isset($row['title']) || !isset($row['gather_time']) || !isset($row['expire']))
                {
                    $this->failed++;
                    echo "without must params:hids,title,gather_time or expire\n";
                    continue;
                }
                if(!is_array($row['hids']))
                {
                    $this->failed++;
                    echo 'param $row[hids] is not array';
                    continue;
                }
                $row['hids'] = implode(',',$row['hids']);

                $model = new PlotKanExt;
                $model->attributes = $row;
                $model->save();
                if($model->hasErrors())
                {
                    $this->failed++;
                    echo current(current($model->getErrors()))."\n";
                    continue;
                }
            }
            echo "\npage:{$this->page}\n";
            $this->page++;
            goto begin;
        }
        $this->end();
    }

    /**
     * 导入看房团活动订单
     */
    public function actionPlotKanOrder()
    {
        $api = SiteConfigModel::model()->importPlotKanOrderApi;
        begin:
        $r = HttpHelper::get($this->buildUrl($api));
        $content = $r['content'];
        $data = CJSON::decode($content);

        if(is_array($data) && !empty($data))
        {
            foreach($data as $row)
            {
                $this->handled++;
                if(!isset($row['oid']) || !isset($row['kid']))
                {
                    $this->failed++;
                    echo "without must params:oid or kid\n";
                    continue;
                }
                $order = OrderExt::model()->find('old_id=:oid', array(':oid'=>$row['oid']));
                if($order)
                    $row['oid'] = $order->id;
                else
                {
                    $this->failed++;
                    echo 'new oid not found';
                    continue;
                }

                $kan = PlotKanExt::model()->find('old_id=:kid', array(':kid'=>$row['kid']));
                if($order)
                    $row['kid'] = $kan->id;
                else
                {
                    $this->failed++;
                    echo 'new kid not found';
                    continue;
                }
                $model = new PlotKanExt;
                $model->attributes = $row;
                $model->save();
                if($model->hasErrors())
                {
                    $this->failed++;
                    echo current(current($model->getErrors()))."\n";
                    continue;
                }
            }
            echo "\npage:{$this->page}\n";
            $this->page++;
            goto begin;
        }
        $this->end();
    }

    /**
     * 导数据修正。。。open_time和delivery_time
     * @return [type] [description]
     */
    public function actionXiuzheng()
    {
        $api = SiteConfigModel::model()->importPlotApi;
        begin:
        $r = HttpHelper::get($this->buildUrl($api));
        $content = $r['content'];
        $data = CJSON::decode($content);

        if(is_array($data) && !empty($data))
        {
            foreach($data as $row)
            {
                $this->handled++;
                if(!isset($row['id']))
                {
                    $this->failed++;
                    echo "without must params:id\n";
                    continue;
                }


                $model = PlotExt::model()->find('old_id=:id', array(':id'=>$row['id']));
                if(!$model)
                {
                    continue;
                }
                $model->data_conf['peripheral'] = $row['peripheral'];
                $model->data_conf['transit'] = $row['transit'];
                $model->data_conf['content'] = $row['content'];

                $model->save();
                if($model->hasErrors())
                {
                    $this->failed++;
                    echo current(current($model->getErrors()))."\n";
                    continue;
                }

            }
            echo "\npage:{$this->page}\n";
            $this->page++;
            goto begin;
        }
        $this->end();
    }

    /**
     * [actionImportShop 导店铺接口]
     * @return [type] [description]
     */
    public function actionImportShop()
    {
        $api = SM::resoldImportConfig()->importShopApi->value;
        begin:
        $r = HttpHelper::get($this->buildUrl($api));
        $content = $r['content'];
        $data = CJSON::decode($content);
        if(is_array($data) && !empty($data))
        {
            foreach($data as $k => $row)
            {
                $this->handled++;
                if(!isset($row['id']))
                {
                    $this->failed++;
                    echo "without must params:id\n";
                    continue;
                }

                $model = new ResoldShopExt;
                $model->scenario = 'import';

                $model->old_id = $row['id'];
                foreach ($row as $key => $value) {
                    if($key != 'id')
                        $model->$key = $value;
                }
                // 区域街道id转换
                $area = AreaExt::model()->find(['condition'=>'old_id=:oid','params'=>[':oid'=>$row['area']]]);
                $street = AreaExt::model()->find(['condition'=>'old_id=:oid','params'=>[':oid'=>$row['street']]]);
                $model->area = $area->id;
                $model->street = $street->id;

                $model->save();
                if($model->hasErrors())
                {
                    $this->failed++;
                    echo current(current($model->getErrors()))."\n";
                    continue;
                }

            }
            echo "\npage:{$this->page}\n";
            $this->page++;
            goto begin;
        }

        $this->end();
    }

    /**
     * [actionImportEsf 导二手房接口]
     * @return [type] [description]
     */
    public function actionImportEsf()
    {
        $api = SM::resoldImportConfig()->importEsfApi->value;
        begin:
        $r = HttpHelper::get($this->buildUrl($api));
        $content = $r['content'];
        unset($r);
        $data = CJSON::decode($content);
        unset($content);
        if(is_array($data) && !empty($data))
        {
            foreach($data as $k => $row)
            {
                $this->handled++;

                if(!isset($row['id']))
                {
                    $this->failed++;
                    echo "without must params:id,id:".$row['id']."\n";
                    continue;
                }

                $row['old_id'] = $row['id'];
                unset($row['id']);

                isset($row['ip']) && $row['ip'] = bindec( decbin( ip2long( $row['ip'] ) ) );
                !$row['username'] && $row['username'] = $row['account'];
                $row['price'] < 0 && $row['price'] = 0;
                $row['size']>0 && $row['ave_price'] = (int)($row['price']/$row['size']*10000);

                $saleStatus = array_flip(Yii::app()->params['saleStatus']);
                !isset($row['sale_status']) && $row['sale_status'] = $saleStatus['下架'];
                if(!isset($row['created']) || !$row['created'])
                    $row['created'] = time();
                $row['year'] = date('Y',$row['created']);
                $row['month'] = date('n',$row['created']);
                $row['day'] = date('j',$row['created']);

                if($row['sale_status'] == $saleStatus['上架'] && (!isset($row['expire_time']) || !$row['expire_time']))
                    $row['expire_time'] = time() + SM::resoldConfig()->resoldExpireTime() * 86400;
                if($row['sale_status'] == $saleStatus['上架'] && (!isset($row['refresh_time']) || !$row['refresh_time']))
                    $row['refresh_ftime'] = time();
                unset($saleStatus);

                if($row['hid']) {
                    $plot = Yii::app()->db->createCommand('select title,street,area from plot where id='.$row['hid'])->queryRow();
                    $row['plot_name'] = $plot['title'];
                    $row['street'] = $plot['street'];
                    $row['area'] = $plot['area'];
                }

                if(!isset($row['floor']) || !$row['floor'] || !is_numeric($row['floor']))
                    $row['floor'] = 0;
                if(!isset($row['total_floor']) || !$row['total_floor'] || !is_numeric($row['total_floor']))
                    $row['total_floor'] = 0;

                if(isset($row['data_conf']) && is_array($row['data_conf']) && $row['data_conf']) {
                    $row['data_conf'] = json_encode($row['data_conf']);
                } elseif(!isset($row['data_conf']) || !$row['data_conf']) {
                    $row['data_conf'] = json_encode(['tags'=>[]]);
                }

                $sql = "insert into resold_esf(";
                foreach ($row as $key => $value) {
                    $key!='images' && $sql .= "$key,";
                }
                $sql = trim($sql,',').") values(";
                foreach ($row as $key => $value) {
                    $key!='images' && $sql .= "'$value',";
                }
                $sql = trim($sql,',').")";

                try
                {
                    if(Yii::app()->db->createCommand($sql)->execute()) {
                        unset($sql);
                        $id = Yii::app()->db->getLastInsertID();
                        if($row['images'])
                        {
                            $insert = '';
                            $imageSql = "insert into resold_image(source,name,url,sort,type,fid,created,updated,deleted) values";
                            foreach ($row['images'] as $key => $value) {
                                $insert .= "('".$value['image_source']."','".$value['image_name']."','".$value['url']."','".$value['image_sort']."','1','".$id."','".time()."','".time()."','0'),";
                                unset($value);
                            }
                            $insert = trim($insert,',');
                            Yii::app()->db->createCommand($imageSql.$insert)->execute();
                        }
                    } else {
                        $this->failed++;
                    }
                }
                catch(Exception $e)
                {
                    echo $e->getMessage().'id:'.$row['old_id'];
                }
                unset($row);
            }
            echo "\npage:{$this->page}\n";
            $this->page++;
            goto begin;
        }

        $this->end();
    }

    /**
     * [actionImportZf 导租房接口]
     * @return [type] [description]
     */
    public function actionImportZf()
    {
        $api = SM::resoldImportConfig()->importZfApi->value;
        begin:
        $r = HttpHelper::get($this->buildUrl($api));
        $content = $r['content'];
        $r = null;
        $data = CJSON::decode($content);
        $content = null;
        if(is_array($data) && !empty($data))
        {
            foreach($data as $k => $row)
            {
                $this->handled++;

                if(!isset($row['id']))
                {
                    $this->failed++;
                    echo "without must params:id\n";
                    continue;
                }
                // id转换
                $row['old_id'] = $row['id'];
                unset($row['id']);
                // ip转换
                isset($row['ip']) && $row['ip'] = bindec( decbin( ip2long( $row['ip'] ) ) );
                // 标签转换
                $data_conf = isset($row['data_conf']) ? $row['data_conf'] : [];
                $tags = [];
                if($data_conf && isset($data_conf['tags']) && $data_conf['tags']) {
                    $modeArr = ['esfzfzztype','esfzfsptype','esfzfxzltype','resoldface','zfxzllevel'];
                    foreach ($data_conf['tags'] as $key => $value) {
                        $tagCate = Yii::app()->db->createCommand('select cate from tag where id='.$value)->queryScalar();
                        if(in_array($tagCate, $modeArr ))
                            $tags[$tagCate] = $value;
                        else
                            $tags[$tagCate][] = $value;
                        $tagCate = null;
                        $value = null;
                    }
                    $data_conf = null;
                }
                $row['data_conf'] = json_encode($tags);

                !$row['username'] && $row['username'] = $row['account'];
                !isset($row['rent_type']) && $row['rent_type'] = 0;
                $saleStatus = array_flip(Yii::app()->params['saleStatus']);
                !isset($row['sale_status']) && $row['sale_status'] = $saleStatus['下架'];
                
                if(!isset($row['created']) || !$row['created'])
                    $row['created'] = time();
                $row['year'] = date('Y',$row['created']);
                $row['month'] = date('n',$row['created']);
                $row['day'] = date('j',$row['created']);

                if($row['sale_status'] == $saleStatus['上架'] && (!isset($row['expire_time']) || !$row['expire_time']))
                    $row['expire_time'] = time() + SM::resoldConfig()->resoldExpireTime() * 86400;
                if($row['sale_status'] == $saleStatus['上架'] && (!isset($row['refresh_time']) || !$row['refresh_time']))
                    $row['refresh_time'] = time();
                unset($saleStatus);

                if($row['hid']) {
                    $plot = Yii::app()->db->createCommand('select title,street,area from plot where id='.$row['hid'])->queryRow();
                    $row['plot_name'] = $plot['title'];
                    $row['street'] = $plot['street'];
                    $row['area'] = $plot['area'];
                }

                if(!isset($row['floor']) || !$row['floor'] || !is_numeric($row['floor']))
                    $row['floor'] = 0;
                if(!isset($row['total_floor']) || !$row['total_floor'] || !is_numeric($row['total_floor']))
                    $row['total_floor'] = 0;

                $row['price'] < 0 && $row['price'] = 0;

                if(isset($row['pay_type']) && is_array($row['pay_type']) && $row['pay_type']) {
                    $row['pay_type'] = json_encode($row['pay_type']);
                } 

                $sql = "insert into resold_zf(";
                foreach ($row as $key => $value) {
                    $key!='images' && $sql .= "$key,";
                }
                $sql = trim($sql,',').") values(";
                foreach ($row as $key => $value) {
                    $key!='images' && $sql .= "'$value',";
                }
                $sql = trim($sql,',').")";
                
                try
                {
                    if(Yii::app()->db->createCommand($sql)->execute()) {
                        unset($sql);
                        $id = Yii::app()->db->getLastInsertID();
                        if($row['images'])
                        {
                            $insert = '';
                            $imageSql = "insert into resold_image(source,name,url,sort,type,fid,created,updated,deleted) values";
                            foreach ($row['images'] as $key => $value) {
                                $insert .= "('".$value['image_source']."','".$value['image_name']."','".$value['url']."','".$value['image_sort']."','2','".$id."','".time()."','".time()."','0'),";
                                $value = null;
                            }
                            $insert = trim($insert,',');
                            Yii::app()->db->createCommand($imageSql.$insert)->execute();
                            $imageSql = null;
                            $insert = null;
                        }
                    } else { 
                        $this->failed++;
                    }
                        
                }
                catch(Exception $e)
                {
                    echo $e->getMessage().'id:'.$row['old_id'];
                }
                $row = null;
            }
            echo "\npage:{$this->page}\n";
            $this->page++;
            goto begin;
        }

        $this->end();
    }

    /**
     * [actionImportQg 导入求购数据]
     * @return [type] [description]
     */
    public function actionImportQg()
    {
        $api = SM::resoldImportConfig()->importQgApi->value;
        begin:
        $r = HttpHelper::get($this->buildUrl($api));
        $content = $r['content'];
        $data = CJSON::decode($content);
        if(is_array($data) && !empty($data))
        {
            foreach($data as $k => $row)
            {
                $this->handled++;
                if(!isset($row['id']))
                {
                    $this->failed++;
                    echo "without must params:id\n";
                    continue;
                }

                $model = new ResoldQgExt;
                $model->scenario = 'import';
                $model->old_id = $row['id'];
                foreach ($row as $key => $value) {
                    if($key != 'id' && $value)
                        $model->$key = $value;
                }

                if(isset($row['hid']) && $row['hid']) {
                    if(!is_array($row['hid']))
                        $row['hid'] = [$row['hid']];
                } else {
                    $row['hid'] = [];
                }
                $model->hid = json_encode($row['hid']);
                $model->phone = trim($row['phone']);
                $model->price = (int)$row['price'];

                isset($row['data_conf']) && $model->data_conf = json_encode($row['data_conf']);
                isset($row['ip']) && $model['ip'] = bindec( decbin( ip2long( $model['ip'] ) ) );

                try
                {
                    $model->save();
                }
                catch(Exception $e)
                {
                    echo $e->getMessage().'id:'.$row['id'];
                }
                if($model->hasErrors())
                {
                    $this->failed++;
                    echo current(current($model->getErrors())).'id:'.$row['id']."\n";
                    continue;
                }

            }
            echo "\npage:{$this->page}\n";
            $this->page++;
            goto begin;
        }

        $this->end();
    }

    /**
     * [actionImportQz 导求租接口]
     * @return [type] [description]
     */
    public function actionImportQz()
    {
        $api = SM::resoldImportConfig()->importQzApi->value;
        begin:
        $r = HttpHelper::get($this->buildUrl($api));
        $content = $r['content'];
        $data = CJSON::decode($content);
        if(is_array($data) && !empty($data))
        {
            foreach($data as $k => $row)
            {
                $this->handled++;
                if(!isset($row['id']))
                {
                    $this->failed++;
                    echo "without must params:id\n";
                    continue;
                }

                $model = new ResoldQzExt;
                $model->scenario = 'import';
                $model->old_id = $row['id'];
                foreach ($row as $key => $value) {
                    if($key != 'id' && $key != 'qzchamber' && $value)
                        $model->$key = $value;
                }

                if(isset($row['hid']) && $row['hid']) {
                    if(!is_array($row['hid']))
                        $row['hid'] = [$row['hid']];
                } else {
                    $row['hid'] = [];
                }
                $model->hid = json_encode($row['hid']);

                $model['ip'] = bindec( decbin( ip2long( $model['ip'] ) ) );
                $data_conf = isset($row['data_conf']) ? $row['data_conf'] : [];
                $tags = [];
                if($data_conf && isset($data_conf['tags']) && $data_conf['tags'])
                    foreach ($data_conf['tags'] as $key => $value) {
                        $tag = TagExt::model()->findByPk($value);
                        $modeArr = ['esfzfsptype','esfzfzztype','esfzfxzltype','floor'];
                        if(in_array($tag->cate, $modeArr ))
                            $tags[$tag->cate] = $value;
                        else
                            $tags[$tag->cate][] = $value;
                    }
                $model->price = (int)$row['price'];
                
                $model->data_conf = json_encode($tags);
                $model->phone = trim($model->phone);
                try
                {
                    $model->save();
                }
                catch(Exception $e)
                {
                    echo $e->getMessage().'id:'.$row['id'];
                }
                if($model->hasErrors())
                {
                    $this->failed++;
                    echo current(current($model->getErrors())).'id:'.$row['id']."\n";
                    continue;
                }

            }
            echo "\npage:{$this->page}\n";
            $this->page++;
            goto begin;
        }

        $this->end();
    }

    /**
     * [actionImportStaff 导职员接口]
     * @return [type] [description]
     */
    public function actionImportStaff()
    {
        $api = SM::resoldImportConfig()->importStaffApi->value;
        begin:
        $r = HttpHelper::get($this->buildUrl($api));
        $content = $r['content'];
        unset($r);
        $data = CJSON::decode($content);
        if(is_array($data) && !empty($data))
        {
            // 套餐初始化
            $packages = ResoldTariffPackageExt::model()->enabled()->findAll();
            $packageArr = [];
            foreach ($packages as $key => $value) {
                $content = json_decode($value->content,true);
                $packageArr[$content['total_num']] = $value->id;
            }
            unset($packages);
            foreach($data as $k => $row)
            {
                $this->handled++;
                if(!isset($row['id']))
                {
                    $this->failed++;
                    echo "without must params:id\n";
                    continue;
                }

                $model = new ResoldStaffExt;
                $model->scenario = 'import';
                $model->old_id = $row['id'];
                foreach ($row as $key => $value) {
                    if($key != 'id' && $key != 'biz_number' && $key != 'biz_expire' && $value)
                        $model->$key = $value;
                }
                $model['ip'] = bindec( decbin( ip2long( $model['ip'] ) ) );

                try
                {
                    if($model->save())
                    {
                        // if($row['biz_number']!=10 && $row['biz_number'])
                        // {
                        //     $staffPackage = new ResoldStaffPackageExt;
                        //     $staffPackage->pid = $packageArr[$row['biz_number']];
                        //     $staffPackage->staff = $model->id;
                        //     $staffPackage->expire_time = $row['biz_expire'];
                        //     $staffPackage->save();
                        // }
                        $model->expireTime = $row['biz_expire'];
                        $model->savePackage();
                    }

                }
                catch(Exception $e)
                {
                    echo $e->getMessage().'id:'.$row['id'];
                }
                if($model->hasErrors())
                {
                    $this->failed++;
                    echo current(current($model->getErrors()))."\n";
                    continue;
                }

            }
            echo "\npage:{$this->page}\n";
            $this->page++;
            goto begin;
        }

        $this->end();
    }

    /**
     * [actionImportBlack 导黑名单接口]
     * @return [type] [description]
     */
    public function actionImportBlack()
    {
        $api = SM::resoldImportConfig()->importBlackApi->value;
        begin:
        $r = HttpHelper::get($this->buildUrl($api));
        $content = $r['content'];
        unset($r);
        $data = CJSON::decode($content);
        if(is_array($data) && !empty($data))
        {
            foreach(array_values($data) as $k => $row)
            {
                $this->handled++;
                try
                {
                    $model = new ResoldBlackExt;
                    $model->phone = $row;
                    $model->save();
                }
                catch(Exception $e)
                {
                    echo $e->getMessage().'id:'.$row['id'];
                }
                if($model->hasErrors())
                {
                    $this->failed++;
                    echo current(current($model->getErrors()))."\n";
                    continue;
                }

            }
            echo "\npage:{$this->page}\n";
            $this->page++;
            goto begin;
        }

        $this->end();
    }

}
