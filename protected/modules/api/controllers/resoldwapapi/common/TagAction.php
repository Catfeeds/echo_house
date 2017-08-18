<?php

/**
 * User: fanqi
 * Date: 2016/9/28
 * Time: 15:27
 * 标签接口
 * =============================
 * [request]请求：
 * string cate 标签名
 * =============================
 * [response]响应：
 * object tags
 */
class TagAction extends CAction
{
    public function run()
    {
        $cate = Yii::app()->request->getQuery('cate');
        $allTag = TagExt::getAllByCate();
        $tags = isset($allTag[$cate]) ? $allTag[$cate] : '';
//        $tags = TagExt::model()->normal()->findAll(['condition'=>'cate = :cate','params'=>['cate'=>$cate],'order'=>'sort asc']);
        if($cate=='zfmode' && $tags)
        {
        	$tagTrans = [];
        	foreach ($tags as $key => $value) {
        		if($value['name']!='不限')
        			$tagTrans[] = $value;
        	}
        	$tags = $tagTrans;
        }
        $this->getController()->frame['data'] = ['tags'=>$tags];
    }
}
