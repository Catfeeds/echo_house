<?php
/**
 * pc导航
 * @author tivon
 * @version 2017.6.1
 */
class HomeNavWidget extends CWidget
{
	public $type = 'home';

	public $limit = '';

	public function run()
	{
		$path = Yii::app()->request->getPathInfo();
		// var_dump($path);exit;
		$path = ltrim($path,'/');
		if(!$path)
			$path = 'index';
		if(strstr($path,'tag')) {
			$path = 'news';
		}
		if(in_array($path, ['regis','login','findpwd'])) {
			$path = 'user';
		}
		$menus = $this->owner->getHomeMenu();
		
		$html = "";
		$licss = $this->type == 'home'?'nav_menu-item':'swiper-slide';
		$aclass = $this->type == 'home'?'':'top_nav_item';
		if($this->limit)
			foreach ($menus as $key => $value) {
				if(strstr($path, $value['active']))  {
					if($key>=$this->limit) {
						$menus = array_slice($menus, $key-$this->limit+1,$this->limit);
					}
				}
			}
		foreach ($menus as $key => $value) {
			// var_dump($path);exit;
			!isset($value['active']) && $value['active'] = '';
			$url = $this->owner->createUrl('/'.$value['url']);
			if($value['active']) {
				if(strstr($path, $value['active']))  {
					$active = $this->type == 'home'?'headMenuNow':'top_nav_item_now';
				}
				else
					$active = '';
			}else
				$active = '';
				
			$name = $value['name'];
			// if($this->type == 'home')
			// 	$html .= '<li class="nav_menu-item">'.$name.'</a></li>';
			// else
				$html .= '<li class="'.$licss.'"><a '.($key+1==count($menus)||strstr($path,'user')?'rel="nofollow"':'').' class="'.($active?$active:$aclass).'" href="'.$url.'"><p>'.$name.'</p></a></li>';
			// if($this->type == 'home')
			// 	$html .= '<li id="menu-item-'.$key.'" class="menu-item menu-item-type-custom menu-item-object-custom '.$active.' menu-item-home menu-item-'.$key.'"><a href="'.$url.'">'.$name.'</a></li>';
			// else
			// 	$html .= '<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-'.$key.'"><a href="'.$url.'">'.$name.'</a></li>';
		}
		echo $html;
	}
}