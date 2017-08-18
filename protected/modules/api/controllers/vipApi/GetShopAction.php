<?php
/**
 * 店铺信息
 * @author steven allen <[<email address>]>
 * @date 2016.10.21
 */
class GetShopAction extends CAction
{
	public function run()
	{
		if(!isset($this->controller->staff->shop))
		{
			$this->controller->returnError('店铺不存在');
		}
		else
		{
			$data = $album = [];
			$shop = $this->controller->staff->shop;
			$area = AreaExt::model()->findByPk($shop->area);
			$street = AreaExt::model()->findByPk($shop->street);
			$phoneArr = array_filter(explode(' ', $shop->phone));
			$directArr = ['name','address','description','image','id','qq'];
			$data = [
				'area'=>$area?$area->name:'',
				'street'=>$street?$street->name:'',
			];
			foreach ($directArr as $key => $value) {
				$data[$value] = $shop->$value;
			}
			if($phoneArr)
				for ($i=1; $i <= count($phoneArr) ; $i++) { 
					$data['phone'.$i] = $phoneArr[$i-1];
				}
			// $data['image'] && $data['image'] = ImageTools::fixImage($data['image']);
			if($shop->images)
				foreach ($shop->images as $key => $value) {
					$album[] = ['url'=>ImageTools::fixImage($value['url']),'pic'=>$value['url']];
					// $data['images'][] = ['url'=>$value->url];
				}
			$this->controller->frame['data'] = ['data'=>$data,'images'=>$album];
		}
		
	}
}