<?php

/**
 * This is the model class for table "sub".
 *
 * The followings are the available columns in table 'sub':
 * @property integer $id
 * @property string $hid
 * @property integer $uid
 * @property integer $time
 * @property string $market_staff
 * @property string $name
 * @property string $phone
 * @property string $notice
 * @property string $code
 * @property string $company_name
 * @property integer $visit_way
 * @property integer $sale_uid
 * @property integer $sex
 * @property integer $is_check
 * @property integer $is_only_sub
 * @property string $note
 * @property integer $status
 * @property integer $deleted
 * @property integer $sort
 * @property integer $created
 * @property integer $updated
 */
class Sub extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'sub';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('uid, status, created, updated', 'required'),
			array('uid, time, visit_way, sale_uid, sex, is_check, is_only_sub, status, deleted, sort, created, updated', 'numerical', 'integerOnly'=>true),
			array('hid, market_staff, name', 'length', 'max'=>100),
			array('phone', 'length', 'max'=>20),
			array('notice', 'length', 'max'=>12),
			array('code', 'length', 'max'=>10),
			array('company_name, note', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, hid, uid, time, market_staff, name, phone, notice, code, company_name, visit_way, sale_uid, sex, is_check, is_only_sub, note, status, deleted, sort, created, updated', 'safe', 'on'=>'search'),
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
			'time' => 'Time',
			'market_staff' => 'Market Staff',
			'name' => 'Name',
			'phone' => 'Phone',
			'notice' => 'Notice',
			'code' => 'Code',
			'company_name' => 'Company Name',
			'visit_way' => 'Visit Way',
			'sale_uid' => 'Sale Uid',
			'sex' => 'Sex',
			'is_check' => 'Is Check',
			'is_only_sub' => 'Is Only Sub',
			'note' => 'Note',
			'status' => 'Status',
			'deleted' => 'Deleted',
			'sort' => 'Sort',
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
		$criteria->compare('hid',$this->hid,true);
		$criteria->compare('uid',$this->uid);
		$criteria->compare('time',$this->time);
		$criteria->compare('market_staff',$this->market_staff,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('notice',$this->notice,true);
		$criteria->compare('code',$this->code,true);
		$criteria->compare('company_name',$this->company_name,true);
		$criteria->compare('visit_way',$this->visit_way);
		$criteria->compare('sale_uid',$this->sale_uid);
		$criteria->compare('sex',$this->sex);
		$criteria->compare('is_check',$this->is_check);
		$criteria->compare('is_only_sub',$this->is_only_sub);
		$criteria->compare('note',$this->note,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('deleted',$this->deleted);
		$criteria->compare('sort',$this->sort);
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
	 * @return Sub the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
