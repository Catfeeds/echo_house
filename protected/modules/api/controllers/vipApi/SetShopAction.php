<?php
/**
 * 更新店铺信息
 * @author steven allen <[<email address>]>
 * @date 2016.10.21
 */
class SetShopAction extends CAction
{
	public function run()
	{
		$staff = $this->controller->staff;
		if(!$staff->is_manager)
			$this->controller->returnError('职员不能编辑店铺信息');
		else
		{
			$datas = Yii::app()->request->getPost('data',[]);
			$images = Yii::app()->request->getPost('images',[]);
			if($datas)
			{
				$datas['phone1'] = (isset($datas['phone1']) && $datas['phone1']) ? (trim($datas['phone1']).' '): '' ;
				$datas['phone2'] = (isset($datas['phone2']) && $datas['phone2']) ? (trim($datas['phone2']).' '): '' ;
				$datas['phone3'] = (isset($datas['phone3']) && $datas['phone3']) ? (trim($datas['phone3'])): '' ;
				
				$datas['phone'] = $datas['phone1'].$datas['phone2'].$datas['phone3'];
				unset($datas['phone1'],$datas['phone2'],$datas['phone3']);
				$shop = (isset($datas['id']) && $datas['id']) ? ResoldShopExt::model()->findByPk($datas['id']) : new ResoldShopExt;
				foreach ($datas as $key => $v) {
					if($key != 'album')
						$shop->$key = $v;
				}
				if($shop->save())
				{
					// 有待优化
					ResoldShopImgExt::model()->deleteAllByAttributes(['sid'=>$shop->id]);
					if($images)
						foreach ($images as $key => $v1) {
							$album = new ResoldShopImgExt;
							$album->sid = $shop->id;
							$album->url = $v1['pic'];
							$album->save();
						}
					$this->controller->frame['msg'] = '保存成功';
				}
				else
					$this->controller->returnError($shop->errors);
			}
		}
	}
}