<?php
/**
 * 用户登录
 * @author weibaqiu
 * @date   2015-06-10
 */
class BbsController extends ApiController
{
	public $host;
	public function init()
	{
		parent::init();
		$p = substr($_SERVER['SERVER_NAME'], 0, strrpos($_SERVER['SERVER_NAME'],'.'));
		$offset = strrpos($p, '.');
		$this->host = substr($_SERVER['SERVER_NAME'], $offset);
	}

	/**
	 * loginStyle为浮窗登录形式的接口
	 */
	public function actionNewLogin()
	{
		$username = Yii::app()->request->getPost('username');
		$password = Yii::app()->request->getPost('password');

		if(Yii::app()->request->isPostRequest && $username && $password){
			$loginForm = new PptLoginForm('login');
			$loginForm->username = $username;
			$loginForm->password = $password;
			if($loginForm->login()){
				$this->response(true,'登录成功');
				Yii::app()->end();
			}
		}
		$this->response(false,'用户名或密码错误');
	}

	/**
	 * 获取当前登录用户信息
	 * @param  string $jsoncallback jsonp请求的参数
	 */
	public function actionUserInfo($jsoncallback='')
	{
		$user = Yii::app()->uc->user;
		if(!$user->getIsGuest()){
			$info = array(
				'uid' => $user->uid,
				'username' => $user->username,
				'icon' => $user->icon,
			);
			//菜单
			$menu = array();
			$menu[] = ['name'=>'修改手机号', 'url'=>Yii::app()->uc->getUpdatePhonePageUrl()];//修改手机号入口
			if(SM::urmConfig()->commonHeader()['menu']) $_menu = SM::urmConfig()->commonHeader()['menu'];
			if(!empty($_menu)&&is_array($_menu))
			{
				foreach($_menu['name'] as $k=>$v)
				{
					if(empty($v)||empty($_menu['url'][$k])) continue;
					$menu[] = array('name'=>$v, 'url'=>str_replace('{username}', $user->username, $_menu['url'][$k]));
				}
			}
			$data = array(
				'userinfo' => $info,
				'menu' => $menu,
			);

			$this->response(true, $data, 'jsonp', $jsoncallback);
		}
		$this->response(false, []);
	}

	/**
	 * 2016-10-14弃用，采用新用户体系退出
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout(false);
		Yii::app()->user->setFlash('synloginHtml', Yii::app()->passport->synLogout());
		$this->redirect(Yii::app()->request->getUrlReferrer());
	}

	/**
	 * 2016-10-14前早就弃用
	 * 登录论坛，中转请求URM，再将cookie设过来
	 * @return json
	 */
	public function actionLogin()
	{
		$api = Yii::app()->params['urmHost'] . Yii::app()->params['bbsLoginApi'];
		$bbsCode = SM::urmConfig()->bbsCode();	//论坛编码
		$username = Yii::app()->request->getPost('username','');
		$password = Yii::app()->request->getPost('password','');
		if(Yii::app()->request->isPostRequest && !empty($api) && !empty($username) && !empty($password))
		{
			$r = HttpHelper::post($api, array(
				'action' => 'login',
				'username' => $username,
				'password' => $password,
			),array(
				'CLIENT-IP' => Yii::app()->request->getUserHostAddress(),
				'X-FORWARDED-FOR' => Yii::app()->request->getUserHostAddress(),
				'CURLOPT_USERAGENT' => Yii::app()->request->userAgent
			));
			if(isset($r['headers']['Set-Cookie']))
			{
				// var_dump($r['headers']['Set-Cookie']);die;
				$setCookie = $r['headers']['Set-Cookie'];
				if(is_array($setCookie))//可能是数组
				{
					foreach($setCookie as $v)
					{
						header('Set-Cookie: '.$v.'; path=/; domain='.$this->host.';', false);
					}
				}
				else
				{
					header('Set-Cookie: '.$setCookie.'; path=/; domain='.$this->host.';');
				}
			}
			echo $r['content'];
		}
		else
		{
			$this->response(false, '用户名或密码错误');
		}
	}
}
