<?php
class CHtmlExt
{
    /**
     * 基于select2分装的多选搜索
     * @param  string $name        input名
     * @param  string $value       默认值
     * @param  string $dataInit    json
     * @param  string $ajaxUrl     请求地址
     * @param  array $htmlOptions htmlOptions
     */
    public static function multiAotucomplete($name,$initValue,$dataInit,$ajaxUrl,$htmlOptions = array(),$select2Options = array())
    {
        if(is_array($dataInit))
            $dataInit = CJSON::encode($dataInit);
        else
            $dataInit = CJSON::encode(array($dataInit));
        $htmlOptions['data-init'] = $dataInit;
        $htmlOptions['value'] = '';
        foreach(CJSON::decode($dataInit) as $k=>$v)
        {
            $htmlOptions['value'] = $k ? ','.$v['id'] : $v['id'];
        }
        $editInput = $name;
        Yii::app()->clientScript->registerScriptFile('/static/global/plugins/select2/select2.min.js', CClientScript::POS_END);
        Yii::app()->clientScript->registerScriptFile('/static/global/plugins/select2/select2_locale_zh-CN.js', CClientScript::POS_END);
        Yii::app()->clientScript->registerCssFile('/static/global/plugins/select2/select2.css');
        Yii::app()->clientScript->registerCssFile('/static/admin/pages/css/select2_custom.css');
        $options = array_merge(array(
            'placeholder' => '请选择',
            'allowClear' => true,
        ),$select2Options);
        $options = CJSON::encode($options);
        $js = <<<EOT
        $(function(){
            $('.select2').select2({$options});
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
        echo CHtml::textField($name,$initValue,$htmlOptions);
    }
}
