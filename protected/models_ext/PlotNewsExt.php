<?php 
/**
 * 球员类
 * @author steven.allen <[<email address>]>
 * @date(2017.2.12)
 */
class PlotNewsExt extends PlotNews{
	/**
     * 定义关系
     */
    public function relations()
    {
        return array(
            'plot'=>array(self::BELONGS_TO, 'PlotExt', 'hid'),
            'staff'=>array(self::BELONGS_TO, 'StaffExt', 'staff_id'),
            // 'images'=>array(self::HAS_MANY, 'AlbumExt', 'pid'),
        );
    }

    /**
     * @return array 验证规则
     */
    public function rules() {
        $rules = parent::rules();
        return array_merge($rules, array(
            // array('name', 'unique', 'message'=>'{attribute}已存在')
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
        // if(!$this->image){
        //     $this->image = SiteExt::getAttr('qjpz','productNoPic');
        // }
    }

    public function beforeValidate() {
        if($this->getIsNewRecord()) {
            // $this->status = 1;
            $plot = $this->plot;
            Yii::app()->controller->sendNotice($plot->title.'更新了最新动态，请登陆后台审核','',1);
            // $users = Yii::app()->db->createCommand("select u.qf_uid,u.phone,u.id,u.name from user u left join save s on u.id=s.uid where s.hid=".$this->hid)->queryAll();
            // if($users) {
            //     foreach ($users as $key => $value) {
            //         $value['phone'] && SmsExt::sendMsg('楼盘动态新增',$value['phone'],['name'=>$value['name'],'pro'=>$plot->title]);
            //         $value['qf_uid'] && Yii::app()->controller->sendNotice('尊敬的'.$value['name'].', '.$plot->title.'更新了最新动态，请点击以下链接查看: http://house.jj58.com.cn/api/index/detail?id='.$this->hid,$value['qf_uid']);
            //     }
            // }

            $this->created = $this->updated = time();
        }
        else {
            if($this->status==1 && Yii::app()->db->createCommand('select status from plot_news where id='.$this->id)->queryScalar()==0) {
                $plot = $this->plot;
                $users = Yii::app()->db->createCommand("select u.qf_uid,u.phone,u.id,u.name from user u left join save s on u.id=s.uid where s.hid=".$this->hid)->queryAll();
                if($users) {
                    foreach ($users as $key => $value) {
                        $value['phone'] && SmsExt::sendMsg('楼盘动态新增',$value['phone'],['name'=>$value['name'],'pro'=>$plot->title]);
                        $value['qf_uid'] && Yii::app()->controller->sendNotice('尊敬的'.$value['name'].', '.$plot->title.'更新了最新动态，请点击以下链接查看: http://house.jj58.com.cn/api/index/detail?id='.$this->hid,$value['qf_uid']);
                    }
                }
            }
            $this->updated = time();
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