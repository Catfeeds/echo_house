<?php

/**
 * 后台模块vip控制器基类
 * @author tivon
 * @date 2015-04-20
 */
class VipController extends Controller
{
    public $controlleName = '';

    public $company = '';
    /**
     * @var string 布局文件路径
     */
    public $layout = '/layouts/base';

    /**
     * @var array 当前访问页面的面包屑. 这个值将被赋值给links属性{@link CBreadcrumbs::links}.
     */
    public $breadcrumbs = array();

    /**
     * 过滤器
     */
    public function filters()
    {
        return array(
            'accessControl - vip/common/login,vip/common/logout,vip/common/init',
        );
    }

    public function init()
    {
        parent::init();
        if(!Yii::app()->user->getIsGuest() && isset(Yii::app()->user->cname))
            $this->company = CompanyExt::model()->findByPk(Yii::app()->user->cid);
    }

    /**
     * 自定义访问规则
     * @return array 返回一个类似{@link accessRules}中的规则数组
     */
    public function RBACRules()
    {
        return array();
    }

    /**
     * 访问控制规则，子类控制器中自定义规则需重写{@link RBACRules()}方法，返回的数组形式相同
     * @return array 访问控制规则
     */
    final public function accessRules()
    {
        $rules = array(
            array('deny',
                'users' => array('?')
            ),
        );
        return array_merge($this->RBACRules(), $rules);
    }

    /**
     * 自定义左侧菜单，设置方法与zii.widget.CMenu相似，详见CMenu.php
     * 使用技巧：
     * 1、系统会自动将'url'与当前访问路由匹配的菜单进行高亮，使用'active'可指定需要高亮的菜单项，只需设置'active'元素的值为一个布尔值的表达式即可。
     * 假设要访问非vip/index/index页面时使得该菜单项高亮，则进行如下设置：
     * array('label'=>'首页','url'=>array('/vip/index/index', 'active'=>$this->route=='vip/index/test'))
     * 这会使得在访问vip/index/test时，vip/index/index菜单项进行高亮
     */
    public function getVipMenu()
    {
        if(Yii::app()->user->id == 1)
        return [
            ['label'=>'管理中心','icon'=>'icon-settings','url'=>'/vip/common/index','active'=>$this->route=='vip/common/index'],
            ['label' => '项目管理', 'icon' => 'icon-speedometer', 'items' => [
                ['label' => '项目列表', 'url' => ['/vip/plot/list']],
                ['label' => '新建项目', 'url' => ['/vip/plot/edit'],'active'=>$this->route=='vip/plot/edit'],
            ]],
            ['label'=>'门店管理','icon'=>'icon-speedometer','url'=>['/vip/company/list'],'active'=>$this->route=='vip/company/edit'],
            ['label'=>'报备管理','icon'=>'icon-speedometer','url'=>['/vip/sub/list'],'active'=>$this->route=='vip/sub/edit'],
            ['label'=>'接入申请管理','icon'=>'icon-speedometer','url'=>['/vip/plotMarketUser/list'],'active'=>$this->route=='vip/plotMarketUser/edit'],
            ['label'=>'分销申请管理','icon'=>'icon-speedometer','url'=>['/vip/cooperate/list'],'active'=>$this->route=='vip/cooperate/edit'],
            ['label'=>'区域管理','icon'=>'icon-speedometer','url'=>['/vip/area/arealist'],'active'=>$this->route=='vip/area/areaedit'],
            ['label'=>'推荐管理','icon'=>'icon-speedometer','url'=>['/vip/recom/list'],'active'=>$this->route=='vip/recom/edit'],
            ['label'=>'举报管理','icon'=>'icon-speedometer','url'=>['/vip/report/list'],'active'=>$this->route=='vip/report/edit'],
            ['label'=>'标签管理','icon'=>'icon-speedometer','url'=>['/vip/tag/list'],'active'=>$this->route=='vip/tag/edit'],
            ['label'=>'用户管理','icon'=>'icon-speedometer','url'=>['/vip/user/list']],
            ['label'=>'站点配置','icon'=>'icon-speedometer','url'=>['/vip/site/list'],'active'=>$this->route=='vip/site/edit'||$this->route=='vip/site/list'],
            // ['label'=>'ahalist','icon'=>'icon-speedometer','url'=>['/vip/aha/list']],

            
        ];
        else
           return [
                ['label'=>'管理中心','icon'=>'icon-settings','url'=>'/vip/common/index','active'=>$this->route=='vip/common/index'],
                ['label' => '项目管理', 'icon' => 'icon-speedometer', 'items' => [
                    ['label' => '项目列表', 'url' => ['/vip/plot/list']],
                    ['label' => '新建项目', 'url' => ['/vip/plot/edit'],'active'=>$this->route=='vip/plot/edit'],
                ]],
                ['label'=>'客户管理','icon'=>'icon-speedometer','url'=>['/vip/sub/list'],'active'=>$this->route=='vip/sub/edit'],
                ['label' => '公司管理', 'icon' => 'icon-speedometer', 'items' => [
                    ['label' => '人员管理', 'url' => ['/vip/user/list']],
                    ['label' => '公司管理', 'url' => ['/vip/company/edit?id='.Yii::app()->user->cid],'active'=>$this->route=='vip/company/edit'],
                ]],
            ]; 
    } 

    /**
     * [getPersonalSalingNum 个人可以上架数目]
     * @return [type] [description]
     */
    public function getPersonalSalingNum($uid=0)
    {
        if(!$uid)
            return 0;
        $userPubNum = SM::resoldConfig()->resoldPersonalSaleNum();
        $salingEsfNum = ResoldEsfExt::model()->saling()->count(['condition'=>'uid=:uid','params'=>[':uid'=>$uid]]);
        $salingZfNum = ResoldZfExt::model()->saling()->count(['condition'=>'uid=:uid','params'=>[':uid'=>$uid]]);
        $salingQgNum = ResoldQgExt::model()->undeleted()->enabled()->count(['condition'=>'uid=:uid','params'=>[':uid'=>$uid]]);
        $salingQzNum = ResoldQzExt::model()->undeleted()->enabled()->count(['condition'=>'uid=:uid','params'=>[':uid'=>$uid]]);
        $totalCanSaleNum = $userPubNum -$salingEsfNum - $salingZfNum - $salingQgNum - $salingQzNum;
        $totalCanSaleNum < 0 && $totalCanSaleNum = 0;
        return $totalCanSaleNum;
    }

    public function actions()
    {
        $alias = 'vip.controllers.common.';
        return [
            'del'=>$alias.'DelAction',
            'changeStatus'=>$alias.'ChangeStatusAction',
            'setSort'=>$alias.'SetSortAction',
        ];
    }

}
