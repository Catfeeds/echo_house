<?php

/**
 * User: fanqi
 * Date: 2016/9/13
 * Time: 10:17
 */
class EsfZfCommonBehavior extends CActiveRecordBehavior
{
    /**
     * @param $model
     * @param string $idName
     * 二手房租房ajax删除
     */
    public function ajaxDel($model, $idName = 'id')
    {
        if (Yii::app()->request->isPostRequest) {
            //获取删除的模型，id名称
            $id = Yii::app()->request->getPost($idName, 0);
            $mod = $model->findByPk($id);
        }
        if ($mod) {
            $mod->deleted = 1;
            if ($mod->save()) {
                Yii::app()->controller->setMessage('删除成功！', 'success');
            } else {
                Yii::app()->controller->setMessage('删除失败！', 'error');
            }
        }
    }

    /**
     * @param $model
     * 电话确认
     */
    public function phoneCheck($model, $id = 'id', $phoneCheckName = 'phone_check')
    {
        if (Yii::app()->request->isPostRequest) {
            if (Yii::app()->request->getPost($phoneCheckName, 0)) {
                $fid = Yii::app()->request->getPost($id, 0);
                $zf = $model->findByPk($fid);
                if ($zf) {
                    $zf->contacted = 1;

                    if ($zf->save()) {
                        Yii::app()->controller->setMessage('提交成功！', 'success');
                    } else {
                        Yii::app()->controller->setMessage('提交失败！', 'error');
                    }

                } else {
                    Yii::app()->controller->setMessage('提交失败！', 'error');
                }
            }

        }
    }

    /**
     * @param $model
     * 排序
     */
    public function ajaxSort($model,$id='id',$sort='sort')
    {
        $id = (int)Yii::app()->request->getParam($id, 0);
        $sort = (int)Yii::app()->request->getParam($sort, 0);
        $zf = $model->findByPk($id);
        if ($zf) {
            $zf->sort = $sort;
            if ($zf->save())
                Yii::app()->controller->setMessage('修改成功', 'success');
            else
                Yii::app()->controller->setMessage('修改失败!', 'error');
            Yii::app()->controller->redirect('zfList');
        }
    }
}