<?php

/**
 * User: fanqi
 * Date: 2016/9/29
 * Time: 10:33
 */

Yii::import('api.controllers.ResoldWapApiController');

class VipApiController extends ResoldWapApiController
{
    /**
    * [$frame 框架]
    * @var array
    */
    public $frame = [];

    /**
    * [$frame 框架]
    * @var array
    */
    public $staff;
    
    /**
    * [$requestType 请求类型]
    * @var string
    */
    public $requestType = '';
    
    /**
    * [init initial]
    * @return [type] [description]
    */
    public function init()
    {
        parent::init();
        Yii::import('application.modules.vip.models.*');
        Yii::import('application.modules.vip.components.*');
        Yii::import('application.models_ext.siteSetting.*');
        $this->frame = $this->rapFrame();
        $this->requestType = Yii::app()->request->getRequestType();
        if(isset(Yii::app()->uc->user->uid) && Yii::app()->uc->user->uid)
            $this->staff = ResoldStaffExt::model()->findStaffByUid(Yii::app()->uc->user->uid);
            // $this->staff = ResoldStaffExt::model()->findStaffByUid(Yii::app()->user->uid);
    }

    /**
     * @return array
     * 配置请求
     */
    public function actions()
    {
        $alias = 'api.controllers.vipApi.';
        return [
            //中介后台
            'index' => $alias . 'IndexAction',//中介用户首页
            'login'=>$alias . 'LoginAction',//登录接口
            'saleEsf' => $alias . 'SaleEsfAction',//管理二手房-我要卖房
            'esfList' => $alias . 'EsfListAction',//管理二手房-列表
            'esfInfo' => $alias . 'EsfInfoAction',//管理二手房-详情
            'setAppoint' => $alias . 'SetAppointAction',//设定预约时间
            'delAppoint' => $alias . 'DelAppointAction',//取消预约时间
            'appointList' => $alias . 'AppointListAction',//管理二手房或管理租房-预约记录
            'saleZf' => $alias . 'SaleZfAction',//管理租房-我要出租
            'zfList' => $alias . 'ZfListAction',//管理租房-列表
            'zfInfo' => $alias . 'ZfInfoAction',//管理租房-详情
            'saleDownZfList' => $alias . 'SaleDownZfListAction',//管理租房-下架租房列表
            'shopFile' => $alias . 'ShopFileAction',//店铺档案
            'esfHurry' => $alias . 'EsfHurryAction',//二手房加急房源
            'zfHurry' => $alias . 'ZfHurryAction',//租房加急房源
            'esfRefresh' => $alias . 'EsfRefreshAction',//二手房刷新房源
            'zfRefresh' => $alias . 'ZfRefreshAction',//租房刷新房源
            'getShop' => $alias . 'GetShopAction',//获取店铺信息
            'setShop' => $alias . 'SetShopAction',//编辑店铺信息
            'setHurry' => $alias . 'SetHurryAction',//设置加急时间
            'setRefresh' => $alias . 'SetRefreshAction',//设置刷新时间
            'changeSaleStatus' => $alias . 'ChangeSaleStatusAction',//上下架状态
            'logout' => $alias . 'LogoutAction',//退出登录
            'delResold' => $alias . 'DelResoldAction',//删除
        ];
    }

    /**
     * @return array
     * 配置拦截器
     */
    public function filters()
    {
        return [
            'uidCheck - login,logout',
            'sensitiveWordControl + saleEsf,saleZf',
            'expire + setAppoint,setHurry,setRefresh',
        ];
    }

    public function filterExpire($chain)
    {
        if($this->staff->getIsExpire()) {
            $this->frame['status'] = 'error';
            $this->frame['msg'] = '您的套餐已到期！';
            echo CJSON::encode($this->frame);exit;
        } else {
            $chain->run();
        }
    }

    public function filterUidCheck($chain)
    {
        if(!isset(Yii::app()->uc->user->uid))
        {
            $this->frame['status'] = 'error';
            $this->frame['msg'] = '未登录';
            echo CJSON::encode($this->frame);exit;
        }
        else
        {
            $chain->run();
        }
    }

    /**
     * @param $chain
     * 敏感词过滤器
     * 将post数据转换成json字符串将敏感词切成数组循环遍历
     */
    public function filterSensitiveWordControl($chain)
    {
        if(!SM::resoldSensitiveConfig()->resoldUseSensitiveWordFilter()) {
            $chain->run();
        }else {
            $filterFile = SM::resoldSensitiveConfig()->resoldSensitive();
            $flag = true;
            if ($filterFile) {
                $words = preg_split("/,|，/", $filterFile);
                if (Yii::app()->request->isPostRequest) {
                    $postData = json_encode($_POST['data']['content'].$_POST['data']['title'], JSON_UNESCAPED_UNICODE);
                    foreach ($words as $word) {
                        if (strpos($postData, $word)) {
                            $this->returnError("数据中存在敏感词汇({$word})");
                            $flag = false;
                            break;
                        }
                    }
                }
            }
            if($flag){
                $chain->run();
            }else{
                $this->afterAction($chain);
            }
        }
            
    }

    /**
     * 根据ID和UID获取以下数据
     * @param $model
     * resold_esf
     * resold_zf
     * resold_qg
     * resold_qz
     */
    public function findResoldById($model,$id){
       $resold = $model::model()->undeleted()->findByPk($id,'uid=:uid',array(':uid'=>$this->staff->uid));
       return $resold;
    }
}