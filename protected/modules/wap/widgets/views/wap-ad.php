<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->params['urmHost'].'static/wap/2.css');
Yii::app()->clientScript->registerScriptFile(Yii::app()->params['urmHost'].'static/wap/js/angular.min.js', CClientScript::POS_BEGIN);
Yii::app()->clientScript->registerScriptFile(Yii::app()->params['urmHost'].'static/wap/js/underscore-min.js', CClientScript::POS_BEGIN);
Yii::app()->clientScript->registerScriptFile(Yii::app()->params['urmHost'].'static/wap/2.js', CClientScript::POS_END);
?>
<?php $adIds = current($ads) ?>
<?php if($sizeName === 'wapbottom' && is_array($adIds)): ?>
	<div class="wrapper">
	    <div class="hj_ad" my-directive did="<?php echo current($adIds) ?>"></div>
	</div>
<?php endif; ?>
<?php if($sizeName === 'waptonglan'): ?>
	<div class="blank20"></div>
	<?php foreach($adIds as $val): ?>
		<div class="wrapper">
		    <div class="hj_ad" my-directive did="<?php echo $val ?>"></div>
		</div>
		<div class="blank10"></div>
	<?php endforeach; ?>
<?php endif; ?>
