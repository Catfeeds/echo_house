<?php
/**
 * 流水日志记录类
 * @author tivon
 * @version 2016-07-20
 */
class JikeUserLog extends CComponent
{
    private $_userLog;

    public function __construct(UserLogExt $userLog)
    {
        $this->_userLog = $userLog;
    }

    /**
     * 写流水
     * @param  string $content 流水记录内容
     * @param  boolean $append  是否在原有内容上追加内容
     * @return boolean
     */
    public function writeLog($content = '', $append = false)
    {
        if($content != '') {
            $this->_userLog->content = $append ? $this->_userLog->content . $content : $content;
        }

        if($this->_userLog->scenario == 'adminLog') {
            //将最新的流水状态更新到用户的回访状态上，除了副主编复盘的状态
            //复盘的状态的话，将mark_new标识更新
            if(isset(UserExt::$syncUserStatus[$this->_userLog->visit_status])
            && UserExt::$syncUserStatus[$this->_userLog->visit_status]){
                $this->_userLog->user->visit_status = $this->_userLog->visit_status;
            }else{
                $this->_userLog->user->mark_new = time();
            }
        } elseif($this->_userLog->scenario == 'staffLog') {
            $this->_userLog->user->mark_new = 0;
            $this->_userLog->user->staff_status = $this->_userLog->staff_status;
        }
        if($this->_userLog->save()) {
            $this->_userLog->user->save();
            return true;
        }
        return false;
    }

    //===================setter/getter=================
    public function setUserLog(UserLogExt $userLog)
    {
        $this->_userLog = $userLog;
    }
}
