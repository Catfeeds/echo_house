<?php
/**
 * 订单分配处理类
 * @author tivon
 * @version 2016-07-18
 */
class JikeUserAssign extends CComponent
{
    /**
     * 要分配的用户
     * @var UserExt
     */
    private $_user;
    /**
     * 要记录的流水
     * @var UserLogExt
     */
    private $_userLog;
    /**
     * 启用的管家帐号，或者要指定的管家帐号
     * @var StaffExt[]
     */
    private static $_staffs = [];
    /**
     * 买房顾问每天可以分配用户的上限，0为不设上限
     * @var integer
     */
    private $_assignLimit = 10;
    /**
     * 是否强制分配
     * 如果启用强制分配，则之前已经被分配的用户会再次被分配（如果是平均分单模式，则会再次
     * 被随机分配给管家；如果是常规模式，则会被分配给指定的管家）
     * @var boolean 强制分配为true，否则为false
     */
    private $_forceAssign = false;
    /**
     * 是否自动分配
     * @var boolean
     */
    private $_isAutoAssign = false;
    /**
     * 该用户是否首次分配
     * @var boolean
     */
    private $_isFirstAssign = false;

    /**
     * 构造函数
     */
    public function __construct(UserExt $user, $staff = null)
    {
        $this->init();
        $this->_user = $user;
        if($user->staff_id == 0) {
            $this->_isFirstAssign = true;
        }
        $this->initUserLog();

        if($staff!==null) {
            if($staff instanceof StaffExt){//走这里的话一般都是后台人工分配，所以不受分配上限的限制
                $this->_assignLimit = 0;
                $staff = [$staff];
            }
            if(is_array($staff)) $this->setStaffs($staff);
        } else {
            //根据站点设置判断是否自动分配
            if(SM::jikeConfig()->mode() == 1) {
                $this->_isAutoAssign = true;
                $this->_assignLimit = (int)SM::jikeConfig()->assignLimit();
            } else {
                throw new CHttpException(500, '站点未启用自动平均分配模式');
            }
            $this->getStaffs();
        }
    }

    public function init()
    {
        $this->onAfterAssign = array($this, 'sendMsg');
        $this->onAfterAssign = array($this, 'writeLog');
    }

    public function initUserLog()
    {
        Yii::import('application.components.jike.JikeUserLog');
        $userLog = new UserLogExt;
        $userLog->phone = $this->_user->phone;
        $userLog->visit_status = UserExt::$assignValue;
        $this->_userLog = new JikeUserLog($userLog);
    }

    /**
     * 设置买房顾问帐号
     * @param array $staff
     */
    public function setStaffs(array $staff)
    {
        self::$_staffs = $staff;
    }

    /**
     * 获取买房顾问账号
     * @param boolean $getFromDb 是否从数据库从新获取
     * @return StaffExt[]
     */
    public function getStaffs($getFromDb = false)
    {
        if($getFromDb || empty(self::$_staffs)) {
            //isWork指可以分配的管家
            self::$_staffs = StaffExt::model()->normal()->staffIsWork()->with('jintiankehuNum')->findAll();
        }
        return self::$_staffs;
    }

    /**
     * 获得可用的买房顾问账号
     * @return [type] [description]
     */
    public function getFreeStaffs()
    {
        $staffs = [];
        foreach(self::$_staffs as $staff) {
            if($this->_assignLimit==0 || $staff->jintiankehuNum < $this->_assignLimit) {
                $staffs[] = $staff;
            }
        }
        return $staffs;
    }

    /**
     * 获取一个可用的买房顾问账号
     * @return StaffExt|null 买房顾问账号
     */
    public function getFreeStaff()
    {
        $staffs = $this->getFreeStaffs();
        $sortStaffs = [];
        $max = 0;
        foreach($staffs as $staff) {
            //按分配数量倒序排，使得pop出的管家帐号是分配最少的，达到平均分配的目的
            if($staff->jintiankehuNum >= $max) {
                $max= $staff->jintiankehuNum;
                $sortStaffs = array_merge([$staff], $sortStaffs);
            } else {
                $sortStaffs = array_merge($sortStaffs, [$staff]);
            }
        }
        return array_pop($sortStaffs);
    }

    /**
     * 是否有可用的买房顾问账号
     * @return boolean 有返回true，无返回false
     */
    public function hasFreeStaff()
    {
        return !empty($this->getFreeStaff());
    }

    /**
     * 分配用户
     * @param boolean $forceAssign 是否强制分配
     * 如果启用强制分配，则之前已经被分配的用户会再次被分配（如果是平均分单模式，则会再次
     * 被随机分配给管家；如果是常规模式，则会被分配给指定的管家）
     * @return boolean 分配成功返回true，分配失败返回false
     */
    public function assign($forceAssign = false)
    {
        if($staff = $this->getFreeStaff()) {
            //1. 如果已经有分配管家了，并且不是强制分配，则不进行分配操作
            //2. 如果上次分配的与这次分配的是同一个人，则不进行分配操作

            if(!$forceAssign && $this->_user->staff_id > 0 || $this->_user->staff_id == $staff->id){
                return true;
            }
            //重新分配时要将买房顾问回访状态重置
            if(!$this->_isFirstAssign) {
                $this->_user->staff_status = 1;
            }
            $this->_user->assign($staff);
            if($this->_user->save()) {
                $this->afterAssign();
                $staff->jintiankehuNum++;
                return true;
            } else {
                $msg = $this->_user->hasErrors() ? current(current($this->_user->getErrors())) : '无';
                Yii::log('自动分单失败，UserExt错误信息：'.$msg,'info','application.models_ext');
            }
        }
        return false;
    }

    /**
     * 写分配流水日志
     * 要区分1. 是否自动分配； 2.是否再次分配
     * @return boolean
     */
    public function writeLog()
    {
        $content = '分配给买房顾问：'.$this->_user->staff->username.'[id：'.$this->_user->staff->id.']';
        if(!$this->_isFirstAssign) {
            $content = '重新' . $content;
        }
        $content = '('.($this->_isAutoAssign ? '系统自动'.$content : $content).')';
        return $this->_userLog->writeLog($content, true);
    }

    public function sendMsg()
    {
        $message = '您有一个新的客户，请进入购房管家查看，'.date('Y-m-d H:i');
        if(SM::jikeConfig()->enableSms()&&$this->_user->staff && $this->_user->staff->phone){
            Yii::import('application.components.ClientMobile');
            ClientMobile::sendSms($this->_user->staff->phone, $message);
        }
    }

    //==================setter/getter方法=========================
    public function setUserLog(JikeUserLog $userLog)
    {
        $this->_userLog = $userLog;
    }

    //==================事件===================
    public function onAfterAssign($event)
    {
        $this->raiseEvent('onAfterAssign', $event);
    }

    public function afterAssign()
    {
        if($this->hasEventHandler('onAfterAssign')) {
            $this->onAfterAssign(new CEvent($this));
        }
    }
}
