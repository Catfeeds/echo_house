<?php
$this->pageTitle = $this->controllerName.'列表';
$this->breadcrumbs = array($this->pageTitle);
?>
<div class="table-toolbar">
    <div class="btn-group pull-left">
        <form class="form-inline" id="f1">
            <div class="form-group">
                <?php echo CHtml::dropDownList('type',$type,array('title'=>'标题','phone'=>'手机','com'=>'公司名'),array('class'=>'form-control','encode'=>false)); ?>
            </div>
            <div class="form-group">
                <?php echo CHtml::textField('value',$value,array('class'=>'form-control chose_text')) ?>
            </div>
            <div class="form-group">
                <?php echo CHtml::dropDownList('time_type',$time_type,array('created'=>'添加时间','updated'=>'修改时间'),array('class'=>'form-control','encode'=>false)); ?>
            </div>
            <?php Yii::app()->controller->widget("DaterangepickerWidget",['time'=>$time,'params'=>['class'=>'form-control chose_text']]);?>
            <div class="form-group">
                <?php echo CHtml::dropDownList('cate',$cate,UserExt::$ids,array('class'=>'form-control chose_select','encode'=>false,'prompt'=>'--选择类型--')); ?>
            </div>
            <div class="form-group">
                <?php echo CHtml::dropDownList('status',$status,['未通过','已通过'],array('class'=>'form-control chose_select','encode'=>false,'prompt'=>'--选择状态--')); ?>
            </div>
            <div class="form-group">
                <?php echo CHtml::dropDownList('viptime',$viptime,['未到期会员','已到期会员'],array('class'=>'form-control chose_select','encode'=>false,'prompt'=>'--选择旧版会员--')); ?>
            </div>
            <div class="form-group">
                <?php echo CHtml::dropDownList('viptimenew',$viptimenew,['未到期会员','已到期会员'],array('class'=>'form-control chose_select','encode'=>false,'prompt'=>'--选择新版会员--')); ?>
            </div>
            <button type="button" onclick="exptt()" class="btn blue">搜索</button>
            <a class="btn yellow" onclick="removeOptions()"><i class="fa fa-trash"></i>&nbsp;清空</a>
        </form>
    </div>
    <div class="pull-right">
        <a href="<?php echo $this->createAbsoluteUrl('edit') ?>" class="btn blue">
            添加<?=$this->controllerName?> <i class="fa fa-plus"></i>
        </a>
        <!-- <button onclick="expit()" type="button" class="btn yellow">导出用户</button> -->
        <?php if(Yii::app()->user->is_m): ?>
        <a target="_blank" href="<?php echo $this->createAbsoluteUrl('export') ?>" class="btn yellow">
            导出用户 
        </a>
        <?php endif;?>
    </div>
    
</div>
   <table class="table table-bordered table-striped table-condensed flip-content table-hover">
    <thead class="flip-content">
    <tr>
        <th class="text-center">排序</th>
        <th class="text-center">ID</th>
        <th class="text-center">用户名</th>
        <th class="text-center">用户类型</th>
        <th class="text-center">电话</th>
        <!-- <th class="text-center">虚拟号</th> -->
        <th class="text-center">公司名</th>
        <th class="text-center">旧版到期时间 <a href="list?sort=2"><i class="fa fa-arrow-down"></i></a></th>
        <th class="text-center">新版套餐 <a href="list?sort=1"><i class="fa fa-arrow-down"></i></a></th>
        <th class="text-center">刷新数</th>
        <th class="text-center">添加时间</th>
        <th class="text-center">修改时间</th>
        <th class="text-center">状态</th>
        <th class="text-center">操作</th>
        <th class="text-center">驳回</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($infos as $k=>$v): ?>
        <tr>
            <td style="text-align:center;vertical-align: middle" class="warning sort_edit"
                data-id="<?php echo $v['id'] ?>"><?php echo $v['sort'] ?></td>
            <td style="text-align:center;vertical-align: middle"><?php echo $v->id; ?></td>
            <td class="text-center"><?=$v->name?></td>
            <td class="text-center"><?=$v->type?UserExt::$ids[$v->type]:''?></td>
            <td class="text-center"><?=$v->phone?></td>
            <td class="text-center"><?=$v->companyinfo?($v->companyinfo->name):''?></td>  
            <td class="text-center"><?=$v->vip_expire?($v->vip_expire>time()?date('Y-m-d',$v->vip_expire):'已到期'):'-'?></td>  
            <td class="text-center"><?=$v->vip_expire_new>time()?(date('Y-m-d',$v->vip_expire_new).'<br>'.$v->can_sub):'-'?></td>
            <td class="text-center"><?=$v->refresh_num?></td>
            <td class="text-center"><?=date('Y-m-d H:i:s',$v->created)?></td>
            <td class="text-center"><?=date('Y-m-d H:i:s',$v->updated)?></td>
            <td class="text-center"><?php echo CHtml::ajaxLink(UserExt::$status[$v->status],$this->createUrl('changeStatus'), array('type'=>'get', 'data'=>array('id'=>$v->id,'class'=>get_class($v)),'success'=>'function(data){location.reload()}'), array('class'=>'btn btn-sm '.UserExt::$statusStyle[$v->status])); ?></td>

            <td style="text-align:center;vertical-align: middle">
                <a href="<?php echo $this->createUrl('edit',array('id'=>$v->id,'referrer'=>Yii::app()->request->url)) ?>" class="btn default btn-xs green"><i class="fa fa-edit"></i> 编辑 </a>
                <?php echo CHtml::htmlButton('删除', array('data-toggle'=>'confirmation', 'class'=>'btn btn-xs red', 'data-title'=>'确认删除？', 'data-btn-ok-label'=>'确认', 'data-btn-cancel-label'=>'取消', 'data-popout'=>true,'ajax'=>array('url'=>$this->createUrl('del'),'type'=>'get','success'=>'function(data){location.reload()}','data'=>array('id'=>$v->id,'class'=>get_class($v)))));?>


            </td>
            <td class="text-center">
            <?php if($v->status==0):?>
            <form action="recall" method="get">
                <input type="text" name="msg">
                <input type="hidden" name="id" value="<?=$v->id?>">
                <input type="submit"  value="提交" class="btn btn-sm blue">
            </form>
        <?php endif;?>
            </td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>
<?php $this->widget('VipLinkPager', array('pages'=>$pager,'class'=>'user','type'=>$cate)); ?>

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
    function expit() {
        $('#f1').attr('action','export');
        $('#f1').submit();
    }
    function exptt() {
        $('#f1').attr('action','');
        $('#f1').submit();
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
