<?php 
/**
 * 用户类
 * @author steven.allen <[<email address>]>
 * @date(2017.2.12)
 */
class UserExt extends User{
    /**
     * @var array 状态
     */
    static $status = array(
        0 => '禁用',
        1 => '启用',
        2 => '回收站',
    );
    /**
     * @var array 状态按钮样式
     */
    static $statusStyle = array(
        0 => 'btn btn-sm btn-warning',
        1 => 'btn btn-sm btn-primary',
        2 => 'btn btn-sm btn-danger'
    );
    public static $ids = [
        '1'=>'总代公司',
        '2'=>'分销公司',
        '3'=>'独立中介',
    ];
    public static $is_jls = [
        0=>'暂无',
        1=>'市场部经理',
        2=>'案场部经理',
        3=>'市场专员',
        4=>'案场助理',
        5=>'案场销售',
    ];
    public static $sex = [
    '未知','男','女'
    ];
	/**
     * 定义关系
     */
    public function relations()
    {
        return array(
            // 'houseInfo'=>array(self::BELONGS_TO, 'HouseExt', 'house'),
            'news'=>array(self::HAS_MANY, 'ArticleExt', 'uid'),
            'comments'=>array(self::HAS_MANY, 'CommentExt', 'uid'),
            'companyinfo'=>array(self::BELONGS_TO, 'CompanyExt', 'cid'),
            'plotplaces'=>array(self::HAS_MANY, 'PlotPlaceExt', 'uid'),
            'plotsales'=>array(self::HAS_MANY, 'PlotSaleExt', 'uid'),
        );
    }

    /**
     * @return array 验证规则
     */
    public function rules() {
        $rules = parent::rules();
        return array_merge($rules, array(
            array('phone', 'unique', 'message'=>'{attribute}已存在'),
            array('qf_uid', 'required')
        ));
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
        if(!$this->image){
            $this->image = SiteExt::getAttr('qjpz','userImg');
        }
    }

    public function beforeValidate() {
        if(!$this->type) {
            $cinfo = $this->companyinfo;
            if(!$cinfo) {
                $this->type = 3;
            } else {
                $this->type = $cinfo->type;
            }
        }
        if($this->deleted==1) {
            $this->status = 1;
        }
        if($this->getIsNewRecord()) {
            if(!$this->qf_uid && !empty($_COOKIE['qf_uid'])) {
                $this->qf_uid = $_COOKIE['qf_uid'];
            }
            if($this->type==3&&$this->status==0) {
                $res = Yii::app()->controller->sendNotice('有新的独立经纪人注册，请登陆后台审核','',1);
            }
            $this->created = $this->updated = time();
        }
        else {
            $this->updated = time();
            if($this->type==3 && $this->status==1 && $this->qf_uid && ((Yii::app()->db->createCommand('select status from user where qf_uid='.$this->qf_uid)->queryScalar())==0)) {
                $res = Yii::app()->controller->sendNotice('您的新房通账号已通过审核，欢迎访问经纪圈新房通',$this->qf_uid);
                // var_dump(SmsExt::sendMsg('经纪人注册通过',$this->phone););exit;
                SmsExt::sendMsg('经纪人注册通过',$this->phone);
                // HttpHelper::get('http://fang.jj58.com.cn/api/index/sendNotice?uid='.$this->qf_uid.'&words=您的账号已通过审核，欢迎访问经纪圈新房通');
            }
        }
            
        return parent::beforeValidate();
    }

    /**
     * 命名范围
     * @return array
     */
    public function scopes()
    {
        $alias = $this->getTableAlias();
        return array(
            'sorted' => array(
                'order' => "{$alias}.sort desc,{$alias}.updated desc",
            ),
            'normal' => array(
                'condition' => "{$alias}.status=1 and {$alias}.deleted=0",
                'order'=>"{$alias}.sort desc,{$alias}.updated desc",
            ),
            'undeleted' => array(
                'condition' => "{$alias}.deleted=0",
                // 'order'=>"{$alias}.sort desc,{$alias}.updated desc",
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

}