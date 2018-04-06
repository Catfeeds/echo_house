<?php

/**
 * This is the model class for table "plot_answer".
 *
 * The followings are the available columns in table 'plot_answer':
 * @property integer $id
 * @property integer $hid
 * @property integer $uid
 * @property integer $aid
 * @property integer $is_nm
 * @property string $note
 * @property integer $sort
 * @property integer $status
 * @property integer $created
 * @property integer $updated
 */
class PlotAnswer extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'plot_answer';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('aid, created', 'required'),
			array('hid, uid, aid, is_nm, sort, status, created, updated', 'numerical', 'integerOnly'=>true),
			array('note', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, hid, uid, aid, is_nm, note, sort, status, created, updated', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'hid' => 'Hid',
			'uid' => 'Uid',
			'aid' => 'Aid',
			'is_nm' => 'Is Nm',
			'note' => 'Note',
			'sort' => 'Sort',
			'status' => 'Status',
			'created' => 'Created',
			'updated' => 'Updated',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('hid',$this->hid);
		$criteria->compare('uid',$this->uid);
		$criteria->compare('aid',$this->aid);
		$criteria->compare('is_nm',$this->is_nm);
		$criteria->compare('note',$this->note,true);
		$criteria->compare('sort',$this->sort);
		$criteria->compare('status',$this->status);
		$criteria->compare('created',$this->created);
		$criteria->compare('updated',$this->updated);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PlotAnswer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
