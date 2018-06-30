<?php 
/**
 * 楼盘类
 * @author steven.allen <[<email address>]>
 * @date(2017.2.14)
 */
defined('EARTH_RADIUS') or define('EARTH_RADIUS', 6378.137);//地球半径
defined('PI') or define('PI', 3.1415926);
class PlotExt extends Plot{
    public static $tagArr = [
        'zxzt','wylx','wzlm','sale_status','sfprice'
    ];
    /**
     * 楼盘状态
     * @var array
     */
    static $status = array(
        0 => '禁用',
        1 => '启用'
    );
    /**
     * 是否新盘
     * @var array
     */
    static $isNew = array(
        0 => '否',
        1 => '是',
    );
    /**
     * 价格单位
     * @var array
     */
    public static $unit = array(
        1 => '元/㎡',
        2 => '万元/套'
    );
    /**
     * 价格标识
     * @var array
     */
    public static $mark = array(
        1 => '均价',
        2 => '起价',
        3 => '一口价',
        4 => '封顶价',
    );
    public static $tags = [
        'size'=>'',
        'buildsize'=>'',
        'capacity'=>'',
        'green'=>'',
        'manage_fee'=>'',
        'manage_company'=>'',
        'developer'=>'',
        'property_years'=>'',
        'household_num'=>'',
        'building_num'=>'',
        'floor_desc'=>'',
        'transit'=>'',
        'content'=>'',
        'peripheral'=>'',
        'wylx'=>'',
        'jzlb'=>'',
        'xmts'=>'',
        'zxzt'=>'',
        'is_new'=>'',
        'carport'=>'',
        'surround_peripheral'=>'',
        'build_year'=>'',
        'investor'=>'',
        'brand'=>'',
        'jy_rule'=>'',
        'kfs_rule'=>'',
        'is_jt'=>'',
        'dk_rule'=>'',
        'zd_company'=>'',
        'wzlm'=>'',
        'sfprice'=>'',
        'wx_share_title'=>'',
        'dllx'=>'',
        'hxjs'=>'',
        'yjfa'=>'',
        'dp1'=>'',
        'dp2'=>'',
        // ''
    ];


    /**
     * @return array 验证规则
     */
    public function rules() {
        $rules = parent::rules();
        return array_merge($rules, array(
            array(implode(',',array_keys(self::$tags)), 'safe'),
            array('title','titlerule'),
            // array('zd_company','required'),
        ));
    }
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        return array_merge($labels,[
            'price'=>'价格',
            ]);
    }

    public function titlerule($attribute,$params)
    {
        if($this->getIsNewRecord() && $this->company_id && $this->title) {
            PlotExt::model()->find("title='".$this->title."' and company_id=".$this->company_id) && $this->addError($attribute, '楼盘名不能重复!'); 
        }
    }

    public function __set($name='',$value='')
    {
       if (isset(self::$tags[$name])){
            if(is_array($this->data_conf))
                $data_conf = $this->data_conf;
            else
                $data_conf = CJSON::decode($this->data_conf);
            self::$tags[$name] = $value;
            $data_conf[$name] = $value;
            // var_dump(1);exit;
            $this->data_conf = json_encode($data_conf);
        }
        else
            parent::__set($name, $value);
    }

    public function __get($name='')
    {
        if (isset(self::$tags[$name])) {
            if(is_array($this->data_conf))
                $data_conf = $this->data_conf;
            else
                $data_conf = CJSON::decode($this->data_conf);

            if(!isset($data_conf[$name]))
                $value = self::$tags[$name];
            else
                $value = self::$tags[$name] ? self::$tags[$name] : $data_conf[$name];

            return $value;
        } else{
            return parent::__get($name);
        }
    }
    /**
     * 定义关系
     */
    public function relations()
    {
        return array(
            'hxs'=>array(self::HAS_MANY, 'PlotHxExt', 'hid','condition'=>'hxs.deleted=0','order'=>'hxs.updated desc'),
            'images'=>array(self::HAS_MANY, 'PlotImageExt', 'hid','condition'=>'images.deleted=0','order'=>'images.sort desc'),
            'news'=>array(self::HAS_MANY, 'PlotNewsExt', 'hid','condition'=>'news.deleted=0','order'=>'news.updated desc'),
            'used_news'=>array(self::HAS_MANY, 'PlotNewsExt', 'hid','condition'=>'used_news.deleted=0 and used_news.status=1','order'=>'used_news.updated desc'),
            'place_user_info'=>array(self::HAS_ONE, 'UserExt', ['id'=>'place_user']),
            'wds'=>array(self::HAS_MANY, 'PlotWdExt', 'pid','condition'=>'wds.deleted=0'),
            'pays'=>array(self::HAS_MANY, 'PlotPayExt', 'hid','condition'=>'pays.deleted=0 and pays.status=1','order'=>'pays.updated desc'),
            'areaInfo' => array(self::BELONGS_TO, 'AreaExt', 'area'),//区
            'streetInfo' => array(self::BELONGS_TO, 'AreaExt', 'street'),//街道
            'subs'=>array(self::HAS_MANY, 'SubExt', 'hid','condition'=>'subs.deleted=0','order'=>'subs.created desc'),
            'checked_subs'=>array(self::HAS_MANY, 'SubExt', 'hid','condition'=>'checked_subs.deleted=0 and checked_subs.is_check=1','order'=>'checked_subs.created desc'),
            'places'=>array(self::HAS_MANY, 'PlotPlaceExt', 'hid','condition'=>'places.deleted=0','order'=>'places.created desc'),
            'users'=>array(self::HAS_MANY, 'PlotUserExt', 'hid'),
            'sfMarkets'=>array(self::HAS_MANY, 'PlotMarketUserExt', 'hid','condition'=>'sfMarkets.deleted=0 and sfMarkets.status=1 and sfMarkets.expire>'.time(),'order'=>'sfMarkets.is_manager desc,sfMarkets.created asc'),
            'owner'=>array(self::BELONGS_TO, 'UserExt', 'uid'),
            'companys'=>array(self::MANY_MANY, 'CompanyExt', 'plot_company(hid,cid)'),
            'company'=>array(self::BELONGS_TO, 'CompanyExt', 'company_id'),
            'staff'=>array(self::BELONGS_TO, 'StaffExt', 'staff_id'),
        );
    }

    /**
     * 返回指定AR类的静态模型
     * @param string $className AR类的类名
     * @return CActiveRecord Admin静态模型
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function afterFind() {
        parent::afterFind();
    }

    public function beforeValidate() {
        !$this->pinyin && $this->pinyin = 'pinyin';
        if(!$this->company_id && $cms = $this->companys) {
            $this->company_id = $cms[0]['id'];
            $this->company_name = $cms[0]['name'];
        }
        if($this->company_id && $this->market_users) {
            $mks = explode(' ', $this->market_users);
            foreach ($mks as $key => $value) {
                preg_match_all('/[0-9]+/', $value,$num);
                if(isset($num[0][0])) {
                    $num = $num[0][0];
                    $numss = $num;
                    $name = str_replace($num, '', $value);
                    // var_dump(UserExt::model()->find("phone='$numss'"),$name);exit;
                    if($name && !UserExt::model()->find("phone='$numss'")){
                        $obj = new UserExt;
                        $obj->phone = $numss;
                        $obj->status = $obj->type = 1;
                        $obj->cid = $this->company_id;
                        $obj->name = $name;
                        $obj->save();
                    }
                }
            }
        }
        if($this->getIsNewRecord()) {
            // 非会员过滤
            if($ow = $this->owner) {
                if($ow->vip_expire<time()) {
                    $this->addError('uid','您目前不是会员身份，请成为会员后操作');
                }
            }
            $this->created = $this->updated = time();
        }
        else {
            $this->updated = time();
        }
        // if(!$this->first_pay) {
        //     $this->first_pay = Yii::app()->db->createCommand("select price from plot_pay where hid=".$this->id." and deleted=0 and status=1 and price!=''")->queryScalar();
        //     // var_dump($this->first_pay);
        //     // $this->save();
        // }
        // var_dump($this->data_conf);exit;
        if(!$this->refresh_time){
            $this->refresh_time = $this->created;
        }
        return parent::beforeValidate();
    }

    public function afterSave()
    {
        parent::afterSave();
        if($this->getIsNewRecord()) {
            Yii::app()->redis->getClient()->hSet('plot_title',$this->id,$this->title);
        }
        if($this->deleted==1) {
            if($hxs = $this->hxs) {
                foreach ($hxs as $key => $value) {
                    $value->deleted = 1;
                    $value->save();
                }
            }
            if($images = $this->images) {
                foreach ($images as $key => $value) {
                    $value->deleted = 1;
                    $value->save();
                }
            }
        }
        CacheExt::delete('wap_init_plotlist');  
        CacheExt::delete('wap_area_plotlist');  
        // PlotExt::setPlotCache(); 
    }

    /**
     * 命名范围
     * @return array
     */
    public function scopes()
    {
        $alias = $this->getTableAlias();
        return array(
            'undeleted' => array(
                'condition' => $alias.'.'.'deleted=0',
            ),
           'normal' => array(
                'condition' => "{$alias}.status=1 and {$alias}.deleted=0",
            ),
        );
    }
    /**
     * 绑定行为类
     */
    public function behaviors() {
        return array(
            'CacheBehavior' => array(
                'class' => 'application.behaviors.CacheBehavior',
                'cacheExp' => 0, //This is optional and the default is 0 (0 means never expire)
                'modelName' => __CLASS__, //This is optional as it will assume current model
            ),
            'BaseBehavior'=>'application.behaviors.BaseBehavior',
        );
    }

    // public function getItsCompany()
    // {
    //     $arr = [];
    //     if($zd_company = $this->zd_company) {
    //         if(!is_array($zd_company)) {
    //             $zd_company = [$zd_company];
    //         }
    //         // $zd_company = array_filter($zd_company);
    //         foreach ($zd_company as $key => $value) {
    //             $obj = CompanyExt::model()->findByPk($value);
    //             $arr[] = ['id'=>$obj->id,'name'=>$obj->name];
    //         }
    //     }
    //     return $arr;
    // }

    public static function setPlotCache()
    {
        return CacheExt::gas('wap_init_plotlist','AreaExt',0,'wap列表页缓存',function (){
                    $info_no_pic = SiteExt::getAttr('qjpz','info_no_pic');
                    $criteria = new CDbCriteria;
                    $criteria->order = 'is_unshow asc,qjsort desc,refresh_time desc';
                    $plots = PlotExt::model()->normal()->getList($criteria);
                    if($datares = $plots->data) {
                        foreach ($datares as $key => $value) {
                            if($area = $value->areaInfo)
                                $areaName = $area->name;
                            else
                                $areaName = '';
                            if($street = $value->streetInfo)
                                $streetName = $street->name;
                            else
                                $streetName = '';
                            $companydes = ['id'=>$value->company_id,'name'=>$value->company_name];
                                
                            // var_dump(Yii::app()->user->getIsGuest());exit;
                            // if(Yii::app()->user->getIsGuest()) {
                            //     $pay = '';
                            // } elseif($pays = $value->pays) {
                            //     $pay = $pays[0]['price'].(count($pays)>1?'('.count($pays).'个方案)':'');
                            // } else {
                            //     $pay = '';
                            // }
                            $wyw = '';
                            $wylx = $value->wylx;
                            if($wylx) {
                             if(!is_array($wylx)) 
                                 $wylx = [$wylx];
                             foreach ($wylx as $w) {
                                 $t = TagExt::model()->findByPk($w)->name;
                                 $t && $wyw .= $t.' ';
                             }
                             $wyw = trim($wyw);
                            }
                            $lists[] = [
                                'id'=>$value->id,
                                'title'=>Tools::u8_title_substr($value->title,18),
                                'price'=>$value->is_unshow?('已'.TagExt::model()->findByPk($value->sale_status)->name):(!$value->price?'待定':$value->price),
                                'unit'=>$value->is_unshow||(!$value->price)?'':PlotExt::$unit[$value->unit],
                                'area'=>$areaName,
                                'street'=>$streetName,
                                'image'=>ImageTools::fixImage($value->image?$value->image:$info_no_pic,220,164),
                                'zd_company'=>$companydes,
                                'pay'=>$value->first_pay,
                                'sort'=>$value->qjsort,    
                                'wylx'=>$wyw,   
                                'distance'=>(object) array('map_lng' => $value->map_lng,'map_lat' => $value->map_lat),
                                'obj'=>$value,
                            ];
                        }
                        $pager = $plots->pagination;
                        return ['list'=>$lists,'page'=>1,'num'=>$pager->itemCount,'page_count'=>$pager->pageCount,];
                    }

                });
    }

    public static function getFirstListFromArea() {
        return CacheExt::gas('wap_area_plotlist','AreaExt',0,'wap区域数据列表',function (){
                    $datas = [];
                    $info_no_pic = SiteExt::getAttr('qjpz','info_no_pic');
                    $areas = AreaExt::model()->normal()->findAll('parent=0');
                    if($areas) {
                        foreach ($areas as $area) {
                            $criteria = new CDbCriteria;
                            $criteria->addCondition('area='.$area->id);
                            $criteria->limit = 20;
                            $criteria->order = 'sort desc,created desc';
                            $plots = PlotExt::model()->normal()->getList($criteria);
                            if($datares = $plots->data) {
                                foreach ($datares as $key => $value) {
                                    // if($area = $value->areaInfo)
                                    $areaName = $area->name;
                                    // else
                                    //     $areaName = '';
                                    if($street = $value->streetInfo)
                                        $streetName = $street->name;
                                    else
                                        $streetName = '';
                                    $companydes = ['id'=>$value->company_id,'name'=>$value->company_name];
                                        
                                    // var_dump(Yii::app()->user->getIsGuest());exit;
                                    // if(Yii::app()->user->getIsGuest()) {
                                    //     $pay = '';
                                    // } elseif($pays = $value->pays) {
                                    //     $pay = $pays[0]['price'].(count($pays)>1?'('.count($pays).'个方案)':'');
                                    // } else {
                                    //     $pay = '';
                                    // }
                                    $wyw = '';
                                    $wylx = $value->wylx;
                                    if($wylx) {
                                     if(!is_array($wylx)) 
                                         $wylx = [$wylx];
                                     foreach ($wylx as $w) {
                                         $t = TagExt::model()->findByPk($w)->name;
                                         $t && $wyw .= $t.' ';
                                     }
                                     $wyw = trim($wyw);
                                    }
                                    $lists[] = [
                                        'id'=>$value->id,
                                        'title'=>Tools::u8_title_substr($value->title,18),
                                        'price'=>$value->price,
                                        'unit'=>PlotExt::$unit[$value->unit],
                                        'area'=>$areaName,
                                        'street'=>$streetName,
                                        'image'=>ImageTools::fixImage($value->image?$value->image:$info_no_pic,220,164),
                                        'zd_company'=>$companydes,
                                        'pay'=>$value->first_pay,
                                        'sort'=>$value->sort, 
                                        'wylx'=>$wyw,   
                                        'distance'=>(object) array('map_lng' => $value->map_lng,'map_lat' => $value->map_lat),
                                        'obj'=>$value,
                                    ];
                                }
                                $pager = $plots->pagination;
                                $datas[$area->id] = ['list'=>$lists,'page'=>1,'num'=>$pager->itemCount,'page_count'=>$pager->pageCount,];
                                unset($lists);
                            }

                        }

                    }
                    return $datas;
                            

                });
    }

    public function changeS()
    {
        $area = $this->area;
        $street = $this->street;
        $wylx = $this->wylx?$this->wylx[0]:0;
        $zxzt = $this->zxzt?$this->zxzt[0]:0;
        $price = $this->price/1000;

        $sql = "select distinct u.qf_uid from user u left join subscribe s on s.uid=u.id where u.qf_uid>0 and (s.area=$area or s.area=0) and (s.street=$street or s.street=0) and (s.wylx=$wylx or s.wylx=0) and (s.zxzt=$zxzt or s.zxzt=0)";
        
        $uids = Yii::app()->db->createCommand($sql)->queryAll();
        // var_dump($uids);exit;
        if($uids) {
            foreach ($uids as $key => $value) {
                Yii::app()->controller->sendNotice('有新的项目符合您的订阅条件：'.$this->title.' 已上线，欢迎前往经纪圈新房通查看。点这里查看项目详情：'.Yii::app()->request->getHostInfo().'/api/index/detail?id='.$this->id,$value['qf_uid']);
            }
        }
        if($owner = $this->owner) {
            $owner->qf_uid && Yii::app()->controller->sendNotice('恭喜您，'.$this->title.'已通过审核并已上线。点这里预览项目详情：'.Yii::app()->request->getHostInfo().'/api/index/detail?id='.$this->id,$owner->qf_uid);
            SmsExt::sendMsg('项目通过审核',$owner->phone,['lpmc'=>$this->title]);
//             if($owner->jjq_openid)
//                 Yii::app()->controller->sendWxNo($owner->jjq_openid,'审核提醒',Yii::app()->request->getHostInfo().'/api/index/detail?id='.$this->id,['first'=>'恭喜您，您的项目'.$this->title.'已经审核通过！',
// 'keyword1'=>'通过',
// 'keyword2'=>date('Y-m-d H:i'),
// 'remark'=>'请点击详情进行预览，更多精彩请关注经纪圈APP']);
            // 恭喜您，${lpmc}已通过后台编辑的完善和审核，请登录经纪圈APP消息列表查看付费链接。
        }
    }

    public function getTags()
    {
        $tagsid = array_merge($this->wylx,$this->zxzt);
        $criteria = new CDbCriteria;
        $criteria->addInCondition('id',$tagsid);
        $data = [];
        if($tagsr = TagExt::model()->findAll($criteria)) {
            foreach ($tagsr as $key => $value) {
                $data[] = ['id'=>$value->id,'name'=>$value->name];
            }
        }
        // $this->dllx && array_unshift($data, ['id'=>0,'name'=>Yii::app()->params['dllx'][$this->dllx]]);
        
        return $data;
    }

    public function afterDelete()
    {
        //删除项目对接人
        Yii::app()->db->createCommand("delete from plot_market_user where hid=".$this->id)->execute();
        parent::afterDelete();
    }

}