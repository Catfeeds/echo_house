<?php $this->pageTitle = '个人中心'?>
    <div class="head">
        <img class="headbg" src="<?=$this->subwappath?>/img/personalheadbg.png">
        <div class="personalhead-big">
            <div class="personalhead-small">
                <img class="personalhead-wu" src="<?=isset($staff->ava)&&$staff->ava?ImageTools::fixImage($staff->ava):$this->subwappath.'/img/personaluserhead.png'?>">
            </div>
        </div>
        <!-- <img class="setup" src="<?=$this->subwappath?>/img/setup.png"> -->
        <?php if(Yii::app()->user->getIsGuest()):?>
        <div class="name">请登录</div>
    <?php else:?>
    <div class="name"><?=$staff->name?></div>
        <div class="status status<?=$staff->status?>"><?=$staff->status==1?'总代':($staff->status==2?'分销':'')?></div>
        <div class="company"><?=$staff->companyinfo?$staff->companyinfo->name:'独立经纪人'?></div>
<?php endif;?>
    </div>
    <div class="functionmodule shadow">
        <div class="functiontag">
            <img class="functiontag-img" src="<?=$this->subwappath?>/img/edit.png">
            <div class="functiontag-text" onclick="tocs()">客户管理</div>
        </div>
        <div class="line"></div>
        <div class="functiontag" onclick="location.href='subwap/mycollection.html'">
            <img class="functiontag-img" src="<?=$this->subwappath?>/img/collection.png">
            <div class="functiontag-text">我的收藏</div>
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
                <li onclick="location.href='subwap/mysubscribe.html'">
                    <img class="panel-img" src="<?=$this->subwappath?>/img/zhuanti.png">
                    <div class="panel-text">我的订阅</div>
                </li>
                <li>
                    <img class="panel-img" src="<?=$this->subwappath?>/img/zhuangxiu.png">
                    <div class="panel-text">发布房源</div>
                </li>
                <li>
                    <img class="panel-img" src="<?=$this->subwappath?>/img/jiancai.png">
                    <div class="panel-text">更换公司</div>
                </li>
                <!-- <li>
                    <img class="panel-img" src="<?=$this->subwappath?>/img/yanfang.png">
                    <div class="panel-text">特惠验房</div>
                </li>
                <li>
                    <img class="panel-img" src="<?=$this->subwappath?>/img/zhuanti.png">
                    <div class="panel-text">精彩专题</div>
                </li>
                <li>
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
    </script>