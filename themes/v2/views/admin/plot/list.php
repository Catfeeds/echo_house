<?php
$this->pageTitle = $this->controllerName.'列表';
$this->breadcrumbs = array($this->pageTitle);
?>
<div class="table-toolbar">
    <div class="btn-group pull-left">
        <form class="form-inline">
            <div class="form-group">
                <?php echo CHtml::dropDownList('type',$type,array('title'=>'标题'),array('class'=>'form-control','encode'=>false)); ?>
            </div>
            <div class="form-group">
                <?php echo CHtml::textField('value',$value,array('class'=>'form-control chose_text')) ?>
            </div>
            <div class="form-group">
                <?php echo CHtml::dropDownList('time_type',$time_type,array('created'=>'添加时间','updated'=>'修改时间'),array('class'=>'form-control','encode'=>false)); ?>
            </div>
             
            <?php Yii::app()->controller->widget("DaterangepickerWidget",['time'=>$time,'params'=>['class'=>'form-control chose_text']]);?>
            <div class="form-group">
                <?php echo CHtml::dropDownList('is_uid',$is_uid,['后台','用户'],array('class'=>'form-control chose_select','encode'=>false,'prompt'=>'--信息来源--')); ?>
            </div>
            <div class="form-group">
                <?php echo CHtml::dropDownList('status',$status,['禁用','启用'],array('class'=>'form-control chose_select','encode'=>false,'prompt'=>'--选择状态--')); ?>
            </div>
            <button type="submit" class="btn blue">搜索</button>
            <a class="btn yellow" onclick="removeOptions()"><i class="fa fa-trash"></i>&nbsp;清空</a>
        </form>
    </div>
    <div class="pull-right">
        <a href="<?php echo $this->createAbsoluteUrl('edit') ?>" class="btn blue">
            添加<?=$this->controllerName?> <i class="fa fa-plus"></i>
        </a>
    </div>
</div>
<table class="table table-bordered table-striped table-condensed flip-content">
    <thead class="flip-content">
        <tr>
            <th class="text-center">排序</th>
            <th class="text-center">id</th>
            <th class="text-center">标题</th>
            <th class="text-center">区域</th>
            <th class="text-center">楼盘发布人</th>
            <th class="text-center">总代公司</th>
            <th class="text-center">对接人数</th>
            <th class="text-center">今日/总 <a href="list?sort=views"><i class="fa fa-arrow-down"></i></a></th>
            <th class="text-center">置顶时间</th>
            <th class="text-center">刷新时间</th>
            <th class="text-center">创建时间</th>
            <th class="text-center">状态</th>
            <th class="text-center">操作</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($infos as $v): $owner = $v->owner;$company = $v->company;$areaInfo = $v->areaInfo; $streetInfo = $v->streetInfo;?>
        <tr>
            <td style="text-align:center;vertical-align: middle" class="warning sort_edit"
                data-id="<?php echo $v['id'] ?>"><?php echo $v['sort'] ?></td>
            <td  class="text-center"><?php echo $v->id ?></td>
            <td  class="text-center"><a href="<?=$this->createUrl('/subwap/detail.html?id='.$v->id)?>" target="_blank"><?php echo $v->title ?></a></td>
            <td class="text-center"><?php echo ($areaInfo?$areaInfo->name:'').'<br>'.($streetInfo?$streetInfo->name:''); ?></td>
            <td  class="text-center"><?php echo $owner?($owner->name.$owner->phone.' '.($owner->vip_expire>time()?'<br>会员':'')):'' ?></td>
            <td  class="text-center"><?=$company?('<a href="'.$this->createUrl('list',['company'=>$company->id]).'">'.$company->name.'</a>'):'暂无'?></td>
            <td  class="text-center"><a target="_blank" href="<?=$this->createUrl('/admin/plotMarketUser/list',['hid'=>$v->id])?>"><?php echo Yii::app()->db->createCommand("select count(id) from plot_makert_user where hid=".$v->id)->queryScalar() ?></a></td>
            <td  class="text-center"><?php echo Yii::app()->redis->getClient()->hGet('plot_views',$v->id).'/'.($v->views + Yii::app()->redis->getClient()->hGet('plot_views',$v->id)+($v->status?300:0)+($v->sort?1000:0))?></td>
            <td class="text-center"><?php echo $v->top_time?date('Y-m-d H:i:s',$v->top_time):'-'; ?></td>
            <td class="text-center"><?php echo date('Y-m-d H:i:s',$v->refresh_time); ?></td>
            <td class="text-center"><?php echo date('Y-m-d',$v->created); ?></td>
            <td class="text-center"><?php echo CHtml::ajaxLink(UserExt::$status[$v->status],$this->createUrl('changeStatus'), array('type'=>'get', 'data'=>array('id'=>$v->id,'class'=>get_class($v)),'success'=>'function(data){location.reload()}'), array('class'=>'btn btn-sm '.UserExt::$statusStyle[$v->status])); ?></td>
            <td  class="text-center">
            <?php echo CHtml::ajaxLink('清除发布人',$this->createUrl('cleanPublisher'), array('type'=>'get', 'data'=>array('id'=>$v->id),'success'=>'function(data){location.reload()}'), array('class'=>'btn btn-xs yellow')); ?>
                <?php echo CHtml::ajaxLink('刷新',$this->createUrl('refresh'), array('type'=>'get', 'data'=>array('id'=>$v->id),'success'=>'function(data){location.reload()}'), array('class'=>'btn btn-xs blue')); ?>
                <a target="_blank" href="<?=$this->createUrl('/admin/plotMarketUser/edit',['hid'=>$v->id])?>" class="btn btn-xs green">新增对接人</a>
                <a href="<?=$this->createUrl('dplist',['hid'=>$v->id])?>" class="btn btn-xs default">点评</a>
                <a href="<?=$this->createUrl('asklist',['hid'=>$v->id])?>" class="btn btn-xs red">提问</a>
                <a href="<?=$this->createUrl('answerlist',['hid'=>$v->id])?>" class="btn btn-xs red">回答</a>
                <a href="<?=$this->createUrl('imagelist',['hid'=>$v->id])?>" class="btn btn-xs red">相册</a>
                <a href="<?=$this->createUrl('hxlist',['hid'=>$v->id])?>" class="btn btn-xs yellow">户型</a>
                <a href="<?=$this->createUrl('newslist',['hid'=>$v->id])?>" class="btn btn-xs blue">动态</a>
                <a href="<?=$this->createUrl('pricelist',['hid'=>$v->id])?>" class="btn btn-xs green">佣金方案</a>
                <a href="<?php echo $this->createUrl('edit',array('id'=>$v->id,'referrer'=>Yii::app()->request->url)) ?>" class="btn default btn-xs green"><i class="fa fa-edit"></i> 编辑 </a>
                <?php echo CHtml::htmlButton('删除', array('data-toggle'=>'confirmation', 'class'=>'btn btn-xs red', 'data-title'=>'确认删除？', 'data-btn-ok-label'=>'确认', 'data-btn-cancel-label'=>'取消', 'data-popout'=>true,'ajax'=>array('url'=>$this->createUrl('ajaxDel'),'type'=>'get','success'=>'function(data){location.reload()}','data'=>array('id'=>$v->id))));?>
            </td>
        </tr>
    <?php endforeach; ?>
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
            $.getJSON('<?php echo $this->createUrl('/admin/plot/setSort')?>',{id:id,sort:sort,class:'<?=isset($infos[0])?get_class($infos[0]):''?>'},function(dt){
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
