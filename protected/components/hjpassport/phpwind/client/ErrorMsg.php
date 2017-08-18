<?php
define('API_CLOSED', 1);
define('API_SIGN_ERROR', 2);
define('API_MODE_NOT_EXISTS', 3);
define('API_METHOD_NOT_EXISTS', 4);
class ErrorMsg {

	var $errCode = 0;
	var $errMessage = '';

	function __construct($errCode, $errMessage) {
		$this->errCode = $errCode;
		$this->errMessage = $errMessage;
	}

	function getErrCode() {
		return $this->errCode;
	}

	function getErrMessage() {
		return $this->errMessage;
	}

	function getResult() {
		return null;
	}
}
