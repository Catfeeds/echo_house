<?php
/**
 * 二手房总数
 * @author steven.allen
 * @date 2016.09.29
 */
class CountAction extends CAction
{
	public function run()
	{
		return ResoldEsfExt::model()->count(['condition'=>'sale_status=1']);
	}
}