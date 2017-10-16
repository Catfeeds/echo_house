<?php 
class UserController extends HomeController{
	public function init()
	{
		parent::init();
		
		if($this->iswap){
			$this->fixedFooter = 'position:fixed;bottom:0px;width:100%';
		}
		$this->hideloginhead = 1;
	}
	public function actionIndex($type='info')
	{
		if(!$this->user) {
			$this->redirect('login');
		}
		$info = $this->user;
		if(Yii::app()->request->getIsPostRequest()) {
			$infos = Yii::app()->request->getPost('UserExt');
			$info->attributes = $infos;
			$info->save();
			if(isset($infos['pwd'])) {
				Yii::app()->user->logout();
				$this->redirect('index');
			}
		}
		switch ($type) {
			case 'info':
				$this->render('index',['type'=>$type]);
				break;
			case 'pwd':
				$this->render('editPwd',['type'=>$type]);
				break;
			case 'phone':
				$this->render('editPhone',['type'=>$type]);
				break;
			default:
				# code...
				break;
		}
		
	}
	public function actionLogin()
	{
		$wrong = 0;
		$phone = $pwd = '';
		if(Yii::app()->request->getIsPostRequest()) {
			$phone = $this->cleanXss(Yii::app()->request->getPost('name'));
			$pwd = $this->cleanXss(Yii::app()->request->getPost('pwd'));
			$model = new HomeLoginForm();
			$model->username = $phone;
			$model->password = $pwd;
			if($model->login()) {
				$this->redirect('/home/index/index');
			}
			else {
				$wrong = 1;
			}
		}
		// var_dump($wrong);exit;
		$this->render('login',['wrong'=>$wrong,'phone'=>$phone,'pwd'=>$pwd]);
	}

	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect('/home/index/index');
	}
	public function actionCheckOld($pwd='')
	{
		if($pwd) {
			if($this->user->pwd==md5($pwd)) {
				echo json_encode(['s'=>'success']);
			} else {
				echo json_encode(['s'=>'wrong']);
			}
		} else {
			echo json_encode(['s'=>'wrong']);
		}
	}

	public function actionMsg($type='news')
	{

		$infos = [];
		switch ($type) {
			case 'news':
				// $infos = $this->user->news;
				$criteria = new CDbCriteria;
				$criteria->addCondition('uid='.$this->user->id);
				$infos = ArticleExt::model()->normal()->getList($criteria,20);
				$this->render('news',['infos'=>$infos->data,'pager'=>$infos->pagination,'type'=>$type]);
				break;
			case 'comments':
				$comments = $this->user->comments;
				$ids = [];
				if($comments) {
					foreach ($comments as $key => $value) {
						$ids[] = $value->major_id;
					}
					$criteria = new CDbCriteria;
					$criteria->addInCondition('id',$ids);
					$infos = ArticleExt::model()->normal()->getList($criteria,20);
				}
				$this->render('news',['infos'=>$infos->data,'pager'=>$infos->pagination,'type'=>$type]);
				break;
			default:
				# code...
				break;
		}
	}

	public function actionRegis()
	{
		if(Yii::app()->request->getIsPostRequest()) {
			$infos = Yii::app()->request->getPost('UserExt');
			// var_dump($infos);exit;
			// $pwd = $this->cleanXss(Yii::app()->request->getPost('pwd'));
			$model = new UserExt;
			$model->attributes = $infos;
			$model->status = 1;
			// $model->password = $pwd;
			if($model->save())
				$this->redirect('/home/user/login');
		}
		$this->render('regis');
	}

	public function actionAddOne($phone='')
	{
		return SmsExt::addOne($phone);
	}

	public function actionCheckCode($phone='',$code='')
	{
		return SmsExt::checkPhone($phone,$code);
	}
	public function actionCheckName($name='')
	{
		echo UserExt::model()->find("name='$name'")?json_encode(['s'=>'error']):json_encode(['s'=>'success']);
	}

	public function actionCheckPhone($phone='')
	{
		if(Yii::app()->db->createCommand("select id from user where phone='$phone' and deleted=0 ")->queryScalar()){
			echo json_encode(['s'=>'error']);
		} else {
			echo json_encode(['s'=>'success']);
		}
	}

	public function actionFindpwd()
	{
		if(Yii::app()->request->getIsPostRequest()) {
			$phone = $this->cleanXss(Yii::app()->request->getPost('phone',''));
			$pwd = Yii::app()->request->getPost('pwd','');
			if($phone && $pwd) {
				$user = UserExt::model()->find('phone=:phone',[':phone'=>$phone]);
				$user->pwd = md5($pwd);
				if($user->save())
					$this->redirect('/home/user/login');
			}	
		}
		$this->render('findpwd');
	}
}