<?php
/**
 * 与论坛相关的方法
 * @author tivon
 * @version 2015-08-17 13:43:33
 */
class BbsTools
{
	/**
	 * 根据论坛用户id获取用户头像
	 * @param $uid 用户论坛id，多个用户使用逗号分割，如"321,43335,6643,3216"
	 * @return string 头像地址
	 */
	public static function getUserAvatar($uid)
	{
		$result = self::getUserInfo($uid);
		if(empty($result))
			return '';
		else
			return $result['icon'];
	}

	/**
	 * 根据帖子id获取帖子信息
	 * @param  integer $tid 帖子id
	 * @return array 返回帖子信息，失败返回false
	 * tid 帖子id
	 * subject 帖子标题
	 * url 帖子链接
	 * author 帖子作者用户名
	 * authorid 帖子作者用户id
	 * hits 帖子浏览量
	 * replies 帖子回复量
	 * fid 帖子所在板块Id
	 * fname 帖子所在板块名
	 * turl 帖子所在板块链接
	 * content 帖子摘要
	 */
	public static function getThreadById($tid)
	{
		// $api = 'http://www.hualongxiang.com/getPostsInfo.php?tid={tid}';
		$api = '';
		// var_dump($api);die;
		$url =  str_replace("{tid}",$tid,$api);

        $res = array();
        if($tid)
        {
            $content = HttpHelper::get($url);
            $content = mb_convert_encoding($content['content'], 'UTF-8', SM::urmConfig()->bbsCode());
            $data = CJSON::decode($content);
        }
        return empty($data) ? false : $data;
	}

	/**
	 * 获得当前登录的用户信息 | 获得指定uid用户的信息
	 * @param  string $uid  用户uid，批量获取使用逗号分割uid，如"165,123,516,1315,15612,4545"
	 * @param  boolean $getSingle  是否只返回一条记录，当批量获取用户信息时有效
	 * @return array 返回用户信息，失败返回false
	 * [
	 * 	username 用户名
	 * 	uid 用户uid
	 * 	icon 用户头像地址
	 * ]
	 */
	public static function getUserInfo($uid='', $getSingle=true)
	{
		if($uid==='')
		{
			$api = Yii::app()->params['urmHost'].Yii::app()->params['bbsGetuserinfoApi'];
			//模拟当前用户请求接口
			$cookieValue = '';
			foreach($_COOKIE as $k=>$v)
			{
				$cookieValue .= $k.'='.urlencode($v).'; ';
			}

			$r = HttpHelper::get($api, array(
				'CURLOPT_COOKIE' => $cookieValue,
				'CURLOPT_USERAGENT' => Yii::app()->request->userAgent
			));

			if(!empty($r['code'])&&!empty($r['content']))
			{
				$userinfo = CJSON::decode($r['content']);
				if(empty($userinfo))
					return false;
				else
					return $userinfo = $userinfo['msg']['userinfo'];
			}
		}
		elseif($uid>0)
		{
			$api = '';
	        if(empty($api)) return false;
	        $result = HttpHelper::post($api, array(
	            'action' => 'userinfoes',
	            'uids' => $uid,
	        ));
	        if(!empty($result['content']) && is_array(CJSON::decode($result['content'])))
	        {
	            $arr = CJSON::decode($result['content']);
	            if($getSingle)
	            	return array(
	            		'uid' => $arr['data'][0]['uid'],
	            		'username' => $arr['data'][0]['username'],
	            		'icon' => $arr['data'][0]['icon'],
	            	);
	            else
		            return $arr['data'];
	        }
		}

		return false;
	}
}
?>
