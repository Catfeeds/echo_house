<?php

/**
 * 工具类脚本
 */
class ToolCommand extends CConsoleCommand
{
    /**
     * 同步本地静态文件到七牛
     */
    public function actionQnSync()
    {
        $basePath = Yii::app()->basePath;
        $baseDir = Yii::app()->name;
        $date = date('YmdHis');
        $QnUrl = Yii::app()->staticFile->host.'/';
        $fileArr = [
            'pro.js' => '/resoldwap/build/pro.js'
        ];
        echo "Start Sync:\n";
        echo "Version:{$date}\n";
        echo "==========================\n";
        foreach ($fileArr as $name => $path) {
            $path = $basePath.'/../'.$path;
            $extPath = $baseDir.'/'.$date.'/'.$name;
            $r = Yii::app()->staticFile->consoleFileUpload($path, $extPath);

            if (isset($r['key'])) {
                echo $QnUrl.$r['key']."\n";
            } else {
                var_dump($r);
            }
        }
    }
    public function actionDo()
    {
        // $infos = PlotExt::model()->normal()->findAll();
        // 更新所有会员项目的到期时间
        $time = time();
        $vips = Yii::app()->db->createCommand("select id,vip_expire,vip_expire_new from user where vip_expire>$time or vip_expire_new>$time")->queryAll();
        if($vips) {
            foreach ($vips as $key => $value) {
                $expire = $value['vip_expire_new']>$value['vip_expire']?$value['vip_expire_new']:$value['vip_expire'];
                // $plot = PlotExt::model()->findAll("uid=".$value['id']);
                Yii::app()->db->createCommand("update plot_makert_user set expire=$expire where uid=".$value['id'])->execute();
            }
        }
        // $sql = "delete from company where name='南京好理想房地产经纪有限公司'";
        // Yii::app()->db->createCommand($sql)->execute();
        // // var_dump(count($infos));exit;
        // foreach ($infos as $key => $value) {
        //     // if(!$value->first_pay && $value->pays) {
        //     //     $value->first_pay = $value->pays[0]['price'];
        //     // }
        //     $value->save();
        //     // sleep(1);
        // }
        echo "ok";
    }

    public function actionAddPlotViews()
    {
        $hids = Yii::app()->redis->getClient()->hGetAll('plot_views');
        // var_dump($hids);exit;
        if($hids) {
            foreach ($hids as $key => $value) {
                $plot = PlotExt::model()->findByPk($key);
                if(!$plot) {
                    continue;
                }
                $plot->views+=$value;
                $plot->save();
                Yii::app()->redis->getClient()->hSet('plot_views',$key,0);
            }
        }
        echo "finished";
    }

    public function actionClearTop()
    {
        $plots = PlotExt::model()->findAll('qjsort>0 and qjtop_time<'.time());
        if($plots) {
            foreach ($plots as $key => $value) {
                $value->qjsort = 0;
                $value->save();
            }
        }
        $plots = PlotExt::model()->findAll('sort>0 and top_time<'.time());
        if($plots) {
            foreach ($plots as $key => $value) {
                $value->sort = 0;
                $value->save();
            }
        }
    }

    public function actionClearListCache()
    {
        // wap_init_plotlist
        CacheExt::delete('wap_init_plotlist');
    }

    public function actionSendNo()
    {
        $infos = UserExt::model()->findAll('vip_expire>'.time().' and vip_expire<'.(time()+86400*3));
        // $infos = PlotMarketUserExt::model()->findAll('expire>'.time().' and expire<'.time()+86400*3);
        foreach ($infos as $key => $user) {
            if($user && $user->phone) {
                SmsExt::sendMsg('会员到期通知',$user->phone,['phone'=>SiteExt::getAttr('qjpz','site_wx'),'name'=>$user->name]);
            }
            // $user = $value->user;
            // if($user&&$user->qf_uid) {
            //     Yii::app()->controller->sendNotice($user->name.'您好，您的新房通VIP会员即将到期，请点击以下链接自助续费: http://house.jj58.com.cn/api/index/vip ，或者联系客服微信:'.SiteExt::getAttr('qjpz','site_wx').'协助续费。',$user->qf_uid);
            //     // if($p = $value->plot) {
            //     //     SmsExt::sendMsg('到期通知',$user->phone,['pro'=>$p->title]);
            //     //     Yii::app()->controller->sendNotice('您的项目'.$p->title.'即将到期，请点击下面链接成为会员，成为会员后您的号码将继续展现，并且可以无限次数发布项目。 http://house.jj58.com.cn/api/index/vip',$user->qf_uid);
            //     // }
            // }
        }
    }

    public function actionAddArea()
    {
        $city_arr = array('安徽'
            => array(
            '合肥(*)', '合肥',
            '安庆', '安庆',
            '蚌埠', '蚌埠',
            '亳州', '亳州',
            '巢湖', '巢湖',
            '滁州', '滁州',
            '阜阳', '阜阳',
            '贵池', '贵池',
            '淮北', '淮北',
            '淮化', '淮化',
            '淮南', '淮南',
            '黄山', '黄山',
            '九华山', '九华山',
            '六安', '六安',
            '马鞍山', '马鞍山',
            '宿州', '宿州',
            '铜陵', '铜陵',
            '屯溪', '屯溪',
            '芜湖', '芜湖',
            '宣城', '宣城'),
             
         '福建'
            => array(
            '福州(*)', '福州',
            '福安', '福安',
            '龙岩', '龙岩',
            '南平', '南平',
            '宁德', '宁德',
            '莆田', '莆田',
            '泉州', '泉州',
            '三明', '三明',
            '邵武', '邵武',
            '石狮', '石狮',
            '晋江', '晋江',
            '永安', '永安',
            '武夷山', '武夷山',
            '厦门', '厦门',
            '漳州', '漳州'),
              
         '甘肃'
            => array(
            '兰州(*)', '兰州',
            '白银', '白银',
            '定西', '定西',
            '敦煌', '敦煌',
            '甘南', '甘南',
            '金昌', '金昌',
            '酒泉', '酒泉',
            '临夏', '临夏',
            '平凉', '平凉',
            '天水', '天水',
            '武都', '武都',
            '武威', '武威',
            '西峰', '西峰',
            '嘉峪关','嘉峪关',
            '张掖', '张掖'),
             
         '广东'
            => array(
            '广州(*)', '广州',
            '潮阳', '潮阳',
            '潮州', '潮州',
            '澄海', '澄海',
            '东莞', '东莞',
            '佛山', '佛山',
            '河源', '河源',
            '惠州', '惠州',
            '江门', '江门',
            '揭阳', '揭阳',
            '开平', '开平',
            '茂名', '茂名',
            '梅州', '梅州',
            '清远', '清远',
            '汕头', '汕头',
            '汕尾', '汕尾',
            '韶关', '韶关',
            '深圳', '深圳',
            '顺德', '顺德',
            '阳江', '阳江',
            '英德', '英德',
            '云浮', '云浮',
            '增城', '增城',
            '湛江', '湛江',
            '肇庆', '肇庆',
            '中山', '中山',
            '珠海', '珠海'),
             
         '广西'
            => array(
            '南宁(*)', '南宁',
            '百色', '百色',
            '北海', '北海',
            '桂林', '桂林',
            '防城港', '防城港',
            '河池', '河池',
            '贺州', '贺州',
            '柳州', '柳州',
            '来宾', '来宾',
            '钦州', '钦州',
            '梧州', '梧州',
            '贵港', '贵港',
            '玉林', '玉林'),
             
         '贵州'
            => array(
            '贵阳(*)', '贵阳',
            '安顺', '安顺',
            '毕节', '毕节',
            '都匀', '都匀',
            '凯里', '凯里',
            '六盘水', '六盘水',
            '铜仁', '铜仁',
            '兴义', '兴义',
            '玉屏', '玉屏',
            '遵义', '遵义'),
             
         '海南'
            => array(
            '海口(*)', '海口',
    '三亚', '三亚',
    '五指山', '五指山',
    '琼海', '琼海',
    '儋州', '儋州',
    '文昌', '文昌',
    '万宁', '万宁',
    '东方', '东方',
    '定安', '定安',
    '屯昌', '屯昌',
    '澄迈', '澄迈',
    '临高', '临高',
    '万宁', '万宁',
    '白沙黎族', '白沙黎族',
    '昌江黎族', '昌江黎族',
    '乐东黎族', '乐东黎族',
    '陵水黎族', '陵水黎族',
    '保亭黎族', '保亭黎族',
    '琼中黎族', '琼中黎族',
    '西沙群岛', '西沙群岛',
    '南沙群岛', '南沙群岛',
    '中沙群岛', '中沙群岛'
            ),
             
         '河北'
            => array(
            '石家庄(*)', '石家庄',
            '保定', '保定',
            '北戴河', '北戴河',
            '沧州', '沧州',
            '承德', '承德',
            '丰润', '丰润',
            '邯郸', '邯郸',
            '衡水', '衡水',
            '廊坊', '廊坊',
            '南戴河', '南戴河',
            '秦皇岛', '秦皇岛',
            '唐山', '唐山',
            '新城', '新城',
            '邢台', '邢台',
            '张家口', '张家口'),
             
         '黑龙江'
            => array(
            '哈尔滨(*)', '哈尔滨',
            '北安', '北安',
            '大庆', '大庆',
            '大兴安岭', '大兴安岭',
            '鹤岗', '鹤岗',
            '黑河', '黑河',
            '佳木斯', '佳木斯',
            '鸡西', '鸡西',
            '牡丹江', '牡丹江',
            '齐齐哈尔', '齐齐哈尔',
            '七台河', '七台河',
            '双鸭山', '双鸭山',
            '绥化', '绥化',
            '伊春', '伊春'),
             
         '河南'
            => array(
            '郑州(*)', '郑州',
            '安阳', '安阳',
            '鹤壁', '鹤壁',
            '潢川', '潢川',
            '焦作', '焦作',
            '济源', '济源',
            '开封', '开封',
            '漯河', '漯河',
            '洛阳', '洛阳',
            '南阳', '南阳',
            '平顶山', '平顶山',
            '濮阳', '濮阳',
            '三门峡', '三门峡',
            '商丘', '商丘',
            '新乡', '新乡',
            '信阳', '信阳',
            '许昌', '许昌',
            '周口', '周口',
            '驻马店', '驻马店'),
             
         '香港'
            => array(
            '香港', '香港',
            '九龙', '九龙',
            '新界', '新界'),
             
         '湖北'
            => array(
            '武汉(*)', '武汉',
            '恩施', '恩施',
            '鄂州', '鄂州',
            '黄冈', '黄冈',
            '黄石', '黄石',
            '荆门', '荆门',
            '荆州', '荆州',
            '潜江', '潜江',
            '十堰', '十堰',
            '随州', '随州',
            '武穴', '武穴',
            '仙桃', '仙桃',
            '咸宁', '咸宁',
            '襄阳', '襄阳',
            '襄樊', '襄樊',
            '孝感', '孝感',
            '宜昌', '宜昌'),
             
         '湖南'
            => array(
            '长沙(*)', '长沙',
            '常德', '常德',
            '郴州', '郴州',
            '衡阳', '衡阳',
            '怀化', '怀化',
            '吉首', '吉首',
            '娄底', '娄底',
            '邵阳', '邵阳',
            '湘潭', '湘潭',
            '益阳', '益阳',
            '岳阳', '岳阳',
            '永州', '永州',
            '张家界', '张家界',
            '株洲', '株洲'),
             
         '江苏'
            => array(
            '南京(*)', '南京',
            '常熟', '常熟',
            '常州', '常州',
            '海门', '海门',
            '淮安', '淮安',
            '江都', '江都',
            '江阴', '江阴',
            '昆山', '昆山',
            '连云港', '连云港',
            '南通', '南通',
            '启东', '启东',
            '沭阳', '沭阳',
            '宿迁', '宿迁',
            '苏州', '苏州',
            '太仓', '太仓',
            '泰州', '泰州',
            '同里', '同里',
            '无锡', '无锡',
            '徐州', '徐州',
            '盐城', '盐城',
            '扬州', '扬州',
            '宜兴', '宜兴',
            '仪征', '仪征',
            '张家港', '张家港',
            '镇江', '镇江',
            '周庄', '周庄'),
             
         '江西'
            => array(
            '南昌(*)', '南昌',
            '抚州', '抚州',
            '赣州', '赣州',
            '吉安', '吉安',
            '景德镇', '景德镇',
            '井冈山', '井冈山',
            '九江', '九江',
            '庐山', '庐山',
            '萍乡', '萍乡',
            '上饶', '上饶',
            '新余', '新余',
            '宜春', '宜春',
            '鹰潭', '鹰潭'),
             
         '吉林'
            => array(
            '长春(*)', '长春',
            '白城', '白城',
            '白山', '白山',
            '珲春', '珲春',
            '辽源', '辽源',
            '梅河', '梅河',
            '吉林', '吉林',
            '四平', '四平',
            '松原', '松原',
            '通化', '通化',
            '延吉', '延吉'),
         '辽宁'
            => array(
            '沈阳(*)', '沈阳',
            '鞍山', '鞍山',
            '本溪', '本溪',
            '朝阳', '朝阳',
            '大连', '大连',
            '丹东', '丹东',
            '抚顺', '抚顺',
            '阜新', '阜新',
            '葫芦岛', '葫芦岛',
            '锦州', '锦州',
            '辽阳', '辽阳',
            '盘锦', '盘锦',
            '铁岭', '铁岭',
            '营口', '营口'),
             
         '澳门'
            => array(
            '澳门', '澳门'),
             
         '内蒙古'
            => array(
            '呼和浩特(*)', '呼和浩特',
            '阿拉善盟', '阿拉善盟',
            '包头', '包头',
            '赤峰', '赤峰',
            '东胜', '东胜',
            '海拉尔', '海拉尔',
            '集宁', '集宁',
            '临河', '临河',
            '通辽', '通辽',
            '乌海', '乌海',
            '乌兰浩特', '乌兰浩特',
            '锡林浩特', '锡林浩特'),
             
         '宁夏'
            => array(
            '银川(*)', '银川',
            '固原', '固原',
            '中卫', '中卫',
            '石嘴山', '石嘴山',
            '吴忠', '吴忠'),
             
         '青海'
            => array(
            '西宁(*)', '西宁',
            '德令哈', '德令哈',
            '格尔木', '格尔木',
            '共和', '共和',
            '海东', '海东',
            '海晏', '海晏',
            '玛沁', '玛沁',
            '同仁', '同仁',
            '玉树', '玉树'),
             
         '山东'
            => array(
            '济南(*)', '济南',
            '滨州', '滨州',
            '兖州', '兖州',
            '德州', '德州',
            '东营', '东营',
            '菏泽', '菏泽',
            '济宁', '济宁',
            '莱芜', '莱芜',
            '聊城', '聊城',
            '临沂', '临沂',
            '蓬莱', '蓬莱',
            '青岛', '青岛',
            '曲阜', '曲阜',
            '日照', '日照',
            '泰安', '泰安',
            '潍坊', '潍坊',
            '威海', '威海',
            '烟台', '烟台',
            '枣庄', '枣庄',
            '淄博', '淄博'),
             
             
         '山西'
            => array(
            '太原(*)', '太原',
            '长治', '长治',
            '大同', '大同',
            '候马', '候马',
            '晋城', '晋城',
            '离石', '离石',
            '临汾', '临汾',
            '宁武', '宁武',
            '朔州', '朔州',
            '忻州', '忻州',
            '阳泉', '阳泉',
            '榆次', '榆次',
            '运城', '运城'),
             
         '陕西'
            => array(
            '西安(*)', '西安',
            '安康', '安康',
            '宝鸡', '宝鸡',
            '汉中', '汉中',
            '渭南', '渭南',
            '商州', '商州',
            '绥德', '绥德',
            '铜川', '铜川',
            '咸阳', '咸阳',
            '延安', '延安',
            '榆林', '榆林'),
             
         '四川'
            => array(
            '成都(*)', '成都',
            '巴中', '巴中',
            '达州', '达州',
            '德阳', '德阳',
            '都江堰', '都江堰',
            '峨眉山', '峨眉山',
            '涪陵', '涪陵',
            '广安', '广安',
            '广元', '广元',
            '九寨沟', '九寨沟',
            '康定', '康定',
            '乐山', '乐山',
            '泸州', '泸州',
            '马尔康', '马尔康',
            '绵阳', '绵阳',
            '眉山', '眉山',
            '南充', '南充',
            '内江', '内江',
            '攀枝花', '攀枝花',
            '遂宁', '遂宁',
            '汶川', '汶川',
            '西昌', '西昌',
            '雅安', '雅安',
            '宜宾', '宜宾',
            '自贡', '自贡',
            '资阳', '资阳'),
             
         '台湾'
            => array(
            '台北(*)', '台北',
            '基隆', '基隆',
            '台南', '台南',
            '台中', '台中',
            '高雄', '高雄',
            '屏东', '屏东',
            '南投', '南投',
            '云林', '云林',
            '新竹', '新竹',
            '彰化', '彰化',
            '苗栗', '苗栗',
            '嘉义', '嘉义',
            '花莲', '花莲',
            '桃园', '桃园',
            '宜兰', '宜兰',
            '台东', '台东',
            '金门', '金门',
            '马祖', '马祖',
            '澎湖', '澎湖',
            '其它', '其它'),
             
         '天津'
            => array(
            '天津', '天津',
            '和平', '和平',
            '东丽', '东丽',
            '河东', '河东',
            '西青', '西青',
            '河西', '河西',
            '津南', '津南',
            '南开', '南开',
            '北辰', '北辰',
            '河北', '河北',
            '武清', '武清',
            '红挢', '红挢',
            '塘沽', '塘沽',
            '汉沽', '汉沽',
            '大港', '大港',
            '宁河', '宁河',
            '静海', '静海',
            '宝坻', '宝坻',
            '蓟县', '蓟县' ),
             
         '新疆'
            => array(
            '乌鲁木齐(*)', '乌鲁木齐',
            '阿克苏', '阿克苏',
            '阿勒泰', '阿勒泰',
            '阿图什', '阿图什',
            '博乐', '博乐',
            '昌吉', '昌吉',
            '东山', '东山',
            '哈密', '哈密',
            '和田', '和田',
            '喀什', '喀什',
            '克拉玛依', '克拉玛依',
            '库车', '库车',
            '库尔勒', '库尔勒',
            '奎屯', '奎屯',
            '石河子', '石河子',
            '塔城', '塔城',
            '吐鲁番', '吐鲁番',
            '伊宁', '伊宁'),
             
         '西藏'
            => array(
            '拉萨(*)', '拉萨',
            '阿里', '阿里',
            '昌都', '昌都',
            '林芝', '林芝',
            '那曲', '那曲',
            '日喀则', '日喀则',
            '山南', '山南'),
             
         '云南'
            => array(
            '昆明(*)', '昆明',
            '大理', '大理',
            '保山', '保山',
            '楚雄', '楚雄',
            '大理', '大理',
            '东川', '东川',
            '个旧', '个旧',
            '景洪', '景洪',
            '开远', '开远',
            '临沧', '临沧',
            '丽江', '丽江',
            '六库', '六库',
            '潞西', '潞西',
            '曲靖', '曲靖',
            '思茅', '思茅',
            '文山', '文山',
            '西双版纳', '西双版纳',
            '玉溪', '玉溪',
            '中甸', '中甸',
            '昭通', '昭通'),
             
         '浙江'
            => array(
            '杭州(*)', '杭州',
            '安吉', '安吉',
            '慈溪', '慈溪',
            '定海', '定海',
            '奉化', '奉化',
            '海盐', '海盐',
            '黄岩', '黄岩',
            '湖州', '湖州',
            '嘉兴', '嘉兴',
            '金华', '金华',
            '临安', '临安',
            '临海', '临海',
            '丽水', '丽水',
            '宁波', '宁波',
            '瓯海', '瓯海',
            '平湖', '平湖',
            '千岛湖', '千岛湖',
            '衢州', '衢州',
            '江山', '江山',
            '瑞安', '瑞安',
            '绍兴', '绍兴',
            '嵊州', '嵊州',
            '台州', '台州',
            '温岭', '温岭',
            '温州', '温州',
   '余姚', '余姚',
   '舟山', '舟山'),
             
         '海外'
            => array(
            '美国(*)', '美国',
            '英国', '英国', 
            '法国', '法国', 
            '瑞士', '瑞士', 
            '澳洲', '澳洲', 
            '新西兰', '新西兰', 
            '加拿大', '加拿大', 
            '奥地利', '奥地利', 
            '韩国', '韩国', 
            '日本', '日本', 
            '德国', '德国', 
   '意大利', '意大利', 
   '西班牙', '西班牙', 
   '俄罗斯', '俄罗斯', 
   '泰国', '泰国', 
   '印度', '印度', 
   '荷兰', '荷兰', 
   '新加坡', '新加坡',
            '欧洲', '欧洲',
            '北美', '北美',
            '南美', '南美',
            '亚洲', '亚洲',
            '非洲', '非洲',
            '大洋洲', '大洋洲'));

    $areas = AreaExt::model()->findAll("parent=0 and name!='上海'");
    foreach ($areas as $key => $area) {
        foreach ($city_arr as $k=>$c) {
            if(in_array($area->name, $c)) {
                if($cid = Yii::app()->db->createCommand("select id from area where parent=0 and name='$k'")->queryScalar()) {
                    $area->parent = $cid;

                } else {
                    $onj = new AreaExt;
                    $onj->name = $k;
                    $onj->parent = 0;
                    $onj->save();
                    $area->parent = $onj->id;
                }
                if($area->save()) {
                    $ccid = $area->parent;
                    $aaid = $area->id;
                    Yii::app()->db->createCommand("update plot set city=$ccid where area=$aaid")->execute();
                }
            }
        }
        echo ($key+1).'/'.count($areas).'============================';
    }
    }


    public function actionPlotRedis()
    {
        $allPlots = Yii::app()->db->createCommand("select id,title from plot")->queryAll();

        foreach ($allPlots as $key => $value) {
            Yii::app()->redis->getClient()->hSet('plot_title',$value['id'],$value['title']);
        }

    }

    public function actionPlotVirtual()
    {
        $ress = UserExt::model()->findAll(['condition'=>'status=1 and type=1 and virtual_no=""']);
        if($ress) {
            foreach ($ress as $key => $user) {
                // 如果项目不存在 跳过
                // if(!Yii::app()->db->createCommand("select id from plot where id=".$value->hid)->queryScalar()) {
                //     continue;
                // }
                // $user = $value->owner;
                // if(!$user) {
                //     continue;
                // }
                if(!$user->virtual_no) {
                    $vps = VirtualPhoneExt::model()->find(['condition'=>"max<999",'order'=>'created desc']);
                    $vp = $vps->phone;
                    $nowext = $vps->max?($vps->max+1):1;
                    $nowext = $nowext<10?('00'.$nowext):($nowext<100?('0'.$nowext):$nowext);
                    // var_dump($nowext);exit;
                    // 生成绑定
                    $obj = Yii::app()->axn;
                    $res = $obj->bindAxnExtension('默认号码池',$user->phone,$nowext,date('Y-m-d H:i:s',time()+86400*1000));
                    if($res->Code=='OK') {
                        $user->virtual_no = $res->SecretBindDTO->SecretNo;
                        $user->virtual_no_ext = $res->SecretBindDTO->Extension;
                        $user->subs_id = $res->SecretBindDTO->SubsId;
                        $user->save();
                        $newvps = VirtualPhoneExt::model()->find(['condition'=>"phone='$user->virtual_no'"]);
                        if($newvps && $user->virtual_no_ext) {
                             $newvps->max = $user->virtual_no_ext;
                         }
                        $newvps->save();
                    } else {
                        // Yii::log(json_encode($res));
                    }
                }
                echo ($key+1).'/'.count($ress).'-------------------';
            }
        }
    }

    public function actionJbVirtual()
    {
        # code...
    }

    public function actionFreePlotUser()
    {
        $num = 0;
        foreach (PlotExt::model()->findAll() as $key => $value) {
            // if($pm = $value->market_users) {
                if($value->market_users) {
                    preg_match_all('/[0-9]+/', $value->market_users, $tmps);
                    if(isset($tmps[0])) {
                        foreach ($tmps[0] as $thisphone) {
                            $user = UserExt::model()->find("phone='".$thisphone."'");
                            // 没有的话加入总代且发送短信且生成虚拟号
                            if(!$user) {
                                $user = new UserExt;
                                $user->phone = $thisphone;
                                $user->name = str_replace($thisphone, '', $value->market_users);
                                $user->type = 1;
                                $user->cid = $value->company_id;
                                $user->status = 1;
                                $user->save();
                                $num++;
                                echo $num;
                            }
                        }
                    }
                }
            // }
        }
    }

    /**
     * 每分钟自动绑定虚拟号
     */
    public function actionBandV()
    {
        // $timeb = time()-60;
        // $users = UserExt::model()->findAll('status=1 and type=1 and virtual_no=""');
        // if($users) {
        //     foreach ($users as $key => $value) {
        //         if(!$value->phone || !is_numeric($value->phone) || strlen($value->phone)!=11)
        //             continue;
        //         if(!$value->virtual_no) {
        //             $vps = VirtualPhoneExt::model()->find(['condition'=>"max<999",'order'=>'created desc']);
        //             if($vps) {
        //                 $vp = $vps->phone;
        //                 $nowext = $vps->max?($vps->max+1):1;
        //                 $nowext = $nowext<10?('00'.$nowext):($nowext<100?('0'.$nowext):$nowext);
        //                 // var_dump($nowext);exit;
        //                 // 生成绑定
        //                 $obj = Yii::app()->axn;
        //                 $res = $obj->bindAxnExtension('默认号码池',$value->phone,$nowext,date('Y-m-d H:i:s',time()+86400*1000));

        //                 if($res->Code=='OK') {
        //                     $value->virtual_no = $res->SecretBindDTO->SecretNo;
        //                     $value->virtual_no_ext = $res->SecretBindDTO->Extension;
        //                     $value->subs_id = $res->SecretBindDTO->SubsId;

        //                     $value->save();
        //                     Yii::log($this->virtual_no);
        //                     $newvps = VirtualPhoneExt::model()->find(['condition'=>"phone='$value->virtual_no'"]);
        //                     if($newvps && $value->virtual_no_ext) {
        //                         $newvps->max = $value->virtual_no_ext;
        //                         $newvps->save();
        //                     }
        //                     // $value->save();
                            
                            
        //                 } else {
        //                     // Yii::log(json_encode($res));
        //                 }
        //             }
        //         }
        //     }
        // }
    }

    public function actionSendFreeUser()
    {
        $timeb = time()-86400;
        $plots = PlotExt::model()->findAll('status=1 and market_users!="" and created>'.$timeb);

        if($plots) {
            foreach ($plots as $key => $value) {
                if($value->market_users) {
                    preg_match_all('/[0-9]+/',$value->market_users,$num);
                    if(isset($num[0]) && count($num[0])>0) {
                        foreach ($num[0] as $num) {
                            $res = Yii::app()->db->createCommand("select vip_expire from user where phone='$num'")->queryScalar();
                            if(!$res) {
                                SmsExt::sendMsg('免费对接人通知',$num,['lpmc'=>$value->title,'phone'=>SiteExt::getAttr('qjpz','site_phone')]);
                            }
                        }
                    }
                }
                
            }
        }
    }

    public function actionSendAllNo()
    {
        $page = 1;
        begin:
        $sql = "select phone,name from user where status=1 limit $page,200";
        $ress = Yii::app()->db->createCommand($sql)->queryAll();
        if($ress) {
            foreach ($ress as $key => $value) {
                if($value['phone'] && $value['name'])
                    SmsExt::sendMsg('群发虚拟号通知短信',$value['phone'],['name'=>$value['name']]);
            }
            echo $page."=====================";
            $page = $page+200;
            goto begin;
        }  else{
            echo "finished";
        }
            
    }

    public function actionSetOpenId()
    {
        $key = "495e6105d4146af1d36053c1034bc819";
        $url = "http://jj58.qianfanapi.com/api1_2/user/get-wechat-info";
        $res = $this->get_response($key,$url,['uid'=>11]);
        var_dump($res);exit;

        $page = 1;
        $key = "495e6105d4146af1d36053c1034bc819";
        $url = "http://jj58.qianfanapi.com/api1_2/user/get-wechat-info";
        begin:
        $sql = "select id,qf_uid from user where qf_uid>0 order by qf_uid asc";
        $ress = Yii::app()->db->createCommand($sql)->queryAll();
        if($ress) {
            foreach ($ress as $value) {
                $res = $this->get_response($key,$url,['uid'=>$value['qf_uid']]);
                // var_dump($res);exit;
                $res = json_decode($res,true);
                // if($res['ret']==0) {
                //     var_dump(1);
                // }
                if($res && isset($res['data']['openid'])) {
                    var_dump($value['qf_uid']);
                    Yii::app()->db->createCommand("update user set jjq_openid='".$res['data']['openid']."' where id=".$value['id'])->execute();
                    // $value->jjq_openid = $res['data']['openid'];
                    // $value->save();
                }
                // if($value['phone'] && $value['name'])
                //     SmsExt::sendMsg('群发虚拟号通知短信',$value['phone'],['name'=>$value['name']]);
            }
            echo $page."=====================";
            $page = $page+200;
            goto begin;
        }  else{
            echo "finished";
        }

        // $users = UserExt::model()->findAll('qf_uid>0');
        // foreach ($users as $key => $value) {
        //     $key = "495e6105d4146af1d36053c1034bc819";
        //     $url = "http://jj58.qianfanapi.com/api1_2/user/get-wechat-info";
        //     $res = $this->get_response($key,$url,[],['uid'=>$value->qf_uid]);
        //     if($res && isset($res['data']['openid'])) {
        //         $value->jjq_openid = $res['data']['openid'];
        //         $value->save();
        //     }
        // }
    }

    public function get_response($secret_key, $url, $get_params, $post_data = array())
    {
        $nonce         = rand(10000, 99999);
        $timestamp  = time();
        $array = array($nonce, $timestamp, $secret_key);
        sort($array, SORT_STRING);
        $token = md5(implode($array));
        $params['nonce'] = $nonce;
        $params['timestamp'] = $timestamp;
        $params['token']     = $token;
        $params = array_merge($params,$get_params);  
        $url .= '?';
        foreach ($params as $k => $v) 
        {
            $url .= $k .'='. $v . '&';
        }
        $url = rtrim($url,'&');   
        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $url);   
        curl_setopt($curlHandle, CURLOPT_HEADER, 0);   
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);  
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);  
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, FALSE);   
        curl_setopt($curlHandle, CURLOPT_POST, count($post_data));  
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $post_data);  
        $data = curl_exec($curlHandle);    
        $status = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
        curl_close($curlHandle);    
        return $data;
    }

    public function actionExeSql($value='')
    {
        $page = 1;
        // $key = "495e6105d4146af1d36053c1034bc819";
        // $url = "http://jj58.qianfanapi.com/api1_2/user/get-wechat-info";
        begin:
        $sql = "select id,adduid from company where adduid>0 limit $page,200";
        $ress = Yii::app()->db->createCommand($sql)->queryAll();
        if($ress) {
            foreach ($ress as $value) {
                $addphone = Yii::app()->db->createCommand("select phone from user where qf_uid=".$value['adduid'])->queryScalar();
                if(!$addphone) {
                    continue;
                }
                Yii::app()->db->createCommand("update company set addphone='$addphone' where id=".$value['id'])->execute();
            }
            echo $page."=====================";
            $page = $page+200;
            goto begin;
        }  else{
            echo "finished";
        }
    }

    public function actionImportUser()
    {
        $arr = [['com'=>'我爱我家嘉泰馨庭店2组','name'=>'杨欣泉','phone'=>'19941362754'],
['com'=>'我爱我家景上公寓店1组','name'=>'金哲恒','phone'=>'18989874479'],
['com'=>'链家地产景芳五区店','name'=>'冉明星','phone'=>'18989464601'],
['com'=>'易房地产闲林店买卖2组','name'=>'何振荣','phone'=>'18969959698'],
['com'=>'亿都房产滨盛店','name'=>'饶志辉','phone'=>'18969056842'],
['com'=>'我爱我家复兴店2组','name'=>'张勇锋','phone'=>'18969021195'],
['com'=>'祥联房产钱江世纪城店','name'=>'陈检','phone'=>'18968148538'],
['com'=>'我爱我家麒麟街店1组','name'=>'徐伟康','phone'=>'18968131390'],
['com'=>'信业居房产华瑞晴庐店','name'=>'朱燕平','phone'=>'18968009597'],
['com'=>'骐骥地产骐骥地产和美家店','name'=>'周丽','phone'=>'18967136757'],
['com'=>'豪世华邦豪世华邦望江门店','name'=>'叶增耀','phone'=>'18967112089'],
['com'=>'我爱我家春和钱塘店2组','name'=>'黄玉','phone'=>'18958156716'],
['com'=>'我爱我家风景大院店2组','name'=>'吴宝丰','phone'=>'18906536597'],
['com'=>'益邦地产月明1店','name'=>'林安妮','phone'=>'18869957465'],
['com'=>'益邦地产月明2店','name'=>'王美颖','phone'=>'18869944765'],
['com'=>'我爱我家孩儿巷店1组','name'=>'谢强强','phone'=>'18868797959'],
['com'=>'我爱我家塘河店2组','name'=>'余森','phone'=>'18868774872'],
['com'=>'我爱我家金色江南店1组','name'=>'叶建洪','phone'=>'18868752850'],
['com'=>'我爱我家和睦店4组','name'=>'卢松仁','phone'=>'18868725356'],
['com'=>'我爱我家塘河店1组','name'=>'郭善坤','phone'=>'18868598212'],
['com'=>'我爱我家望江店4组','name'=>'杨能栋','phone'=>'18868497773'],
['com'=>'益邦地产龙湖1店','name'=>'杜胜超','phone'=>'18868186070'],
['com'=>'策展房地产项目三区','name'=>'闫明明','phone'=>'18868137660'],
['com'=>'我爱我家望江店1组','name'=>'张洋洋','phone'=>'18868124209'],
['com'=>'我爱我家十亩田店1组','name'=>'来龙','phone'=>'18867112817'],
['com'=>'住邦房产江南明城店','name'=>'侯正伟','phone'=>'18857894261'],
['com'=>'住邦房产蓝爵国际店','name'=>'钟达','phone'=>'18857889019'],
['com'=>'我爱我家庆春店2组','name'=>'金凤程','phone'=>'18857860020'],
['com'=>'我爱我家柳浪东苑店1组','name'=>'张凤','phone'=>'18857518061'],
['com'=>'我爱我家松木场店3组','name'=>'董志祥','phone'=>'18857125253'],
['com'=>'我爱我家湖墅嘉园店1组','name'=>'陆卫东','phone'=>'18855089909'],
['com'=>'我爱我家仙林桥店1组','name'=>'甘军','phone'=>'18814899162'],
['com'=>'我爱我家天水店1组','name'=>'吴小翠','phone'=>'18814800213'],
['com'=>'我爱我家泊林春天店1组','name'=>'李博','phone'=>'18768496718'],
['com'=>'我爱我家凤凰南苑店1组','name'=>'唐冬','phone'=>'18768444702'],
['com'=>'我爱我家河坊街店1组','name'=>'毛尧星','phone'=>'18768438655'],
['com'=>'我爱我家新工新村店1组','name'=>'梁亮','phone'=>'18768183382'],
['com'=>'我爱我家潮鸣寺店1组','name'=>'林鹤','phone'=>'18768180978'],
['com'=>'豪世华邦大浒F','name'=>'俞炳申','phone'=>'18767935354'],
['com'=>'我爱我家南岸晶都店1组','name'=>'王绎翔','phone'=>'18767929703'],
['com'=>'益邦地产月明4店','name'=>'钱柯城','phone'=>'18767510334'],
['com'=>'易房地产旭辉城买卖2组','name'=>'沈万玉','phone'=>'18758923044'],
['com'=>'我爱我家和睦店1组','name'=>'应磊','phone'=>'18758878408'],
['com'=>'我爱我家塘河店3组','name'=>'韦根','phone'=>'18758591609'],
['com'=>'我爱我家米市巷店1组','name'=>'黄振家','phone'=>'18758294517'],
['com'=>'恒邦房产恒邦房产山阴店','name'=>'章梦迪','phone'=>'18758210997'],
['com'=>'我爱我家信义坊店1组','name'=>'杨成全','phone'=>'18758195809'],
['com'=>'口碑地产吉屋建设四路店','name'=>'张娜娜','phone'=>'18758095601'],
['com'=>'我爱我家海潮店1组','name'=>'孙金钱','phone'=>'18758037557'],
['com'=>'亿辉煌房产亿辉煌房产售楼处','name'=>'伍文军','phone'=>'18758032723'],
['com'=>'世佳地产世佳地产萧山店','name'=>'罗旋','phone'=>'18758030993'],
['com'=>'我爱我家马市街店3组','name'=>'何亚宾','phone'=>'18758006959'],
['com'=>'都市房网阿果拉戈雅公寓店','name'=>'李罗云','phone'=>'18757735190'],
['com'=>'我爱我家新武林店1组','name'=>'钱洪波','phone'=>'18757586826'],
['com'=>'我爱我家庆春店1组','name'=>'叶秀超','phone'=>'18757416397'],
['com'=>'我爱我家天阳上河店2组','name'=>'张清洋','phone'=>'18757177510'],
['com'=>'我爱我家世纪之光店1组','name'=>'李云','phone'=>'18750468168'],
['com'=>'我爱我家定安店1组','name'=>'祝俊超','phone'=>'18720143829'],
['com'=>'易房地产彩虹城店买卖2组','name'=>'危凯峰','phone'=>'18702674322'],
['com'=>'我爱我家孩儿巷店1组','name'=>'张瑞丽','phone'=>'18694559115'],
['com'=>'易房地产白金海岸店买卖2组','name'=>'张巧','phone'=>'18672126602'],
['com'=>'豪世华邦美政店','name'=>'张伟','phone'=>'18668229172'],
['com'=>'我爱我家文晖店2组','name'=>'王丽','phone'=>'18668178216'],
['com'=>'我爱我家武林店2组','name'=>'杨苗苗','phone'=>'18668141980'],
['com'=>'我爱我家天阳上河店1组','name'=>'杨凯','phone'=>'18668089772'],
['com'=>'住邦房产住邦房产振宁店','name'=>'郭新标','phone'=>'18668069081'],
['com'=>'天居房产萧然东路店','name'=>'俞太阳','phone'=>'18668039219'],
['com'=>'豪世华邦鼓楼B','name'=>'徐晓豪','phone'=>'18667395230'],
['com'=>'我爱我家旭辉城店1组','name'=>'侯洋','phone'=>'18667166748'],
['com'=>'豪世华邦鼓楼B','name'=>'步飞飞','phone'=>'18661318619'],
['com'=>'我爱我家湖墅店2组','name'=>'韦龙云','phone'=>'18658835331'],
['com'=>'我爱我家复兴店2组','name'=>'徐建','phone'=>'18658278789'],
['com'=>'Q房网钱江十三店','name'=>'毛林枫','phone'=>'18658182892'],
['com'=>'住邦房产住邦房产开发区店','name'=>'李政','phone'=>'18658137941'],
['com'=>'名仕之家巧园店','name'=>'陈健宇','phone'=>'18657866681'],
['com'=>'我爱我家佳境天城店1组','name'=>'周帅军','phone'=>'18657169792'],
['com'=>'我爱我家国信嘉园店2组','name'=>'符永豪','phone'=>'18657119115'],
['com'=>'我爱我家建国店3组','name'=>'徐仁','phone'=>'18657107241'],
['com'=>'益邦地产月明3店','name'=>'杨蒙蒙','phone'=>'18654289712'],
['com'=>'我爱我家望江南店1组','name'=>'石东波','phone'=>'18606711776'],
['com'=>'名仕之家金色江南店','name'=>'常见','phone'=>'18606539310'],
['com'=>'易房地产半岛店买卖3组','name'=>'符序','phone'=>'18557518280'],
['com'=>'我爱我家狮虎桥店3组','name'=>'何列刚','phone'=>'18506858881'],
['com'=>'都市房网礼信江汉店','name'=>'蔺少健','phone'=>'18506838957'],
['com'=>'我爱我家和睦店4组','name'=>'农生勇','phone'=>'18506167483'],
['com'=>'豪世华邦青秀城店','name'=>'韩力','phone'=>'18501165690'],
['com'=>'易房地产璞悦湾店一手2组','name'=>'胡群星','phone'=>'18479671909'],
['com'=>'我爱我家湖墅嘉园店3组','name'=>'梅灵山','phone'=>'18458898670'],
['com'=>'我爱我家天阳上河店2组','name'=>'孙小锋','phone'=>'18458891725'],
['com'=>'住邦房产住邦房产振宁店','name'=>'徐少帅','phone'=>'18458867312'],
['com'=>'住邦房产华瑞晴庐店','name'=>'孙斌','phone'=>'18458237655'],
['com'=>'我爱我家金色江南店1组','name'=>'付东键','phone'=>'18457115722'],
['com'=>'我爱我家金色江南店1组','name'=>'付东键','phone'=>'18457115722'],
['com'=>'我爱我家名城公馆店1组','name'=>'刘明亮','phone'=>'18395996696'],
['com'=>'我爱我家佳境天城店1组','name'=>'孙严','phone'=>'18368835986'],
['com'=>'住邦房产住邦房产振宁店','name'=>'陈小丽','phone'=>'18368828806'],
['com'=>'我爱我家复兴店1组','name'=>'李浩','phone'=>'18368157973'],
['com'=>'住邦房产住邦房产振宁店','name'=>'张鹏','phone'=>'18368145497'],
['com'=>'堂雅房产春和钱塘店','name'=>'刘波','phone'=>'18368127976'],
['com'=>'Q房网Q房网滨盛店','name'=>'钟华洪','phone'=>'18368045214'],
['com'=>'我爱我家佳境天城店1组','name'=>'曹海兵','phone'=>'18368005734'],
['com'=>'我爱我家佳境天城店1组','name'=>'曹海兵','phone'=>'18368005734'],
['com'=>'我爱我家湖墅嘉园店1组','name'=>'万志彪','phone'=>'18367162589'],
['com'=>'策锦房产售楼部','name'=>'路婧文','phone'=>'18362619992'],
['com'=>'豪世华邦银树湾店','name'=>'冀连军','phone'=>'18358198651'],
['com'=>'住邦房产江南明城店','name'=>'唐林春','phone'=>'18358183423'],
['com'=>'我爱我家复兴南苑店1组','name'=>'余鹏飞','phone'=>'18358126110'],
['com'=>'豪世华邦豪世华邦孩儿巷店','name'=>'王顶良','phone'=>'18358111197'],
['com'=>'我爱我家马市街店3组','name'=>'叶涛','phone'=>'18357180074'],
['com'=>'我爱我家马市街店1组','name'=>'曹春荣','phone'=>'18357163207'],
['com'=>'住邦房产住邦房产振宁店','name'=>'陈啊敏','phone'=>'18357029962'],
['com'=>'我爱我家景上公寓店1组','name'=>'周超','phone'=>'18334316443'],
['com'=>'浙联地产佳境天城店','name'=>'李超','phone'=>'18329191832'],
['com'=>'住邦房产住邦房产振宁店','name'=>'曹若琳','phone'=>'18329181524'],
['com'=>'我爱我家信义坊店1组','name'=>'朱道俊','phone'=>'18314886790'],
['com'=>'房咖地产金色钱塘店','name'=>'洪国凯','phone'=>'18279862110'],
['com'=>'易房地产彩虹城店买卖3组','name'=>'陈志豪','phone'=>'18279477458'],
['com'=>'住邦房产钱塘明月博学店','name'=>'王乐','phone'=>'18270338821'],
['com'=>'住邦房产钱塘明月博学店','name'=>'张海霞','phone'=>'18268863169'],
['com'=>'我爱我家和睦店1组','name'=>'汪锦文','phone'=>'18268191617'],
['com'=>'我爱我家湖墅店2组','name'=>'罗利强','phone'=>'18268182942'],
['com'=>'科地地产速腾滨盛店','name'=>'艾迪森','phone'=>'18268126889'],
['com'=>'我爱我家白荡海店1组','name'=>'蒋晓军','phone'=>'18268121764'],
['com'=>'住邦房产华瑞晴庐店','name'=>'王雪','phone'=>'18268086976'],
['com'=>'我爱我家信义坊店1组','name'=>'吕黎锋','phone'=>'18266947301'],
['com'=>'我爱我家湖漫雅筑店1组','name'=>'罗晓伟','phone'=>'18266945317'],
['com'=>'住邦房产华瑞晴庐店','name'=>'葛啸峰','phone'=>'18258893226'],
['com'=>'我爱我家春江郦城店1组','name'=>'童家玲','phone'=>'18258277824'],
['com'=>'我爱我家锦昌文华店1组','name'=>'吴志成','phone'=>'18258224249'],
['com'=>'我爱我家佳境天城店1组','name'=>'孙英俊','phone'=>'18258189075'],
['com'=>'住邦房产华瑞晴庐店','name'=>'李勤','phone'=>'18258185776'],
['com'=>'住邦房产江南明城店','name'=>'陈蕊花','phone'=>'18258160171'],
['com'=>'益邦地产月明4店','name'=>'王力','phone'=>'18258002775'],
['com'=>'鸿邦地产鸿邦地产教工店','name'=>'钱铁标','phone'=>'18257534843'],
['com'=>'置家房产宝龙广场店','name'=>'刘斌','phone'=>'18205879972'],
['com'=>'我爱我家和睦二店2组','name'=>'曾伟平','phone'=>'18167161727'],
['com'=>'豪世华邦鼓楼B','name'=>'步飞飞','phone'=>'18167160197'],
['com'=>'我爱我家官河锦庭店1组','name'=>'姚赛','phone'=>'18167153562'],
['com'=>'我爱我家春和钱塘店2组','name'=>'赵和平','phone'=>'18167135657'],
['com'=>'我爱我家望江府店3组','name'=>'李道余','phone'=>'18167131553'],
['com'=>'豪世华邦鼓楼B','name'=>'吴长江','phone'=>'18158438390'],
['com'=>'豪世华邦美政店','name'=>'郝明明','phone'=>'18158415703'],
['com'=>'豪世华邦美政店','name'=>'吴奎峰','phone'=>'18158415385'],
['com'=>'豪世华邦长庆店','name'=>'金心怡','phone'=>'18158145872'],
['com'=>'我爱我家庆春店1组','name'=>'许航','phone'=>'18157190168'],
['com'=>'我爱我家旭辉城二店1组','name'=>'苟坤','phone'=>'18157187103'],
['com'=>'房咖地产金色钱塘店','name'=>'杜绪涛','phone'=>'18157131516'],
['com'=>'我爱我家风景大院店2组','name'=>'牛森','phone'=>'18143488753'],
['com'=>'豪世华邦豪世华邦狮虎桥店','name'=>'陈琳','phone'=>'18143461560'],
['com'=>'信业居房产钱塘明月店','name'=>'吴银凤','phone'=>'18143459332'],
['com'=>'豪世华邦贾家B','name'=>'田壮强','phone'=>'18143451307'],
['com'=>'豪世华邦豪世华邦海月店','name'=>'陈浩东','phone'=>'18143451092'],
['com'=>'房咖地产金色钱塘店','name'=>'吴鹏辉','phone'=>'18143448336'],
['com'=>'我爱我家太阳国际店3组','name'=>'李滨','phone'=>'18143405491'],
['com'=>'置家房产宝龙广场店','name'=>'王乐','phone'=>'18106569011'],
['com'=>'住邦房产住邦房产振宁店','name'=>'罗军','phone'=>'18106516355'],
['com'=>'我爱我家新华路店1组','name'=>'崔微','phone'=>'18106512433'],
['com'=>'豪世华邦长庆店','name'=>'陶智华','phone'=>'18072990917'],
['com'=>'益邦地产星耀城二期3店','name'=>'卢杰','phone'=>'18072979771'],
['com'=>'鸣家房产长青店','name'=>'江滔','phone'=>'18072924934'],
['com'=>'豪世华邦豪世华邦孩儿巷店','name'=>'张成','phone'=>'18072879750'],
['com'=>'我爱我家和睦二店2组','name'=>'孙光友','phone'=>'18072860263'],
['com'=>'豪世华邦豪世华邦河坊街店','name'=>'巴咏玖','phone'=>'18072845829'],
['com'=>'豪世华邦豪世华邦孩儿巷店','name'=>'涂梦梁','phone'=>'18072841993'],
['com'=>'豪世华邦豪世华邦海月店','name'=>'王秋莲','phone'=>'18072789062'],
['com'=>'我爱我家莱茵传奇店1组','name'=>'张子然','phone'=>'18072769669'],
['com'=>'信业居房产钱塘明月店','name'=>'马天柱','phone'=>'18072727685'],
['com'=>'豪世华邦武林店','name'=>'颜新才','phone'=>'18072709076'],
['com'=>'豪世华邦美政店','name'=>'袁浩','phone'=>'18072703963'],
['com'=>'我爱我家莱茵传奇店2组','name'=>'王绪龙','phone'=>'18069432192'],
['com'=>'我爱我家临浦店1组','name'=>'江力','phone'=>'18069404008'],
['com'=>'Q房网Q房网孩儿巷店','name'=>'叶燕青','phone'=>'18067985542'],
['com'=>'21世纪旭恒东方华城店','name'=>'郭朝阳','phone'=>'18067924106'],
['com'=>'豪世华邦豪世华邦孩儿巷店','name'=>'姜小冬','phone'=>'18067915003'],
['com'=>'蓝湖房地产金色钱塘店','name'=>'余芳鹏','phone'=>'18067911311'],
['com'=>'豪世华邦美政店','name'=>'张全','phone'=>'18058773917'],
['com'=>'豪世华邦江城店','name'=>'刘瑞孟','phone'=>'18058773889'],
['com'=>'豪世华邦江城店','name'=>'王天明','phone'=>'18058773886'],
['com'=>'豪世华邦鼓楼B','name'=>'徐晓豪','phone'=>'18058773831'],
['com'=>'豪世华邦豪世华邦海月店','name'=>'宋帅康','phone'=>'18058773329'],
['com'=>'豪世华邦豪世华邦狮虎桥店','name'=>'陆健','phone'=>'18058773293'],
['com'=>'豪世华邦仙林桥A','name'=>'丁朗','phone'=>'18058773225'],
['com'=>'豪世华邦大浒A','name'=>'陈波','phone'=>'18058773129'],
['com'=>'豪世华邦豪世华邦狮虎桥店','name'=>'孙玉梅','phone'=>'18058773016'],
['com'=>'Q房网凯旋二店','name'=>'李君龙','phone'=>'18058766880'],
['com'=>'越源房产萧山店','name'=>'郑国凡','phone'=>'18058761162'],
['com'=>'我爱我家春和钱塘店2组','name'=>'张艳','phone'=>'18058744496'],
['com'=>'房友豪屋中梁百悦城店','name'=>'袁丽','phone'=>'18058193173'],
['com'=>'豪世华邦美政店','name'=>'占思杰','phone'=>'18058137392'],
['com'=>'豪世华邦武林店','name'=>'蓝樟华','phone'=>'18058135519'],
['com'=>'豪世华邦豪世华邦望江门店','name'=>'张开林','phone'=>'18057196603'],
['com'=>'易房地产莱茵传奇店买卖3组','name'=>'李前龙','phone'=>'18057191786'],
['com'=>'豪世华邦抚宁巷店','name'=>'林后锔','phone'=>'18057186512'],
['com'=>'易房地产莱茵传奇店买卖3组','name'=>'袁君','phone'=>'18057152597'],
['com'=>'豪世华邦风景大院店A','name'=>'吴小梅','phone'=>'18057142217'],
['com'=>'我爱我家春江郦城店1组','name'=>'余亮','phone'=>'18042469350'],
['com'=>'我爱我家和睦二店2组','name'=>'张体洋','phone'=>'18039300661'],
['com'=>'豪世华邦仙林桥A','name'=>'薛杰峰','phone'=>'18006715896'],
['com'=>'豪世华邦仙林桥A','name'=>'薛杰峰','phone'=>'18006715896'],
['com'=>'豪世华邦仙林桥A','name'=>'黄兵','phone'=>'18006711005'],
['com'=>'住邦房产开元店','name'=>'沈俞阳','phone'=>'17858830144'],
['com'=>'我爱我家湖漫雅筑店2组','name'=>'金晓兰','phone'=>'17858525950'],
['com'=>'我爱我家霞飞郡店1组','name'=>'邢桐','phone'=>'17857981055'],
['com'=>'我爱我家天水店1组','name'=>'周康超','phone'=>'17857980885'],
['com'=>'宏轩房产宁泰店','name'=>'雷章利','phone'=>'17826871969'],
['com'=>'我爱我家近江店1组','name'=>'杨鑫','phone'=>'17826863375'],
['com'=>'达麦地产售楼部','name'=>'赵越超','phone'=>'17826824865'],
['com'=>'21世纪旭恒星耀城店','name'=>'廖才川','phone'=>'17816852323'],
['com'=>'我爱我家霞飞郡店1组','name'=>'曾城军','phone'=>'17815611815'],
['com'=>'住邦房产蓝爵国际店','name'=>'卢进京','phone'=>'17794525587'],
['com'=>'扬瑞房产望京店','name'=>'罗迪','phone'=>'17794509250'],
['com'=>'我爱我家和睦店1组','name'=>'聂彪','phone'=>'17794500209'],
['com'=>'我爱我家江南国际城店1组','name'=>'王群飞','phone'=>'17794500181'],
['com'=>'我爱我家滨盛店2组','name'=>'武梦中','phone'=>'17774003626'],
['com'=>'易房地产江南摩卡店买卖3组','name'=>'周延兵','phone'=>'17770084819'],
['com'=>'绿城置换运河宸园二店','name'=>'方马超','phone'=>'17767260307'],
['com'=>'住邦房产钱塘明月博学店','name'=>'孙钊','phone'=>'17767252373'],
['com'=>'住邦房产国泰店','name'=>'何百勇','phone'=>'17767197301'],
['com'=>'橙佳地产莱茵店','name'=>'陈涛','phone'=>'17767175830'],
['com'=>'我爱我家凤起花园店5组','name'=>'张大佩','phone'=>'17767050626'],
['com'=>'名仕之家金色江南店','name'=>'郁亮亮','phone'=>'17764568001'],
['com'=>'富亨房地产钱江世纪城店','name'=>'许伟建','phone'=>'17764536641'],
['com'=>'我爱我家嘉泰馨庭店2组','name'=>'吕梦','phone'=>'17742007362'],
['com'=>'Q房网长庆店','name'=>'陈稳','phone'=>'17717455262'],
['com'=>'我爱我家近江二店1组','name'=>'王峰','phone'=>'17706818939'],
['com'=>'我爱我家近江店1组','name'=>'田源源','phone'=>'17706712630'],
['com'=>'华畅地产东南海1组','name'=>'刘磊','phone'=>'17706513595'],
['com'=>'灵通房产灵通房产北干店','name'=>'应之文','phone'=>'17706400082'],
['com'=>'我爱我家孩儿巷店1组','name'=>'章自强','phone'=>'17705810530'],
['com'=>'我爱我家佳境天城店1组','name'=>'刘宁','phone'=>'17682334720'],
['com'=>'益邦地产龙湖1店','name'=>'陈金中','phone'=>'17681897969'],
['com'=>'我爱我家湖漫雅筑店1组','name'=>'杨锋','phone'=>'17681887661'],
['com'=>'我爱我家春和钱塘店2组','name'=>'成东荣','phone'=>'17681855918'],
['com'=>'我爱我家东方郡二店1组','name'=>'孔相辉','phone'=>'17681827477'],
['com'=>'正滨房产市场部渠道','name'=>'韩杰','phone'=>'17681801186'],
['com'=>'我爱我家和睦店4组','name'=>'张永书','phone'=>'17630940122'],
['com'=>'易房地产彩虹城店买卖1组','name'=>'何腾','phone'=>'17606845568'],
['com'=>'我爱我家江南国际城店1组','name'=>'车磊','phone'=>'17606513375'],
['com'=>'世强地产东南海店','name'=>'胡冬冬','phone'=>'17601360266'],
['com'=>'我爱我家金色江南店1组','name'=>'张向莹','phone'=>'17505815663'],
['com'=>'21世纪旭恒金色江南店','name'=>'晏志辉','phone'=>'17364522177'],
['com'=>'恒峰地产南秀路店','name'=>'陈钧明','phone'=>'17364516810'],
['com'=>'名仕之家佳境天城一店','name'=>'祝方柽','phone'=>'17357124406'],
['com'=>'Q房网云溪香山二店','name'=>'季乔华','phone'=>'17316940519'],
['com'=>'我爱我家近江店1组','name'=>'张彦芳','phone'=>'17195878658'],
['com'=>'顺家地产潘水店','name'=>'王丽军','phone'=>'17131060207'],
['com'=>'策锦房产售楼部','name'=>'路婧文','phone'=>'17098139916'],
['com'=>'豪世华邦豪世华邦海月店','name'=>'王秋莲','phone'=>'17098139884'],
['com'=>'我爱我家孩儿巷店1组','name'=>'章自强','phone'=>'17098139745'],
['com'=>'21世纪旭恒金色江南店','name'=>'章志伟','phone'=>'17098139713'],
['com'=>'我爱我家庆春店1组','name'=>'许航','phone'=>'17098139704'],
['com'=>'我爱我家和睦店4组','name'=>'农生勇','phone'=>'17098139518'],
['com'=>'我爱我家天水店1组','name'=>'黄超斌','phone'=>'17098139473'],
['com'=>'Q房网Q房网孩儿巷店','name'=>'叶燕青','phone'=>'17098139427'],
['com'=>'豪世华邦豪世华邦海月店','name'=>'陈浩东','phone'=>'17098139409'],
['com'=>'豪世华邦豪世华邦孩儿巷店','name'=>'马锋强','phone'=>'17098136705'],
['com'=>'Q房网Q房网孩儿巷店','name'=>'杨小飞','phone'=>'17098136601'],
['com'=>'喜信房产滨江店','name'=>'余泽琪','phone'=>'17098136569'],
['com'=>'我爱我家和睦店4组','name'=>'卢松仁','phone'=>'17098136442'],
['com'=>'我爱我家景上公寓店1组','name'=>'熊宏伟','phone'=>'17098136427'],
['com'=>'豪世华邦鼓楼B','name'=>'步飞飞','phone'=>'17098136302'],
['com'=>'堂雅房产春和钱塘店','name'=>'樊志宇','phone'=>'17098136017'],
['com'=>'易房地产旭辉城买卖2组','name'=>'沈涛','phone'=>'17098136012'],
['com'=>'豪世华邦美政店','name'=>'郝明明','phone'=>'17098136002'],
['com'=>'我爱我家定安店4组','name'=>'俞杰','phone'=>'17098134682'],
['com'=>'顺联房产泊林春天店','name'=>'任谟楹','phone'=>'17098134500'],
['com'=>'易房地产半岛店买卖3组','name'=>'符序','phone'=>'17098134492'],
['com'=>'豪世华邦豪世华邦海月店','name'=>'陈浩东','phone'=>'17098134433'],
['com'=>'我爱我家霞飞郡店1组','name'=>'程普','phone'=>'17098134417'],
['com'=>'鼎隆地产金色江南店','name'=>'吕林林','phone'=>'17098134247'],
['com'=>'我爱我家望江店1组','name'=>'张洋洋','phone'=>'17098134246'],
['com'=>'我爱我家复兴店2组','name'=>'缪鹏','phone'=>'17098134190'],
['com'=>'畅然房产售楼部二组','name'=>'王帆','phone'=>'17098134166'],
['com'=>'益邦地产星耀城二期2店','name'=>'米申华','phone'=>'17098134118'],
['com'=>'我爱我家仙林桥店1组','name'=>'万尚平','phone'=>'17098134069'],
['com'=>'我爱我家湖墅嘉园店3组','name'=>'梅灵山','phone'=>'17098133621'],
['com'=>'豪世华邦豪世华邦孩儿巷店','name'=>'涂梦梁','phone'=>'17098132807'],
['com'=>'我爱我家凤起花园店5组','name'=>'张大佩','phone'=>'17098132781'],
['com'=>'我爱我家景上公寓店1组','name'=>'方程晓','phone'=>'17098132752'],
['com'=>'我爱我家塘河店2组','name'=>'徐伟','phone'=>'17098132732'],
['com'=>'我爱我家复兴店2组','name'=>'徐建','phone'=>'17098132730'],
['com'=>'我爱我家潮鸣寺店1组','name'=>'党新强','phone'=>'17098132568'],
['com'=>'亘辉房地产大成名座店','name'=>'李春燕','phone'=>'17098132513'],
['com'=>'豪世华邦美政店','name'=>'张全','phone'=>'17098132488'],
['com'=>'我爱我家复兴店1组','name'=>'谢磊','phone'=>'17098132469'],
['com'=>'我爱我家凤凰南苑店1组','name'=>'陈林根','phone'=>'17098132384'],
['com'=>'我爱我家孩儿巷店1组','name'=>'孙许坤','phone'=>'17098132196'],
['com'=>'我爱我家和睦二店2组','name'=>'孙光友','phone'=>'17098132130'],
['com'=>'我爱我家庆春店2组','name'=>'胡翔','phone'=>'17098132051'],
['com'=>'房咖地产金色钱塘店','name'=>'杜绪涛','phone'=>'17098131776'],
['com'=>'君诚房产君诚房产振宁店','name'=>'裴敏君','phone'=>'17098131672'],
['com'=>'名仕之家巧园店','name'=>'管毓进','phone'=>'17098131621'],
['com'=>'蓝湖房地产金色钱塘店','name'=>'余芳鹏','phone'=>'17098131593'],
['com'=>'我爱我家庆春店2组','name'=>'李栋','phone'=>'17098131426'],
['com'=>'我爱我家湖墅嘉园店1组','name'=>'王宏建','phone'=>'17098130034'],
['com'=>'豪世华邦豪世华邦和睦店B组','name'=>'戴兴波','phone'=>'17098130029'],
['com'=>'我爱我家柳浪东苑店1组','name'=>'陈超','phone'=>'17098099720'],
['com'=>'住邦房产江南明城店','name'=>'江训高','phone'=>'17098098977'],
['com'=>'益邦地产星耀城二期4店','name'=>'郦卓嫔','phone'=>'17098097714'],
['com'=>'我爱我家塘河店1组','name'=>'郭善坤','phone'=>'17098097697'],
['com'=>'我爱我家天水店1组','name'=>'吴小翠','phone'=>'17098096970'],
['com'=>'豪世华邦江城店','name'=>'刘瑞孟','phone'=>'17098096831'],
['com'=>'顺驰地产金惠代理二组','name'=>'王娅','phone'=>'17098096706'],
['com'=>'顺驰地产莱茵传奇一组','name'=>'朱泽阳','phone'=>'17098096620'],
['com'=>'鸿邦地产鸿邦地产教工店','name'=>'寿寒凯','phone'=>'17098094909'],
['com'=>'我爱我家世纪之光店1组','name'=>'傅喻民','phone'=>'17098094105'],
['com'=>'我爱我家天阳上河店1组','name'=>'李崇','phone'=>'17098093981'],
['com'=>'我爱我家和睦店1组','name'=>'叶康','phone'=>'17098093740'],
['com'=>'我爱我家和睦店1组','name'=>'应磊','phone'=>'17098093720'],
['com'=>'豪世华邦仙林桥A','name'=>'丁朗','phone'=>'17098093408'],
['com'=>'我爱我家仙林桥店1组','name'=>'赵子虎','phone'=>'17098093289'],
['com'=>'我爱我家天水店1组','name'=>'汪晓峰','phone'=>'17098092757'],
['com'=>'我爱我家十亩田店1组','name'=>'贡娇燕','phone'=>'17098092724'],
['com'=>'我爱我家和睦店1组','name'=>'吕梁','phone'=>'17098092530'],
['com'=>'我爱我家定安店1组','name'=>'祝俊超','phone'=>'17098092429'],
['com'=>'我爱我家塘河店3组','name'=>'韦根','phone'=>'17098092041'],
['com'=>'易房地产半岛店买卖3组','name'=>'于海江','phone'=>'17098091946'],
['com'=>'我爱我家湖墅嘉园店3组','name'=>'黄世安','phone'=>'17098091894'],
['com'=>'豪世华邦豪世华邦孩儿巷店','name'=>'许勇勇','phone'=>'17098091810'],
['com'=>'我爱我家麒麟街店1组','name'=>'徐伟康','phone'=>'17098091800'],
['com'=>'豪世华邦长庆店','name'=>'金心怡','phone'=>'17098091632'],
['com'=>'我爱我家美政花苑店1组','name'=>'景风鸣','phone'=>'17098091622'],
['com'=>'我爱我家和睦二店2组','name'=>'曾伟平','phone'=>'17098091134'],
['com'=>'我爱我家孩儿巷店1组','name'=>'孙许坤','phone'=>'17098090759'],
['com'=>'我爱我家曼城店1组','name'=>'潘锡城','phone'=>'17098090653'],
['com'=>'住邦房产华瑞晴庐店','name'=>'李勤','phone'=>'17098090453'],
['com'=>'豪世华邦鼓楼B','name'=>'步飞飞','phone'=>'17098090446'],
['com'=>'我爱我家马市街店3组','name'=>'何亚宾','phone'=>'17098090175'],
['com'=>'我爱我家麒麟街店1组','name'=>'陈明','phone'=>'17098090165'],
['com'=>'我爱我家复兴店1组','name'=>'林海羊','phone'=>'17098090056'],
['com'=>'名仕之家积家店','name'=>'张国兴','phone'=>'17098090007'],
['com'=>'豪世华邦豪世华邦孩儿巷店','name'=>'姜小冬','phone'=>'17098079504'],
['com'=>'豪世华邦美政店','name'=>'吴奎峰','phone'=>'17098077196'],
['com'=>'易房地产浦沿店1组','name'=>'钟建林','phone'=>'17098077137'],
['com'=>'住邦房产莱茵传奇店','name'=>'何佳伟','phone'=>'17098054959'],
['com'=>'我爱我家庆春店2组','name'=>'刘秋平','phone'=>'17098054414'],
['com'=>'我爱我家天水店5组','name'=>'黄西浩','phone'=>'17098050564'],
['com'=>'领策房地产售楼部','name'=>'朱金富','phone'=>'17091638968'],
['com'=>'Q房网仙林店','name'=>'汪秀才','phone'=>'17091637884'],
['com'=>'易房地产春晓一店','name'=>'任鹏叡','phone'=>'17091637069'],
['com'=>'我爱我家天水店1组','name'=>'周康超','phone'=>'17091636214'],
['com'=>'我爱我家和睦店4组','name'=>'沈亮','phone'=>'17091635595'],
['com'=>'益邦地产星耀城二期1店','name'=>'景嘉向','phone'=>'17091635272'],
['com'=>'我爱我家复兴店2组','name'=>'杨为江','phone'=>'17091634615'],
['com'=>'我爱我家景上公寓店1组','name'=>'张文俊','phone'=>'17091634613'],
['com'=>'豪世华邦仙林桥A','name'=>'薛杰峰','phone'=>'17091633883'],
['com'=>'豪世华邦美政店','name'=>'张伟','phone'=>'17091633654'],
['com'=>'豪世华邦仙林桥A','name'=>'薛杰峰','phone'=>'17091633527'],
['com'=>'我爱我家尚景国际店1组','name'=>'季永风','phone'=>'17091632557'],
['com'=>'我爱我家庆春店2组','name'=>'李艳凤','phone'=>'17091630544'],
['com'=>'我爱我家麒麟街店1组','name'=>'钱佳','phone'=>'17091630383'],
['com'=>'我爱我家十亩田店1组','name'=>'刘鹏','phone'=>'17005680122'],
['com'=>'我爱我家旭辉城二店1组','name'=>'陈皖苏','phone'=>'16657118692'],
['com'=>'易房地产贺田尚城店买卖1组','name'=>'钟建明','phone'=>'16607970571'],
['com'=>'策展房地产售楼部','name'=>'兰杨','phone'=>'16605813093'],
['com'=>'我爱我家滨盛店3组','name'=>'曾观仁','phone'=>'15995680695'],
['com'=>'易房地产金色江南买卖2组','name'=>'楼章伟','phone'=>'15990832825'],
['com'=>'我爱我家庆春店2组','name'=>'李艳凤','phone'=>'15990162086'],
['com'=>'浙联地产佳境天城店','name'=>'郭钧健','phone'=>'15990133099'],
['com'=>'我爱我家麒麟街店1组','name'=>'陈明','phone'=>'15990100727'],
['com'=>'住邦房产华瑞晴庐店','name'=>'童波','phone'=>'15990047990'],
['com'=>'我爱我家温馨人家店1组','name'=>'钱佳诚','phone'=>'15988980092'],
['com'=>'我爱我家拱北店1组','name'=>'陈赵平','phone'=>'15988858201'],
['com'=>'我爱我家狮虎桥店1组','name'=>'杨红叶','phone'=>'15988478660'],
['com'=>'我爱我家金地天逸店2组','name'=>'瞿永伟','phone'=>'15988449325'],
['com'=>'我爱我家玲珑府店2组','name'=>'甄波波','phone'=>'15988446879'],
['com'=>'我爱我家天水店1组','name'=>'黄超斌','phone'=>'15988411609'],
['com'=>'我爱我家塘河店1组','name'=>'熊敏','phone'=>'15988407137'],
['com'=>'21世纪旭恒金色江南店','name'=>'罗喜','phone'=>'15988180133'],
['com'=>'我爱我家塘河店2组','name'=>'陈牛','phone'=>'15988172443'],
['com'=>'住邦房产世纪之光店','name'=>'周虹','phone'=>'15988138977'],
['com'=>'鸿邦地产鸿邦地产教工店','name'=>'曾德亮','phone'=>'15968895995'],
['com'=>'住邦房产住邦房产振宁店','name'=>'马其萍','phone'=>'15968138293'],
['com'=>'我爱我家望江南店2组','name'=>'杨活仁','phone'=>'15967679587'],
['com'=>'永台房产项目三部3组','name'=>'曾勇','phone'=>'15958403295'],
['com'=>'鼎隆地产金地天逸店','name'=>'李红影','phone'=>'15958198655'],
['com'=>'我爱我家天阳上河店1组','name'=>'李崇','phone'=>'15957170069'],
['com'=>'住邦房产住邦房产永久店','name'=>'文士毛','phone'=>'15955438402'],
['com'=>'祥源房地产钱江世纪城','name'=>'熊龙','phone'=>'15925686348'],
['com'=>'我爱我家建国南苑店2组','name'=>'朱海燕','phone'=>'15925644669'],
['com'=>'我爱我家望江府店1组','name'=>'娄益锋','phone'=>'15906679410'],
['com'=>'我爱我家风景大院店2组','name'=>'夏维炎','phone'=>'15906664186'],
['com'=>'我爱我家风景大院店2组','name'=>'何鸿隽','phone'=>'15906664186'],
['com'=>'我爱我家天水店5组','name'=>'姚圆','phone'=>'15906603237'],
['com'=>'我爱我家嘉泰店1组','name'=>'凌雄','phone'=>'15888804217'],
['com'=>'中原地产渠道一部六组','name'=>'王焱','phone'=>'15888335469'],
['com'=>'益邦地产月明4店','name'=>'邢伊甸','phone'=>'15869179321'],
['com'=>'我爱我家松木场店1组','name'=>'倪员梅','phone'=>'15869107785'],
['com'=>'我爱我家佑圣观店1组','name'=>'陈波','phone'=>'15868445102'],
['com'=>'我爱我家复兴店1组','name'=>'林海羊','phone'=>'15868423467'],
['com'=>'我爱我家信义坊店1组','name'=>'胡淳坚','phone'=>'15868180219'],
['com'=>'万有房产万有房产明星店','name'=>'郭方方','phone'=>'15868125856'],
['com'=>'中原地产渠道五部一组','name'=>'孙雨思','phone'=>'15867190126'],
['com'=>'我爱我家天水店1组','name'=>'汪晓峰','phone'=>'15867156111'],
['com'=>'我爱我家半道红店3组','name'=>'肖德成','phone'=>'15867135145'],
['com'=>'我爱我家复兴店2组','name'=>'杨为江','phone'=>'15867126001'],
['com'=>'豪世华邦风景大院店A','name'=>'朱小华','phone'=>'15858257666'],
['com'=>'我爱我家建国南苑店2组','name'=>'耿直','phone'=>'15858185135'],
['com'=>'千恩房产未来科技城店','name'=>'陈业鹏','phone'=>'15858185020'],
['com'=>'豪世华邦豪世华邦和睦店B组','name'=>'戴兴波','phone'=>'15858129330'],
['com'=>'我爱我家湖墅嘉园店3组','name'=>'黄世安','phone'=>'15857152754'],
['com'=>'我爱我家稻香园店1组','name'=>'王俭忱','phone'=>'15857108495'],
['com'=>'信业居房产华瑞晴庐店','name'=>'代肖','phone'=>'15855357697'],
['com'=>'我爱我家曼城店1组','name'=>'方学智','phone'=>'15825519187'],
['com'=>'我爱我家景上公寓店1组','name'=>'张文俊','phone'=>'15824030896'],
['com'=>'住邦房产华瑞晴庐店','name'=>'黄伟','phone'=>'15779969174'],
['com'=>'Q房网仙林店','name'=>'余裕琦','phone'=>'15757149803'],
['com'=>'Q房网蓝桥名苑一组','name'=>'李国超','phone'=>'15738800380'],
['com'=>'我爱我家鼓楼店1组','name'=>'陶行风','phone'=>'15715731291'],
['com'=>'我爱我家佳境天城二店1组','name'=>'李思远','phone'=>'15700187722'],
['com'=>'忆杭房产江南文苑店店','name'=>'李磊','phone'=>'15700186629'],
['com'=>'益邦地产星耀城二期3店','name'=>'肖文','phone'=>'15700119183'],
['com'=>'我爱我家仙林桥店1组','name'=>'李永兵','phone'=>'15700111286'],
['com'=>'Q房网Q房网蓝色钱江一店','name'=>'陈超凡','phone'=>'15695710682'],
['com'=>'易房地产璞悦湾店买卖1组','name'=>'郑毛松','phone'=>'15672719531'],
['com'=>'我爱我家东方郡二店3组','name'=>'杨魏然','phone'=>'15669932200'],
['com'=>'易房地产金色江南买卖2组','name'=>'杨炎','phone'=>'15669002612'],
['com'=>'Q房网Q房网孩儿巷店','name'=>'穆鹏远','phone'=>'15658857691'],
['com'=>'我爱我家白金海岸店1组','name'=>'顾寅','phone'=>'15658820883'],
['com'=>'我爱我家复兴店1组','name'=>'柴跃','phone'=>'15658805157'],
['com'=>'豪世华邦贾家A','name'=>'邵志勤','phone'=>'15605885170'],
['com'=>'住邦房产钱塘明月城北店','name'=>'黄世海','phone'=>'15558155317'],
['com'=>'骐骥地产骐骥地产和美家店','name'=>'郜倩倩','phone'=>'15558137060'],
['com'=>'我爱我家近江店2组','name'=>'郭子勤','phone'=>'15558018282'],
['com'=>'益邦地产星耀城二期3店','name'=>'罗义','phone'=>'15527827110'],
['com'=>'易房地产白金海岸店买卖3组','name'=>'张柯强','phone'=>'15397165006'],
['com'=>'我爱我家佳境天城店2组','name'=>'黎爱平','phone'=>'15397090651'],
['com'=>'益邦地产金色江南4店','name'=>'穆文庆','phone'=>'15397054005'],
['com'=>'Q房网Q房网仓基店','name'=>'闵先芹','phone'=>'15395710293'],
['com'=>'我爱我家米市巷店1组','name'=>'卢浩','phone'=>'15394212826'],
['com'=>'越源房产萧山店','name'=>'陈亮德','phone'=>'15382360701'],
['com'=>'豪世华邦豪世华邦孩儿巷店','name'=>'马锋强','phone'=>'15382358329'],
['com'=>'畅然房产售楼部','name'=>'林杰','phone'=>'15382310918'],
['com'=>'扬瑞房产望京店','name'=>'严婷','phone'=>'15382302886'],
['com'=>'我爱我家官河锦庭店1组','name'=>'陈淼仙','phone'=>'15381160965'],
['com'=>'我爱我家官河锦庭店1组','name'=>'陈萍','phone'=>'15381160965'],
['com'=>'祥联房产钱江世纪城店','name'=>'张秀秀','phone'=>'15381160221'],
['com'=>'我爱我家庆春店2组','name'=>'胡翔','phone'=>'15381064054'],
['com'=>'我爱我家景上公寓店1组','name'=>'方程晓','phone'=>'15381044973'],
['com'=>'我爱我家和睦店4组','name'=>'沈亮','phone'=>'15375666811'],
['com'=>'鸿邦地产鸿邦地产教工店','name'=>'寿寒凯','phone'=>'15372420456'],
['com'=>'住邦房产住邦房产振宁店','name'=>'罗湘','phone'=>'15372026695'],
['com'=>'Q房网仙林店','name'=>'汪秀才','phone'=>'15356719291'],
['com'=>'君诚房产君诚房产振宁店','name'=>'俞任红','phone'=>'15356714686'],
['com'=>'易房地产白金海岸店买卖3组','name'=>'阮天丽','phone'=>'15356687769'],
['com'=>'易房地产国信嘉园店买卖2组','name'=>'邓茂生','phone'=>'15356677375'],
['com'=>'我爱我家世纪之光店1组','name'=>'罗潇威','phone'=>'15356598012'],
['com'=>'荣惠地产明星店','name'=>'马杨','phone'=>'15356144133'],
['com'=>'中原地产温馨人家二组','name'=>'何成','phone'=>'15355464586'],
['com'=>'畅然房产售楼部二组','name'=>'王帆','phone'=>'15355023108'],
['com'=>'Q房网Q房网紫花一店','name'=>'张天津','phone'=>'15336886363'],
['com'=>'易房地产半岛店买卖2组','name'=>'施敏','phone'=>'15336504510'],
['com'=>'我爱我家春江花月店2组','name'=>'朱成骁','phone'=>'15325961123'],
['com'=>'房咖地产金色钱塘店','name'=>'穆壮壮','phone'=>'15314674209'],
['com'=>'鼎隆地产泊林春天店','name'=>'许介理','phone'=>'15314622525'],
['com'=>'我爱我家世纪之光店1组','name'=>'何芒辉','phone'=>'15306568938'],
['com'=>'我爱我家新工新村店1组','name'=>'刘晶晶','phone'=>'15268525760'],
['com'=>'奥居房地产钱江世纪城店','name'=>'方玲玲','phone'=>'15268520938'],
['com'=>'久信房产闻堰店','name'=>'崔培虎','phone'=>'15268155445'],
['com'=>'华義房产知稼苑店','name'=>'冯兵兵','phone'=>'15268155310'],
['com'=>'我爱我家孩儿巷店1组','name'=>'孙许坤','phone'=>'15268117935'],
['com'=>'我爱我家江南国际城店1组','name'=>'李云菲','phone'=>'15268105292'],
['com'=>'我爱我家美政花苑店1组','name'=>'郑勇','phone'=>'15268026652'],
['com'=>'我爱我家凤凰南苑店1组','name'=>'陈林根','phone'=>'15267773663'],
['com'=>'我爱我家北干二苑店1组','name'=>'杨燕','phone'=>'15267156661'],
['com'=>'我爱我家江城店1组','name'=>'姚钱','phone'=>'15267121782'],
['com'=>'华義房产知稼苑店','name'=>'付树辉','phone'=>'15267080632'],
['com'=>'链家地产链家地产和睦店','name'=>'汪卫红','phone'=>'15258837150'],
['com'=>'我爱我家十亩田店1组','name'=>'雷海锋','phone'=>'15258834141'],
['com'=>'聚信房产钱江世纪城店','name'=>'胡小红','phone'=>'15258803661'],
['com'=>'名仕之家和美家一店','name'=>'钟金梅','phone'=>'15257140604'],
['com'=>'华義房产知稼苑店','name'=>'王草草','phone'=>'15255789195'],
['com'=>'我爱我家和睦二店2组','name'=>'王梦飞','phone'=>'15249806743'],
['com'=>'住邦房产华瑞晴庐店','name'=>'宋建','phone'=>'15224053560'],
['com'=>'我爱我家景上公寓店1组','name'=>'熊宏伟','phone'=>'15224013868'],
['com'=>'我爱我家塘河店1组','name'=>'李东羊','phone'=>'15201574792'],
['com'=>'我爱我家白金海岸店1组','name'=>'李敏','phone'=>'15168482786'],
['com'=>'岳麟资产农业大厦店','name'=>'杜亚勤','phone'=>'15168437211'],
['com'=>'我爱我家大关西苑店3组','name'=>'汪建文','phone'=>'15168362950'],
['com'=>'我爱我家定安店4组','name'=>'俞杰','phone'=>'15168348335'],
['com'=>'房友旺洋居东郡国际店','name'=>'郑飞','phone'=>'15168315320'],
['com'=>'世佳地产世佳地产萧山店','name'=>'高西娥','phone'=>'15167168524'],
['com'=>'房友旺洋居东郡国际店','name'=>'徐自强','phone'=>'15167137496'],
['com'=>'驰家地产萧山店','name'=>'陈龙','phone'=>'15167127952'],
['com'=>'我爱我家塘河店2组','name'=>'郑涛','phone'=>'15158899195'],
['com'=>'Q房网Q房网孩儿巷店','name'=>'杨小飞','phone'=>'15158888043'],
['com'=>'仕成地产西湖店','name'=>'张凯杰','phone'=>'15158836456'],
['com'=>'21世纪旭恒东方华城店','name'=>'赵金程','phone'=>'15158299398'],
['com'=>'我爱我家天水店1组','name'=>'吴小翠','phone'=>'15158192673'],
['com'=>'我爱我家望江南店2组','name'=>'石军龙','phone'=>'15158183521'],
['com'=>'我爱我家天水店1组','name'=>'许瀚彬','phone'=>'15158128548'],
['com'=>'喜信房产滨江店','name'=>'余泽琪','phone'=>'15158090292'],
['com'=>'我爱我家和睦店1组','name'=>'叶康','phone'=>'15158087366'],
['com'=>'房友房产南岸晶都店','name'=>'闻日星','phone'=>'15158079215'],
['com'=>'住邦房产开元店','name'=>'王燕婵','phone'=>'15157931175'],
['com'=>'豪世华邦贾家B','name'=>'魏兵','phone'=>'15151530929'],
['com'=>'我爱我家东方郡店5组','name'=>'程慧君','phone'=>'15088755270'],
['com'=>'我爱我家金色江南店1组','name'=>'张君','phone'=>'15088713031'],
['com'=>'我爱我家狮虎桥店3组','name'=>'包军','phone'=>'15088697798'],
['com'=>'我爱我家潮鸣寺店1组','name'=>'党新强','phone'=>'15068818284'],
['com'=>'我爱我家复兴店2组','name'=>'缪鹏','phone'=>'15068147520'],
['com'=>'豪世华邦豪世华邦和睦店B组','name'=>'祝建标','phone'=>'15068126339'],
['com'=>'住邦房产钱塘明月城北店','name'=>'喻翔','phone'=>'15068120993'],
['com'=>'我爱我家麒麟街店1组','name'=>'陈康','phone'=>'15067197018'],
['com'=>'我爱我家狮虎桥店1组','name'=>'刘娜','phone'=>'15067179917'],
['com'=>'住邦房产华瑞晴庐店','name'=>'刘洪','phone'=>'15067179632'],
['com'=>'我爱我家新工新村店1组','name'=>'王希佳','phone'=>'15067125515'],
['com'=>'我爱我家仙林桥店1组','name'=>'赵子虎','phone'=>'15058166537'],
['com'=>'顺驰地产莱茵传奇一组','name'=>'赵生','phone'=>'15058152562'],
['com'=>'Q房网景芳店B组','name'=>'於恺杰','phone'=>'15057279979'],
['com'=>'我爱我家武林店2组','name'=>'朱晓红','phone'=>'15057196720'],
['com'=>'我爱我家和睦店1组','name'=>'胡勇','phone'=>'15057195293'],
['com'=>'豪世华邦银树湾店','name'=>'曹璇','phone'=>'15057166856'],
['com'=>'住邦房产钱塘明月城北店','name'=>'李明','phone'=>'15057155370'],
['com'=>'恒邦房产恒邦房产山阴店','name'=>'钱梦萍','phone'=>'13989892540'],
['com'=>'我爱我家鸥江公寓店1组','name'=>'林晟','phone'=>'13968138136'],
['com'=>'我爱我家麒麟街店1组','name'=>'单怡炜','phone'=>'13968110323'],
['com'=>'我爱我家麒麟街店1组','name'=>'单怡炜','phone'=>'13968110323'],
['com'=>'鼎隆地产建设一路店','name'=>'杨文瑛','phone'=>'13959288312'],
['com'=>'我爱我家和睦店1组','name'=>'吕梁','phone'=>'13958626665'],
['com'=>'我爱我家和睦店4组','name'=>'曾元元','phone'=>'13958193622'],
['com'=>'豪世华邦豪世华邦孩儿巷店','name'=>'戚征涛','phone'=>'13958080994'],
['com'=>'我爱我家庆春店2组','name'=>'刘秋平','phone'=>'13958045760'],
['com'=>'我爱我家金色钱塘店2组','name'=>'张焕云','phone'=>'13957173345'],
['com'=>'我爱我家复兴店2组','name'=>'席娟霞','phone'=>'13957135466'],
['com'=>'我爱我家庆春店2组','name'=>'李栋','phone'=>'13957131074'],
['com'=>'我爱我家塘河店2组','name'=>'徐伟','phone'=>'13868088003'],
['com'=>'我爱我家潮鸣寺店1组','name'=>'方伟','phone'=>'13868052704'],
['com'=>'我爱我家信义坊店1组','name'=>'徐裕飞','phone'=>'13867427231'],
['com'=>'鸿邦地产鸿邦地产教工店','name'=>'夏小群','phone'=>'13867161413'],
['com'=>'恒峰地产南秀路店','name'=>'黄文中','phone'=>'13867113637'],
['com'=>'我爱我家武林店2组','name'=>'陈彩霞','phone'=>'13858100737'],
['com'=>'祥策地产钱塘明月店','name'=>'张玉新','phone'=>'13857391396'],
['com'=>'我爱我家仙林桥店1组','name'=>'陈光玉','phone'=>'13857164621'],
['com'=>'益邦地产售楼处','name'=>'钱狄肖','phone'=>'13819550275'],
['com'=>'名仕之家佳境天城二店','name'=>'李丽','phone'=>'13819152246'],
['com'=>'我爱我家近江店5组','name'=>'简齐东','phone'=>'13819117726'],
['com'=>'久信房产闻堰店','name'=>'俞晓力','phone'=>'13806513145'],
['com'=>'我爱我家仙林桥店1组','name'=>'任本龙','phone'=>'13777868528'],
['com'=>'我爱我家文晖店1组','name'=>'习武军','phone'=>'13777868296'],
['com'=>'我爱我家新武林店1组','name'=>'杨双平','phone'=>'13777465546'],
['com'=>'顺马地产金惠店','name'=>'刘红伟','phone'=>'13777378068'],
['com'=>'我爱我家塘河店1组','name'=>'叶志朋','phone'=>'13777067687'],
['com'=>'益邦地产月明4店','name'=>'邢泽阳','phone'=>'13758593969'],
['com'=>'我爱我家白金海岸店2组','name'=>'张一鸣','phone'=>'13758235031'],
['com'=>'住邦房产蓝爵国际店','name'=>'闻坚良','phone'=>'13758188753'],
['com'=>'我爱我家庆春店2组','name'=>'王震','phone'=>'13758177146'],
['com'=>'万有房产万有房产明星店','name'=>'李金伟','phone'=>'13758170098'],
['com'=>'我爱我家江南国际城店1组','name'=>'李云菲','phone'=>'13758148332'],
['com'=>'鸿邦地产鸿邦地产教工店','name'=>'李银','phone'=>'13757177130'],
['com'=>'浙豪房产白马店','name'=>'周继东','phone'=>'13757140764'],
['com'=>'我爱我家花园南村店1组','name'=>'祝典文','phone'=>'13757100249'],
['com'=>'住邦房产钱塘明月博学店','name'=>'汪土明','phone'=>'13755608587'],
['com'=>'顺驰地产金惠代理二组','name'=>'王娅','phone'=>'13754316702'],
['com'=>'住邦房产住邦房产振宁店','name'=>'黄彩云','phone'=>'13750846646'],
['com'=>'我爱我家东方郡店1组','name'=>'董国财','phone'=>'13738170924'],
['com'=>'住邦房产华瑞晴庐店','name'=>'王岩','phone'=>'13738156246'],
['com'=>'住邦房产明怡店','name'=>'范维','phone'=>'13738154054'],
['com'=>'我爱我家东门市场店1组','name'=>'孙龙宝','phone'=>'13738148264'],
['com'=>'住邦房产钱塘明月城北店','name'=>'冯刚','phone'=>'13738143394'],
['com'=>'众邦地产拱秀店买卖一组','name'=>'舒鹏','phone'=>'13738137922'],
['com'=>'我爱我家左岸花园店1组','name'=>'洪功臣','phone'=>'13738121403'],
['com'=>'我爱我家莱茵传奇店1组','name'=>'闫磊','phone'=>'13738096813'],
['com'=>'越源房产萧山店','name'=>'高宝宝','phone'=>'13738050742'],
['com'=>'易房地产彩虹城店买卖2组','name'=>'赖鉴','phone'=>'13736386272'],
['com'=>'住邦房产明怡店','name'=>'李军','phone'=>'13735813317'],
['com'=>'荣惠地产明星店','name'=>'吴洲','phone'=>'13735573258'],
['com'=>'君诚房产君诚房产振宁店','name'=>'裴敏君','phone'=>'13735453001'],
['com'=>'Q房网望江门店','name'=>'杨洋','phone'=>'13732240074'],
['com'=>'豪世华邦豪世华邦佑圣观店','name'=>'邵桢洲','phone'=>'13732214947'],
['com'=>'阔丰地产钱江世纪城店','name'=>'郑滔滔','phone'=>'13706717364'],
['com'=>'我爱我家通策广场店1组','name'=>'范永彪','phone'=>'13685752327'],
['com'=>'益邦地产月明3店','name'=>'冷凌峰','phone'=>'13675837712'],
['com'=>'骐骥地产骐骥地产和美家店','name'=>'娄瑾','phone'=>'13675754964'],
['com'=>'豪世华邦豪世华邦屏风街店','name'=>'褚银港','phone'=>'13666689349'],
['com'=>'住邦房产住邦房产振宁店','name'=>'王振敏','phone'=>'13666657669'],
['com'=>'21世纪旭恒旭辉城店','name'=>'钟特川','phone'=>'13666601016'],
['com'=>'我爱我家世纪之光店1组','name'=>'傅喻民','phone'=>'13656710215'],
['com'=>'我爱我家世纪之光店1组','name'=>'傅喻民','phone'=>'13656710215'],
['com'=>'万有房产万有房产明星店','name'=>'李水琴','phone'=>'13655714737'],
['com'=>'我爱我家长庆店1组','name'=>'方云霞','phone'=>'13646824221'],
['com'=>'我爱我家金色江南店1组','name'=>'余钧','phone'=>'13646642167'],
['com'=>'我爱我家东方郡二店2组','name'=>'柏少伟','phone'=>'13634151763'],
['com'=>'住邦房产钱塘明月博学店','name'=>'潘锣杰','phone'=>'13634122587'],
['com'=>'我爱我家建国南苑店2组','name'=>'汪文杰','phone'=>'13616537395'],
['com'=>'扬瑞房产望京店','name'=>'吴佼航','phone'=>'13615890966'],
['com'=>'我爱我家和睦店1组','name'=>'董晓玲','phone'=>'13615815842'],
['com'=>'我爱我家十亩田店1组','name'=>'刘寒','phone'=>'13606718662'],
['com'=>'我爱我家天水店3组','name'=>'潘振龙','phone'=>'13606545604'],
['com'=>'我爱我家复兴店1组','name'=>'谢磊','phone'=>'13588823364'],
['com'=>'我爱我家庆春店1组','name'=>'王凯功','phone'=>'13588795423'],
['com'=>'鸿邦地产鸿邦地产教工店','name'=>'王贤恩','phone'=>'13588717054'],
['com'=>'名仕之家和美家一店','name'=>'秦勇','phone'=>'13588236865'],
['com'=>'住邦房产钱塘明月城北店','name'=>'李重阳','phone'=>'13588071677'],
['com'=>'我爱我家世纪之光店1组','name'=>'林尧','phone'=>'13587806123'],
['com'=>'21世纪旭恒星耀城店','name'=>'陈文','phone'=>'13575758275'],
['com'=>'我爱我家世纪之光店1组','name'=>'裘科迅','phone'=>'13575561422'],
['com'=>'房友旺洋居东郡国际店','name'=>'付闯','phone'=>'13566199473'],
['com'=>'尊园房地产东方花城店A组','name'=>'曹志强','phone'=>'13524325257'],
['com'=>'我爱我家佳境天城二店1组','name'=>'丁翠容','phone'=>'13515813890'],
['com'=>'房宇房地产颐和花园店','name'=>'杨刚志','phone'=>'13486363073'],
['com'=>'鼎隆地产钱江世纪城店','name'=>'王晨','phone'=>'13486351230'],
['com'=>'我爱我家望江府店3组','name'=>'马世响','phone'=>'13456927194'],
['com'=>'我爱我家学林尚苑店2组','name'=>'管嘉诚','phone'=>'13456800399'],
['com'=>'我爱我家江城店2组','name'=>'石园园','phone'=>'13456761892'],
['com'=>'我爱我家仙林桥店1组','name'=>'甘军','phone'=>'13456753021'],
['com'=>'易房地产世纪之光二组','name'=>'彭程','phone'=>'13429659633'],
['com'=>'住邦房产明怡店','name'=>'李炯','phone'=>'13429158713'],
['com'=>'我爱我家松木场店3组','name'=>'韩莉萍','phone'=>'13396719232'],
['com'=>'我爱我家望江南店1组','name'=>'贾佼佼','phone'=>'13396563301'],
['com'=>'我爱我家江南国际城店1组','name'=>'陈立佳','phone'=>'13396515496'],
['com'=>'骐骥地产骐骥地产和美家店','name'=>'崔志淘','phone'=>'13362890801'],
['com'=>'Q房网Q房网雄镇楼店','name'=>'吴晓航','phone'=>'13362102909'],
['com'=>'易房地产世纪之光一组','name'=>'喻源','phone'=>'13357162076'],
['com'=>'我爱我家麒麟街店1组','name'=>'钱佳','phone'=>'13355784280'],
['com'=>'中原地产温馨人家四组','name'=>'李邵钰','phone'=>'13346163101'],
['com'=>'住邦房产住邦房产振宁店','name'=>'李瑞雪','phone'=>'13336127388'],
['com'=>'豪世华邦豪世华邦孩儿巷店','name'=>'许勇勇','phone'=>'13336123291'],
['com'=>'我爱我家金色江南店1组','name'=>'姬海伟','phone'=>'13336089072'],
['com'=>'我爱我家鼓楼店1组','name'=>'沈建强','phone'=>'13336037377'],
['com'=>'绿城置换莱茵传奇店','name'=>'方立超','phone'=>'13306532255'],
['com'=>'我爱我家佑圣观店3组','name'=>'王团部','phone'=>'13306531225'],
['com'=>'我爱我家塘河店2组','name'=>'魏巍','phone'=>'13282816836'],
['com'=>'我爱我家和睦店1组','name'=>'叶康','phone'=>'13282439775'],
['com'=>'我爱我家仙林桥店1组','name'=>'万尚平','phone'=>'13282118340'],
['com'=>'我爱我家复兴店2组','name'=>'姜世威','phone'=>'13282030522'],
['com'=>'住邦房产莱茵传奇店','name'=>'何佳伟','phone'=>'13282007912'],
['com'=>'益邦地产月明1店','name'=>'尚道虎','phone'=>'13276810916'],
['com'=>'我爱我家曼城店2组','name'=>'王懿','phone'=>'13264862939'],
['com'=>'我爱我家美政花苑店1组','name'=>'景风鸣','phone'=>'13248403763'],
['com'=>'豪世华邦豪世华邦海月店','name'=>'陈浩东','phone'=>'13221834052'],
['com'=>'我爱我家景上公寓店1组','name'=>'金哲恒','phone'=>'13221819047'],
['com'=>'御诚聚房地产崇化店','name'=>'丁威','phone'=>'13208029538'],
['com'=>'信业居房产钱塘明月店','name'=>'赵天文','phone'=>'13186983336'],
['com'=>'我爱我家之江九里店3组','name'=>'滕海波','phone'=>'13185711787'],
['com'=>'顺家地产霞飞郡店','name'=>'熊冬冬','phone'=>'13173660717'],
['com'=>'绿城置换远洋店','name'=>'张元','phone'=>'13173650327'],
['com'=>'链杰地产滨江店','name'=>'桂跃玲','phone'=>'13162791709'],
['com'=>'我爱我家十亩田店1组','name'=>'俞恩涛','phone'=>'13157109231'],
['com'=>'21世纪不动产义桥店','name'=>'俞坷良','phone'=>'13148454350'],
['com'=>'我爱我家天阳上河店2组','name'=>'洪功名','phone'=>'13148363743'],
['com'=>'我爱我家十亩田店1组','name'=>'贡娇燕','phone'=>'13136155302'],
['com'=>'顺马地产金惠店','name'=>'王建冬','phone'=>'13136136016'],
['com'=>'我爱我家近江店1组','name'=>'严毅','phone'=>'13136123212'],
['com'=>'我爱我家狮虎桥店3组','name'=>'邱煜','phone'=>'13136116122'],
['com'=>'我爱我家复兴店1组','name'=>'史英权','phone'=>'13116766052'],
['com'=>'我爱我家中央花城店1组','name'=>'胡齐华','phone'=>'13116614267'],
['com'=>'我爱我家东方郡店1组','name'=>'冯超','phone'=>'13093783692'],
['com'=>'我爱我家塘河店2组','name'=>'张浩','phone'=>'13093725995'],
['com'=>'Q房网春和钱塘店','name'=>'杨建丰','phone'=>'13083991221'],
['com'=>'我爱我家湖墅嘉园店1组','name'=>'王宏建','phone'=>'13083973543'],
['com'=>'鼎胜房产二部','name'=>'官龙春','phone'=>'13083970067'],
['com'=>'我爱我家望江店1组','name'=>'张洋洋','phone'=>'13073694584'],
['com'=>'我爱我家天水店1组','name'=>'李坦克','phone'=>'13067868713'],
['com'=>'我爱我家中央花城店1组','name'=>'黄华','phone'=>'13067821905'],
['com'=>'尊园房地产东方花城店F组','name'=>'陈香归','phone'=>'13067738100'],
['com'=>'豪世华邦大浒F','name'=>'吴祖文','phone'=>'13047601528'],
['com'=>'我爱我家银爵世纪店3组','name'=>'黄呈祥','phone'=>'13036258720'],
['com'=>'鸣家房产长青店','name'=>'张平心','phone'=>'13034208111'],
['com'=>'我爱我家同人店1组','name'=>'吴义胜','phone'=>'13030890791'],
['com'=>'我爱我家彩虹城二店2组','name'=>'刘刚','phone'=>'13024684573'],
['com'=>'名仕之家积家店','name'=>'张赛鹏','phone'=>'13023639386'],
['com'=>'益邦地产月明1店','name'=>'李书','phone'=>'13023613657'],
['com'=>'Q房网Q房网孩儿巷店','name'=>'程思源','phone'=>'13023611531'],
['com'=>'我爱我家天水店5组','name'=>'黄西浩','phone'=>'13018901581'],
['com'=>'我爱我家柳浪东苑店1组','name'=>'陈超','phone'=>'13003612658'],];
        foreach ($arr as $key => $value) {
            $com = $value['com'];
            if(!($comobj = CompanyExt::model()->find("name='$com'"))) {
                $comobj = new CompanyExt;
                $comobj->name = $com;
                $comobj->type = 2;
                $comobj->status = 1;
                $comobj->area = 21;
                $comobj->street = 34;
                $comobj->save();
            }
            $phone = $value['phone'];
            if(UserExt::model()->find("phone='$phone'")) {
                continue;
            }
            $user = new UserExt;
            $user->cid = $comobj->id;
            $user->name = $value['name'];
            $user->phone = $phone;
            $user->type = 2;
            $user->status = 1;
            $user->save();
        }
        echo "finished";
    }
}