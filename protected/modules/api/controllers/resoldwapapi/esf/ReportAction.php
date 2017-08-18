<?php
/**
 * 二手房举报接口
 * @author steven allen <[<email address>]>
 * @date 2016.10.20
 */
class ReportAction extends CAction
{
	public function run()
	{
		$jbArr = [
		'esf'=>1,'zf'=>2,'qg'=>3,'qz'=>4
		];
		$reason = Yii::app()->request->getPost('reason','');
		$content = Yii::app()->request->getPost('content','');
		$infoid = Yii::app()->request->getPost('infoid',0);
		// $infoname = Yii::app()->request->getPost('infoname','');
		$phone = Yii::app()->request->getPost('phone','');
		$type = Yii::app()->request->getPost('type','');
		$code = Yii::app()->request->getPost('code','');
		$code == 'NaN' && $code = 0;
		$uid = isset(Yii::app()->uc->user->uid)?Yii::app()->uc->user->uid:0;
		$account = isset(Yii::app()->uc->user->username)?Yii::app()->uc->user->username:'';
		if(!$infoid || !$phone || !$type || !$code)
			return $this->controller->returnError('参数错误');
        if(!ResoldSmsExt::model()->count(['condition'=>'code=:code and phone=:phone and created>:time','params'=>[':code'=>$code,':phone'=>$phone,':time'=>time()-15*60]]))
            return $this->getController()->returnError('验证码错误');
		
		$info = $type=='esf'?ResoldEsfExt::model()->findByPk($infoid):($type=='zf'?ResoldZfExt::model()->findByPk($infoid):[]);
		$type = $jbArr[$type];
		
		$report = new ResoldReportExt;
		$report->reason = $reason;
		$report->content = $content;
		$report->infoid = $infoid;
		$report->infoname = $info?$info->title:'';
		$report->phone = $phone;
		$report->type = $type;
		$report->uid = $uid;
		$report->account = $account;
		if(!$report->save())
		{
			$this->controller->frame['status'] = 'error';
			$this->controller->frame['msg'] = $report->errors;
		}

	}
	
}