<?php
$this->pageTitle = '技术开放接口';
$docUrl = 'http://doc.hangjiayun.com/house/rest/';
 ?>
<div class="alert alert-info">
	<strong>提示：</strong>为了方便能在房产平台外调用数据展示或者向房产平台写入数据，这里提供了对外的开放接口以供使用，根据需求使用对应的接口，按照文档说明要求来使用。可<a href="<?=$docUrl; ?>"><b>点击这里</b></a>新开窗口查看文档。调用接口时所使用的token值为<span class="label label-danger"><?=md5(SM::UrmConfig()->siteID); ?></span>，切记！
</div>
<iframe src="<?=$docUrl; ?>" width="100%" height="700px" frameborder="0"></iframe>
