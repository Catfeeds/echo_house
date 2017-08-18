<?php

/**
 * User: fanqi
 * Date: 2016/9/23
 * Time: 13:50
 * 中介用户首页
 */
class IndexAction extends CAction
{
    public function run()
    {
        if($this->controller->staff->id_expire<time()) {
            $this->controller->returnError('您的账号已到期！');
            // $this->controller->redirect('/resoldhome');
        }
        $uid = $this->controller->staff->uid;
        // $uid = Yii::app()->uc->user->uid;
        $hurryTime = time()-SM::resoldConfig()->resoldHurryTime()*3600;
        $staff = $resoldStaff = ResoldStaffExt::model()->findStaffByUid($uid,['select'=>'id,image,name,uid,sid,is_manager']);
        if($staff->staffPackage)
        {
            $package = $staff->staffPackage->package;

            //如果上架的大于能上架的则下架掉多出来的 (组长说先干二手房 orz)
            if(($num = $staff->getCanSaleNum()) < 0)
            {
                $downNum = 0 - $num;
                Yii::app()->db->createCommand("UPDATE resold_esf SET sale_status=2 WHERE sale_status=1 AND uid=$uid ORDER BY refresh_time ASC LIMIT $downNum")->execute();
                if($downNum = 0 - $staff->getCanSaleNum() > 0) {
                    Yii::app()->db->createCommand("UPDATE resold_zf SET sale_status=2 WHERE sale_status=1 AND uid=$uid ORDER BY refresh_time ASC LIMIT $downNum")->execute();
                }
            }

            //如果加急的大于能加急的则取消加急
            if($staff->getIsExpire())
            {
                Yii::app()->db->createCommand("UPDATE resold_esf SET hurry=0 WHERE hurry>=$hurryTime AND uid=$uid ORDER BY hurry ASC")->execute();
                Yii::app()->db->createCommand("UPDATE resold_zf SET hurry=0 WHERE hurry>=$hurryTime AND uid=$uid ORDER BY hurry ASC")->execute();
            }

            //如果预约的大于能预约的则取消预约
            if($staff->getIsExpire())
            {
                Yii::app()->db->createCommand("DELETE FROM resold_appoint WHERE uid=$uid ORDER BY status ASC")->execute();
            }
            //如果更换套餐

        }
        else
            $package = [];
        //查找出来的数据除规定的字段以外其他会显示为null，这里需要清理一下
        $vipUser = array_filter($resoldStaff->attributes);
        //二手房数量
        $salingEsfNum = $resoldStaff->getSalingInfoNum(1,1);
        $salingZfNum = $resoldStaff->getSalingInfoNum(2,1);
        $saledEsfNum = $resoldStaff->getSalingInfoNum(1,2);
        $saledZfNum = $resoldStaff->getSalingInfoNum(2,2);

        $vipUser['count_esf'] = $salingEsfNum + $saledEsfNum;

        //租房数量
        // $onsaleZfs = $resoldStaff->onsaleZfs;
        // $outsaleZfs = $resoldStaff->outsaleZfs;
        $vipUser['count_zf'] = $salingZfNum + $saledZfNum;
        $vipUser['saling'] = $salingEsfNum + $salingZfNum;
        $vipUser['appointing'] = count($resoldStaff->appointEsfs);
        //套餐上架量
        $content = CJSON::decode($package->content);
        $vipUser['package'] = $content;
        $vipUser['is_manager'] = $resoldStaff->is_manager;
        $vipUser['image'] = isset($vipUser['image'])&&$vipUser['image']?$vipUser['image']:SM::resoldImageConfig()->resoldStaffNoPic();
        $vipUser['image'] = ImageTools::fixImage($vipUser['image'],60,60,0);
        $this->getController()->frame['data']=[
            'vipUser'=>$vipUser
        ];
    }
}