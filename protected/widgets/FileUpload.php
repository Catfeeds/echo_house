<?php
/**
 * 上传文件组件
 */
class FileUpload extends CWidget
{
	/**
	 * @var string 文件上传接口地址
	 */
	public $url;
	/**
	 * @var string 文件上传input框的name值，即<input type="file" name="">中的name值，根据需求自定义更改。
	 */
	public $inputName = 'filename';
	/**
	 * @var boolean 是否上传多张，默认上传单张
	 */
	public $multi = false;
	/**
	 * @var boolean 单张图片是否可以移除，仅当上传单图模式时才可用，即{@link $multi}为false。
	 */
	public $remove = true;
	/**
	 * @var string 上传后的js回调函数代码，是一个匿名函数function(data){//TODO}，参数data是上传接口返回的json数据
	 */
	public $callback = '';
	/**
	 * @var string 点击移除按钮后的js代码，仅当上传单图模式时才可用，即{@link $multi}为false。
	 */
	public $removeCallback = '';
	/**
	 * @var CActiveRecord AR模型类，该上传文件所对应数据表，会生成一个input隐藏域记录上传文件的地址。仅当上传单图模式时才可用，即{@link $multi}为false。
	 */
	public $model;
	/**
	 * @var string AR模型类中属性字段名，该上传文件所对应数据表，会生成一个input隐藏域记录上传文件的地址。仅当上传单图模式时才可用，即{@link $multi}为false。
	 */
	public $attribute;
	/**
	 * @var int 显示的图片宽度
	 */
	public $width = 0;
	/**
	 * @var int 显示的图片高度
	 */
	public $height = 0;
	/**
	 * @var int 七牛图片剪裁模式，0等比缩放，1正方形剪裁
	 */
	public $mode = 2;
	/**
	 * @var array 需要提交的额外表单参数
	 */
	public $param = array();
	/**
	 * @var integer 允许上传文件大小，单位B，默认5M
	 */
	public $fileSize = 50000000;
	/**
	 * @var boolean 是否对在该调用场景上传的图片打水印
	 */
	public $waterMark = false;
	/**
	 * 是否预览全图
	 * @var boolean
	 */
	public $preview = true;
	public $words = '选择文件';
	public $style = '';
	public $noremove = 0;
	/**
	 * 初始化小物件
	 */
	public function init()
	{
		if($this->url===null)
			$this->url = Yii::app()->createUrl('api/file/upload');

		$this->param = array_merge( array(
			// 'CSRF_TOKEN' => Yii::app()->request->csrfToken,
			'filename' => $this->inputName,
			'width' => $this->width,
			'height' => $this->height,
			'mode' => $this->mode,
			'wm' => $this->waterMark?1:0,
		),$this->param);

		if($this->callback=='' && !empty($this->model) && !empty($this->attribute))
		{
			$this->callback = "function(data){
				if(data.code!=1){
					alert('上传失败，请重新尝试');
					return;
				}";

			if($this->noremove)
				$this->callback .= "$('#singlePic".$this->id."').html('').append('<img src=\'\'/>').find('img').attr('src',data.msg.url).show();";
			else
				$this->callback .= "$('#singlePic".$this->id."').html('').append('".$this->getRemoveButtonHtml()."').append('<img src=\'\'/>').find('img').attr('src',data.msg.url).show();";

			$inputId = CHtml::getIdByName(CHtml::resolveName($this->model, $this->attribute));
			$this->callback .= "$('#singlePic".$this->id."').append('".CHtml::activeHiddenField($this->model,$this->attribute)."');
			    	$('#".$inputId."').val(data.msg.pic);
			    }";
		}

		if($this->multi==false&&$this->removeCallback=='')
			$this->removeCallback = "$('#singlePic').html('')";
	}

	/**
	 * 执行小物件
	 */
	public function run()
	{
		//为兼容IE浏览器，不使用第一个模板了
		$this->render('fileupload',['words'=>$this->words,'style'=>$this->style,'noremove'=>$this->noremove]);
	}

	public function getRemoveButtonHtml()
	{
		if($this->remove&&!$this->multi) {
			$button = <<<EOT
			<a href="javascript:;" class="btn btn-icon-only btn-circle red removebutton" style="position:absolute;bottom:0px;right:0px;" alt="移除" title="移除"><i class="fa fa-times"></i></a>
EOT;
			return $button;
		} else {
			return '';
		}

	}

	public function registerRemoveJs()
	{
		if($this->remove&&!$this->multi&&$this->id=='yw1') {
			$js = <<<EOT
			$('.removebutton').live('click', function(){
				var p = $(this).parent();
				var hidden = p.find('input[type=hidden]');
				if(hidden.length) {
					var clo_hidden = hidden.clone();
					clo_hidden.val('');
					p.html(clo_hidden);
				}
				// console.log(hidden);
				// $(hidden).val('');
			});
EOT;
			return $js;
		}
	}
}



 ?>
