<?php

/**
 * This is the model class for table "cooperate".
 *
 * The followings are the available columns in table 'cooperate':
 * @property integer $id
 * @property integer $hid
 * @property string $user_company
 * @property string $user_phone
 * @property string $user_name
 * @property integer $uid
 * @property string $com_phone
 * @property integer $sort
 * @property integer $status
 * @property integer $deleted
 * @property integer $created
 * @property integer $updated
 */
class Cooperate extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'cooperate';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('created', 'required'),
			array('hid, uid, sort, status, deleted, created, updated', 'numerical', 'integerOnly'=>true),
			array('user_company, user_name', 'length', 'max'=>100),
			array('user_phone, com_phone', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, hid, user_company, user_phone, user_name, uid, com_phone, sort, status, deleted, created, updated', 'safe', 'on'=>'search'),
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
			'user_company' => 'User Company',
			'user_phone' => 'User Phone',
			'user_name' => 'User Name',
			'uid' => 'Uid',
			'com_phone' => 'Com Phone',
			'sort' => 'Sort',
			'status' => 'Status',
			'deleted' => 'Deleted',
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
		$criteria->compare('user_company',$this->user_company,true);
		$criteria->compare('user_phone',$this->user_phone,true);
		$criteria->compare('user_name',$this->user_name,true);
		$criteria->compare('uid',$this->uid);
		$criteria->compare('com_phone',$this->com_phone,true);
		$criteria->compare('sort',$this->sort);
		$criteria->compare('status',$this->status);
		$criteria->compare('deleted',$this->deleted);
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
	 * @return Cooperate the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
