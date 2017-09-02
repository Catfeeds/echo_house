<?php
/**
 * 缓存管理脚本
 */
class CacheCommand extends CConsoleCommand
{
	/**
	 * [actionCleanMemcache 清除memcache缓存]
	 * @param  string $id [description]
	 * @return [type]     [description]
	 */
	public function actionCleanMemcache($id='')
	{
		if(CacheExt::delete($id))
			echo "finished\n";
	}
}