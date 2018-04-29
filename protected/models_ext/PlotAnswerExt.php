<?php 
/**
 * 球员类
 * @author steven.allen <[<email address>]>
 * @date(2017.2.12)
 */
class PlotAnswerExt extends PlotAnswer{
	/**
     * 定义关系
     */
    public function relations()
    {
        return array(
            'plot'=>array(self::BELONGS_TO, 'PlotExt', 'hid'),
            'user'=>array(self::BELONGS_TO, 'UserExt', 'uid'),
            'ask'=>array(self::BELONGS_TO, 'PlotAskExt', 'aid'),
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
            $this->created = $this->updated = time();
            $plot = $this->plot;
            $ask = $this->ask;
            if($plot && $ask) {
                $res = Yii::app()->controller->sendNotice('有新的用户对关于'.$plot->title.'项目的'.$ask->title.'提问进行回答，内容为：'.$this->note.'，点击以下链接查看：'.Yii::app()->request->getHostInfo().'/api/index/detail?id='.$plot->id.'，请登陆后台审核','',1);
                $askuser = $ask->user;
                if(isset($askuser['phone']))
                    SmsExt::sendMsg('回答提醒提问人',$askuser['phone'],['name'=>$askuser['name'],'lpmc'=>$plot->title]);
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