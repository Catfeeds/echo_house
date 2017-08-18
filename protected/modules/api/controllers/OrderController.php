<?php
/**
 * 订单提交的api
 */
class OrderController extends ApiController
{
    /**
     * ajax提交订单
     * 提交方式：Ajax\POST
     * 接收参数：
     *         name[可选] 姓名
     *         phone[必须] 手机号
     *         spm[必须] spm值
     *         note[可选] 备注
     */
    public function actionAjaxSubmit()
    {
        // $this->response(false, '提交失败！');
        if(Yii::app()->request->getIsAjaxRequest())
        {
            $model = new OrderExt;
            $model->attributes = $_POST;
            if($model->spm_b != "看房团" && $model->repeatSubmit() || $model->spm_b == "看房团" && $model->repeatKanSubmit())
                $this->response(false, '您今天已提交过，换个地方看看吧^_^');
            elseif($model->save()){
                $this->response(true, '提交成功！');
            }
            else
                $this->response(false, '提交失败！');
        }
        else
            throw new CHttpException('404', 'Page not found');
    }

    /**
     * ajax提交订单[看房团页面下方自由组团报名处]
     * 提交方式：ajax\post
     * 接收参数：
     *     name[可选] 姓名
     *     phone[必须] 手机号
     *     loupan[可选] 意向楼盘
     *     jiage[可选] 预算价格
     *     huxing[可选] 意向户型
     *     yxqy[可选] 意向区域
     */
    public function actionAjaxKanOrderSubmit()
    {
        if(Yii::app()->request->getIsAjaxRequest())
        {
            $note = array();
            if($yxqy = Yii::app()->request->getPost('yxqy'))
                $note[] = '意向区域：'.$yxqy;
            if($loupan = Yii::app()->request->getPost('loupan', ''))
                $note[] = '意向楼盘：'.$loupan;
            if($jiage = Yii::app()->request->getPost('jiage', 0))
                $note[] = '价格：'.intval($jiage).'万';
            if($huxing = Yii::app()->request->getPost('huxing', array()))
            {
                $note[] = '户型：'.implode('、', $huxing);
            }
            if($beizhu = Yii::app()->request->getPost('note',''))
            {
                $note[] = $beizhu;
            }

            $model = new OrderExt;
            $model->attributes = $_POST;
            $model->note = implode('；', $note);
            if($model->repeatSubmit())
                $this->response(false, '您今天已提交过，换个地方看看吧');
            elseif($model->save()){
                $this->response(true, '提交成功！');
            }else{
                $this->response(false, '提交失败！');
            }
        }
        else
            throw new CHttpException('404', 'Page not found');
    }
}
?>
