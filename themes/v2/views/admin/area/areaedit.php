<?php
/**
 * 区域添加编辑
 * @author shichang
 * @date 2015-09-11
 */
$this->pageTitle = '编辑区域';
$this->breadcrumbs = array('区域管理' => '/admin/area/arealist', $this->pageTitle);
?>
<?php $form = $this->beginWidget('HouseForm', array('htmlOptions' => array('class' => 'form-horizontal'))) ?>
    <div class="form-group">
        <label class="col-md-2 control-label">区域名称</label>
        <div class="col-md-4">
            <?php echo $form->textField($area, 'name', array('class' => 'form-control')) ?>
        </div>
        <div class="col-md-2"><?php echo $form->error($area, 'name') ?></div>
    </div>
    <div class="form-group">
        <label class="col-md-2 control-label">父分类<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            <?php echo $form->dropDownList($area, 'parent', $catelist, array('class' => 'form-control', 'multiple' => false, 'encode' => false)) ?>
        </div>
        <div class="col-md-2"><?php echo $form->error($area, 'parent') ?></div>
    </div>
    <div class="form-group">
        <label class="col-md-2 control-label">区域拼音</label>
        <div class="col-md-4">
            <?php echo $form->textField($area, 'pinyin', array('class' => 'form-control')) ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-2 control-label">区域排序</label>
        <div class="col-md-4">
            <?php echo $form->textField($area, 'sort', array('class' => 'form-control')) ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-2 control-label">区域状态</label>
        <div class="col-md-4 radio-list">
        <?php echo $form->radioButtonList($area, 'status', AreaExt::$status, array('separator' => '', 'template'=>'<label>{input} {label}</label>')) ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-2 control-label text-nowrap">地图坐标</label>
        <div class="col-md-10">
            <button type="button" class="btn green-meadow show-map" data-toggle="modal" href="#large">地图标识</button>
            <span id="coordText" style="padding-left:10px;">
                   经度：<?php echo $area->map_lng!=0 ? $area->map_lng : $maps['lng']; ?> &nbsp;
                   纬度：<?php echo $area->map_lat!=0 ? $area->map_lat : $maps['lat']; ?>
            </span>
        </div>
        <div class="col-md-12"></div>
    </div>
    <div class="form-actions">
        <div class="row">
            <div class="col-md-offset-3 col-md-9">
                <button type="submit" class="btn green">保存</button>
                <?php echo CHtml::link('返回', $this->createUrl('arealist'), array('class' => 'btn default')) ?>
            </div>
        </div>
    </div>


    <div class="modal fade bs-modal-lg" id="large" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">标识地图</h4>
                </div>
                <div class="modal-body">
                    <p>
                        经度：<input id="lng" readonly_="readonly" size="20" name="AreaExt[map_lng]" type="text" value="<?php echo $area->map_lng!=0 ? $area->map_lng : $maps['lng']; ?>">                    纬度：<input id="lat" readonly_="readonly" size="20" name="AreaExt[map_lat]" type="text" value="<?php echo $area->map_lat!=0 ? $area->map_lat : $maps['lat']; ?>">                    缩放：<input type="text" name="AreaExt[map_zoom]" id="zoom" readonly="readonly" size="2" value="<?php echo $area->map_zoom!=0 ? $area->map_zoom : $maps['zoom']; ?>"/>
                        搜索：<input type="text" name="local" id="local" value="" size="15" onkeypress="return false;">
                        <input type="button" onclick="getLatLng($('#local').val());" value="搜索" class="cancel">
                    </p>
                    <div id="map" style="height:400px; width:100%"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="changeCoordText()" class="btn blue" data-dismiss="modal">保存</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
<?php $this->endWidget(); ?>

<?php
Yii::app()->clientScript->registerScriptFile('/static/global/plugins/bmap.js', CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile('/static/admin/pages/scripts/map.js', CClientScript::POS_END);

 ?>
