<?php
/**
 * 二手房详情
 * @author steven.allen
 * @date 2016.09.30
 */
class InfoAction extends CAction
{
	public function run($id)
	{
		$esf = ResoldEsfExt::model()->saling()->findByPk($id);
		if(!$esf)
		{
			$this->controller->frame['status'] = 'error';
			$this->controller->frame['msg'] = '房源不存在';
		}
		else
		{
			// 点击量+1
			$esf->hits = $esf->hits + 1;
			$esf->save();

			$this->controller->addViewRecord('sell',['id'=>$esf->id,'category'=>$esf->category,'title'=>$esf->title]);

			$data = [];
			
			// 照搬的字段
			$data['title'] = $esf->title;
			$data['id'] = $esf->id;
			$data['price'] = $esf->price;
			$data['ave_price'] = $esf->ave_price;
			$data['size'] = $esf->size;
			$data['age'] = $esf->age?$esf->age:'暂无';
			$data['bedroom'] = $esf->bedroom;
			$data['livingroom'] = $esf->livingroom;
			$data['bathroom'] = $esf->bathroom;
			$data['floor'] = $esf->floor;
			$data['total_floor'] = $esf->total_floor;
			$data['content'] = $esf->content;
			$data['username'] = $esf->username;
			$data['phone'] = $esf->phone;
			$data['hid'] = $esf->hid;
			$data['image'] = ImageTools::fixImage($esf->image,640,400);
			$data['created'] = date('Y-m-d',$esf->created);
			$data['updated'] = date('Y-m-d',$esf->updated);
			$data['wuye_fee'] = $esf->wuye_fee;
			$data['pc_url'] = $this->controller->createUrl('/resoldhome/esf/info/id/'.$id);
			$decoration = TagExt::model()->findByPk($esf->decoration);
			$data['decoration'] = $decoration?$decoration->name:'暂无';

			// 区域街道
			$data['area'] = $esf->areaInfo?$esf->areaInfo->name:'';
			$data['street'] = $esf->streetInfo?$esf->streetInfo->name:'';
			$data['sstreet'] = $esf->street;
			$data['sale_time'] = Tools::friendlyDate($esf->sale_time);
			$data['source'] = Yii::app()->params['source'][$esf->source];
			$data['source'] = $data['source']=='后台'?'个人':$data['source'];
			$data['category'] = Yii::app()->params['category'][$esf->category];
			$towards = TagExt::model()->findByPk($esf->towards);
			$data['towards'] = $towards?$towards->name:'暂无';

			// 楼盘相关的字段
			$data['plot_name'] = $esf->plot->title;
			$data['address'] = $esf->address;
			$data['zbpt'] = Tools::export($esf->plot->data_conf['transit']).Tools::export($esf->plot->data_conf['peripheral'],'暂无信息');
			$data['map_lng'] = $esf->plot->map_lng;
			$data['map_lat'] = $esf->plot->map_lat;
			
			$data['sarea'] = $esf->area;
			$data['sstreet'] = $esf->street;

			if($esf->source==2)
			{
				$staff = ResoldStaffExt::model()->findStaffByUid($esf->uid);
				$data['qq'] = $staff&&$staff->qq?$staff->qq:'';
			}
			// 标签
			$data_conf = json_decode($esf->data_conf,true);
			$tags = $data_conf['tags'];
			$tagArr = [];
			if($esf->getEsfTag())
				foreach ($esf->getEsfTag() as $key => $t) {
					foreach ($t as $k => $value) {
						if(is_array($value))
						{
							$value && $tagArr[$key][] = $value['name'];
						}
						else
							$value && $tagArr[$key] = $t['name'];
					}

				}
			$data['data_ext'] = $tagArr;
			$data['pic_url'] = $this->controller->createUrl('/resoldhome/esf/info',['id'=>$id]);
			$this->controller->frame['data'] = $data;
		}
	}
}
