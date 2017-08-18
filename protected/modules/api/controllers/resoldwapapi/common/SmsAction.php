<?php
/**
 * 短信接口
 * User: jt
 * Date: 2016/11/22 9:33
 */

class SmsAction extends CAction
{
    public function run()
    {
        $mobile = Yii::app()->request->getPost('phone');
        if(empty($mobile) || !preg_match("/^1[3|4|5|8|7][0-9]{9}$/",$mobile)){
            return $this->controller->returnError('手机不能为空或格式错误');
        }
        $time = YII_BEGIN_TIME-60;
        $criteria = new CDbCriteria(array(
            'condition'=> 'phone=:phone and created >= :time and status=1',
            'params'=>array(':phone'=>$mobile,':time'=>$time)
        ));
        $old_resold_sms = ResoldSmsExt::model()->find($criteria);
        if($old_resold_sms)
            return $this->controller->returnError('发送间隔小于60秒');
        $code = ClientMobile::getCode(4);
        $msg = '您的验证码是'.$code.'，在15分钟内有效，如非本人操作请忽略本短信';
        $return = ClientMobile::sendSms($mobile,$msg);
        $resold_sms = new ResoldSmsExt();
        $resold_sms->phone = $mobile;
        $resold_sms->msg = $msg;
        $resold_sms->code = $code;
        $resold_sms->origin = $_POST['origin'];
        $resold_sms->status = $return ? 1 : 0;
        if($return && $resold_sms->save()){
            return $this->controller->frame['msg'] = '发送成功';
        }else{
            return $this->controller->returnError('发送失败');
        }
    }
}