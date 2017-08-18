<?php
/**
 * 文件上传接口
 * @author weibaqiu
 * @date 2015-04-24
 */
class FileController extends ApiController
{

	/**
	 * 文件上传接口
	 * 参数：
	 * filename: 必须，<input type="file" name="">的name值
	 * 上传成功返回：
	 * {
	 * 	code:true,
	 * 	{
	 * 		pic:"path/filename.jpg",
	 * 		url:"http://pichost/path/filename.jpg"
	 * 	}
	 * }
	 * 上传失败返回:
	 * {
	 * 	code: false,
	 * 	"上传失败"
	 * }
	 * @return json
	 */
	public function actionUpload()
	{
		if(Yii::app()->request->isPostRequest)
		{
			$name = Yii::app()->request->getPost('filename','file');
			$width = Yii::app()->request->getPost('width', 0);
			$height = Yii::app()->request->getPost('height', 0);
			$mode = Yii::app()->request->getPost('mode', 2);
			$wm = Yii::app()->request->getPost('wm', 0);
			$file = Yii::app()->file;
			// var_dump($file,$name);exit;
			$url = $file->upload($name);
			if($wm){
				$url = $file->waterMark($url);
			}
			if(!empty($url))
				$this->response(true, array('pic'=>$url,'url'=>ImageTools::fixImage($url, $width, $height, $mode)));
			else
				$this->response(false, '上传失败');
		}
	}
}




?>
