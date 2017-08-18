<?php
/**
 * 标签控制器
 * @author steven.allen <[<email address>]>
 * @date(2017.2.5)
 */
class TagController extends AdminController{

	public function init()
	{
		parent::init();	
	}
	/**
     * 标签列表
     */
    public function actionList()
    {
        $data = TagExt::model()->findAll(array(
            'order' => 'sort asc'
        ));
        $list = array();
        foreach ($data as $v) {
            $list[$v->cate][] = $v;
        }
        asort($list);
        $this->render('list', array(
            'list' => $list,
        ));
    }

    /**
     * 添加\编辑标签
     */
    public function actionEdit($id=0,$cate='')
    {
        $model = $id ? TagExt::model()->findByPk($id) : new TagExt;
        if(Yii::app()->request->isPostRequest)
        {
            $model->attributes = Yii::app()->request->getPost('TagExt', array());
            if($model->save())
                $this->setMessage('保存成功', 'success', array('list'));
            else
                $this->setMessage('保存失败', 'error');
        }

        if($model->getIsNewRecord()) {
            $model->cate = $cate;
        }
        $dropDownListCates = $model->getIsDirectTag() ? TagExt::$xinfangCate['direct'] : TagExt::$xinfangCate['range'];

        $this->render('edit', array(
            'model' => $model,
            'dropDownListCates' => $dropDownListCates,
        ));
    }

    /**
     * ajax修改状态
     */
    public function actionAjaxStatus()
    {
        if(Yii::app()->request->isPostRequest)
        {
            $cate = Yii::app()->request->getPost('cate', '');
            $status = Yii::app()->request->getPost('status', null);
            if($cate && $status!==null) {
                $tags = TagExt::model()->findAll('cate=:cate', [':cate'=>$cate]);
                $transaction = Yii::app()->db->beginTransaction();
                try
                {
                    foreach($tags as $tag)
                    {
                        $tag->status = $status;
                        $tag->changeStatus()->save();
                    }
                    $transaction->commit();
                    $this->setMessage('修改成功', 'success');
                }catch(Exception $e){
                    $transaction->rollback();
                    $this->setMessage('修改失败', 'error');
                }
            }
            $id = Yii::app()->request->getPost('id', 0);
            $model = TagExt::model()->findByPk($id);
            if($model && $model->changeStatus()->save())
            {
                $this->setMessage('修改成功', 'success');
            }
            else
                $this->setMessage('修改失败', 'error');
        }
    }

    /**
     * ajax修改排序
     */
    public function actionAjaxSort()
    {
        if (Yii::app()->request->isPostRequest) {
            $sort = Yii::app()->request->getPost('sort');
            $transaction = Yii::app()->db->beginTransaction();
            try {
                foreach (explode(',', $sort) as $k => $id) {
                    $row = TagExt::model()->findByPk($id);
                    $row->sort = $k;
                    $row->save();
                }
                $transaction->commit();
                $this->response(true, '修改成功！');
            } catch (Exception $e) {
                $transaction->rollback();
                $this->response(false, $e->getMessage());
            }
        }
    }

    /**
     * 删除标签
     */
    public function actionAjaxDel()
    {
        if(Yii::app()->request->getIsPostRequest()) {
            $id = Yii::app()->request->getPost('id', 0);
            if($tag = TagExt::model()->findByPk($id)) {
                if($tag->delete()){
                    
                    $this->setMessage('删除成功','success');
                }
            }
        }
        $this->setMessage('删除失败','error', ['list']);
    }
}