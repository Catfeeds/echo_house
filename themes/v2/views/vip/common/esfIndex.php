<?php
$this->pageTitle = '房产数据营销后台欢迎您';
?>
<style type="text/css">
    .page-content{
       background: #F1F3FA;
    }
</style>
<div class="row">
    <div class="col-lg-3 col-md-3">
        <div class="dashboard-stat blue-madison">
            <div class="visual">
                <i class="fa fa-comments"></i>
            </div>
            <div class="details">
                <div class="number">
                    <?php echo $newEsfs.'/'.$totalEsfs ?>
                </div>
                <div class="desc">
                    今日新增二手房数量/总数
                </div>
            </div>
            <a class="more" href="<?php echo $this->createUrl('esf/esfList')?>">
                查看更多 <i class="m-icon-swapright m-icon-white"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-md-3">
        <div class="dashboard-stat red-intense">
            <div class="visual">
                <i class="fa fa-bar-chart-o"></i>
            </div>
            <div class="details">
                <div class="number">
                    <?php echo $newZfs.'/'.$totalZfs ?>
                </div>
                <div class="desc">
                    今日新增租房数量/总数
                </div>
            </div>
            <a class="more" href="<?php echo $this->createUrl('zf/zfList')?>">
                查看更多 <i class="m-icon-swapright m-icon-white"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-md-3">
        <div class="dashboard-stat green-haze">
            <div class="visual">
                <i class="fa fa-shopping-cart"></i>
            </div>
            <div class="details">
                <div class="number">
                    <?php echo ($newQgs+$newQzs).'/'.($totalQgs+$totalQzs) ?>
                </div>
                <div class="desc">
                    今日新增求租求购数量/总数
                </div>
            </div>
            <a class="more" href="<?php echo $this->createUrl('esf/qgList')?>">
                查看更多 <i class="m-icon-swapright m-icon-white"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-md-3">
        <div class="dashboard-stat purple-plum">
            <div class="visual">
                <i class="fa fa-globe"></i>
            </div>
            <div class="details">
                <div class="number">
                    <?php echo $newPackages.'/'.$totalPackages ?>
                </div>
                <div class="desc">
                    今天开通套餐数量/总数量
                </div>
            </div>
            <a class="more" href="<?php echo $this->createUrl('resoldStaff/resoldStaffList')?>">
                查看更多 <i class="m-icon-swapright m-icon-white"></i>
            </a>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-md-6">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-red-sunglo">
                    <span class="caption-subject bold uppercase">待审核的二手房</span>
                    <span class="badge badge-danger">
                                                <?=$totalCheckEsfs?></span>
                </div>
                <div class="actions">
                    <a href="<?php echo $this->createUrl('esf/esfList',['checkStatus'=>2]) ?>" class="btn btn-default btn-sm"><i class="fa fa-search"></i>更多</a>
                </div>
            </div>
            <div class="portlet-body">
            <div class="table-scrollable">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>
                                 #
                            </th>
                            <th style="width: 60%">
                                 标题
                            </th>
                            <th>
                                 面积
                            </th>
                            <th>
                                 总价
                            </th>
                            <th>
                                 发布时间
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($checkEsfs) foreach ($checkEsfs as $key => $value) {?>
                            <tr>
                                <td><?=$value['id']?></td>
                                <td><?=$value['title']?></td>
                                <td><?=$value['size']?></td>
                                <td><?=$value['price']?></td>
                                <td><?=date('Y-m-d',$value['created'])?></td>
                            </tr>
                        <?php }?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-red-sunglo">
                    <span class="caption-subject bold uppercase">待审核的租房</span>
                    <span class="badge badge-danger">
                                                <?=$totalCheckZfs?></span>
                </div>
                <div class="actions">
                    <a href="<?php echo $this->createUrl('zf/zfList',['checkStatus'=>2]) ?>" class="btn btn-default btn-sm"><i class="fa fa-search"></i>更多</a>
                </div>
            </div>
            <div class="portlet-body">
            <div class="table-scrollable">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>
                                 #
                            </th>
                            <th style="width: 60%">
                                 标题
                            </th>
                            <th>
                                 面积
                            </th>
                            <th>
                                 租金
                            </th>
                            <th>
                                 发布时间
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($checkZfs) foreach ($checkZfs as $key => $value) {?>
                            <tr>
                                <td><?=$value['id']?></td>
                                <td><?=$value['title']?></td>
                                <td><?=$value['size']?></td>
                                <td><?=$value['price']?></td>
                                <td><?=date('Y-m-d',$value['created'])?></td>
                            </tr>
                        <?php }?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-red-sunglo">
                    <span class="caption-subject bold uppercase">举报管理</span>
                    <span class="badge badge-danger">
                                                <?=$totalReports?></span>
                </div>
                <div class="actions">
                    <a href="<?php echo $this->createUrl('resoldReport/list',['deal'=>0]) ?>" class="btn btn-default btn-sm"><i class="fa fa-search"></i>更多</a>
                </div>
            </div>
            <div class="portlet-body">
            <div class="table-scrollable">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>
                                 #
                            </th>
                            <th>
                                 房源
                            </th>
                            <th>
                                 举报类型
                            </th>
                            <th>
                                 备注
                            </th>
                            <th>
                                 手机号
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($reports) foreach ($reports as $key => $value) { ?>
                            <tr>
                                <td><?=$value['id']?></td>
                                <td><?=Tools::u8_title_substr($value->infoname,40)?></td>
                                <td><?=Yii::app()->params['report_type'][$value['type']]?></td>
                                <td><?=$value['reason']?></td>
                                <td><?=$value['phone']?></td>
                            </tr>
                        <?php }?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-red-sunglo">
                    <span class="caption-subject bold uppercase">即将到期中介</span>
                    <span class="badge badge-danger">
                                                <?=$totalStaffPackages?></span>
                </div>
                <div class="actions">
                    <a href="<?php echo $this->createUrl('resoldStaff/resoldStaffList') ?>" class="btn btn-default btn-sm"><i class="fa fa-search"></i>更多</a>
                </div>
            </div>
            <div class="portlet-body">
            <div class="table-scrollable">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>
                                 #
                            </th>
                            <th>
                                 帐号
                            </th>
                            <th>
                                 套餐类型
                            </th>
                            <th>
                                 到期时间
                            </th>
                            <th>
                                 手机号
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($staffPackages) foreach ($staffPackages as $key => $value) {?>
                            <tr>
                                <td><?=$value['id']?></td>
                                <td><?=$value->staffs->account?></td>
                                <td><?php if($package = $value->package){  $package = json_decode($package['content'],true);echo $package['total_num'].'/'.$value->staffs->hurry_num.'/'.$package['appoint_num'];}else{echo '无';}  ?></td>
                                <td><?=date('Y-m-d',$value['expire_time'])?></td>
                                <td><?=$value->staffs->phone?></td>
                            </tr>
                        <?php }?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
<div class="col-md-12">
<div class="portlet light bordered">
    <div class="portlet-title">
        <div class="caption">
            <i class="icon-bar-chart font-green-haze"></i>
            <span class="caption-subject bold uppercase font-green-haze"> <?=SM::urmConfig()->cityName().($chart['area']?$chart['area']['name']:'')?>二手房价格走势 </span>
            <span class="caption-helper">最近12个月二手房均价统计</span>

        </div>
    </div>
    <div class="portlet-body">
        <ul class="nav nav-pills">
        <li class="">
            <a href="<?=$this->createUrl('esfIndex')?>"  aria-expanded="false">全市</a>
            </li>
        <?php $areas = AreaExt::model()->findAll(['condition'=>'parent=0']); if($areas) foreach ($areas as $key => $value) {?>
           <li class="">
            <a href="<?=$this->createUrl('esfIndex',['type'=>$value->id])?>"  aria-expanded="false"><?=$value->name?></a>
            </li>
        <?php }?>

        </ul>
        <div id="ddltj" class="chart" style="height: 800;">
        </div>
    </div>
</div>
</div>
<script type="text/javascript">
<?php Tools::startJs(); ?>
    var myChart = echarts.init(document.getElementById("ddltj"));
    var options = {
        tooltip : {
            trigger: 'axis'
        },
        toolbox: {
            show : true,
            feature : {
                mark : {show: true},
                dataView : {show: true, readOnly: false},
                magicType : {show: true, type: ['line', 'bar']},
                restore : {show: true},
                saveAsImage : {show: true}
            }
        },
        calculable : true,
        xAxis : [
            {
                name : '年-月',
                type : 'category',
                boundaryGap : false,
                data : <?php echo CJSON::encode(isset($chart['xAxis'])?$chart['xAxis']:[]); ?>,
            }
        ],
        yAxis : [
            {
                name : '单位(元/平方)',
                type : 'value',
                axisLabel : {
                    formatter: '{value}'
                }
            }
        ],
        series : [
            {
                name: '二手房均价',
                type:'line',
                data:<?php echo CJSON::encode(isset($chart['series'])?$chart['series']:[]); ?>,
                <?php if(isset($chart['series'])&&$chart['series']): ?>
                markPoint : {
                    data : [
                        {type:'max', 'name':'均价'},
                        {type:'min', 'name':'均价'},
                    ]
                },
                <?php endif; ?>
               /* markLine : {
                    data : [
                        {type : 'average', name : '平均值'}
                    ]
                }*/
            }
        ]
    };
    myChart.setOption(options);
    <?php Tools::endJs('js');?>
</script>
</div>

<?php
Yii::app()->clientScript->registerScriptFile('/static/global/scripts/echarts/echarts-all.js', CClientScript::POS_END);
?>
</script>
