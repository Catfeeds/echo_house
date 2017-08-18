<?php
/**
 * 动态表单
 * @author tivon
 * @date 2015-04-25
 */
class HouseForm extends CActiveForm
{
    public function init()
    {
        parent::init();
        $this->htmlOptions['class'] = 'form-horizontal';
        $this->enableClientValidation = 'true';
        $this->clientOptions = array(
        'validateOnType'=>'true',
        'validateOnSubmit'=>'true',
        'validateOnChange'=>'true',
        'afterValidate'=>'js:function(form, data, hasError){if(hasError){for(id in data){$("#"+id).parents(".form-group").addClass("has-error");}}else{return true;}}',

        );
    }

    public function error($model,$attribute,$htmlOptions=array(),$enableAjaxValidation=true,$enableClientValidation=true)
    {
        $htmlOptions = array_merge($htmlOptions, array(
            'class'=>'help-block',
            'message'=>'测试',
            'afterValidateAttribute'=>'js:function(form, attribute, data, hasError){if(hasError){$("#"+attribute.id).parents(".form-group").addClass("has-error")}else{$("#"+attribute.id).parents(".form-group").removeClass("has-error")}}',
        ));
        $id=CHtml::activeId($model,$attribute);
        $inputID=isset($htmlOptions['inputID']) ? $htmlOptions['inputID'] : $id;
        $id = $inputID.'_em_';

        $err = $model->getError($attribute);
        if(!empty($err))
            Yii::app()->clientScript->registerScript('errTip',"$('#".$id."').parents('.form-group').addClass('has-error');");

        return parent::error($model,$attribute,$htmlOptions);
    }

    /**
     * 封装select2 Autocomplete，设定参数$htmlOptions['url']为ajax请求接口
     * $htmlOptions['data-init-text']设定初始显示的文本
     * $htmlOptions['formatResult']是一段js代码，设置下拉列表展示的格式，其中js变量item表示ajax返回数据中result的每一项
     * @param  [type] $model       [description]
     * @param  [type] $attribute   [description]
     * @param  [type] $htmlOptions [description]
     * @return [type]              [description]
     */
    public function autocomplete($model, $attribute, $htmlOptions)
    {
        CHtml::resolveNameID($model, $attribute, $htmlOptions);
        if(isset($htmlOptions['formatResult']))
        {
            $formatResult = $htmlOptions['formatResult'];
        }
        $js = '
            $("#'.$htmlOptions['id'].'").select2({
            ajax: {
                url: "'.$htmlOptions['url'].'",
                dataType: "json",
                delay: 250,
                data: function (keyword) {
                  return {
                    kw: keyword
                  };
                },
                results: function(data, page) {
                    return data;
                }, // 构造返回结果

            },

            formatSelection: function(item) {
                return item.name;//注意此处的name，要和ajax返回数组的键值一样
            }, // 选择结果中的显示
            formatResult: function(item) {
                return '.(empty($formatResult)?"item.name":$formatResult).';//注意此处的name
            }, // 搜索列表中的显示
            escapeMarkup: function (markup) { return markup; },
            minimumInputLength: 1,
            templateResult: formatRepo,
            templateSelection: formatRepoSelection,
            initSelection: function(element, callback){
                callback({id:1,name:\''.(empty($htmlOptions['data-init-text'])?'':$htmlOptions['data-init-text']).'\'});
            },
          });

            function formatRepo (repo) {
                if (repo.loading) return repo.text;

                var markup =\'<div class="clearfix">\' +
                \'<div class="col-sm-1">\' +
                \'<img src="\' + repo.owner.avatar_url + \'" style="max-width: 100%" />\' +
                \'</div>\' +
                \'<div clas="col-sm-10">\' +
                \'<div class="clearfix">\' +
                \'<div class="col-sm-6">\' + repo.full_name + \'</div>\' +
                \'<div class="col-sm-3"><i class="fa fa-code-fork"></i> \' + repo.forks_count + \'</div>\' +
                \'<div class="col-sm-2"><i class="fa fa-star"></i> \' + repo.stargazers_count + \'</div>\' +
                \'</div>\';

                if (repo.description) {
                  markup += \'<div>\' + repo.description + \'</div>\';
                }

                markup += \'</div></div>\';

                return markup;
              }

              function formatRepoSelection (repo) {
                return repo.full_name || repo.text;
              }
        ';
        Yii::app()->clientScript->registerCssFile('/static/global/plugins/select2/select2.css');
        Yii::app()->clientScript->registerCssFile('/static/global/plugins/select2/select2-bootstrap.css');
        Yii::app()->clientScript->registerScriptFile('/static/global/plugins/select2/select2.min.js',CClientScript::POS_END);
        Yii::app()->clientScript->registerScriptFile('/static/global/plugins/select2/select2_locale_zh-CN.js',CClientScript::POS_END);
        Yii::app()->clientScript->registerScript('select2'.$htmlOptions['id'], $js, CClientScript::POS_READY);
        return CHtml::activeHiddenField($model, $attribute, $htmlOptions);
    }

    /**
     * 基于select2封装的多选autocomplete
     * "data-init"，设置初始选项，二维array格式，每个元素代表一个选项，格式为[id:21,text=>'世茂香槟湖']
     * "url"，设置ajax请求URL，接收参数kw，返回格式[more:false,results:[{id:12,name:"世茂香槟湖"},{id:13,name:"武进万达"}]]
     * @param  CActiveRecord $model 模型对象实例
     * @param  string $attribute    模型对象字段名
     * @param  array  $htmlOptions html属性
     */
    public function multiAutocomplete($model, $attribute,$dataInit,$ajaxUrl,$htmlOptions = array())
    {
        if(is_array($dataInit))
            $dataInit = CJSON::encode($dataInit);
        $htmlOptions['data-init'] = $dataInit;
        $htmlOptions['value'] = '';
        foreach(CJSON::decode($dataInit) as $k=>$v)
        {
            $htmlOptions['value'] = $k ? ','.$v['id'] : $v['id'];
        }
        $editInput = get_class($model).'_'.$attribute;
        Yii::app()->clientScript->registerScriptFile('/static/global/plugins/select2/select2.min.js', CClientScript::POS_END);
        Yii::app()->clientScript->registerScriptFile('/static/global/plugins/select2/select2_locale_zh-CN.js', CClientScript::POS_END);
        Yii::app()->clientScript->registerCssFile('/static/global/plugins/select2/select2.css');
        Yii::app()->clientScript->registerCssFile('/static/admin/pages/css/select2_custom.css');

        $js = <<<EOT
        $(function(){
            $('.select2').select2({
                placeholder: '请选择',
                allowClear: true
            });
            var ajaxOptions =
            {
                url: "{$ajaxUrl}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        kw:params
                    };
                },
                results:function(data){
                    var items = [];
                    $.each(data.results,function(){
                        var tmp = {
                            id : this.id,
                            text : this.name
                        }
                        items.push(tmp);
                    });
                    return {
                        results: items
                    };
                },
                processResults: function (data, page) {
                    var items = [];
                     $.each(data.msg,function(){
                        var tmp = {
                            id : this.id,
                            text : this.title
                        }
                        items.push(tmp);
                    });
                    return {
                        results: items
                    };
                }
            }
            var edit_input = $('#{$editInput}');
            var data = {};
            if( edit_input.length && edit_input.data('init') ){
                data = eval(edit_input.data('init'));
            }

            $('#{$editInput}').select2({
                multiple:true,
                ajax: ajaxOptions,
                language: 'zh-CN',
                initSelection: function(element, callback){
                    callback(data);
                }
            });
        });
EOT;
        Yii::app()->clientScript->registerScript($editInput, $js, CClientScript::POS_END);
        echo CHtml::activeTextField($model,$attribute,$htmlOptions);
    }


    /**
     * Renders a radio button list for a model attribute.
     * This method is a wrapper of {@link CHtml::activeRadioButtonList}.
     * Please check {@link CHtml::activeRadioButtonList} for detailed information
     * about the parameters for this method.
     * @param CModel $model the data model
     * @param string $attribute the attribute
     * @param array $data value-label pairs used to generate the radio button list.
     * @param array $htmlOptions addtional HTML options.
     * @return string the generated radio button list
     */
    /*
    public function radioButtonList($model,$attribute,$data,$htmlOptions=array())
    {
        $htmlOptions = array_merge($htmlOptions,array(
            'template' => '<div class="md-radio">
                {input}
                <label for="{id}">
                <span></span>
                <span class="check"></span>
                <span class="box"></span>
                {labelTitle}</label>
            </div>',
            'class' => 'md-radiobtn'
        ));
        return self::activeRadioButtonList($model,$attribute,$data,$htmlOptions);
    }

    /**
     * 重写{CHtml::activeRadioButtonList()}
     */
    /*
    public static function activeRadioButtonList($model,$attribute,$data,$htmlOptions=array())
    {
        CHtml::resolveNameID($model,$attribute,$htmlOptions);
        $selection=CHtml::resolveValue($model,$attribute);
        if($model->hasErrors($attribute))
            CHtml::addErrorCss($htmlOptions);
        $name=$htmlOptions['name'];
        unset($htmlOptions['name']);

        if(array_key_exists('uncheckValue',$htmlOptions))
        {
            $uncheck=$htmlOptions['uncheckValue'];
            unset($htmlOptions['uncheckValue']);
        }
        else
            $uncheck='';

        $hiddenOptions=isset($htmlOptions['id']) ? array('id'=>CHtml::ID_PREFIX.$htmlOptions['id']) : array('id'=>false);
        $hidden=$uncheck!==null ? CHtml::hiddenField($name,$uncheck,$hiddenOptions) : '';

        return self::CHtmlradioButtonList($name,$selection,$data,$htmlOptions);
    }*/

    /**
     * 重写{@link CHtml::radioButtonList}
     */
    /*
    public static function CHtmlradioButtonList($name,$select,$data,$htmlOptions=array())
    {
        $template=isset($htmlOptions['template'])?$htmlOptions['template']:'{input} {label}';
        $separator=isset($htmlOptions['separator'])?$htmlOptions['separator']:"<br/>\n";
        $container=isset($htmlOptions['container'])?$htmlOptions['container']:'span';
        unset($htmlOptions['template'],$htmlOptions['separator'],$htmlOptions['container']);

        $labelOptions=isset($htmlOptions['labelOptions'])?$htmlOptions['labelOptions']:array();
        unset($htmlOptions['labelOptions']);

        if(isset($htmlOptions['empty']))
        {
            if(!is_array($htmlOptions['empty']))
                $htmlOptions['empty']=array(''=>$htmlOptions['empty']);
            $data=array_merge($htmlOptions['empty'],$data);
            unset($htmlOptions['empty']);
        }

        $items=array();
        $baseID=isset($htmlOptions['baseID']) ? $htmlOptions['baseID'] : CHtml::getIdByName($name);
        unset($htmlOptions['baseID']);
        $id=0;
        foreach($data as $value=>$labelTitle)
        {
            $checked=!strcmp($value,$select);
            $htmlOptions['value']=$value;
            $htmlOptions['id']=$baseID.'_'.$id++;
            $option=CHtml::radioButton($name,$checked,$htmlOptions);
            $beginLabel=CHtml::openTag('label',$labelOptions);
            $label=CHtml::label($labelTitle,$htmlOptions['id'],$labelOptions);
            $endLabel=CHtml::closeTag('label');
            $items[]=strtr($template,array(
                '{input}'=>$option,
                '{beginLabel}'=>$beginLabel,
                '{label}'=>$label,
                '{labelTitle}'=>$labelTitle,
                '{endLabel}'=>$endLabel,
                '{k}'=>$id-1,
                '{id}'=>$htmlOptions['id'],
            ));
        }
        if(empty($container))
            return implode($separator,$items);
        else
            return CHtml::tag('div',array('class'=>'md-radio-inline'),CHtml::tag($container,array('id'=>$baseID),implode($separator,$items)));
    }*/
}
 ?>
