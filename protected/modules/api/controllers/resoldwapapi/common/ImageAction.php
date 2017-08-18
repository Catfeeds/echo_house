<?php
/**
 * 相册接口
 * @author steven allen <[<email address>]>
 * @date 2016.10.26
 */
class ImageAction extends CAction
{
	public function run()
	{
		$imgs = [];
		Yii::import('application.models_ext.PlotImgExt');
		$fid = Yii::app()->request->getQuery('fid',0);
		$type = Yii::app()->request->getQuery('type',0);
		$is_cut = Yii::app()->request->getQuery('is_cut',0);

		if(!$fid || !$type)
			$this->controller->returnError('参数错误');
		else
		{	
			$data = $data['images'] = [];
			$data['type'] = $type;
			if($type==3)
			{
				$imgs = PlotImgExt::model()->findAll(['condition'=>'hid=:fid','params'=>[':fid'=>$fid],'order'=>'sort desc,is_cover desc,created desc','limit'=>30]);
			}
			else
			{
				$imgs = ResoldImageExt::model()->undeleted()->findAll(['condition'=>'fid=:fid and type=:type','params'=>[':fid'=>$fid,':type'=>$type],'order'=>'sort desc,created desc','limit'=>30]);
			}
			if($imgs)
			{
				foreach ($imgs as $key => $value) {
					$tmp = ['id'=>$value->id,'url'=>$is_cut?ImageTools::fixImage($value->url,640,400):(ImageTools::fixImage($value->url,500,500,3)),'name'=>isset($value->name)?$value->name:$value->title,'fid'=>isset($value->fid)?$value->fid:$value->hid,'pic'=>$value->url];
					$data['images'][] = $tmp;
				}
			}
			else
			{
				$data['images'][] = ['id'=>0,'url'=>$is_cut?ImageTools::fixImage(SM::resoldImageConfig()->resoldNoPic(),640,400):(ImageTools::fixImage(SM::resoldImageConfig()->resoldNoPic(),500,500,3)),'name'=>'','fid'=>$fid,'pic'=>SM::resoldImageConfig()->resoldNoPic()];
			}
			if($data['images'] && !$is_cut)
				foreach ($data['images'] as $key => $value) {
					$imageInfo = Yii::app()->file->getInfo($value['pic']);
					// var_dump($value['url'],$imageInfo);exit;
					$data['images'][$key]['w'] = $imageInfo['width'];
					$data['images'][$key]['h'] = $imageInfo['height'];
				}
			$data['count'] = isset($data['images'])?count($data['images']):0;

			$this->controller->frame['data'] = $data;
		}
	}
}