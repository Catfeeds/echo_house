<?php
//webuploader CSS
Yii::app()->clientScript->registerCssFile("/static/global/plugins/webuploader/webuploader.css");
Yii::app()->clientScript->registerCssFile("/static/global/plugins/fancybox/source/jquery.fancybox.css");
Yii::app()->clientScript->registerCssFile("/static/admin/pages/css/portfolio.css");
//webuploader JS
Yii::app()->clientScript->registerScriptFile('/static/global/plugins/webuploader/webuploader.js',CClientScript::POS_END);


 ?>

<div id="uploader<?php echo $this->id; ?>" class="wu-example">
    <div class="btns">
        <div id="picker<?php echo $this->id; ?>" style="<?=$style?>" onmousemove="$(window).resize();" class="webuploader-container"><?=$words?></div>
    </div>
</div>
<div id="singlePic<?php echo $this->id; ?>" style="float:left; width:auto; height:auto" class="btn-group">
    <?php
    if($this->model instanceof CModel&&$this->attribute) {
        //强制转字符串，可能resolveValue出来的是一个对象，该对象需要实现__toString
        $value = (string)CHtml::resolveValue($this->model, $this->attribute);
    }
    if(isset($value)&&!empty($value)): ?>
    <?php if($noremove==0):?>
        <?=$this->getRemoveButtonHtml(); ?>
        <?php endif;?>
        <?php if($this->preview): ?>
            <a class="mix-preview fancybox-button" href="<?php echo ImageTools::fixImage($value); ?>" title="原图" data-rel="fancybox-button">
    			<img src="<?php echo ImageTools::fixImage($value, $this->width,$this->height,$this->mode) ?>" >
    		</a>
        <?php else: ?>
           
        <?php endif; ?>
		<?php echo CHtml::activeHiddenField($this->model,$this->attribute) ?>
	<?php endif; ?>
</div>


<?php
$js = '
	// 初始化Web Uploader
	var uploader'.$this->id.' = WebUploader.create({

	    // 选完文件后，是否自动上传。
	    auto: true,
        //paste在实例多个按钮时只需要设置一次，否则DOM结构会出现错位
	    '.($this->id=='yw1'?'paste: document.body,':'').'
        compress : null,
	    // swf文件路径
	    swf: "/static/global/plugins/webuploader/Uploader.swf",

	    // 文件接收服务端。
	    server: "'.$this->url.'",
	    fileSingleSizeLimit: '.$this->fileSize.',

	    // 选择文件的按钮。可选。
	    // 内部根据当前运行是创建，可能是input元素，也可能是flash.
	    pick: {id:"#picker'.$this->id.'",multiple:'.($this->multi?'true':'false').'},
	    fileVal:"'.$this->inputName.'",
	    formData:'.CJSON::encode($this->param).',
	    // 只允许选择图片文件。
	    accept: {
	        title: "Images",
	        extensions: "gif,jpg,jpeg,bmp,png,swf",
	        mimeTypes: "image/jpg,image/jpeg,image/png,image/gif,image/bmp"
	    }
	});
';
if(!empty($this->callback))
	$js .= '
		//回调函数
		uploader'.$this->id.'.on( "uploadSuccess", function( object, data ) {
			callback'.$this->id.'(data);
		});
		var callback'.$this->id.' = '.$this->callback.'
	';

$js .= $this->registerRemoveJs();
Yii::app()->clientScript->registerScript($this->id,$js,CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile('/static/global/scripts/qiniu.js',CClientScript::POS_END);//七牛js
Yii::app()->clientScript->registerScriptFile('/static/global/plugins/jquery-mixitup/jquery.mixitup.min.js',CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile('/static/global/plugins/fancybox/source/jquery.fancybox.pack.js',CClientScript::POS_END);

?>
