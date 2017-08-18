<?php
class RedirectController extends ApiController
{
    /**
     * 跳转楼盘首页
     * @param  integer $id 旧平台的楼盘id
     */
    public function actionPlot($id=-1,$name='')
    {
        $criteria = new CDbCriteria();
        if($name){
            $criteria->addCondition('title=:title');
            $criteria->params[':title'] = $name;
        }else{
            $criteria->addCondition('old_id=:id');
            $criteria->params[':id'] = $id;
        }
        $plot = PlotExt::model()->find($criteria);
        if($plot){
            $this->redirect(['/home/plot/index','py'=>$plot->pinyin]);
        }else{
            $this->redirect('/');
        }
    }
}
