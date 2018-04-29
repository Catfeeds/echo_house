<?php 
/**
 * 球员类
 * @author steven.allen <[<email address>]>
 * @date(2017.2.12)
 */
class PlotAskExt extends PlotAsk{
	/**
     * 定义关系
     */
    public function relations()
    {
        return array(
            'plot'=>array(self::BELONGS_TO, 'PlotExt', 'hid'),
            'user'=>array(self::BELONGS_TO, 'UserExt', 'uid'),
            'answers'=>array(self::HAS_MANY, 'PlotAnswerExt', 'aid','condition'=>'answers.status=1','order'=>'answers.sort desc,answers.updated desc'),
            // 'answers_count'=>array(self::STAT, 'PlotAnswerExt', 'aid','condition'=>'answers_count.status=1'),
            
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
            $this->created = $this->updated = time();
            $plot = $this->plot;
            if($plot) {
                $res = Yii::app()->controller->sendNotice('有新的用户对'.$plot->title.'进行提问，内容为：'.$this->title.'，点击以下链接查看：'.Yii::app()->request->getHostInfo().'/api/index/detail?id='.$plot->id.'，请登陆后台审核','',1);
                
                $users = Yii::app()->db->createCommand("select u.qf_uid,u.phone,u.id,u.name from user u left join save s on u.id=s.uid where s.hid=".$this->hid)->queryAll();
                if($users) {
                    foreach ($users as $key => $value) {
                        $value['phone'] && SmsExt::sendMsg('提问提醒对接人',$value['phone'],['name'=>$value['name'],'lpmc'=>$plot->title]);
                        $value['qf_uid'] && Yii::app()->controller->sendNotice('尊敬的'.$value['name'].', '.$plot->title.'有了新的提问，请点击以下链接查看: http://house.jj58.com.cn/api/index/detail?id='.$this->hid,$value['qf_uid']);
                    }
                }
            }
        }
        else
            $this->updated = time();
        return parent::beforeValidate();
    }

    public function afterSave()
    {
        parent::afterSave();
        
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
                'condition' => "{$alias}.status=1",
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