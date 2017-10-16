<?php

/**
 * This is the model class for table "company_package".
 *
 * The followings are the available columns in table 'company_package':
 * @property integer $id
 * @property integer $cid
 * @property string $cname
 * @property integer $expire
 * @property integer $plot_num
 * @property integer $msg_num
 * @property integer $sort
 * @property integer $status
 * @property integer $deleted
 * @property integer $created
 * @property integer $updated
 */
class CompanyPackage extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'company_package';
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
			array('cid, expire, plot_num, msg_num, sort, status, deleted, created, updated', 'numerical', 'integerOnly'=>true),
			array('cname', 'length', 'max'=>100),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, cid, cname, expire, plot_num, msg_num, sort, status, deleted, created, updated', 'safe', 'on'=>'search'),
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
			'cid' => 'Cid',
			'cname' => 'Cname',
			'expire' => 'Expire',
			'plot_num' => 'Plot Num',
			'msg_num' => 'Msg Num',
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
		$criteria->compare('cid',$this->cid);
		$criteria->compare('cname',$this->cname,true);
		$criteria->compare('expire',$this->expire);
		$criteria->compare('plot_num',$this->plot_num);
		$criteria->compare('msg_num',$this->msg_num);
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
	 * @return CompanyPackage the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
