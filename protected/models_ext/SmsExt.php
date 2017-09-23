<?php 
/**
 * 短信验证码类
 * @author steven.allen <[<email address>]>
 * @date(2017.2.12)
 */
class SmsExt extends Sms{
	/**
     * 定义关系
     */
    public function relations()
    {
        return array(
            // 'houseInfo'=>array(self::BELONGS_TO, 'HouseExt', 'house'),
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
        if($this->getIsNewRecord())
            $this->created = $this->updated = time();
        else
            $this->updated = time();
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

    public static function addOne($phone='',$type='注册')
    {
        // 一分钟有效期外或者新的可以保存
        if($phone) {
            if($info = SmsExt::model()->find("phone='$phone'")) {
                if(time()-$info->created<60) {
                    return false;
                }
            }
            $code = rand(1000,9999);
            $model = new SmsExt;
            $model->phone = $phone;
            $model->code = $code;
            if($model->save()) {
                Yii::app()->msg->sendSms(Yii::app()->params['msgArr'][$type],$phone,['code'=>$code]);
                // Yii::app()->mns->run($phone,$code);
                return true;
            } else {
                return false;
            }
        }
    }
    public static function sendOne($phone='',$type='')
    {
        // 一分钟有效期外或者新的可以保存
        if($phone) {
            if($info = SmsExt::model()->find("phone='$phone'")) {
                if(time()-$info->created<60) {
                    return false;
                }
            }
            $code = rand(1000,9999);
            $model = new SmsExt;
            $model->phone = $phone;
            $model->code = $code;
            if($model->save()) {
                Yii::app()->msg->sendSms(Yii::app()->params['msgArr'][$type],$phone,['code'=>$code]);
                // Yii::app()->mns->run($phone,$code);
                return true;
            } else {
                return false;
            }
        }
    }
    public static function sendMsg($type='',$phone='',$arr=null)
    {
        // 一分钟有效期外或者新的可以保存
        if($phone) {
            return Yii::app()->msg->sendSms(Yii::app()->params['msgArr'][$type],$phone,$arr);
            } else {
                return false;
           
            }
    }

    public static function checkPhone($phone='',$code='')
    {
        if((int)$phone && $code) {
            $criteria = new CDbCriteria;
            $criteria->addCondition("phone='$phone'");
            $criteria->order = 'created desc';
            $info = SmsExt::model()->find($criteria);
            // var_dump(15*60);exit;
            if(!$info) {
                return false;
                // 15分钟有效
            } elseif($info->code == $code && time()-$info->created<=15*60) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}