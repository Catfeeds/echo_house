<?php

/**
 * This is the model class for table "plot".
 *
 * The followings are the available columns in table 'plot':
 * @property integer $id
 * @property string $title
 * @property string $pinyin
 * @property string $fcode
 * @property integer $sale_status
 * @property integer $tag_id
 * @property integer $is_new
 * @property integer $area
 * @property integer $street
 * @property integer $open_time
 * @property integer $delivery_time
 * @property string $address
 * @property string $sale_addr
 * @property string $sale_tel
 * @property string $map_lng
 * @property string $map_lat
 * @property integer $map_zoom
 * @property string $image
 * @property integer $price
 * @property integer $unit
 * @property string $market_user
 * @property string $market_users
 * @property integer $price_mark
 * @property string $data_conf
 * @property integer $status
 * @property integer $sort
 * @property integer $views
 * @property integer $deleted
 * @property integer $created
 * @property integer $updated
 * @property integer $old_id
 */
class Plot extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'plot';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, pinyin, area, street, data_conf, created', 'required'),
			array('sale_status, tag_id, is_new, area, street, open_time, delivery_time, map_zoom, price, unit, price_mark, status, sort, views, deleted, created, updated, old_id', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>50),
			array('pinyin, sale_tel', 'length', 'max'=>100),
			array('fcode', 'length', 'max'=>1),
			array('address, sale_addr, image', 'length', 'max'=>150),
			array('map_lng, map_lat', 'length', 'max'=>60),
			array('market_user', 'length', 'max'=>12),
			array('market_users', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, title, pinyin, fcode, sale_status, tag_id, is_new, area, street, open_time, delivery_time, address, sale_addr, sale_tel, map_lng, map_lat, map_zoom, image, price, unit, market_user, market_users, price_mark, data_conf, status, sort, views, deleted, created, updated, old_id', 'safe', 'on'=>'search'),
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
			'id' => '主键id',
			'title' => '楼盘名称',
			'pinyin' => '拼音',
			'fcode' => '首字母',
			'sale_status' => '销售状态',
			'tag_id' => '对应论坛标签id',
			'is_new' => '是否新盘',
			'area' => '所在区域',
			'street' => '所在商圈',
			'open_time' => '开盘时间',
			'delivery_time' => '最新交付时间',
			'address' => '楼盘地址',
			'sale_addr' => '售楼地址',
			'sale_tel' => '售楼电话',
			'map_lng' => '经度',
			'map_lat' => '纬度',
			'map_zoom' => '地图放大',
			'image' => '配图',
			'price' => '价格',
			'unit' => '单位',
			'market_user' => 'Market User',
			'market_users' => 'Market Users',
			'price_mark' => '价格标识',
			'data_conf' => '项目信息存储',
			'status' => '状态',
			'sort' => '排序',
			'views' => '访问量',
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
		$criteria->compare('title',$this->title,true);
		$criteria->compare('pinyin',$this->pinyin,true);
		$criteria->compare('fcode',$this->fcode,true);
		$criteria->compare('sale_status',$this->sale_status);
		$criteria->compare('tag_id',$this->tag_id);
		$criteria->compare('is_new',$this->is_new);
		$criteria->compare('area',$this->area);
		$criteria->compare('street',$this->street);
		$criteria->compare('open_time',$this->open_time);
		$criteria->compare('delivery_time',$this->delivery_time);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('sale_addr',$this->sale_addr,true);
		$criteria->compare('sale_tel',$this->sale_tel,true);
		$criteria->compare('map_lng',$this->map_lng,true);
		$criteria->compare('map_lat',$this->map_lat,true);
		$criteria->compare('map_zoom',$this->map_zoom);
		$criteria->compare('image',$this->image,true);
		$criteria->compare('price',$this->price);
		$criteria->compare('unit',$this->unit);
		$criteria->compare('market_user',$this->market_user,true);
		$criteria->compare('market_users',$this->market_users,true);
		$criteria->compare('price_mark',$this->price_mark);
		$criteria->compare('data_conf',$this->data_conf,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('sort',$this->sort);
		$criteria->compare('views',$this->views);
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
	 * @return Plot the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
