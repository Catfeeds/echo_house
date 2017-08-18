<?php
/**
 * 二手房首页wap接口
 * @author steven.allen
 * @date 2016.09.18
 */
class IndexAction extends CAction
{
	/**
	 * [actionIndex 首页接口]
	 * @return [type] [description]
	 */
	public function run()
	{
		// 首页顶部图片和logo
		$top['image'] = ImageTools::fixImage(SM::resoldImageConfig()->resoldWapIndexImage->value);
		$top['logo'] = ImageTools::fixImage(SM::resoldImageConfig()->resoldWapSiteLogo()); // SM::resoldConfig()->;

		//导航模块
		$nav[] = ['二手房','找租房','小区找房','邻校房','写字楼','商铺','买房宝典','我要卖房'];

		// 推荐区域
		$recom = [];
		$recoms = ResoldRecomExt::model()->getRecom('wapsyzb')->findAll();
		if($recoms)
			foreach ($recoms as $key => $value) {
				$recom[] = ['pic'=>$value['image'],'url'=>$value['url'],'title'=>$value['title'],'sub_title'=>$value['s_title']];
			}

		$this->controller->frame['data'] = [
		'top'=>$top,
		'recom'=>$recom,
		];
	}
}
