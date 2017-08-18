<?php
/**
 * admin后台菜单widget
 * @author tivon
 * @date 2015-04-20
 */
Yii::import('zii.widgets.CMenu');
class HouseMenu extends CMenu
{
	/**
	 * @var string 父菜单li的激活样式
	 */
	public $activeCssClass = 'active';
	/**
	 * @var string 有展开项菜单前的图标
	 */
	public $itemPlusIcon = 'fa fa-group';
	/**
	 * @var string 无展开项菜单前的图标
	 */
	public $itemMinusIcon = 'fa fa-minus-square-o';
	/**
	 * @var string 首页菜单项前的图标
	 */
	public $firstIcon = 'fa icon-home';
	/**
	 * @var boolean 当子菜单被激活时，父菜单是否要激活。默认是true
	 */
	public $activateParents = true;

	/**
	 * 初始化菜单
	 * @return void
	 */
	public function init(){
		if(empty($this->items)) $this->items = $this->owner->getXinfangMenu();	//获得
		parent::init();
	}

	/**
	 * 调用 {@link renderMenu} 来渲染菜单项
	 */
	protected function renderMenu($items)
	{
		if(count($items))
		{
			// echo CHtml::openTag('ul',$this->htmlOptions)."\n";
			$this->renderMenuRecursive($items);
			// echo CHtml::closeTag('ul');
		}
	}

	/**
	 * 递归渲染每个菜单项
	 * @param array $items 需要被递归渲染的菜单项
	 */
	protected function renderMenuRecursive($items)
	{
		$count=0;
		$n=count($items);
		foreach($items as $item)
		{
			$count++;
			$options=isset($item['itemOptions']) ? $item['itemOptions'] : array();
			$class=array();
			if($item['active'] && $this->activeCssClass!='')
				$class[]=$this->activeCssClass;
			if($count===1 && $this->firstItemCssClass!==null)
				$class[]=$this->firstItemCssClass;
			if($count===$n && $this->lastItemCssClass!==null)
				$class[]=$this->lastItemCssClass;
			if($this->itemCssClass!==null)
				$class[]=$this->itemCssClass;
			if($class!==array())
			{
				if(empty($options['class']))
					$options['class']=implode(' ',$class);
				else
					$options['class'].=' '.implode(' ',$class);
			}

			echo CHtml::openTag('li', $options);

			$menu=$this->renderMenuItem($item);
			if(isset($this->itemTemplate) || isset($item['template']))
			{
				$template=isset($item['template']) ? $item['template'] : $this->itemTemplate;
				echo strtr($template,array('{menu}'=>$menu));
			}
			else
				echo $menu;

			if(isset($item['items']) && count($item['items']))
			{
				$item['submenuOptions'] = array('class'=>'sub-menu');
				echo "\n".CHtml::openTag('ul',isset($item['submenuOptions']) ? $item['submenuOptions'] : $this->submenuHtmlOptions)."\n";
				$this->renderMenuRecursive($item['items']);
				echo CHtml::closeTag('ul')."\n";
			}

			echo CHtml::closeTag('li')."\n";
		}
	}

	/**
	 * 渲染每个菜单项的内容
	 * @param array $item 需要被渲染的单个菜单项. 详见{@link items}了解菜单项里的内容.
	 * @return string
	 */
	protected function renderMenuItem($item)
	{
		$label=$this->linkLabelWrapper===null ? $item['label'] : CHtml::tag($this->linkLabelWrapper, $this->linkLabelWrapperHtmlOptions, $item['label']);

		$content = '';
		if(isset($item['icon']))
			$content .= CHtml::tag('i', array('class'=>$item['icon']), '');
		else
			$content .= CHtml::tag('i', array('class'=>'fa fa-folder'), '');//CHtml::tag('i', array('class'=>$this->itemMinusIcon), '');
		if($item['active'])
			$content .= CHtml::tag('span', array('class'=>'selected'),'');
		if(isset($item['items']))
			$content .= CHtml::tag('span', array('class'=>'arrow'),'');
		$content .= CHtml::tag('span', array('class'=>'title'),$item['label']);

		return isset($item['url']) ? CHtml::link($content, $item['url'], isset($item['linkOptions']) ? $item['linkOptions'] : array()) : CHtml::tag('a', array(), $content);
		// return CHtml::link($label,$item['url'],isset($item['linkOptions']) ? $item['linkOptions'] : array());

	}

	/**
	 * 规范化{@link items}属性，使得'active'状态能被每个菜单项正确识别
	 * @param array $items 被规范化的菜单items
	 * @param string $route 当前请求的路由
	 * @param boolean $active 是否有活动的子菜单
	 * @return array the 规范化后的菜单项
	 */
	protected function normalizeItems($items,$route,&$active)
	{
		foreach($items as $i=>$item)
		{
			if(isset($item['visible']) && !$item['visible'])
			{
				unset($items[$i]);
				continue;
			}
			if(!isset($item['label']))
				$item['label']='';
			if($this->encodeLabel)
				$items[$i]['label']=CHtml::encode($item['label']);
			$hasActiveChild=false;
			if(isset($item['items']))
			{
				$items[$i]['items']=$this->normalizeItems($item['items'],$route,$hasActiveChild);
				if(empty($items[$i]['items']) && $this->hideEmptyItems)
				{
					unset($items[$i]['items']);
					if(!isset($item['url']))
					{
						unset($items[$i]);
						continue;
					}
				}
			}
			if(!isset($item['active']))
			{
				if($this->activateParents && $hasActiveChild || $this->activateItems && $this->isItemActive($item,$route))
					$active=$items[$i]['active']=true;
				else
					$items[$i]['active']=false;
			}
			elseif($item['active'])
				$active=true;

			if($this->activateItems && $this->isItemActive($item,$route))
				$active=$items[$i]['active']=true;
		}
		return array_values($items);
	}
}




?>
