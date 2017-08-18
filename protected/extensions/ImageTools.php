<?php
/**
 * 图片工具类，主要针对云存储的图片
 * @author tivon
 * @date 2015-04-29
 */
class ImageTools extends CComponent
{
	/**
	 * 缩放图像
	 * @param  string  $value  图片资源数据（如七牛的key值）
	 * @param  integer $width  图片宽度
	 * @param  integer $height 图片高度
	 * @param  integer $mode   图片剪裁模式，0为正方形剪裁，1为等比剪裁缩放
	 * @return string
	 */
	public static function fixImage($value, $width=0, $height=0, $mode=1)
	{
		if(strpos($value, 'http')!==false && strpos($value, 'hualongxiang')===false || empty($value)) return $value;
		return (strpos($value, 'http')===false && Yii::app()->file->enableCloudStorage) ? self::qiniuImage($value, $width, $height, $mode) : self::localImage($value, $width, $height);
	}

	/**
	 * 添加七牛水印
	 * @param  string $url 七牛图片地址
	 * @return string      带水印的图片地址
	 */
	public static function waterMark($url)
	{
		if(SM::waterMarkConfig()->enable()&&SM::waterMarkConfig()->waterMarkPic())
		{
			$baseUrl = str_replace(array('+', '/'), array('-', '_'), base64_encode(self::qiniuImage(SM::waterMarkConfig()->waterMarkPic())));
			if(SM::waterMarkConfig()->position())
				$gravity = SM::waterMarkConfig()->position();
			else
				$gravity = 'SouthEast';
			$waterMark = 'watermark/1/image/'.$baseUrl.'/gravity/'.$gravity;
			if(strpos($url,'?')===false)
				$url = $url.'?'.$waterMark.'/dissolve/100';
			else
				$url = $url.'|'.$waterMark.'/dissolve/100';
		}
		return $url;
	}

	/**
	 * 七牛缩放图像
	 * @param  string  $key    七牛存储的key值
	 * @param  integer $width  图片宽度
	 * @param  integer $height 图片高度
	 * @return string
	 */
	private static function qiniuImage($key, $width=0, $height=0, $mode=1)
	{
		$url = Yii::app()->file->host . ltrim($key,'\/');
		$op = array();
		if($width>0) $op = array('w',$width);
		if($height>0) $op = array_merge($op, array('h',$height));
		if(!empty($op)) {
			if(strstr($url,'?vframe')) //判断是否为视频截图
				$url.='|imageView2/'.$mode.'/'.implode('/', $op).'/interlace/1/q/100';
			else
				$url .= '?imageView2/'.$mode.'/'.implode('/', $op).'/interlace/1/q/100';
		}
		return $url;
	}

	private static function localImage($value, $width=0, $height=0)
	{
		if( is_numeric($width) && is_numeric($height) && $width>=0 && $height>=0 && ($width+$height)>0) {
			$value = str_replace( substr($value,strrpos($value, '.'),strlen($value)) , '.s.'.$width.'x'.$height.substr($value,strrpos($value, '.'),strlen($value)) , $value );
		}
		if(strpos($value, 'http')===false) $value = Yii::app()->file->host.ltrim($value,'\/');
		return $value;
	}

	/**
	 * [fixOldImage 旧数据图片匹配]
	 * @param  [type]  $value  [description]
	 * @param  integer $width  [description]
	 * @param  integer $height [description]
	 * @param  integer $mode   [description]
	 * @return [type]          [description]
	 */
	public static function fixOldImage($value, $width=0, $height=0, $mode=1)
	{
		if(strpos($value, 'http')!==false || empty($value)) return $value;
			if(strpos($value, 'http')===false && strpos($value, 'file')!==false) {
				// $value = str_replace(substr($value, strrpos($value, '.'), strlen($value)), '.s.' . $width . 'x' . $height . substr($value, strrpos($value, '.'), strlen($value)), $value);
				$value = str_replace('/files', 'http://pic.hualongxiang.com/app', $value);
			}else{
				$value = self::fixImage($value,$width,$height,$mode);
			}
		return $value;
	}
}
