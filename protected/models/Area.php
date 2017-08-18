<?php

/**
 * This is the model class for table "area".
 *
 * The followings are the available columns in table 'area':
 * @property integer $id
 * @property integer $parent
 * @property string $name
 * @property string $pinyin
 * @property integer $sort
 * @property integer $status
 * @property string $map_lng
 * @property string $map_lat
 * @property integer $map_zoom
 * @property integer $deleted
 * @property integer $created
 * @property integer $updated
 * @property integer $old_id
 */
class Area extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'area';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, created', 'required'),
			array('parent, sort, status, map_zoom, deleted, created, updated, old_id', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>16),
			array('pinyin', 'length', 'max'=>25),
			array('map_lng, map_lat', 'length', 'max'=>60),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, parent, name, pinyin, sort, status, map_lng, map_lat, map_zoom, deleted, created, updated, old_id', 'safe', 'on'=>'search'),
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
			'id' => '主键',
			'parent' => '父级ID',
			'name' => '区域名',
			'pinyin' => '拼音',
			'sort' => '排序',
			'status' => '状态',
			'map_lng' => '坐标经度',
			'map_lat' => '坐标纬度',
			'map_zoom' => '地图缩放层级',
			'deleted' => '删除时间',
			'created' => '添加时间',
			'updated' => '修改时间',
			'old_id' => '旧数据id',
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
		$criteria->compare('parent',$this->parent);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('pinyin',$this->pinyin,true);
		$criteria->compare('sort',$this->sort);
		$criteria->compare('status',$this->status);
		$criteria->compare('map_lng',$this->map_lng,true);
		$criteria->compare('map_lat',$this->map_lat,true);
		$criteria->compare('map_zoom',$this->map_zoom);
		$criteria->compare('deleted',$this->deleted);
		$criteria->compare('created',$this->created);
		$criteria->compare('updated',$this->updated);
		$criteria->compare('old_id',$this->old_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Area the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
