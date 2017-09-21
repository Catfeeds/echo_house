<?php
/**
 * Created by PhpStorm.
 * User: wanggris
 * Date: 15-9-12
 * Time: 上午11:55
 */
$this->pageTitle = '编辑房源';
$this->breadcrumbs = array($this->pageTitle);
$maps = array('zoom' => 14, 'lat' => SiteExt::getAttr('qjpz','map_lat') ? SiteExt::getAttr('qjpz','map_lat') : "31.810077", 'lng' => SiteExt::getAttr('qjpz','map_lng') ? SiteExt::getAttr('qjpz','map_lng') : "119.974454");
$parentArea = AreaExt::model()->parent()->normal()->findAll();
$parent = $plot->area?$plot->area:(isset($parentArea[0])?$parentArea[0]->id:0);
$childArea = $parent ? AreaExt::model()->getByParent($parent)->normal()->findAll() : array(0=>'--无子分类--');
?>
<?php $this->widget('ext.ueditor.UeditorWidget',array('id'=>'PlotExt_peripheral','options'=>"toolbars:[['fullscreen','source','undo','redo','|','customstyle','paragraph','fontfamily','fontsize'],
        ['bold','italic','underline','fontborder','strikethrough','superscript','subscript','removeformat',
        'formatmatch', 'autotypeset', 'blockquote', 'pasteplain','|',
        'forecolor','backcolor','insertorderedlist','insertunorderedlist','|',
        'rowspacingtop','rowspacingbottom', 'lineheight','|',
        'directionalityltr','directionalityrtl','indent','|'],
        ['justifyleft','justifycenter','justifyright','justifyjustify','|','link','unlink','|',
        'insertimage','emotion','scrawl','insertvideo','music','attachment','map',
        'insertcode','|',
        'horizontal','inserttable','|',
        'print','preview','searchreplace']]")); ?>
<?php $this->widget('ext.ueditor.UeditorWidget',array('id'=>'PlotExt_dk_rule','options'=>"toolbars:[['fullscreen','source','undo','redo','|','customstyle','paragraph','fontfamily','fontsize'],
        ['bold','italic','underline','fontborder','strikethrough','superscript','subscript','removeformat',
        'formatmatch', 'autotypeset', 'blockquote', 'pasteplain','|',
        'forecolor','backcolor','insertorderedlist','insertunorderedlist','|',
        'rowspacingtop','rowspacingbottom', 'lineheight','|',
        'directionalityltr','directionalityrtl','indent','|'],
        ['justifyleft','justifycenter','justifyright','justifyjustify','|','link','unlink','|',
        'insertimage','emotion','scrawl','insertvideo','music','attachment','map',
        'insertcode','|',
        'horizontal','inserttable','|',
        'print','preview','searchreplace']]")); ?>
<?php $form = $this->beginWidget('HouseForm',array('htmlOptions'=>array('class'=>'form-horizontal'),'enableAjaxValidation'=>false)) ?>

<div class="tabbale">
    <ul class="nav nav-tabs nav-tabs-lg">
        <li class="active">
            <a href="#tab_1" data-toggle="tab"> 基本信息 </a>
        </li>
        <li>
            <a href="#tab_2" data-toggle="tab"> 楼盘参数 </a>
        </li>
        <li>
            <a href="#tab_3" data-toggle="tab"> 其他参数 </a>
        </li>
    </ul>
    <div class="tab-content col-md-12" style="padding-top:20px;">
    <!-- 基本信息 -->
    <div class="tab-pane col-md-12 active" id="tab_1">
        <!-- 基本信息左侧 -->
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-2 control-label text-nowrap">楼盘名称<span class="required" aria-required="true">*</span></label>
                <div class="col-md-10">
                    <!--<input type="text" class="form-control" placeholder="">-->
                    <?php echo $form->textField($plot,'title',array('class'=>'form-control','data-target'=>'pointname','onblur'=>'checkName(this)')); ?>
                    <span class="help-block"><?php echo $form->error($plot, 'title'); ?></span>
                </div>
                <div class="col-md-12"></div>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label text-nowrap">微信分享标题</label>
                <div class="col-md-10">
                    <!--<input type="text" class="form-control" placeholder="">-->
                    <?php echo $form->textField($plot,'wx_share_title',array('class'=>'form-control','data-target'=>'pointname')); ?>
                    <span class="help-block"><?php echo $form->error($plot, 'wx_share_title'); ?></span>
                </div>
                <div class="col-md-12"></div>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label text-nowrap">物业类型</label>
                <div class="col-md-10">
                    <?php echo $form->dropDownList($plot, 'wylx',  CHtml::listData(TagExt::model()->getTagByCate('wylx')->normal()->findAll(),'id','name'), array('class'=>'form-control select2','multiple'=>'multiple')); ?>
                </div>
                <div class="col-md-12"><?php echo $form->error($plot, 'wylx'); ?></div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label text-nowrap">销售状态</label>
                <div class="col-md-10">
                    <?php echo $form->dropDownList($plot, 'sale_status', CHtml::listData(TagExt::model()->getTagByCate('xszt')->normal()->findAll(),'id','name'), array('class'=>'form-control', 'empty'=>array($plot->sale_status=>'请选择销售状态'))); ?>
                    <span class="help-block"><?php echo $form->error($plot, 'sale_status'); ?></span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label text-nowrap">楼盘状态</label>
                <div class="col-md-10">
                    <div class="radio-list">
                        <?php echo $form->radioButtonList($plot,'status', PlotExt::$status,array('class'=>'radio-inline', 'separator'=>'&nbsp;&nbsp;','template'=>'<label>{input} {label}</label>')) ?>
                    </div>
                </div>
                <div class="col-md-12"><?php echo $form->error($plot, 'status'); ?></div>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label text-nowrap">楼盘封面</label>
                <div class="col-md-10">
                    <div id="uploader" class="wu-example">
                        <div class="btns">
                            <!--<div id="cover_img">选择文件</div>-->
                            <?php $this->widget('FileUpload',array('model'=>$plot,'attribute'=>'image','inputName'=>'image','width'=>'300','removeCallback'=>"$('#image').html('')")); ?>
                        </div>
                    </div>
                    <div id="singlePicyw1"></div>
                </div>
                <div class="col-md-12"><?php echo $form->error($plot, 'image'); ?></div>
            </div>
            <?php if(Yii::app()->user->id==1):?>
            <div class="form-group">
                <label class="col-md-2 control-label text-nowrap">总代公司</label>
                <div class="col-md-10">
                    <?php echo $form->dropDownList($plot, 'zd_company',  CHtml::listData(CompanyExt::model()->normal()->findAll('type=1'),'id','name'), array('class'=>'form-control select2')); ?>
                </div>
                <div class="col-md-12"><?php echo $form->error($plot, 'zd_company'); ?></div>
            </div>
            <?php endif;?>
            <div class="form-group">
                <label class="col-md-2 control-label text-nowrap">代理类型</label>
                <div class="col-md-10">
                    <div class="radio-list">
                        <?php echo $form->radioButtonList($plot,'dllx', Yii::app()->params['dllx'],array('class'=>'radio-inline', 'separator'=>'&nbsp;&nbsp;','template'=>'<label>{input} {label}</label>')) ?>
                    </div>
                </div>
                <div class="col-md-12"><?php echo $form->error($plot, 'dllx'); ?></div>
            </div>
            <?php if(Yii::app()->user->id==1):?>
            <div class="form-group">
                <label class="col-md-2 control-label text-nowrap">对接人</label>
                <div class="col-md-10">
                    <?php echo $form->textField($plot,'market_users',array('class'=>'form-control','placeholder'=>'格式为：张三13861111111 多个用空格隔开')); ?>
                </div>
                <div class="col-md-12"><?php echo $form->error($plot, 'market_users'); ?></div>
            </div>
        <?php endif;?>
             <div class="form-group">
                <label class="col-md-2 control-label text-nowrap">指定对接</label>
                <div class="col-md-10">
                    <?php echo $form->textField($plot,'market_user',array('class'=>'form-control','placeholder'=>'格式为：张三13861111111')); ?>
                </div>
                <div class="col-md-12"><?php echo $form->error($plot, 'market_users'); ?></div>
            </div>
            
            <div class="form-group">
                <label class="col-md-2 control-label text-nowrap">楼盘卖点</label>
                <div class="col-md-10">
                    <?php echo $form->textarea($plot, 'peripheral'); ?>
                </div>
                <div class="col-md-12"><?php echo $form->error($plot, 'peripheral'); ?></div>
            </div>

        </div>
        <!-- 基本信息右侧 -->
        <div class="col-md-6">
        <div class="form-group">
                <div class="form-group">
                    <label class="col-md-2 control-label text-nowrap">楼盘拼音<span class="required" aria-required="true">*</span></label>
                    <div class="col-md-5">
                        <?php echo $form->textField($plot,'pinyin',array('class'=>'form-control')); ?>
                        <span class="help-block"><?php echo $form->error($plot, 'pinyin'); ?></span>
                    </div>
                    <label class="col-md-2 control-label text-nowrap">首字母<span class="required" aria-required="true">*</span></label>
                    <div class="col-md-3">
                        <?php echo $form->textField($plot,'fcode',array('class'=>'form-control')); ?>
                        <span class="help-block"><?php echo $form->error($plot, 'fcode'); ?></span>
                    </div>
                </div>
            </div>
        <div class="form-group">
                <label class="col-md-2 control-label text-nowrap">所在区域<span class="required" aria-required="true">*</span></label>
                <div class="col-md-10">
                    <?php
                    echo $form->dropDownList($plot , 'area' ,CHtml::listData($parentArea,'id','name') , array(
                            'class'=>'form-control input-inline',
                            'ajax' =>array(
                                'url' => Yii::app()->createUrl('admin/area/ajaxGetArea'),
                                'update' => '#PlotExt_street',
                                'data'=>array('area'=>'js:this.value'),
                            )
                        )
                    );
                    ?>
                    <?php
                    echo $form->dropDownList($plot , 'street' ,$childArea ? CHtml::listData($childArea,'id','name'):array(0=>'--无子分类--') , array('class'=>'form-control input-inline'));
                    ?>
                    <span class="help-block"><?php echo $form->error($plot, 'area').$form->error($plot, 'street'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label text-nowrap">楼盘地址<span class="required" aria-required="true">*</span></label>
                <div class="col-md-10">
                    <?php echo $form->textField($plot,'address',array('class'=>'form-control')); ?>
                </div>
                <div class="col-md-12"><?php echo $form->error($plot, 'address'); ?></div>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label text-nowrap">地图坐标</label>
                <div class="col-md-10">
                    <button type="button" class="btn green-meadow show-map" data-toggle="modal" href="#large">地图标识</button>
                    <span id="coordText" style="padding-left:10px;">
                       <?php if($plot->map_lng || $plot->map_lat): ?>
                           经度：<?php echo $plot->map_lng!=0 ? $plot->map_lng : $maps['lng']; ?> &nbsp;
                           纬度：<?php echo $plot->map_lat!=0 ? $plot->map_lat : $maps['lat']; ?>
                       <?php endif;?>
                    </span>
                </div>
                <div class="col-md-12"><?php echo $form->error($plot, 'map_lng'); ?><?php echo $form->error($plot, 'map_lat'); ?></div>
            </div>
            

            

            <div class="form-group">
                <label class="col-md-2 control-label text-nowrap">装修状态</label>
                <div class="col-md-10">
                    <?php echo $form->dropDownList($plot, 'zxzt',  CHtml::listData(TagExt::model()->getTagByCate('zxzt')->normal()->findAll(),'id','name'), array('class'=>'form-control select2','multiple'=>'multiple')); ?>
                </div>
                <div class="col-md-12"><?php echo $form->error($plot, 'zxzt'); ?></div>
            </div>

            <div class="form-group">
                <div class="col-md-12" style="margin-left: 44px">
                    <?php echo $form->dropDownList($plot,'price_mark',PlotExt::$mark,array('class'=>'form-control input-inline','style'=>'width:auto;')); ?>
                    <?php echo $form->textField($plot,'price',array('class'=>'form-control input-inline')); ?>
                    <?php echo $form->dropDownList($plot,'unit',PlotExt::$unit,array('class'=>'form-control input-small input-inline')); ?>
                </div>
                <div class="col-md-12"><?php echo $form->error($plot, 'price') ?></div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label text-nowrap">首付比例</label>
                <div class="col-md-10">
                    <?php echo $form->dropDownList($plot, 'sfprice',  CHtml::listData(TagExt::model()->getTagByCate('sfprice')->normal()->findAll(),'id','name'), array('class'=>'form-control select2','multiple'=>'multiple')); ?>
                </div>
                <div class="col-md-12"><?php echo $form->error($plot, 'sfprice'); ?></div>
            </div>
            
            
            <div class="form-group">
                <label class="col-md-2 control-label text-nowrap">带看规则</label>
                <div class="col-md-10">
                    <?php echo $form->textarea($plot,'dk_rule'); ?>
                </div>
                <div class="col-md-12"><?php echo $form->error($plot, 'dk_rule'); ?></div>
            </div>
        </div>
    </div>

    <!-- 楼盘详情 -->
    <div class="tab-pane col-md-12" id="tab_2">

        <div class="col-md-6">
        <div class="form-group">
                <label class="col-md-2 control-label text-nowrap">开盘时间</label>
                <div class="col-md-6">
                    <div class="input-group date form_datetime" >
                        <?php echo $form->textField($plot,'open_time',array('class'=>'form-control','value'=>($plot->open_time?date('Y-m-d',$plot->open_time):''))); ?>
                        <span class="input-group-btn">
                          <button class="btn default date-set" type="button"><i class="fa fa-calendar"></i></button>
                       </span>
                    </div>
                    <span class="help-inline">留空即为已开盘</span>
                </div>
                <div class="col-md-2">
                    <span class="help-inline"> </span>
                </div>
                <div class="col-md-12"></div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label text-nowrap">交付时间</label>
                <div class="col-md-6">
                    <div class="input-group date form_datetime">
                        <?php echo $form->textField($plot,'delivery_time',array('class'=>'form-control','value'=>($plot->delivery_time?date('Y-m-d',$plot->delivery_time):''))); ?>
                        <span class="input-group-btn">
                          <button class="btn default date-set" type="button"><i class="fa fa-calendar"></i></button>
                       </span>
                    </div>
                </div>
                <div class="col-md-2">
                    <span class="help-inline"> </span>
                </div>
                <div class="col-md-12"></div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label text-nowrap">占地面积</label>
                <div class="col-md-10">
                    <?php echo $form->textField($plot,'size',array('class'=>'form-control input-inline')); ?>
                    <span class="help-inline"> ㎡ </span>
                </div>
                <div class="col-md-12"><?php echo $form->error($plot, 'size'); ?></div>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label text-nowrap">建筑面积</label>
                <div class="col-md-10">
                    <?php echo $form->textField($plot,'buildsize',array('class'=>'form-control input-inline')); ?>
                    <span class="help-inline"> ㎡ </span>
                </div>
                <div class="col-md-12"><?php echo $form->error($plot, 'buildsize'); ?></div>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label text-nowrap">容积率</label>
                <div class="col-md-10">
                    <?php echo $form->textField($plot,'capacity',array('class'=>'form-control input-inline')); ?>
                    <span class="help-inline">  </span>
                </div>
                <div class="col-md-12"><?php echo $form->error($plot, 'capacity'); ?></div>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label text-nowrap">绿化率</label>
                <div class="col-md-10">
                    <?php echo $form->textField($plot,'green',array('class'=>'form-control input-inline')); ?>
                    <span class="help-inline"> % </span>
                </div>
                <div class="col-md-12"><?php echo $form->error($plot, 'green'); ?></div>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label text-nowrap">产权年限</label>
                <div class="col-md-10">
                    <?php echo $form->textField($plot,'property_years',array('class'=>'form-control input-inline','placeholder'=>'70年，50年')); ?>
                </div>
                <div class="col-md-12"><?php echo $form->error($plot, 'property_years'); ?></div>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label text-nowrap">总户数</label>
                <div class="col-md-10">
                    <?php echo $form->textField($plot,'household_num',array('class'=>'form-control input-inline')); ?>
                </div>
                <div class="col-md-12"><?php echo $form->error($plot, 'household_num'); ?></div>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label text-nowrap">楼栋总数</label>
                <div class="col-md-10">
                    <?php echo $form->textField($plot,'building_num',array('class'=>'form-control input-inline')); ?>
                </div>
                <div class="col-md-12"><?php echo $form->error($plot, 'building_num'); ?></div>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label text-nowrap">楼层状况</label>
                <div class="col-md-10">
                    <?php echo $form->TextArea($plot,'floor_desc',array('class'=>'form-control','rows'=>5)); ?>
                </div>
                <div class="col-md-12"><?php echo $form->error($plot, 'floor_desc'); ?></div>
            </div>

        </div>
        <div class="col-md-6">
            
            <div class="form-group">
                <label class="col-md-2 control-label text-nowrap">交通状况</label>
                <div class="col-md-10">
                    <?php echo $form->TextArea($plot, 'transit', array('class'=>'form-control','rows'=>5)); ?>
                </div>
                <div class="col-md-12"><?php echo $form->error($plot, 'transit'); ?></div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label text-nowrap">开发商</label>
                <div class="col-md-10">
                    <?php echo $form->textField($plot,'developer',array('class'=>'form-control')); ?>
                </div>
                <div class="col-md-12"><?php echo $form->error($plot, 'developer'); ?></div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label text-nowrap">品牌商</label>
                <div class="col-md-10">
                    <?php echo $form->textField($plot,'brand',array('class'=>'form-control')); ?>
                </div>
                <div class="col-md-12"><?php echo $form->error($plot, 'brand'); ?></div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label text-nowrap">楼盘电话</label>
                <div class="col-md-10">
                    <?php echo $form->textField($plot,'sale_tel',array('class'=>'form-control')); ?>
                </div>
                <div class="col-md-12"><?php echo $form->error($plot, 'sale_tel'); ?></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-2 control-label text-nowrap">物业公司</label>
                <div class="col-md-10">
                    <?php echo $form->textField($plot,'manage_company',array('class'=>'form-control')); ?>
                </div>
                <div class="col-md-12"><?php echo $form->error($plot, 'manage_company'); ?></div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label text-nowrap">物业费</label>
                <div class="col-md-10">
                    <?php echo $form->textField($plot,'manage_fee',array('class'=>'form-control input-inline')); ?>
                    <span class="help-inline"> 元/m²•月 </span>
                </div>
                <div class="col-md-12"><?php echo $form->error($plot, 'manage_fee'); ?></div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label text-nowrap">车位数</label>
                <div class="col-md-10">
                    <?php echo $form->textField($plot,'carport',array('class'=>'form-control input-inline')); ?>
                </div>
                <div class="col-md-12"><?php echo $form->error($plot, 'carport'); ?></div>
            </div>
        </div>
    </div>

    <div class="tab-pane col-md-12" id="tab_3">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-2 control-label text-nowrap">项目特色</label>
                <div class="col-md-10">
                <?php echo $form->textField($plot,'xmts',array('class'=>'form-control','data-target'=>'pointname')); ?>
                </div>
                <div class="col-md-12"><?php echo $form->error($plot, 'xmts'); ?></div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label text-nowrap">项目介绍</label>
                <div class="col-md-10">
                    <?php echo $form->textArea($plot, 'content', array('id'=>'content','class'=>'form-control','rows'=>5)); ?>
                </div>
                <div class="col-md-12"><?php echo $form->error($plot, 'content'); ?></div>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label text-nowrap">建造年代</label>
                <div class="col-md-10">
                    <?php echo $form->textField($plot,'build_year',array('class'=>'form-control input-inline'))?>
                </div>
                <div class="col-md-12"><?php echo $form->error($plot,'build_year')?></div>
            </div>

            

        </div>
    </div>
    <div class="tab-pane col-md-12" id="tab_4">
        <div class="form-group">
            <label class="col-md-2 control-label text-nowrap">结佣规则</label>
            <div class="col-md-10">
                <?php echo $form->textField($plot,'jy_rule',array('class'=>'form-control' ,  'placeholder'=>'')); ?>
            </div>
            <div class="col-md-12"><?php echo $form->error($plot, 'jy_rule'); ?></div>
        </div>

        <div class="form-group">
            <label class="col-md-2 control-label text-nowrap">开发商规则</label>
            <div class="col-md-10">
                <?php echo $form->textField($plot,'kfs_rule',array('class'=>'form-control' ,  'placeholder'=>'')); ?>
            </div>
            <div class="col-md-12"><?php echo $form->error($plot, 'kfs_rule'); ?></div>
        </div>
    </div>

    <div class="col-md-12 center-block text-center">
        <div class="btn-group text-center">
            <button class="btn green-meadow col-md-offset-4">提交</button>
            <a href = "<?php echo $this->createUrl('/admin/plot/list')?>" class="btn default col-md-offset-4">返回</a>
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
                    经度：<input id="lng" readonly_="readonly" size="20" name="PlotExt[map_lng]" type="text" value="<?php echo $plot->map_lng!=0 ? $plot->map_lng : $maps['lng']; ?>">                    纬度：<input id="lat" readonly_="readonly" size="20" name="PlotExt[map_lat]" type="text" value="<?php echo $plot->map_lat!=0 ? $plot->map_lat : $maps['lat']; ?>">                    缩放：<input type="text" name="PlotExt[map_zoom]" id="zoom" readonly="readonly" size="2" value="<?php echo $plot->map_zoom!=0 ? $plot->map_zoom : $maps['zoom']; ?>"/>
                    搜索：<input type="text" name="local" id="local" value="" size="15">
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
<?php
Yii::app()->clientScript->registerScriptFile('/static/global/plugins/bootbox/bootbox.min.js', CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile('/static/global/plugins/bmap.js', CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile('/static/admin/pages/scripts/map.js', CClientScript::POS_END);
?>

<?php $this->endWidget(); ?>

<?php
//Select2
Yii::app()->clientScript->registerScriptFile('/static/global/plugins/select2/select2.min.js', CClientScript::POS_END);
Yii::app()->clientScript->registerCssFile('/static/global/plugins/select2/select2.css');
Yii::app()->clientScript->registerCssFile('/static/admin/pages/css/select2_custom.css');

//boostrap datetimepicker
Yii::app()->clientScript->registerCssFile('/static/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css');
Yii::app()->clientScript->registerScriptFile('/static/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js', CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile('/static/global/plugins/bootstrap-datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js', CClientScript::POS_END, array('charset'=> 'utf-8'));

// Yii::app()->clientScript->registerScriptFile('/static/global/plugins/bootbox/bootbox.min.js', CClientScript::POS_END);

$js = "
            $(function(){
               $('.select2').select2({
                  placeholder: '请选择',
                  allowClear: true
               });

                 $('.form_datetime').datetimepicker({
                     autoclose: true,
                     isRTL: Metronic.isRTL(),
                     format: 'yyyy-mm-dd',
                     minView: 'month',
                     language: 'zh-CN',
                     pickerPosition: (Metronic.isRTL() ? 'bottom-right' : 'bottom-left'),
                 });

            });


            ";

Yii::app()->clientScript->registerScript('add',$js,CClientScript::POS_END);
?>
<?php
//Yii::app()->clientScript->registerScriptFile('/static/admin/pages/scripts/union-select.js', CClientScript::POS_END);
//Yii::app()->clientScript->registerScriptFile('/static/admin/pages/scripts/union-select.js', CClientScript::POS_END);
?>
