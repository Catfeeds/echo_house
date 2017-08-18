<?php
/**
 * 系统初始化
 * CHANGELOG:
 * 2016年8月23日更改：将所有使用AR类模型插入初始化数据的方式改成sql插入，由于后期该表会对AR模型更新，所以不能用新的AR类操作旧的数据表结构
 */
class SysCommand extends CConsoleCommand
{
    /**
     * 初始化操作
     * 1、初始RBAC权限
     * 2、初始后台管理员帐号
     */
    public function actionInit()
    {
        $tables = Yii::app()->db->getSchema()->getTableNames();
        if(!empty($tables)){
            echo "数据库已初始化过，停止操作";
            die;
        }
        if(!$this->confirm('是否确定初始化操作，这将重建所有表和清空数据！')) return false;
        //建表
        $this->initTables();

        $transaction = Yii::app()->db->beginTransaction();
        try
        {
            //创建管理员帐号，默认密码1234567
            Yii::app()->db->createCommand("INSERT INTO `admin` VALUES ('1', 'admin', '0192023a7bbd73250516f069df18b500', '', '1', '0', '0', '0','0')")->execute();
            //初始化权限
            $this->actionInitRbac();
            //初始化数据
            $this->actionInitData();
            $this->actionInitBaikeCate();
            $transaction->commit();
            echo "finished\n";
        }
        catch(Exception $e)
        {
            $transaction->rollBack();
            echo "failed:\n";
            echo $e->getMessage()."\n";
        }
    }

    /**
     * 初始化RBAC权限
     */
    public function actionInitRbac()
    {
        $auth = Yii::app()->authManager;
        //初始化两个角色
        $admin = $auth->createRole('admin','管理员',1,'系统管理员');
        $staff = $auth->createRole('staff','工作人员',1,'工作人员');

        //集客模块
        $task = $auth->createTask('jike','集客管理',1,'集客管理模块');

        $auth->createOperation('guanlijike','管理集客信息',1,'可查看和编辑集客信息');//操作节点
        $task->addChild('guanlijike');
        $admin->addChild('guanlijike');

        $auth->createOperation('maifangguwen','管理买房顾问',1,'对买房顾问的帐号进行管理');//操作节点
        $task->addChild('maifangguwen');
        $admin->addChild('maifangguwen');

        $auth->createOperation('jihuochi','管理激活池',1,'对买房顾问认定为失效或休眠的用户单独管理');//操作节点
        $task->addChild('jihuochi');
        $admin->addChild('jihuochi');

        //系统
        $task = $auth->createTask('xitong','系统管理',1,'系统模块管理');

        $auth->createOperation('gongzuorenyuan','工作人员管理',1,'可添加、修改工作人员信息和密码，权限管理');//管理工作人员
        $task->addChild('gongzuorenyuan');
        $admin->addChild('gongzuorenyuan');

        $auth->createOperation('zhandianpeizhi','站点配置',1,'可配置站点信息、LOGO等，以及各种数据接口的配置');//站点配置
        $task->addChild('zhandianpeizhi');
        $admin->addChild('zhandianpeizhi');

        $auth->createOperation('guanggaoguanli','广告管理',1,'可添加\删除广告');//站点配置
        $task->addChild('guanggaoguanli');
        $admin->addChild('guanggaoguanli');

        //内容管理
        $task = $auth->createTask('neirong','内容管理',1,'除集客管理之外的内容编辑管理');

		$auth->createOperation('guanlineirong','管理内容',1,'可查看和编辑普通的非集客内容，如团购、看房团、楼盘信息等内容');//操作节点
		$task->addChild('guanlineirong');
        $admin->addChild('guanlineirong');
        //////////////////////RBAC结束////////////////////////

        $auth->assign('admin',1);   //授权管理员角色

        echo "finished:RBAC\n";
    }

    /**
	 * 导入数据表结构
	 * @return void
	 */
	public function initTables()
	{
		$sql = file_get_contents(__DIR__.'/../data/hj_house.sql');
		$a = explode(';', trim(trim($sql),';'));
		foreach($a as $b)
		{
			$c = $b.';';
			Yii::app()->db->createCommand($c)->execute();
		}
	}

    /**
     * 初始化数据
     * 这些数据不写在建表sql中，系统初始化时再插入
     * @return void
     */
    public function actionInitData()
    {
        //推荐位
        $this->initRecomCate();
        //内置标签
        $this->actionInitTag();
    }

    /**
     * 初始化推荐位数据
     */
    public function initRecomCate()
    {
        $cateArr = array(
            array('name'=>'首页', 'pinyin'=>'sy',
                'items'=>array(
                    array('name'=>'搜索框右侧广告(200x80)', 'pinyin'=>'sysskycggw'),
                    array('name'=>'新近二手房广告位(226x90)', 'pinyin'=>'syzxesfggw'),
                    array('name'=>'近期开盘广告位(226x90)', 'pinyin'=>'syzxkpggw'),
                    array('name'=>'图文轮换(1920x330)', 'pinyin'=>'sytwlh'),
                    array('name'=>'热门资讯', 'pinyin'=>'syrmzx',
                        'items' => array(
                            array('name'=>'头条', 'pinyin'=>'syrmzxtt'),
                            array('name'=>'头条下', 'pinyin'=>'syrmzxttx'),
                            array('name'=>'图文列表', 'pinyin'=>'syrmzxtwlb'),
                            array('name'=>'文字列表', 'pinyin'=>'syrmzxwzlb'),
                            array('name'=>'楼盘测评', 'pinyin'=>'syrmzxlpcp',
                                'items'=>array(
                                    array('name'=>'图片(220x160)','pinyin'=>'syrmzxlpcptp'),
                                    array('name'=>'文字列表', 'pinyin'=>'syrmzxlpcpwzlb')
                                )
                            )
                        )
                    ),
                    array('name'=>'新房', 'pinyin'=>'syxf',
                        'items' => array(
                            array('name'=>'新盘-热门楼盘(200x145)','pinyin'=>'syxfrmlp'),
                            array('name'=>'新盘-近期开盘(200x145)','pinyin'=>'syxfzxkp'),
                            array('name'=>'新盘-刚需楼盘(200x145)','pinyin'=>'syxfgxlp'),
                            array('name'=>'新盘-婚房推荐(200x145)','pinyin'=>'syxfhftj'),
                            array('name'=>'新盘-学区房(200x145)','pinyin'=>'syxfxqf'),
                        )
                    ),
                    array('name'=>'通栏','pinyin'=>'sytl',
                        'items'=>array(
                            array('name'=>'左侧列表','pinyin'=>'sytlzc'),
                            array('name'=>'右侧列表','pinyin'=>'sytlyc')
                        )
                    ),
                    array('name'=>'业主小区','pinyin'=>'syyzxq'),
                    array('name'=>'合作商家(150x60)','pinyin'=>'syhzsj'),
                    array('name'=>'友情链接','pinyin'=>'syyqlj'),
                    array('name'=>'资讯模块二（默认隐藏）','pinyin'=>'syzxmk2',
                        'items'=>array(
                            array('name'=>'左侧文字列表','pinyin'=>'syzxmk2zcwzlb'),
                            array('name'=>'中间图文列表','pinyin'=>'syzxmk2zjtwlb'),
                            array('name'=>'右侧单图文','pinyin'=>'syzxmk2ycdtw')
                        )
                    )
                )
            ),
            array('name'=>'看房团','pinyin'=>'kft',
                'items' => array(
                    array('name'=>'精彩回顾(360x270)','pinyin'=>'kftjchg'),
                )
            ),
            array('name'=>'学区房','pinyin'=>'xqf',
                'items'=>array(
                    array('name'=>'学区房推荐(270x200)','pinyin'=>'xqftj'),
                )
            ),
            array('name'=>'wap首页','pinyin'=>'wapsy',
                'items'=>array(
                    array('name'=>'wap首页图文轮换(640x300)','pinyin'=>'wapsytwlh')
                ),
            ),
            array('name'=>'热搜推荐','pinyin'=>'rstj',
                'items'=>array(
                    array('name'=>'PC端问答热搜', 'pinyin'=>'pcwdrs'),
                    array('name'=>'pc端热搜推荐', 'pinyin'=>'pcrstj'),
                )
            ),
            /*其他站点去除小首页推荐位
            array('name'=>'小首页','pinyin'=>'xsy',
                'items'=>array(
                    array('name'=>'小首页一','pinyin'=>'xsy1',
                        'items'=>array(
                            array('name'=>'类目一','pinyin'=>'xsy1lm1'),
                            array('name'=>'类目二','pinyin'=>'xsy1lm2'),
                            array('name'=>'类目三','pinyin'=>'xsy1lm3'),
                            array('name'=>'类目四','pinyin'=>'xsy1lm4'),
                        )
                    ),
                    array('name'=>'小首页二','pinyin'=>'xsy2'),
                )
            ),*/
            array('name'=>'买房顾问','pinyin'=>'mfgw',
                'items'=>array(
                    array('name'=>'看房进行时','pinyin'=>'mfgwkfjxs'),
                    array('name'=>'买房报喜','pinyin'=>'mfgwmfbx'),
                    array('name'=>'买房日记','pinyin'=>'mfgwmfrj'),
                )
            ),
        );
        $this->importRecomCateRecursive($cateArr);
        echo "finished:catelist\n";
    }

    /**
     * 初始化内置标签
     */
    public function actionInitTag()
    {
        $tags = array(
            'xszt' => array('待售','在售','尾盘','售罄'),
            'zxzt' => array('毛坯','简装','精装','公共部分精装'),
            'gmsj' => array('观望了解','随时购买','一个月内','三个月内','半年内','一年内'),
            'xcfl' => array('配套图','项目现场','样板间','效果图','实景图','交通图','活动图','工程进度','沙盘图'),
            'jzlb' => array('小高层','多层','低层','高层','超高层','独栋别墅','叠拼','双拼','联排','花园洋房','写字楼','大平层','小公寓','商铺','别墅'),
            'wylx' => array('复式挑高','跃层住宅','大平层','酒店式公寓','花园洋房','两限房','商铺','写字楼','别墅','住宅','经济适用房'),
            'xmts' => array('宜居生态地产','国际化社区','特色别墅','豪华居住区','景观居所','科技住宅','小户型','品牌地产','学区房','水景地产','低总价','BRT沿线房','现房','创意地产','湖景地产','配套商品房','旅游地产','经济住宅','低密居所','地铁沿线','复合地产','公园地产','投资地产','养老地产','南北通透','赠送面积'),
            'jglb' => array('高层','小高层','花园洋房','别墅','商铺','多层','小公寓','大平层','写字楼'),
        );
        $sql = '';
        foreach($tags as $cate=>$v)
        {
            foreach($v as $vv)
            {
                $sql .= "INSERT INTO `tag` (`name`, `cate`, `status`, `created`) VALUES ('".$vv."', '".$cate."', '1', '".time()."');";
            }
        }
        Yii::app()->db->createCommand($sql)->execute();
        echo "finished:tag\n";
    }

    /**
     * 递归导入推荐位
     */
    private function importRecomCateRecursive($items, $parent=0)
    {
        if(is_array($items)&&isset($items['items']))
        {
            $new_items = $items['items'];
            // $model = new RecomCateExt;
            // $model->attributes = array(
            //     'name' => $items['name'],
            //     'pinyin' => $items['pinyin'],
            //     'parent' => $parent,
            // );
            // $model->save();
            $sql = 'INSERT INTO `recom_cate` (`name`, `pinyin`, `parent`, `status`, `created`) VALUES ("'.$items['name'].'", "'.$items['pinyin'].'", "'.$parent.'", "1", "'.time().'");';
            Yii::app()->db->createCommand($sql)->execute();

            $this->importRecomCateRecursive($items['items'], Yii::app()->db->getLastInsertID());
        }
        elseif(is_array($items)&&isset($items['name'])&&isset($items['pinyin']))
        {
            // $model = new RecomCateExt;
            // $model->attributes = array(
            //     'name' => $items['name'],
            //     'pinyin' => $items['pinyin'],
            //     'parent' => $parent,
            // );
            // $model->save();
            $sql = 'INSERT INTO `recom_cate` (`name`, `pinyin`, `parent`, `status`, `created`) VALUES ("'.$items['name'].'", "'.$items['pinyin'].'", "'.$parent.'", "1", "'.time().'");';
            Yii::app()->db->createCommand($sql)->execute();
        }
        elseif(is_array($items))
        {
            foreach($items as $item)
            {
                $this->importRecomCateRecursive($item, $parent);
            }
        }
    }

    public function actionInitBaikeCate()
    {
        $baikeCates = [
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
        $this->importBaikeCateRecursive($baikeCates);
        echo "finished:baikecate\n";
    }

    private function importBaikeCateRecursive($cates=null, $parent=0)
    {
        if(is_array($cates) && isset($cates['name']) && isset($cates['pinyin']) && isset($cates['items'])) {//一级
            $childs = $cates['items'];
            unset($cates['items']);
            // $model = new BaikeCateExt;
            // $model->attributes = $cates;
            // if(!$model->save()) {
            //     $msg = $model->hasErrors() ? current(current($model->getErrors())) : 'code:1';
            //     throw new Exception("百科分类添加失败,".$msg);
            // }
            $belong = isset($cates['belong']) ? $cates['belong'] : 0;
            $sql = 'INSERT INTO `baike_cate` (`name`, `pinyin`, `parent`, `status`,`belong`,`created`) VALUES ("'.$cates['name'].'", "'.$cates['pinyin'].'", "'.$parent.'", "1","'.$belong.'", "'.time().'");';
            $row = Yii::app()->db->createCommand($sql)->execute();
            if(!$row) {
                throw new Exception('百科分类插入数为0');
            }
            $lastId = Yii::app()->db->getLastInsertID();
            $this->importBaikeCateRecursive($childs, $lastId);

        }elseif(is_array($cates) && isset($cates['name']) && isset($cates['pinyin'])){//二级
            // $model = new BaikeCateExt;
            // $model->attributes = $cates;
            // $model->parent = $parent;
            // if(!$model->save()) {
            //     $msg = $model->hasErrors() ? current(current($model->getErrors())) : 'code:2';
            //     throw new Exception("百科分类添加失败,".$msg);
            // }
            $belong = isset($cates['belong']) ? $cates['belong'] : 0;
            $sql = 'INSERT INTO `baike_cate` (`name`, `pinyin`, `parent`, `status`,`belong`,`created`) VALUES ("'.$cates['name'].'", "'.$cates['pinyin'].'", "'.$parent.'", "1","'.$belong.'", "'.time().'");';
            $row = Yii::app()->db->createCommand($sql)->execute();
            if(!$row) {
                throw new Exception('百科分类插入数为0');
            }
        }elseif(is_array($cates)) {//最外部大循环
            foreach($cates as $v) {
                $this->importBaikeCateRecursive($v,$parent);
            }
        }
    }
}
