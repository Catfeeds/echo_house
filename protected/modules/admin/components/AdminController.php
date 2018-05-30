<?php

/**
 * 后台模块admin控制器基类
 * @author tivon
 * @date 2015-04-20
 */
class AdminController extends Controller
{
    public $controlleName = '';
    /**
     * @var string 布局文件路径
     */
    public $layout = '/layouts/base';

    /**
     * @var array 当前访问页面的面包屑. 这个值将被赋值给links属性{@link CBreadcrumbs::links}.
     */
    public $breadcrumbs = array();
    public $staff;
    /**
     * 过滤器
     */
    public function filters()
    {
        return array(
            'accessControl - admin/common/login,admin/common/logout,admin/common/init',
        );
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

    public function getAllMenu()
    {
        return [
            ['label'=>'管理中心','icon'=>'icon-settings','url'=>'/admin/common/index','active'=>$this->route=='admin/common/index'],
            ['label' => '项目管理', 'icon' => 'icon-speedometer', 'items' => [
                ['label' => '项目列表', 'url' => ['/admin/plot/list']],
                ['label' => '项目修改流水', 'url' => ['/admin/plot/editlist']],
                ['label' => '项目动态列表', 'url' => ['/admin/plot/newslistnew']],
                ['label' => '项目呼叫列表', 'url' => ['/admin/plot/calllist']],
                ['label' => '新建项目', 'url' => ['/admin/plot/edit'],'active'=>$this->route=='admin/plot/edit'],
            ]],
            ['label' => '公司管理', 'icon' => 'icon-speedometer', 'items' => [
                ['label' => '公司列表', 'url' => ['/admin/company/list'],'active'=>$this->route=='admin/company/edit'],
                ['label' => '合作公司', 'url' => ['/admin/companyPackage/list'],'active'=>$this->route=='admin/companyPackage/edit'],
            ]],
            ['label'=>'报备管理','icon'=>'icon-speedometer','url'=>['/admin/sub/list'],'active'=>$this->route=='admin/sub/edit'],
            ['label'=>'对接人申请管理','icon'=>'icon-speedometer','url'=>['/admin/plotMarketUser/list'],'active'=>$this->route=='admin/plotMarketUser/edit'],
            ['label'=>'分销签约管理','icon'=>'icon-speedometer','url'=>['/admin/cooperate/list'],'active'=>$this->route=='admin/cooperate/edit'],
            ['label'=>'收藏管理','icon'=>'icon-speedometer','url'=>['/admin/save/list'],'active'=>$this->route=='admin/save/edit'],
            // ['label'=>'订阅管理','icon'=>'icon-speedometer','url'=>['/admin/userSubscribe/list'],'active'=>$this->route=='admin/userSubscribe/edit'],
            ['label'=>'区域管理','icon'=>'icon-speedometer','url'=>['/admin/area/arealist'],'active'=>$this->route=='admin/area/areaedit'],
            // ['label'=>'推荐管理','icon'=>'icon-speedometer','url'=>['/admin/recom/list'],'active'=>$this->route=='admin/recom/edit'],
            ['label'=>'举报管理','icon'=>'icon-speedometer','url'=>['/admin/report/list'],'active'=>$this->route=='admin/report/edit'],
            ['label'=>'上下架管理','icon'=>'icon-speedometer','url'=>['/admin/down/list'],'active'=>$this->route=='admin/down/edit'],
            ['label'=>'标签管理','icon'=>'icon-speedometer','url'=>['/admin/tag/list'],'active'=>$this->route=='admin/tag/edit'],
            ['label'=>'用户管理','icon'=>'icon-speedometer','url'=>['/admin/user/list']],
            ['label'=>'员工管理','icon'=>'icon-speedometer','url'=>['/admin/staff/list'],'active'=>$this->route=='admin/staff/edit'],
            ['label'=>'虚拟号码管理','icon'=>'icon-speedometer','url'=>['/admin/virtualPhone/list'],'active'=>$this->route=='admin/virtualPhone/edit'],
            ['label' => '推广管理', 'icon' => 'icon-speedometer', 'items' => [
                ['label' => '域名管理', 'url' => ['/admin/url/list'],'active'=>$this->route=='admin/url/edit'],
                ['label' => '客户管理', 'url' => ['/admin/plotUser/list'],'active'=>$this->route=='admin/plotUser/edit'],
            ]],
            // ['label'=>'推广管理','icon'=>'icon-speedometer','url'=>['/admin/url/list'],'active'=>$this->route=='admin/url/edit'],
            ['label'=>'站点配置','icon'=>'icon-speedometer','url'=>['/admin/site/list'],'active'=>$this->route=='admin/site/edit'||$this->route=='admin/site/list'],
        ];
    }

    /**
     * 自定义左侧菜单，设置方法与zii.widget.CMenu相似，详见CMenu.php
     * 使用技巧：
     * 1、系统会自动将'url'与当前访问路由匹配的菜单进行高亮，使用'active'可指定需要高亮的菜单项，只需设置'active'元素的值为一个布尔值的表达式即可。
     * 假设要访问非admin/index/index页面时使得该菜单项高亮，则进行如下设置：
     * array('label'=>'首页','url'=>array('/admin/index/index', 'active'=>$this->route=='admin/index/test'))
     * 这会使得在访问admin/index/test时，admin/index/index菜单项进行高亮
     */
    public function getVipMenu()
    {
        $allmenu = $this->getAllMenu();
        if(Yii::app()->user->id == 1){
            return $allmenu;
        }
        else {

            $data = [];
            $user = StaffExt::model()->findByPk(Yii::app()->user->id);
            $this->staff = $user;
            $hisarr = json_decode($user->arr,true);
            if($hisarr) {
                // foreach ($hisarr as $key => $value) {
                    foreach ($allmenu as $m=>$n) {
                        if(in_array($m+1, $hisarr)) {
                            $data[] = $allmenu[$m];
                        }
                    }
                // }
            }
            return $data;
        }
    } 

    public function getFormatMenu()
    {
        $arr = $this->getAllMenu();
        $data = [];
        foreach ($arr as $key => $value) {
            $data[$key+1] = $value['label'];
        }
        return $data;
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
        $alias = 'admin.controllers.common.';
        return [
            'del'=>$alias.'DelAction',
            'changeStatus'=>$alias.'ChangeStatusAction',
            'setSort'=>$alias.'SetSortAction',
        ];
    }

}
