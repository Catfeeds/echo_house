<?php $this->pageTitle = '个人中心'?>
    <div class="head">
        <img class="headbg" src="<?=$this->subwappath?>/img/personalheadbg.png">
        <div class="personalhead-big">
            <div class="personalhead-small">
                <img class="personalhead-wu" src="<?=isset($staff->ava)&&$staff->ava?ImageTools::fixImage($staff->ava):$this->subwappath.'/img/personaluserhead.png'?>">

            </div>
        </div>
        <?php if($this->staff&& $this->staff->vip_expire>time()):?>
        <img src="<?=$this->subwappath?>/img/vip.png" alt="" class="vipuser">
    <?php endif;?>
        <!-- <img class="setup" src="<?=$this->subwappath?>/img/setup.png"> -->
        <?php if(Yii::app()->user->getIsGuest()):?>
        <div class="name">请登录</div>
    <?php else:?>
    <div class="name"><?=$staff->name?></div>
        <div class="status status<?=$staff->type?>"><?=$staff->type==1?'总代':($staff->type==2?'分销':'独立经纪人')?></div>
        <div class="company"><?=$staff->companyinfo?($staff->companyinfo->name.'('.$staff->companyinfo->code.')'):''?></div>
<?php endif;?>
    </div>
    <div class="functionmodule shadow">
        <div class="functiontag" onclick="tocs()">
            <img class="functiontag-img" src="<?=$this->subwappath?>/img/edit.png">
            <div class="functiontag-text">客户管理</div>
        </div>
        <div class="line"></div>
        <div class="functiontag" onclick="<?php if($this->staff):?>location.href='subwap/mycollection.html'<?php else:?>alert('请先登录')<?php endif;?>">
            <img class="functiontag-img" src="<?=$this->subwappath?>/img/collection.png">
            <div class="functiontag-text">我的关注</div>
        </div>
        <div class="line"></div>
        <div class="functiontag">
            <img class="functiontag-img" src="<?=$this->subwappath?>/img/function.png">
            <div class="functiontag-text">更多功能</div>
            <img id="upandown" class="up" src="<?=$this->subwappath?>/img/up.png">
        </div>
        <div class="panel">
            <div class="line"></div>
            <ul class="iconcontainer clearfloat">
                <li onclick="vip()">
                    <img class="panel-img" src="<?=$this->subwappath?>/img/vipmanage.png">
                    <div class="panel-text">会员服务</div>
                </li>
                <li onclick="location.href='subwap/mysubscribe.html'">
                    <img class="panel-img" src="<?=$this->subwappath?>/img/zhuanti.png">
                    <div class="panel-text">我的订阅</div>
                </li>
                <li onclick="checkfb()">
                    <img class="panel-img" src="<?=$this->subwappath?>/img/zhuangxiu.png">
                    <div class="panel-text">发布房源</div>
                </li>
                <li onclick="join()">
                    <img class="panel-img" src="<?=$this->subwappath?>/img/jiancai.png">
                    <div class="panel-text"><?=$staff&&$staff->cid?'更换':'加入'?>公司</div>
                </li>
                <li onclick="sale()">
                    <img class="panel-img" src="<?=$this->subwappath?>/img/placesale.png">
                    <div class="panel-text">案场销售</div>
                </li>
                <li onclick="assist()">
                    <img class="panel-img" src="<?=$this->subwappath?>/img/assist3.png">
                    <div class="panel-text">案场助理</div>
                </li>
                
                <!-- <li>
                    <img class="panel-img" src="<?=$this->subwappath?>/img/diary.png">
                    <div class="panel-text">装修日记</div>
                </li>
                <li>
                    <img class="panel-img" src="<?=$this->subwappath?>/img/gonglue.png">
                    <div class="panel-text">装修攻略</div>
                </li>
                <li>
                    <img class="panel-img" src="<?=$this->subwappath?>/img/zhuangxiu.png">
                    <div class="panel-text">装修公司</div>
                </li>
                <li>
                    <img class="panel-img" src="<?=$this->subwappath?>/img/jiancai.png">
                    <div class="panel-text">建材公司</div>
                </li> -->
            </ul>   
        </div>
    </div>
    <div class="service shadow">
        <a class="functiontag" href="tel:<?=SiteExt::getAttr('qjpz','site_phone')?>">
            <img class="functiontag-img" src="<?=$this->subwappath?>/img/service.png">
            <div class="functiontag-text">联系客服</div>
        </a>
    </div>
    <script>
        function tocs() {
            <?php if($this->staff):?>
                <?php if($this->staff->type==1):?>
                location.href = 'subwap/customerlist.html';
                <?php else:?>
                location.href = 'subwap/userlist.html';
                <?php endif;?>
            <?php endif;?>
        }
        function checkfb() {
           <?php if($this->staff):?>
                <?php if($this->staff->type==1):?>
                location.href = 'subwap/personallist.html';
                <?php else:?>
                alert('只有总代身份才能发布房源哦~');
                <?php endif;?>
            <?php endif;?>
        }
        function join() {
           <?php if($this->staff):?>
                <?php if(!$this->staff->cid):?>
                location.href = 'subwap/joincompany.html';
                <?php else:?>
                var r=confirm("您确定要离开<?=$this->staff->companyinfo->name?>吗？");
                if(r==true){
                    $.get("/api/plot/leave?id=<?=$this->staff->id?>",function(data) {
                    if(data.status=='success') {
                        alert('解绑成功');
                        location.href = 'subwap/joincompany.html';
                    }
                });
                }               
                <?php endif;?>
            <?php endif;?>
        }
        function sale() {
            <?php if($this->staff):?>
            location.href = 'subwap/salelist.html';
            <?php else:?>
            alert('请登录后操作');
            <?php endif;?>
        }
        function assist() {
            <?php if($this->staff):?>
            location.href = 'subwap/customerlist.html';
            <?php else:?>
            alert('请登录后操作');
            <?php endif;?>
        }
        function vip() {
            <?php if($this->staff):?>
            location.href = 'subwap/duijierennew.html';
            <?php else:?>
            alert('请登录后操作');
            <?php endif;?>
        }
    </script>