<?php

/**
 * This is the model class for table "subscribe".
 *
 * The followings are the available columns in table 'subscribe':
 * @property integer $id
 * @property integer $uid
 * @property integer $area
 * @property integer $street
 * @property string $minprice
 * @property string $maxprice
 * @property integer $sfprice
 * @property integer $wylx
 * @property integer $zxzt
 * @property integer $status
 * @property integer $sort
 * @property integer $created
 * @property integer $updated
 */
class Subscribe extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'subscribe';
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
			array('uid, area, street, sfprice, wylx, zxzt, status, sort, created, updated', 'numerical', 'integerOnly'=>true),
			array('minprice, maxprice', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, uid, area, street, minprice, maxprice, sfprice, wylx, zxzt, status, sort, created, updated', 'safe', 'on'=>'search'),
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
			'uid' => 'Uid',
			'area' => 'Area',
			'street' => 'Street',
			'minprice' => 'Minprice',
			'maxprice' => 'Maxprice',
			'sfprice' => 'Sfprice',
			'wylx' => 'Wylx',
			'zxzt' => 'Zxzt',
			'status' => 'Status',
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
		$criteria->compare('uid',$this->uid);
		$criteria->compare('area',$this->area);
		$criteria->compare('street',$this->street);
		$criteria->compare('minprice',$this->minprice,true);
		$criteria->compare('maxprice',$this->maxprice,true);
		$criteria->compare('sfprice',$this->sfprice);
		$criteria->compare('wylx',$this->wylx);
		$criteria->compare('zxzt',$this->zxzt);
		$criteria->compare('status',$this->status);
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
	 * @return Subscribe the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
