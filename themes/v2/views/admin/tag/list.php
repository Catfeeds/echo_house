 <?php
$this->pageTitle = '标签管理';
$this->breadcrumbs = array($this->pageTitle);
?>

<div class="portlet-body">
    <div class="col-md-12">
        <div class="alert alert-info alert-dismissable">
            <strong>注:</strong>直接点击标签可进行启用禁用切换，蓝色状态表示启用（在网站前台显示），灰色表示禁用（在网站前台不显示）；鼠标按住标签可拖动进行排序；点击标签右侧箭头展开菜单可进行编辑\删除操作。标签删除后，所有关联该标签的信息将解除与该标签的关联关系。
        </div>
        <!-- 直接式标签开始 -->
        <div class="portlet light bg-inverse ">
            <div class="portlet-title">
                <div class="caption">
					<i class="fa fa-tag font-red-sunglo"></i>
					<span class="caption-subject bold font-red-sunglo uppercase">直接式标签</span>
					<span class="caption-helper">可与数据直接关联的标签</span>
				</div>
            </div>
            <div class="portlet-body">
                <?php foreach(TagExt::$xinfangCate['direct'] as $catePinyin=>$cateName):  ?>
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption font-blue-hoki">
                            <i class="fa fa-tag font-blue-hoki"></i><?php echo $cateName; ?>
                        </div>
                        <div class="actions">
                            <!-- <?php echo CHtml::ajaxLink('一键禁用', $this->createUrl('/admin/tag/ajaxStatus'), ['data'=>['cate'=>$catePinyin,'status'=>1], 'type'=>'post', 'success'=>'js:function(d){if(d.code){location.reload();}else{toastr.error(d.msg);}}'],['class'=>'btn btn-default btn-sm']); ?>
                            <?php echo CHtml::ajaxLink('一键启用', $this->createUrl('/admin/tag/ajaxStatus'), ['data'=>['cate'=>$catePinyin,'status'=>0], 'type'=>'post', 'success'=>'js:function(d){if(d.code){location.reload();}else{toastr.error(d.msg);}}'],['class'=>'btn btn-default btn-sm']); ?> -->

                            <a href="<?=$this->createUrl('/admin/tag/edit',['cate'=>$catePinyin]); ?>" class="btn btn-default btn-sm"><i class="fa fa-pencil"></i>新增标签</a>
                        </div>
                    </div>
                    <div class="portlet-body sort_item">
                        <?php if(isset($list[$catePinyin])): ?>
                            <?php foreach($list[$catePinyin] as $v): ?><div class="btn-group" style="margin-bottom:5px;margin-right:5px;" data-id="<?=$v->id; ?>">
                                <?php echo CHtml::ajaxLink($v->name.'('.$v->anum.')', $this->createUrl('ajaxStatus'), array('data'=>array('id'=>$v->id, 'status'=>$v->status), 'type'=>'post', 'success'=>'js:function(d){if(d.code){location.reload();}else{toastr.error(d.msg);}}'), array('class'=>TagExt::$statusStyle[$v->status])); ?>
                                <a class="<?=TagExt::$statusStyle[$v->status];?> dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-angle-down"></i></a>
                                <ul class="dropdown-menu" role="menu">
        							<li><?=CHtml::link('编辑', ['/admin/tag/edit','id'=>$v->id]); ?></li>
                                    <li><?=CHtml::link('查看文章', ['/admin/news/list','tag'=>$v->name]); ?></li>
                                    <li><?php echo CHtml::ajaxLink('删除', $this->createUrl('/admin/tag/ajaxDel'), ['data'=>['id'=>$v->id], 'type'=>'post', 'success'=>'js:function(d){if(d.code){location.reload();}else{toastr.error(d.msg);}}'],['data-toggle'=>'confirmation', 'data-placement'=>'right','data-title'=>'是否确认要删除“'.$v->name.'”？', 'data-btn-ok-label'=>'确认', 'data-btn-cancel-label'=>'取消', 'data-popout'=>true, 'href'=>'javascript:;']); ?></li>
        						</ul>
                            </div><?php endforeach; ?>
                        <?php else: ?>
                            暂无标签
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach;?>
            </div>
        </div>
        <!-- 直接式标签结束 -->
    </div>
</div>

<script type="text/javascript">
<?php Tools::startJs() ?>
    $('.sort_item').sortable({
        opacity: 0.6,
        revert: true,
        start: function(event, ui) {
            //拖动时会出现按钮与右侧下拉按钮分离的情况，是因为拖动时btn-group宽度窄了一点
            ui.item.width(ui.item.width()+1);
        },
        update: function(event, ui){
            ids = [];
            $(this).find('div').each(function(){
                ids.push($(this).data('id'));
            });
            $.post('<?php echo $this->createUrl("ajaxSort"); ?>', {sort: ids.join(',')}, function(d){
                if(d.code)
                    toastr.success('修改成功！');
                else
                    toastr.error('修改失败!');
            });
        }
    });
<?php Tools::endJs('dragsort'); ?>
</script>
