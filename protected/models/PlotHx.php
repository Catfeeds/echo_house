<?php

/**
 * This is the model class for table "plot_hx".
 *
 * The followings are the available columns in table 'plot_hx':
 * @property integer $id
 * @property integer $hid
 * @property string $title
 * @property string $image
 * @property integer $bedroom
 * @property integer $bathroom
 * @property integer $livingroom
 * @property string $sale_status
 * @property string $size
 * @property string $content
 * @property integer $status
 * @property integer $sort
 * @property integer $deleted
 * @property integer $created
 * @property integer $updated
 */
class PlotHx extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'plot_hx';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('hid, created', 'required'),
			array('hid, bedroom, bathroom, livingroom, status, sort, deleted, created, updated', 'numerical', 'integerOnly'=>true),
			array('title, image', 'length', 'max'=>255),
			array('sale_status', 'length', 'max'=>100),
			array('size', 'length', 'max'=>10),
			array('content', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, hid, title, image, bedroom, bathroom, livingroom, sale_status, size, content, status, sort, deleted, created, updated', 'safe', 'on'=>'search'),
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
			'title' => 'Title',
			'image' => 'Image',
			'bedroom' => 'Bedroom',
			'bathroom' => 'Bathroom',
			'livingroom' => 'Livingroom',
			'sale_status' => 'Sale Status',
			'size' => 'Size',
			'content' => 'Content',
			'status' => 'Status',
			'sort' => 'Sort',
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
		$criteria->compare('title',$this->title,true);
		$criteria->compare('image',$this->image,true);
		$criteria->compare('bedroom',$this->bedroom);
		$criteria->compare('bathroom',$this->bathroom);
		$criteria->compare('livingroom',$this->livingroom);
		$criteria->compare('sale_status',$this->sale_status,true);
		$criteria->compare('size',$this->size,true);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('sort',$this->sort);
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
	 * @return PlotHx the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
