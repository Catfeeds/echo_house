<?php
/**
 * UcReceiver基类
 * @author tivon
 * @version 2016-01-18
 */
class UcReceiverAction extends CAction
{
    public function getData()
    {
        return $this->getController()->data;
    }

    /**
     * api_client中函数data_format改造
     */
    protected function response($res, $mode=null)
    {
        $data = new ApiResponse($res, $mode);
        $res = array(
			'charset' => S_CHARSET,
		);
		if (strtolower(get_class($data)) == 'apiresponse') {
			$res['result'] = $data->getResult();
		} else {
			$res['errCode'] = $data->getErrCode();
			$res['errMessage'] = $data->getErrMessage();
		}
		return serialize($res);
    }
}
