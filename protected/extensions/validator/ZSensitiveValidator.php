<?php
/**
 * 敏感词检测.
 *
 */
class ZSensitiveValidator extends CValidator
{
    /**
     * @var array 敏感词数组,为null，则不进行敏感词检测
     */
    public $senstiveArray;

    /**
     * Validates the attribute of the object.
     * If there is any error, the error message is added to the object.
     * @param CModel $object the object being validated
     * @param string $attribute the attribute being validated
     */
    protected function validateAttribute($object,$attribute)
    {
        $content = $object->$attribute;
        $words ='';
        if($this->senstiveArray&&is_array($this->senstiveArray)&&count($this->senstiveArray)>0){
            foreach($this->senstiveArray as $v){
                if($v!==''&&strpos($content,$v)>0){
                    $words.=$v.',';
                }
            }
        }
        if(strlen($words)>0){
            $message=$this->message!==null?$this->message:Yii::t('yii','{attribute}不能包含敏感字：{words}',array('{words}'=>$words));
            $this->addError($object,$attribute,$message);
        }
    }

    /**
     * Returns the JavaScript needed for performing client-side validation.
     * @param CModel $object the data object being validated
     * @param string $attribute the name of the attribute to be validated.
     * @return string the client-side validation script.
     * @see CActiveForm::enableClientValidation
     * @since 1.1.7
     */
    public function clientValidateAttribute($object,$attribute)
    {
        if($this->senstiveArray&&is_array($this->senstiveArray)&&count($this->senstiveArray)>0)
        {
            $senstive=CJSON::encode($this->senstiveArray);
            $str = <<< EOD
            var sensitive={$senstive},words='';
            var content = $('iframe').contents().find('body').html();
            $.each(sensitive,function(n,e){
                if(content.indexOf(e)>0){
                    if(words) words+= ',';
                    words+=e;
                }
            })
           if(words.length>0){
               words='不能包含敏感字：'+words;
               messages.push(words);
               setTimeout(function(){
                   Metronic.scrollTo($('#ArticleExt_content_em_'),-1);
               },300);
            }
EOD;
            return $str;
        }
    }
}
