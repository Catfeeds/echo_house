<?php
$this->pageTitle = $this->controllerName.'列表';
$this->breadcrumbs = array($this->pageTitle);
$statusArr = SubExt::$status;
?>
<div class="table-toolbar">
    <div class="btn-group pull-left">
        <form class="form-inline">
            <div class="form-group">
                <?php echo CHtml::dropDownList('type',$type,array('title'=>'分销公司'),array('class'=>'form-control','encode'=>false)); ?>
            </div>
            <div class="form-group">
                <?php echo CHtml::textField('value',$value,array('class'=>'form-control chose_text')) ?>
            </div>
            <div class="form-group">
                <?php echo CHtml::dropDownList('time_type',$time_type,array('created'=>'添加时间','updated'=>'修改时间'),array('class'=>'form-control','encode'=>false)); ?>
            </div>
            <?php Yii::app()->controller->widget("DaterangepickerWidget",['time'=>$time,'params'=>['class'=>'form-control chose_text']]);?>
            
            <div class="form-group">
                <?php echo CHtml::dropDownList('cate',$cate,$statusArr,array('class'=>'form-control chose_select','encode'=>false,'prompt'=>'--选择状态--')); ?>
            </div>
            <div class="form-group">
                <?php echo CHtml::dropDownList('hid',$hid,CHtml::listData($plots,'id','title'),array('class'=>'form-control chose_select','encode'=>false,'prompt'=>'--选择楼盘--')); ?>
            </div>
            <div class="form-group">
                <?php echo CHtml::dropDownList('jl',$jl,$this->company->getScjl(),array('class'=>'form-control chose_select','encode'=>false,'prompt'=>'--选择市场组--')); ?>
            </div>
            <div class="form-group">
                <?php echo CHtml::dropDownList('sczy',$sczy,$this->company->getSczy(),array('class'=>'form-control chose_select','encode'=>false,'prompt'=>'--选择市场专员--')); ?>
            </div>
            <button type="submit" class="btn blue">搜索</button>
            <a class="btn yellow" onclick="removeOptions()"><i class="fa fa-trash"></i>&nbsp;清空</a>
        </form>
    </div>
    <!-- <div class="pull-right">
        <a href="<?php echo $this->createAbsoluteUrl('edit') ?>" class="btn blue">
            添加<?=$this->controllerName?> <i class="fa fa-plus"></i>
        </a>
    </div> -->
</div>
   <table class="table table-bordered table-striped table-condensed flip-content table-hover">
    <thead class="flip-content">
    <tr>
        <th class="text-center">项目信息</th>
        <th class="text-center">市场对接人</th>
        <th class="text-center">分销业务员</th>
        <th class="text-center">分销公司</th>
        <th class="text-center">客户信息</th>
        <th class="text-center">客户码</th>
        <th class="text-center">报备时间</th>
        <th class="text-center">状态</th>
        <th class="text-center">操作</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($infos as $k=>$v): ?>
        <tr>
            <td class="text-center"><?=$v->plot->title?></td>
            <td class="text-center"><?=$v->notice?(UserExt::model()->find("phone='".$v->notice."'")->name.$v->notice):''?></td>
            <td class="text-center"><?=$v->user?($v->user->name.'/'.$v->user->phone):''?></td> 
            <td class="text-center"><?=$v->company_name?$v->company_name:'独立经纪人'?></td>
            <td class="text-center"><?=$v->name.'/'.$v->phone?></td> 
            <td class="text-center"><?=$v->code?></td> 
            <td class="text-center"><?=date('Y-m-d H:i:s',$v->created)?></td>
            <td class="text-center" style="text-align:center;vertical-align: middle">
                <!-- <div class="btn-group">
                    <button id="btnGroupVerticalDrop1" type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                    <?=$statusArr[$v->status]?> <i class="fa fa-angle-down"></i>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                    <?php foreach($statusArr as $key=>$v1){?>
                        <li>
                            <?=CHtml::ajaxLink($v1,$this->createUrl('ajaxStatus',['kw'=>$key,'ids'=>$v->id]),['success'=>'function(){location.reload();}'])?>
                        </li>
                      <?php  }?>
                    </ul>
                </div> -->
                <?=$statusArr[$v->status]?>
            </td>
            <td style="text-align:center;vertical-align: middle">
            <a href="<?php echo $this->createUrl('pro',array('id'=>$v->id)); ?>" class="btn default btn-xs blue">查看流水 </a>
            </td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>
<?php $this->widget('VipLinkPager', array('pages'=>$pager)); ?>

<script>
<?php Tools::startJs(); ?>
    setInterval(function(){
        $('#AdminIframe').height($('#AdminIframe').contents().find('body').height());
        var $panel_title = $('#fade-title');
        $panel_title.html($('#AdminIframe').contents().find('title').html());
    },200);
    function do_admin(ts){
        $('#AdminIframe').attr('src',ts.data('url')).load(function(){
            self = this;
            //延时100毫秒设定高度
            $('#Admin').modal({ show: true, keyboard:false });
            $('#Admin .modal-dialog').css({width:'1000px'});
        });
    }
    function set_sort(_this, id, sort){
            $.getJSON('<?php echo $this->createUrl('/admin/league/setSort')?>',{id:id,sort:sort,class:'<?=isset($infos[0])?get_class($infos[0]):''?>'},function(dt){
                location.reload();
            });
        }
    function do_sort(ts){
        if(ts.which == 13){
            _this = $(ts.target);
            sort = _this.val();
            id = _this.parent().data('id');
            set_sort(_this, id, sort);
        }
    }

    $(document).on('click',function(e){
          var target = $(e.target);
          if(!target.hasClass('sort_edit')){
             $('.sort_edit').trigger($.Event( 'keypress', 13 ));
          }
    });
    $('.sort_edit').click(function(){
        if($(this).find('input').length <1){
            $(this).html('<input type=\"text\" value=\"' + $(this).html() + '\" class=\"form-control input-sm sort_edit\" onkeypress=\"return do_sort(event)\" onblur=\"set_sort($(this),$(this).parent().data(\'id\'),$(this).val())\">');
            $(this).find('input').select();
        }
    });
    var getChecked  = function(){
        var ids = "";
        $(".checkboxes").each(function(){
            if($(this).parents('span').hasClass("checked")){
                if(ids == ''){
                    ids = $(this).val();
                } else {
                    ids = ids + ',' + $(this).val();
                }
            }
        });
        return ids;
    }

    $(".group-checkable").click(function () {
        var set = $(this).attr("data-set");
        $(set).each(function () {
            $(this).attr("checked", !$(this).attr("checked"));
        });
        $.uniform.update(set);
    });
    //清空选项
    function removeOptions()
    {
        // alert($('.chose_select').val());
        $('.chose_text').val('');
        $('.chose_select').val('');
    }

    $("#hname").on("dblclick",function(){
        var hnames = $(".hname");
        console.log(hnames);
        hnames.each(function(){
            var _this = $(this);
            $.getJSON("<?php echo $this->createUrl('/api/houses/getsearch') ?>",{key:_this.html()},function(dt){
                _this.append(" (" + dt.msg[1].length + ")");
            });
        });
    });
<?php Tools::endJs('js') ?>
</script>
