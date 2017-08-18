<?php

/**
 * 二手房wap接口
 * @author steven.allen
 * @date 2016.09.18
 */
class ResoldWapApiController extends ApiController
{
    /**
    * [$frame 框架]
    * @var array
    */
    public $frame = [];

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
       $this->frame = $this->rapFrame();
       $this->requestType = Yii::app()->request->getRequestType();
    }


    public function actions()
    {
        $alias = 'api.controllers.resoldwapapi.';
        return array(
            //公共接口
            'commontags' => $alias . 'common.TagAction',//标签接口
            'commonimage' => $alias . 'common.ImageAction',//相册
            'commonplace' => $alias . 'common.PlaceAction',//位置接口
            'commonrecom' => $alias . 'common.RecomAction',//推荐接口
            'commonuser' => $alias . 'common.UserAction',//推荐接口
            'commonsys' => $alias . 'common.SysLoginAction',//推荐接口
            'commonparams' => $alias . 'common.ParamsAction',//参数接口(数据都在params.php)
            'usertest' => $alias.'common.UserTestAction',
            'commonsingleplace' => $alias.'common.SinglePlaceAction',// 获取单个位置接口
            'commonsiteconfig' => $alias.'common.SiteConfigAction',// 获取单个位置接口
            'sms' => $alias.'common.SmsAction', //发送短信接口
            //首页
            'indexindex' => $alias . 'index.IndexAction',//首页
            //二手房模块
            'esfinfo' => $alias . 'esf.InfoAction',//二手房首页wap接口
            'esflist' => $alias . 'esf.ListAction',//二手房列表接口
            'esffiltermenu' => $alias . 'esf.FilterMenuAction',//二手房筛选菜单
            'esfsearchajax' => $alias . 'esf.SearchAjaxAction',//二手房搜索ajax请求列表
            'esfplotprice' => $alias . 'esf.PlotPriceAction',//二手房价格走势
            'esfreport' => $alias . 'esf.ReportAction',//举报列表
            'esfcountesf' => $alias . 'esf.CountAction',//二手房总数接口
            //地图找房、地图租房
            'mapfindarea' => $alias . 'mapfind.AreaAction',//区域找房
            'mapfindstreet' => $alias . 'mapfind.StreetAction',//街区找房
            'mapfindplot'=> $alias . 'mapfind.PlotAction',
            'mapfindlocation'=> $alias . 'mapfind.NowLocationAction',
            'plotmap'=> $alias . 'mapfind.MapFindEsfAction',
            //邻校房
            'schoollist' => $alias . 'school.ListAction',//邻校列表
            'schoolsearch' => $alias . 'school.SearchAction',//学区搜索
            'schoolinfo' => $alias . 'school.InfoAction',//学校详情

            //小区房价
            'plotlist' => $alias . 'plot.ListAction',//小区列表
            'plotsearch' => $alias . 'plot.SearchAction',//小区搜索
            'plotfiltermenu' => $alias . 'plot.FilterMenuAction',//小区筛选(二手房或租房判断放入其中)
            'plotrecom' => $alias . 'plot.RecomAction',//小区搜索推荐
            'plotindex' => $alias . 'plot.IndexAction',//小区首页
            'plotinfo' => $alias . 'plot.InfoAction',//小区详情
            'plotsearchajax' => $alias . 'plot.SearchAjaxAction',//小区搜索ajax请求列表
            'plotimage' => $alias . 'plot.ImageAction',//小区相册
            'plotchart' => $alias . 'plot.ChartAction',//小区价格走势图
            //商铺
            'splist' => $alias . 'sp.ListAction',//商铺列表
            'spsearch' => $alias . 'school.SearchAction',//学区搜索
            'spfiltermenu' => $alias . 'esf.sp.FilterMenuAction',//商铺筛选
            'spinfo' => $alias . 'sp.InfoAction',//商铺详情
            //写字楼
            'xzllist' => $alias . 'xzl.ListAction',//写字楼列表
            'xzlsearch' => $alias . 'xzl.SearchAction',//学区搜索
            'xzlinfo' => $alias . 'xzl.InfoAction',//写字楼详情
            //租房模块
            'zflist' => $alias . 'zf.ListAction',//租房列表
            'zfinfo' => $alias . 'zf.InfoAction',//租房详情
            'zfsearch' => $alias . 'zf.SearchAction',//租房搜索
            'zfsearchajax' => $alias . 'zf.SearchAjaxAction',//租房搜索ajax请求列表
            'zffilter'=>$alias.'zf.FilterAction', //住宅租房筛选菜单
            'zfcount' => $alias . 'zf.CountAction',//租房总数接口
            //求购模块
            'qglist' => $alias . 'qg.ListAction',//求购列表
            'qginfo' => $alias . 'qg.InfoAction',//求购详情
            'qgsearch' => $alias . 'qg.SearchAction',//求购详情

            //求租模块
            'qzlist' => $alias . 'qz.ListAction',//求租列表
            'qzinfo' => $alias . 'qz.InfoAction',//求租详情
            'qzsearch' => $alias . 'qz.SearchAction',//求租搜索
            ///经纪人
            'stafflist' => $alias . 'staff.ListAction',//经纪人列表
            'staffIndex' => $alias . 'staff.IndexAction',//经纪人店铺
            'staffesflist' => $alias . 'staff.EsfListAction',//经纪人的二手房房源列表
            'staffzflist' => $alias . 'staff.ZfListAction',//经纪人的租房房源列表
            ///中介门店
            'agencylist' => $alias . 'agency.ListAction',//中介列表
            'agencyindex' => $alias . 'agency.IndexAction',//中介首页
            'agencyesflist' => $alias . 'agency.EsfListAction',//中介二手房房源列表
            'agencyzflist' => $alias . 'agency.ZfListAction',//中介租房房源列表
            'agencystafflist' => $alias . 'agency.StaffListAction',//中介精英列表
            'agencymsg' => $alias . 'agency.MsgAction',//商家介绍
            // 百科
            'baiketaglist' => $alias . 'baike.BaikeTagListAction',//百科标签
            'baikelist' => $alias . 'baike.BaikeListAction',//百科列表
            'changebaike' => $alias . 'baike.ChangeBaikeAction',//百科列表
            'baikeinfo' => $alias . 'baike.BaikeInfoAction',//百科列表

            'wechat'=> $alias . 'common.WechatAction',
        );

    }

    public function filters()
    {
        return [
            //请求方式必须为get的拦截器
            'isGet + esflist,',
            //请求方式必须为post的拦截器
            'isPost + ,',
        ];
    }

    /**
     * @param $chain
     * get的拦截器
     */
    public function filterIsGet($chain)
    {
        if ($this->requestType != 'GET') {
            $this->frame['msg'] = '请求方式错误';
            $this->frame['status'] = 'error';
            echo CJSON::encode($this->frame);
        } else
            $chain->run();
    }

    /**
     * @param $chain
     * post拦截器
     */
    public function filterIsPost($chain)
    {
        if ($this->requestType != 'POST') {
            $this->frame['msg'] = '请求方式错误';
            $this->frame['status'] = 'error';
            echo CJSON::encode($this->frame);
        } else
            $chain->run();
    }

    public function afterAction($action)
    {
        header("Content-Type: application/json");
        $this->frame['status']=='success' && !$this->frame['msg'] && $this->frame['msg'] = '操作成功';
        echo CJSON::encode($this->frame);
        Yii::app()->end();
    }

    public function returnError($msg){
        $this->frame['status'] = 'error';
        $this->frame['msg'] = $msg;
        return false;
     }

     /**
      * [getApiCate 接口对应标签数组]
      * @param  string $cate [description]
      * @return [type]       [description]
      */
     public function getApiCate($cate='')
     {
         $arr = [
            'esfzfsptype'=>'tag_shoptype',
            'esfzfzztype'=>'tag_housetype',
            'esfsplevel'=>'tag_shoplevel',
            'zfxzllevel'=>'tag_writelevel',
            'esfzfxzltype'=>'tag_writetype',
            'resoldhuxing'=>'tag_askrenthuxing',
            'resoldzx'=>'tag_zx',
            'qgzzqwlc'=>'tag_mindfloor',
            'qgzzqwfl'=>'tag_mindage',
            'esfspkjyxm'=>'tag_esfspkjyxm',
            'zfspkjyxm'=>'tag_zfspkjyxm',
         ];
         if($cate && in_array($cate, array_keys($arr)))
            return $arr[$cate];
        else
            return false;
     }


    public function behaviors()
    {
        return array(
            'ViewRecordBehavior' => 'api.components.ViewRecordBehavior'
        );
    }

    /**
     * [getUserCanPubNum 根据uid找到可以发布房源数]
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
    public function getUserCanPubNum($uid)
    {
        $criteria = new CDbCriteria(array(
            'condition'=>'t.uid=:uid',
            'params'=>['uid'=>$uid]
        ));
        if($staff = ResoldStaffExt::model()->find($criteria))
        {
            return $staff->getCanSaleNum();
        }
        else
        {
            $userPubNum = SM::resoldConfig()->resoldPersonalSaleNum();
            $salingEsfNum = ResoldEsfExt::model()->saling()->count($criteria);
            $salingZfNum = ResoldZfExt::model()->saling()->count($criteria);
            $salingQgNum = ResoldQgExt::model()->undeleted()->enabled()->count($criteria);
            $salingQzNum = ResoldQzExt::model()->undeleted()->enabled()->count($criteria);
            $totalCanSaleNum = $userPubNum -$salingEsfNum - $salingZfNum - $salingQgNum - $salingQzNum;
            $totalCanSaleNum < 0 && $totalCanSaleNum = 0;
            return ['canSaleEsfNum'=>$totalCanSaleNum,'canSaleZfNum'=>$totalCanSaleNum,'canSaleQgNum'=>$totalCanSaleNum,'canSaleQzNum'=>$totalCanSaleNum];
        }
    }
}
