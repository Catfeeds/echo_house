<?php
/**
 * 记录操作日志
 * @author tivon
 * @date 2015-06-02
 */
class ActiveRecordLogableBehavior extends CActiveRecordBehavior
{
    private $_oldattributes = array();

    /**
     * save操作
     * @param  [type] $event [description]
     * @return [type]        [description]
     */
    public function afterSave($event='')
    {
        if(Yii::app() instanceof CConsoleApplication || Yii::app()->controller===null || Yii::app()->controller->module===null ||  Yii::app()->controller->module->id!='admin') return;
        //非新记录，即非插入
        if (!$this->Owner->isNewRecord) {

            $newattributes = $this->Owner->getAttributes();             //获得AR类中已修改的各字段值
            $oldattributes = $this->getOldAttributes();                 //之前的旧数据

            //比较新旧数据
            foreach ($newattributes as $name => $value) {
                if (isset($oldattributes[$name])) {
                    $old = $oldattributes[$name];
                } else {
                    $old = '';
                }

                //如果该字段旧数据与新数据不一样，则进行记录
                if ($value != $old) {
                    //$changes = $name . ' ('.$old.') => ('.$value.'), ';
                    $data = array(
                        'description' => 'User ' . Yii::app()->user->name                    //设置日志内容格式，描述具体操作
                                            . ' changed ' . $name . ' for '
                                            . get_class($this->Owner)
                                            . '[' . $this->Owner->getPrimaryKey() .'].',
                        'action' => 'CHANGE',
                        'model' => get_class($this->Owner),
                        'mid' => $this->Owner->getPrimaryKey(),
                        'field' => $name,
                        'created' => time(),
                        'uid' => Yii::app()->user->id,
                        'username' => Yii::app()->user->username,
                    );
                    $this->save($data);
                }
            }
        } else {//新纪录直接保存操作日志入库
            $data = array(
                'description' => 'User ' . Yii::app()->user->name
                                    . ' created ' . get_class($this->Owner)
                                    . '[' . $this->Owner->getPrimaryKey() .'].',
                'action' => 'CREATE',
                'model' => get_class($this->Owner),
                'mid' => $this->Owner->getPrimaryKey(),
                'field' => '',
                'uid' => Yii::app()->user->id,
                'username' => Yii::app()->user->username,
                'created' => time(),
            );
            $this->save($data);
        }
    }

    /**
     * 删除操作
     * @param  [type] $event [description]
     * @return [type]        [description]
     */
    public function afterDelete($event='')
    {
        if(Yii::app() instanceof CConsoleApplication || Yii::app()->controller===null || Yii::app()->controller->module===null || Yii::app()->controller->module->id!='admin') return;
        $data = array(
            'description' => 'User ' . Yii::app()->user->name . ' deleted '
                                . get_class($this->Owner)
                                . '[' . $this->Owner->getPrimaryKey() .'].',
            'action' => 'DELETE',
            'model' => get_class($this->Owner),
            'mid' => $this->Owner->getPrimaryKey(),
            'field' => '',
            'uid' => Yii::app()->user->id,
            'username' => Yii::app()->user->username,
            'created' => time(),
        );
        $this->save($data);
    }

    public function afterFind($event='')
    {
        if(Yii::app() instanceof CConsoleApplication || Yii::app()->controller===null || Yii::app()->controller->module===null || Yii::app()->controller->module->id!='admin') return;
        //保存查询出来的旧数据
        $this->setOldAttributes($this->Owner->getAttributes());
    }

    public function getOldAttributes()
    {
        return $this->_oldattributes;
    }

    public function setOldAttributes($value)
    {
        $this->_oldattributes=$value;
    }

    public function save($data)
    {
        $model = new ActiveRecordLogExt;
        $model->attributes = $data;
        return $model->save();
    }
}
?>
