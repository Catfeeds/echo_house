<?php
/**
 * 用户测试接口
 */
class UserTestAction extends CAction
{
	public function run($uid)
	{
 		if($staff = ResoldStaffExt::model()->findStaffByUid($uid))
		{
			$data = [
				'is_staff'=>1,
				'id'=>$staff->id,
				'uid'=>$uid,
				'name'=>$staff->name,
				'image'=>$staff->image,
				'is_manager'=>$staff->is_manager,
				'phone'=>$staff->phone,
			];
		}
		else
		{
			$data = [
				'is_staff'=>0,
				'id'=>1111,
				'uid'=>'',
				'name'=>'游客测试',
				'image'=>'https://ss1.baidu.com/6ONXsjip0QIZ8tyhnq/it/u=3214414828,3146317039&fm=58',
				'is_manager'=>'0',
				'phone'=>'13861242424',
			];
		}
		$this->controller->frame['data'] = $data;
 	}	
}