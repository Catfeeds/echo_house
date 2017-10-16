<?php
/**
 * home前台面包屑
 * @author sc
 * @date 2015-10-30
 */
Yii::import('zii.widgets.CBreadcrumbs');
class HomeBreadcrumbs extends CBreadcrumbs
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
			$links[]=CHtml::link((SM::urmConfig()->cityName().'房产'),array('/home/index/index'));
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
		$this->render('homeBreadcrumbs', array('links'=>$links));
	}
}

?>
