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
 * @property integer $place_user
 * @property integer $tag_id
 * @property integer $is_unshow
 * @property integer $is_new
 * @property integer $city
 * @property integer $area
 * @property integer $street
 * @property integer $staff_id
 * @property integer $uid
 * @property integer $refresh_time
 * @property integer $qjtop_time
 * @property integer $top_time
 * @property integer $open_time
 * @property integer $delivery_time
 * @property string $address
 * @property string $sale_addr
 * @property string $sale_tel
 * @property string $map_lng
 * @property string $map_lat
 * @property integer $map_zoom
 * @property integer $call_num
 * @property string $image
 * @property integer $price
 * @property integer $unit
 * @property string $market_user
 * @property string $market_users
 * @property integer $price_mark
 * @property string $first_pay
 * @property string $data_conf
 * @property integer $status
 * @property integer $company_id
 * @property string $company_name
 * @property integer $ff_num
 * @property integer $qjsort
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
			array('sale_status, place_user, tag_id, is_unshow, is_new, city, area, street, staff_id, uid, refresh_time, qjtop_time, top_time, open_time, delivery_time, map_zoom, call_num, price, unit, price_mark, status, company_id, ff_num, qjsort, sort, views, deleted, created, updated, old_id', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>50),
			array('pinyin, sale_tel', 'length', 'max'=>100),
			array('fcode', 'length', 'max'=>1),
			array('address, sale_addr, image', 'length', 'max'=>150),
			array('map_lng, map_lat', 'length', 'max'=>60),
			array('market_user', 'length', 'max'=>20),
			array('market_users, first_pay, company_name', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, title, pinyin, fcode, sale_status, place_user, tag_id, is_unshow, is_new, city, area, street, staff_id, uid, refresh_time, qjtop_time, top_time, open_time, delivery_time, address, sale_addr, sale_tel, map_lng, map_lat, map_zoom, call_num, image, price, unit, market_user, market_users, price_mark, first_pay, data_conf, status, company_id, company_name, ff_num, qjsort, sort, views, deleted, created, updated, old_id', 'safe', 'on'=>'search'),
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
			'title' => 'Title',
			'pinyin' => 'Pinyin',
			'fcode' => 'Fcode',
			'sale_status' => 'Sale Status',
			'place_user' => 'Place User',
			'tag_id' => 'Tag',
			'is_unshow' => 'Is Unshow',
			'is_new' => 'Is New',
			'city' => 'City',
			'area' => 'Area',
			'street' => 'Street',
			'staff_id' => 'Staff',
			'uid' => 'Uid',
			'refresh_time' => 'Refresh Time',
			'qjtop_time' => 'Qjtop Time',
			'top_time' => 'Top Time',
			'open_time' => 'Open Time',
			'delivery_time' => 'Delivery Time',
			'address' => 'Address',
			'sale_addr' => 'Sale Addr',
			'sale_tel' => 'Sale Tel',
			'map_lng' => 'Map Lng',
			'map_lat' => 'Map Lat',
			'map_zoom' => 'Map Zoom',
			'call_num' => 'Call Num',
			'image' => 'Image',
			'price' => 'Price',
			'unit' => 'Unit',
			'market_user' => 'Market User',
			'market_users' => 'Market Users',
			'price_mark' => 'Price Mark',
			'first_pay' => 'First Pay',
			'data_conf' => 'Data Conf',
			'status' => 'Status',
			'company_id' => 'Company',
			'company_name' => 'Company Name',
			'ff_num' => 'Ff Num',
			'qjsort' => 'Qjsort',
			'sort' => 'Sort',
			'views' => 'Views',
			'deleted' => 'Deleted',
			'created' => 'Created',
			'updated' => 'Updated',
			'old_id' => 'Old',
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
		$criteria->compare('place_user',$this->place_user);
		$criteria->compare('tag_id',$this->tag_id);
		$criteria->compare('is_unshow',$this->is_unshow);
		$criteria->compare('is_new',$this->is_new);
		$criteria->compare('city',$this->city);
		$criteria->compare('area',$this->area);
		$criteria->compare('street',$this->street);
		$criteria->compare('staff_id',$this->staff_id);
		$criteria->compare('uid',$this->uid);
		$criteria->compare('refresh_time',$this->refresh_time);
		$criteria->compare('qjtop_time',$this->qjtop_time);
		$criteria->compare('top_time',$this->top_time);
		$criteria->compare('open_time',$this->open_time);
		$criteria->compare('delivery_time',$this->delivery_time);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('sale_addr',$this->sale_addr,true);
		$criteria->compare('sale_tel',$this->sale_tel,true);
		$criteria->compare('map_lng',$this->map_lng,true);
		$criteria->compare('map_lat',$this->map_lat,true);
		$criteria->compare('map_zoom',$this->map_zoom);
		$criteria->compare('call_num',$this->call_num);
		$criteria->compare('image',$this->image,true);
		$criteria->compare('price',$this->price);
		$criteria->compare('unit',$this->unit);
		$criteria->compare('market_user',$this->market_user,true);
		$criteria->compare('market_users',$this->market_users,true);
		$criteria->compare('price_mark',$this->price_mark);
		$criteria->compare('first_pay',$this->first_pay,true);
		$criteria->compare('data_conf',$this->data_conf,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('company_id',$this->company_id);
		$criteria->compare('company_name',$this->company_name,true);
		$criteria->compare('ff_num',$this->ff_num);
		$criteria->compare('qjsort',$this->qjsort);
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
