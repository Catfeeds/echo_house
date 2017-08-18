<?php
/**
 * 带不同颜色的提示条
 * 用于提示用户操作结果，在操作判断完成后使用Yii::app()->user->setFlash('success');设置成功提示（相应的可以使用success表达成功信息、使用error表达错误信息）
 * @author tivon
 * @date 2015-04-22
 */
class HouseTip extends CWidget
{
	/**
	 * @var string 提示类型，有四种，分别是：success、info、warning、error
	 */
	public $type = 'info';
	/**
	 * @var array 提示条样式
	 */
	public $styleClass = array(
		'success' => 'alert-success',
		'info' => 'alert-info',
		'warning' => 'alert-warning',
		'danger' => 'alert-error',
		'error' => 'alert-error',
	);

	/**
	 * 初始化widget
	 */
	public function init()
	{
		$flashes = array_keys(Yii::app()->user->getFlashes(false));
		if(!empty($flashes))
			$this->type = $flashes[0];
	}

	/**
	 * 执行widget
	 */
	public function run()
	{
		$this->render('houseTip');
	}
}

 ?>
