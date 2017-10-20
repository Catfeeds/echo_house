<?php
class CommonRightWidget extends CWidget
{
	public $rmtjs = [];

	public $leas = [];
	public $points = [];
	public $comms = [];
	public $matchs = [];

	public function run()
	{
		foreach (['rmtjs','leas','points','comms','matchs'] as $key => $value) {
			$data[$value] = $this->$value;
		}
		$this->render('common',$data);
	}
}