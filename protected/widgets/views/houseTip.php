<?php 
if(Yii::app()->user->hasFlash($this->type))
{
	$js = '
		
		toastr["'.$this->type.'"]("'.Yii::app()->user->getFlash($this->type).'", "");
	';
	Yii::app()->clientScript->registerScript('toastrjs', $js, CClientScript::POS_END);
}

?>