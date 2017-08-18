<?php
/**
 * 用户接口 获取用户登录状态 用户信息
 * @author steven allen <[<email address>]>
 * @date 2016.10.21
 */
class UserAction extends CAction
{
	public function run()
	{
		if(Yii::app()->uc->user->getIsGuest())
			$this->controller->returnError('未登录');
		else
		{
			if($staff = ResoldStaffExt::model()->findStaffByUid(Yii::app()->uc->user->uid))
			{
				$data = [
					'is_staff'=>1,
					'id'=>$staff->id,
					'uid'=>Yii::app()->uc->user->uid,
					'name'=>$staff->name,
					'image'=>ImageTools::fixImage($staff->image,60,60),
					'is_manager'=>$staff->is_manager,
					'phone'=>$staff->phone,
					'can_sale_num'=>$staff->getCanSaleNum(),
					'qq'=>$staff->qq?$staff->qq:'',
				];
			}
			else
			{
				$data = [
					'is_staff'=>0,
					'id'=>Yii::app()->uc->user->id,
					'uid'=>Yii::app()->uc->user->uid,
					'name'=>Yii::app()->uc->user->username,
					'image'=>ImageTools::fixImage(Yii::app()->uc->user->icon,60,60),
					'is_manager'=>'0',
					'phone'=>Yii::app()->uc->user->phone,
					'can_sale_num'=>$this->controller->getUserCanPubNum(Yii::app()->uc->user->uid),
					'qq'=>'',
				];
			}
			$this->controller->frame['data'] = $data;
		}
			
	}
}
