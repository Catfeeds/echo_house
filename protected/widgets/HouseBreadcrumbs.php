<?php
/**
 * admin后台面包屑
 * @author tivon
 * @date 2015-04-20
 */
Yii::import('zii.widgets.CBreadcrumbs');
class HouseBreadcrumbs extends CBreadcrumbs
{
	/**
	 * 渲染面包屑.
	 */
	public function run()
	{
		if(empty($this->links))
			return;

		$links=array();
		if($this->homeLink===null)
			$links[]=CHtml::link(Yii::t('zii','首页'),array('/'.Yii::app()->controller->module->name.'/common/index'));
		elseif($this->homeLink!==false)
			$links[]=$this->homeLink;
		foreach($this->links as $label=>$url)
		{
			if(is_string($label) || is_array($url))
				$links[]=strtr($this->activeLinkTemplate,array(
					'{url}'=>CHtml::normalizeUrl($url),
					'{label}'=>$this->encodeLabel ? CHtml::encode($label) : $label,
				));
			else
				$links[]=str_replace('{label}',$this->encodeLabel ? CHtml::encode($url) : $url,$this->inactiveLinkTemplate);
		}
		// var_dump($links);die;
		$this->render('houseBreadcrumbs', array('links'=>$links));
	}
}

?>