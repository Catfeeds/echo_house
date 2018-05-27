<?php

/**
 * This is the model class for table "plot_call".
 *
 * The followings are the available columns in table 'plot_call':
 * @property integer $id
 * @property string $calla
 * @property string $callb
 * @property integer $hid
 * @property string $title
 * @property integer $time
 * @property integer $msg_time
 * @property integer $created
 * @property integer $updated
 */
class PlotCall extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'plot_call';
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
			array('hid, time, msg_time, created, updated', 'numerical', 'integerOnly'=>true),
			array('calla, callb', 'length', 'max'=>20),
			array('title', 'length', 'max'=>100),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, calla, callb, hid, title, time, msg_time, created, updated', 'safe', 'on'=>'search'),
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
			'calla' => 'Calla',
			'callb' => 'Callb',
			'hid' => 'Hid',
			'title' => 'Title',
			'time' => 'Time',
			'msg_time' => 'Msg Time',
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
		$criteria->compare('calla',$this->calla,true);
		$criteria->compare('callb',$this->callb,true);
		$criteria->compare('hid',$this->hid);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('time',$this->time);
		$criteria->compare('msg_time',$this->msg_time);
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
	 * @return PlotCall the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
