<?php

/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property integer $id
 * @property string $pwd
 * @property string $wx
 * @property string $phone
 * @property string $name
 * @property integer $parent
 * @property string $jjq_openid
 * @property string $openid
 * @property integer $is_jl
 * @property integer $is_manage
 * @property integer $is_true
 * @property string $id_nation
 * @property string $id_birth
 * @property string $id_addr
 * @property string $id_no
 * @property string $id_pic
 * @property string $subs_id
 * @property string $virtual_no_ext
 * @property string $virtual_no
 * @property integer $refresh_num
 * @property integer $qf_uid
 * @property integer $cid
 * @property integer $vip_expire
 * @property string $company
 * @property integer $type
 * @property string $ava
 * @property string $image
 * @property integer $status
 * @property integer $deleted
 * @property integer $sort
 * @property integer $created
 * @property integer $updated
 */
class User extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('pwd, name, created', 'required'),
			array('parent, is_jl, is_manage, is_true, refresh_num, qf_uid, cid, vip_expire, type, status, deleted, sort, created, updated', 'numerical', 'integerOnly'=>true),
			array('pwd, jjq_openid, openid, id_addr, id_no, id_pic, company, ava, image', 'length', 'max'=>255),
			array('wx, name, id_birth, subs_id, virtual_no', 'length', 'max'=>100),
			array('phone', 'length', 'max'=>15),
			array('id_nation', 'length', 'max'=>20),
			array('virtual_no_ext', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, pwd, wx, phone, name, parent, jjq_openid, openid, is_jl, is_manage, is_true, id_nation, id_birth, id_addr, id_no, id_pic, subs_id, virtual_no_ext, virtual_no, refresh_num, qf_uid, cid, vip_expire, company, type, ava, image, status, deleted, sort, created, updated', 'safe', 'on'=>'search'),
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
			'pwd' => 'Pwd',
			'wx' => 'Wx',
			'phone' => 'Phone',
			'name' => 'Name',
			'parent' => 'Parent',
			'jjq_openid' => 'Jjq Openid',
			'openid' => 'Openid',
			'is_jl' => 'Is Jl',
			'is_manage' => 'Is Manage',
			'is_true' => 'Is True',
			'id_nation' => 'Id Nation',
			'id_birth' => 'Id Birth',
			'id_addr' => 'Id Addr',
			'id_no' => 'Id No',
			'id_pic' => 'Id Pic',
			'subs_id' => 'Subs',
			'virtual_no_ext' => 'Virtual No Ext',
			'virtual_no' => 'Virtual No',
			'refresh_num' => 'Refresh Num',
			'qf_uid' => 'Qf Uid',
			'cid' => 'Cid',
			'vip_expire' => 'Vip Expire',
			'company' => 'Company',
			'type' => 'Type',
			'ava' => 'Ava',
			'image' => 'Image',
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
		$criteria->compare('pwd',$this->pwd,true);
		$criteria->compare('wx',$this->wx,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('parent',$this->parent);
		$criteria->compare('jjq_openid',$this->jjq_openid,true);
		$criteria->compare('openid',$this->openid,true);
		$criteria->compare('is_jl',$this->is_jl);
		$criteria->compare('is_manage',$this->is_manage);
		$criteria->compare('is_true',$this->is_true);
		$criteria->compare('id_nation',$this->id_nation,true);
		$criteria->compare('id_birth',$this->id_birth,true);
		$criteria->compare('id_addr',$this->id_addr,true);
		$criteria->compare('id_no',$this->id_no,true);
		$criteria->compare('id_pic',$this->id_pic,true);
		$criteria->compare('subs_id',$this->subs_id,true);
		$criteria->compare('virtual_no_ext',$this->virtual_no_ext,true);
		$criteria->compare('virtual_no',$this->virtual_no,true);
		$criteria->compare('refresh_num',$this->refresh_num);
		$criteria->compare('qf_uid',$this->qf_uid);
		$criteria->compare('cid',$this->cid);
		$criteria->compare('vip_expire',$this->vip_expire);
		$criteria->compare('company',$this->company,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('ava',$this->ava,true);
		$criteria->compare('image',$this->image,true);
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
	 * @return User the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
