<?php
/**
 * @SWG\Info(title="球布斯数据接口", version="0.1")
 */
class DataController extends ApiController
{
	/**
     * @SWG\Get(path="联赛接口数据结构",
     *   tags={"联赛"},
     *   summary="联赛接口数据结构",
     *   description="联赛接口数据结构",
     *   operationId="placeOrder",
     *   produces={"application/xml", "application/json"},
	 *   @SWG\Parameter(name="name",in="path",description="联赛简称",required=true,type="string"),
     *   @SWG\Parameter(name="full_name",in="path",description="联赛全名",required=true,type="string"),
     *   @SWG\Parameter(name="eng",in="path",description="英文名",required=true,type="string"),
     *   @SWG\Parameter(name="country",in="path",description="国家",required=true,type="string"),
     *   @SWG\Parameter(name="pinyin",in="path",description="拼音",required=true,type="string"),
     *   @SWG\Parameter(name="descpt",in="path",description="简介",required=true,type="string"),
     *   @SWG\Parameter(name="image",in="path",description="图片地址",required=true,type="string"),

     *   @SWG\Response(response=400, description="Invalid Order")
     * )
     */
	public function actionLeague()
	{
		$page = 0;
		$url = SiteExt::getAttr('qjpz','leagueApi');
		do{
			$res = HttpHelper::get($url,['page'=>$page]);
			if(isset($res['content']) && $data = json_decode($res['content'],true)) {
				foreach ($data as $key => $value) {
					$name = $value['name'];
					if(Yii::app()->db->createCommand("select id from league where name='$name'")->queryScalar()) {
						continue;
					} 
					$league = new LeagueExt;
					$league->old_id = $value['no'];
					foreach (['id','name','country'] as $v) {
						$league->$v = $value[$v];
					}
					// $league->attributes = $value;
					$league->save();
				}
			}else {
				$data = [];
			}
			$page++;
		}while($data);
		
		echo '成功！';
	}

	public function actionNews()
	{
		$page = 0;
		$url = SiteExt::getAttr('qjpz','leagueApi');
		do{
			$res = HttpHelper::get($url,['page'=>$page]);
			if(isset($res['content']) && $data = json_decode($res['content'],true)) {
				foreach ($data as $key => $value) {
					$name = $value['name'];
					if(Yii::app()->db->createCommand("select id from league where name='$name'")->queryScalar()) {
						continue;
					} 
					$league = new LeagueExt;
					$league->old_id = $value['no'];
					foreach (['id','name','country'] as $v) {
						$league->$v = $value[$v];
					}
					// $league->attributes = $value;
					$league->save();
				}
			}else {
				$data = [];
			}
			$page++;
		}while($data);
		
		echo '成功！';
	}

}