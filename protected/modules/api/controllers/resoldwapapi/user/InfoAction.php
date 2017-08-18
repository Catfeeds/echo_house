<?php
/**
 * 获取用户信息
 * @author steven.allen
 * @date 2016.09.18
 */
class InfoAction extends CAction
{
	public function run($id=0)
	{
		$user['image'] = '';
		$user['name'] = '';
		$user['phone'] = '';

		$this->controller->frame['data'] = [
		'user'=>$user,
		];

	}
}