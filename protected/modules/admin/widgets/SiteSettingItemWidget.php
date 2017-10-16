<?php
/**
 * 站点配置自定义空间设置
 * @author tivon
 * @version 2016-10-25
 */
class SiteSettingItemWidget extends CWidget
{
    /**
     * 配置模型对象
     * @var BaseConfigExt
     */
    public $model;
    /**
     * 配置模型对应的属性名称
     * @var string
     */
    public $attribute;
    /**
     * 当前页面form表单widget对象
     * @var CActiveForm
     */
    public $form;

    public static $simpleType = [
        'textField', 'dropDownList', 'textArea', 'radioButtonList',
         'hiddenField','checkBox','listBox','fileUpload'];
    /**
     * 当该配置元素为简单类型的控件时，通过此选项来配置空间的html选项
     * $htmlOptions['data']: 将被用于dropDownList、radioButtonList、listBox的供选择项
     * @var array
     */
    public $htmlOptions = [];

    public function run()
    {
        if($this->getIsEnabled()) {
            echo $this->getHtml();
        }
    }

    public function getHtml()
    {
        $html = $this->getSingleWidget();
        return $html;
    }

    /**
     * 获取单个控件Html
     * 多选项会根据配置项{@see SiteSettingExt}中定义的data来生成html
     * 若控件类型不是定义的几种，则将控件类型视为模板去加载
     * 自定义生成多个控件时，模板最外层需要加<div class="single"></div>
     * @return string
     */
    public function getSingleWidget()
    {
        $htmlOptions = array_merge($this->model->{$this->attribute}->htmlOptions, $this->htmlOptions);
        $widgetType = $this->model->{$this->attribute}->widgetType;
        if(in_array($widgetType, self::$simpleType)) {
            //定义基本模板
            if(isset($htmlOptions['template'])) {
                $template = $htmlOptions['template'];
                unset($htmlOptions['template']);
            } else {
                $template = '{input}';
            }
            //radioButtonList样式单独处理
            if($widgetType=='radioButtonList') {
                $htmlOptions = array_merge($htmlOptions, ['separator'=>'&nbsp;']);
                $template = CHtml::tag('div', ['class'=>'radio-list'], $template);
            }
            //根据控件类型判断生成对应html
            switch ($widgetType) {
                case 'textField'://no break;
                case 'textArea'://no break;
                case 'checkBox'://no break;
                case 'hiddenField':
                    //都是单项
                    $html = $this->form->$widgetType($this->model, $this->attribute, $htmlOptions);
                    return strtr($template, ['{input}'=>$html]);
                    break;
                case 'dropDownList'://no break;
                case 'listBox'://no break
                case 'radioButtonList'://no break
                    $data = $this->model->{$this->attribute}->data;
                    $html = $this->form->$widgetType($this->model, $this->attribute, $data, $htmlOptions);
                    return strtr($template, ['{input}'=>$html]);
                    break;
                case 'fileUpload':
                    return $this->render('SiteSettingItemWidgetFileUpload', null, true);
            }
        } elseif($this->getViewFile($widgetType)!==false) {
            return $this->render($widgetType, null, true);
        }
        return '';
    }

    /**
     * 是否启用
     * @return boolean
     */
    public function getIsEnabled()
    {
        return $this->form!==null && $this->attribute!==null && $this->model!==null;
    }

    /**
     * 对于自定义模板提供的删除按钮
     * 样式class为del，可自定义点击后的回调函数，默认删除按钮所在行的父节点
     * @param string $callback js回调函数代码
     */
    public function addDelButton($callback = '')
    {

        if(!$callback) {
            $callback = <<<EOT
            function(){
                $(this).parent().remove();
            }
EOT;
        }

        $clickEvent = <<<EOT
        $(".del").live('click',{$callback});
EOT;
        Yii::app()->clientScript->registerScript('deljs', $clickEvent, CClientScript::POS_END);
        return '<div class="del help-inline" style="cursor: pointer"><i class="glyphicon glyphicon-remove"></i>删除</div>';
    }

    public function addMoveButton($shangyicallback = '', $xiayicallback='')
    {

        if(!$shangyicallback) {
            $shangyicallback = <<<EOT
            function(){
                var row = $(this).parent();
                if(row.index()!==0) {
                    row.fadeOut().fadeIn();
                    row.prev().before(row);
                }
            }
EOT;
        }


        if(!$xiayicallback) {
            $xiayicallback = <<<EOT
            function(){
                var row = $(this).parent();
                var len = $('.xiayi').length;
                if (row.index() != len - 1) {
                    row.fadeOut().fadeIn();
                    row.next().after(row);
                }
            }
EOT;
        }

        $clickEvent = <<<EOT
        $(".shangyi").live('click',{$shangyicallback});
        $(".xiayi").live('click',{$xiayicallback});
EOT;
        Yii::app()->clientScript->registerScript('movejs', $clickEvent, CClientScript::POS_END);
        return '<div class="shangyi help-inline" style="cursor: pointer"><i class="fa fa-caret-up"></i>上移</div><div class="xiayi help-inline" style="cursor: pointer"><i class="fa fa-caret-down"></i>下移</div>';
    }

    /**
     * 压缩HTML代码
     * @param  string $html 要压缩的html代码原文
     * @return string       压缩过后的html代码
     */
    public function compressHtml($html)
    {
        $html=str_replace("\r\n",'',$html);//清除换行符
        $html=str_replace("\n",'',$html);//清除换行符
        $html=str_replace("\t",'',$html);//清除制表符
        $pattern=array(
            "/> *([^ ]*) *</",//去掉注释标记
            "/[\s]+/",
            "/<!--[^!]*-->/",
            "/\" /",
            "/ \"/",
            "'/\*[^*]*\*/'"
        );
        $replace=array (
            ">\\1<",
            " ",
            "",
            "\"",
            "\"",
            ""
        );
        return preg_replace($pattern, $replace, $html);
    }
}
