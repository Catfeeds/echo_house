<?php

/**
 * 区域管理相关
 * @author SC
 * @date 2015-09-11
 */
class AreaController extends AdminController {

    /**
     * 编辑区域列表
     */
    public function actionAreaList() {
        $list = AreaExt::model()->undeleted()->findAll();
        $tree = Tools::makeTree($list);
        $this->render('arealist', array('tree' => $tree));
    }

    /**
     * 区域排序
     */
    public function actionSortArea()
    {
        if(Yii::app()->request->isAjaxRequest && $data = Yii::app()->request->getPost('data', '')){
            $list = AreaExt::model()->undeleted()->findAll(['index'=>'id']);
            $data = CJSON::decode($data) ? CJSON::decode($data) : array();
            $list = $this->sortArea($data, $list);
            $this->setMessage('保存成功','success');
        }
        $this->setMessage('保存失败','error');
    }

    /**
     * 处理前台排序过的数据
     * @param  array $data array
     * @param  AreaExt[] 取出的区域列表
     * @return AreaExt[]
     */
    protected function sortArea($data, $list)
    {
        foreach($data as $k=>$v){
            if(isset($list[$v['id']])){
                $model = $list[$v['id']];
                $model->sort = $k;
                $model->status = $v['status'];
                $model->save();
            }
            if(isset($v['children'])){
                $list = $this->sortArea($v['children'], $list);
            }
        }
        return $list;
    }

//    public function actionAddArea() {
//        $this->render('addarea');
//    }

    /**
     * 区域管理 -- 区域添加编辑
     */
    public function actionAreaEdit($id=0) {
        if ($id > 0) {
            $area = AreaExt::model()->findByPk($id);
            $msg = '修改';
        } else {
            $area = new AreaExt();
            $msg = '添加';
        }

        $_data = Tools::menuMake(AreaExt::model()->normal()->findAll());
        $catelist[0] = '--根节点--';
        foreach ($_data as $v) {
            $catelist[$v['id']] = $v['name'];
        }

        if (Yii::app()->request->isPostRequest) {
            $AreaInfo = Yii::app()->request->getPost('AreaExt');

            $area->setAttributes($AreaInfo);

            if ($area->save()) {
                $this->setMessage($msg . '成功！','success',$this->createAbsoluteUrl('arealist'));
                //$this->redirect($this->createAbsoluteUrl('arealist'));
            } else {
                $this->setMessage($msg . '失败！','error');
                //$this->redirect(Yii::app()->request->urlReferrer);
            }
        }
        $maps = array('zoom' => 14, 'lat' => SiteExt::getAttr('qjpz','map_lat') ? SiteExt::getAttr('qjpz','map_lat') : "31.810077", 'lng' => SiteExt::getAttr('qjpz','map_lng') ? SiteExt::getAttr('qjpz','map_lng') : "119.974454");
        $this->render('areaedit', array(
            'area' => $area,
            'maps' => $maps,
            'catelist' => $catelist,
        ));
    }

    /**
     * 区域管理 -- 删除区域
     */
    public function actionAreaDel() {
        $id = Yii::app()->request->getParam('id', 0);
        if ($id > 0) {
            $isTrue = FALSE;
            $count = AreaExt::model()->normal()->count('parent=:parent',array(':parent'=>$id));
            $area = AreaExt::model()->findByPk($id);
            if($count == 0&&$area->delete()){
                $isTrue = TRUE;
            }else{
                $isTrue = FALSE;
            }
            if ($isTrue) {
                echo CJSON::encode(array('code' => 100));
                exit;
            } else {
                echo CJSON::encode(array('code' => -1));
                exit;
            }
        } else {
            echo CJSON::encode(array('code' => -1));
            exit;
        }
    }

    public function actionFoo() {
        $this->render('userinfo', array());
    }

    /**
     * 区域管理 -- 处理排序
     */
    public function actionAreaSort() {
        $sort = $_GET['Area']['sort'];
        $count = 0;
        if (!empty($sort) || !empty($title)) {
            foreach ($sort as $k => $v) {
                $count = Area::model()->updateByPk($k, array('sort' => $v));
                $count++;
            }
            if ($count > 0) {
                Yii::app()->user->setFlash('success', '操作成功！');
                $this->redirect(Yii::app()->request->urlReferrer);
            } else {
                Yii::app()->user->setFlash('danger', '操作失败！');
                $this->redirect(Yii::app()->request->urlReferrer);
            }
        }
    }

    /**
     * ajax获取二级联动下拉菜单[楼盘信息编辑页用]
     * @param  integer $area 上级id
     */
    public function actionAjaxGetArea($area)
    {
        $data = AreaExt::model()->getByParent($area)->normal()->findAll();
        if($data)
        {
            foreach($data as $v)
            {
                echo CHtml::tag('option', array('value'=>$v->id), CHtml::encode($v->name), true);
            }
        }
        else
            echo CHtml::tag('option', array('value'=>0), CHtml::encode('--无子分类--'), true);
    }

    /**
     * 所属区域二级联动下拉菜单
     */
    public function actionShowArea() {
        $pid = Yii::app()->request->getParam('parent', 0);
        AreaExt::showArea($pid);
    }

    public function actionClearCache()
    {
        $ids = ['wap_all_area','all_area','all_street'];
        foreach ($ids as $key => $id) {
            CacheExt::delete($id);
        }
        $this->setMessage('操作成功');
    }

}
