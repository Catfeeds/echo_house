<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
    <meta name="format-detection" content="telephone=no,email=no,address=no">
    <title><?=$info->title?></title>
    <script src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>js/jquery-1.8.3.min.js"></script>
    <script src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>js/idangerous.swiper.min.js"></script>
    <link rel="stylesheet" href="<?=Yii::app()->theme->baseUrl.'/static/home/'?>css/details2.css">
    <link rel="stylesheet" href="<?=Yii::app()->theme->baseUrl.'/static/home/'?>css/common.css">
    <link rel="stylesheet" href="<?=Yii::app()->theme->baseUrl.'/static/home/'?>css/swiper.min.css">
    <script src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>js/details2.js"></script>
    <script src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>js/750rem.js"></script>
    <script src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>js/swiper.min.js"></script>
    <script type="text/javascript" src="http://api.map.baidu.com/api?key=&v=1.1&services=true"></script>
</head>
<body>
<div class="wrap">
    <div class="header_wrap">
        <!--领红包-->
        <div class="tankuang">
            <div class="tankuang_con">
                <div class="tankuang_content">
                    <div class="big_bg">
                        <img src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>img/details2/hb-gold.png" alt="">
                        <p class="tankuang_tit">成功抢到购房红包</p>
                        <p class="tankuang_tel">红包凭手机号码领取</p>
                    </div>
                    <div class="big_kuang">
                        <p class="jia">
                            <span style="font-size: 0.16rem">￥</span><b>2000</b><span style="font-size: 0.16rem"> 购房红包</span>
                        </p>
                        <p class="kuang_zi">购买本楼盘任意户型，签约后返还现金</p>
                    </div>
                    <input type="hidden" id="uhid" name="PlotUser[hid]" value="<?=$info->id?>">
                    <input type="text" name="PlotUser[name]" id="uname" placeholder="请输入您姓名" class="tankuang_text">
                    <input type="text" name="PlotUser[phone]" id="uphone" placeholder="请输入您的手机号" class="tankuang_text">
                    <button onclick="checkword()" class="tankuang_btn">领取</button>
                    <button class="cha">关闭</button>
                </div>
            </div>
        </div>
        <!--大图-->
        <div class="header">
            <div class="swiper-container">
                <div class="swiper-wrapper">
                <?php $imgs = $info->images; if($imgs) {
                            foreach($imgs as $im) {
                                $imgarr[] = $im['url'];
                            }
                            if($imgarr) {
                            foreach($imgarr as $img) {
                            ?>
                            <div class="swiper-slide">
                                    <img src="<?=ImageTools::fixImage($img,375,245)?>" alt="">
                                </div>
                            <?php }
                            }
                            } ?>
                </div>
                <div class="header_back"></div>
                <p class="header_tit">君悦·金鸾湾-北京站</p>
                <p class="header_tit2">效果图</p>
                <div class="clear"></div>
            </div>
        </div>
        <!--电话-->
        <div class="sales">
            <p class="title"><?=$info->title?><span>|</span><span><?php
                $tags = $info->getTags();
                if($tags) {

                    foreach($tags as $tag ) {
                    if(in_array($tag['id'],$info->wylx))
                        echo $tag['name'];
                    }
                } 
                
                ?> · 在售</span></p>
            <p class="jaiqian"><?=$info->price?><span>元/㎡</span></p>
            <button class="sales_btn open">开盘、变价提醒</button>
            <div class="line"></div>
            <p class="sales_tel">
                <img src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>img/details2/tel.png" alt="">
                <span>预约看房电话：<?=$user->phone?></span>
            </p>
            <div class="line"></div>
            <p class="dizhi">
                <img src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>img/details2/dizhi.png" alt="">
                <span><?=$info->address?></span>
                <button class="dizhi_btn">售楼部</button>
            </p>
        </div>
        <!--红包-->
        <div class="mod-box">
            <a class="coupon-a hongbao " href="javascript:;" id="qiangyouhui">
                <div class="coupon-left">
                    <div class="money"><strong>2000</strong>元</div>
                    <p>专属特惠红包</p>
                </div>
                <div class="coupon-right">
                    <div class="triangle-border-right">
                        <em class="circular0"></em><em class="circular1"></em>
                        <i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i>
                    </div>
                    <div class="change-block">
                        <div class="progress-bar-block">
                            <div class="progress-bar"><span style="width: 78%"></span></div>
                        </div>
                        <span class="progress-text">已抢&nbsp;79%</span>
                        <span class="coupon-btn open">立即领取</span>
                    </div>
                </div>
            </a>
            <div class="clear"></div>
            <div class="pmd">
                <img src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>img/details2/hongbao.png" alt="">
                <marquee behavior="scroll" direction=left   behavior="alternate"  direction="left" loop="-1">
                <?php foreach ($userdata as $key => $value) { ?>
                        <span>恭喜<?=$value['name'].'('.$value['phone'].')'?>成功领取红包</span>
                    <?php } ?>
                </marquee>
            </div>
            <div class="clear"></div>
        </div>
        <!--主力户型-->
        <?php if($hxs = $info->hxs):?>
        <div class="house">
            <p class="tit">主力户型</p>
            <div class="line"></div>
            <div class=" swiper-container1">
                <div class="swiper-wrapper">
                <?php foreach($hxs as $hx){?>
                 <div class="swiper-slide">
                        <img src="<?=ImageTools::fixImage($hx->image,100,76)?>" alt="" class="house_pic">
                        <div class="hongbao_zi">红包</div>
                        <p><?=$hx->size?>㎡ | <?=$hx->bedroom?>室<?=$hx->livingroom?>厅<?=$hx->bathroom?>卫</p></div>
                        <?php }?>
                </div>
            </div>
        </div>
    <?php endif;?>
        <!--楼盘详情-->
        <div class="details">
            <p class="tit">楼盘详情</p>
            <div class="line"></div>
            <div style="font-size: 14px"><?=$info->peripheral?></div>

        </div>
        <!--楼盘信息-->
        <div class="message">
            <p class="tit">楼盘信息</p>
            <div class="line"></div>
            <div>
                <p class="mes_word">
                    <span class="mes_rl">项目名称</span>
                    <?=$info->title?></p>
                <p class="mes_word">
                    <span class="mes_rl">开发商</span>
                    <?=$info->developer?></p>
                <p class="mes_word">
                    <span class="mes_rl">物业公司</span>
                    <?=$info->manage_company?></p>
            </div>
            <div class="more_mes">
                <p class="mes_word">
                    <span class="mes_rl">容积率</span>
                    <?=$info->capacity?></p>
                <p class="mes_word">
                    <span class="mes_rl">绿化率</span>
                    <?=$info->green?></p>
                <p class="mes_word">
                    <span class="mes_rl">楼层状况</span>
                    <?=$info->floor_desc?></p>
                <p class="mes_word">
                    <span class="mes_rl">交通</span>
                    <?=$info->transit?></p>
                <p class="mes_word">
                    <span class="mes_rl">项目简介</span>
                    <?=$info->content?></p>
            </div>
            <div class="more">
                <p class="down">更多信息</p>
                <p class="up">收起</p>
            </div>
        </div>
        <!--周边配套-->
        <div class="map">
            <p class="tit">周边配套</p>
            <div class="line"></div>
            <div class="ditu">
                <div id="dituContent" class="concent_ditu"></div>
                <div class="map_list">
                    <ul>
                        <li onclick="mapRoundSearch('交通')"><img src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>img/details2/jiaotong.png" alt="">
                            <p>交通</p></li>
                        <li onclick="mapRoundSearch('商业')"><img src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>img/details2/shangye.png" alt="">
                            <p>商业</p></li>
                        <li onclick="mapRoundSearch('学校')"><img src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>img/details2/xuexiao.png" alt="">
                            <p>学校</p></li>
                        <li onclick="mapRoundSearch('医疗')"><img src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>img/details2/yiliao.png" alt="">
                            <p>医疗</p></li>
                    </ul>
                </div>
            </div>

        </div>
        <!--楼盘点评-->
        <div class="comment">
            <p class="tit">楼盘点评</p>
            <div class="line"></div>
            <div class="comment_user">
                <img src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>img/details2/user.png" alt="" class="user_pic">
                <div class="user">
                    <p>用户 **vt</p>
                    <p>TA已到访售楼部</p>
                </div>
                <div class="clear"></div>
            </div>
            <p class="comment_word"><?=$info->dp1?>
            </p>

            <div class="line"></div>
            <div class="comment_user">
                <img src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>img/details2/user.png" alt="" class="user_pic">
                <div class="user">
                    <p>用户 ***明</p>
                    <p>TA已到访售楼部</p>
                </div>
                <div class="clear"></div>
            </div>
            <p class="comment_word"><?=$info->dp2?>
            </p>
        </div>
        <!--优惠流程-->
        <!-- <div class="youhui">
            <p class="tit">优惠流程</p>
            <div class="line"></div>
            <div class="flow_wrap">
                <div class="foot_flow foot_flow1">
                    <p><img src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>img/details2/zaixian.png" alt=""></p>
                    <p class="big_word">在线申请</p>
                </div>
                <div class="foot_flow foot_flow2">
                    <img src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>img/details2/youjiantou.png" alt="">
                </div>
                <div class="foot_flow foot_flow1">
                    <p><img src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>img/details2/xinxi.png" alt=""></p>
                    <p class="big_word">短信确认</p>
                </div>
                <div class="foot_flow foot_flow2">
                    <img src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>img/details2/youjiantou.png" alt="">
                </div>
                <div class="foot_flow foot_flow1">
                    <p><img src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>img/details2/kanfang.png" alt=""></p>
                    <p class="big_word">到访看房</p>
                </div>
                <div class="foot_flow foot_flow2">
                    <img src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>img/details2/youjiantou.png" alt="">
                </div>
                <div class="foot_flow foot_flow1">
                    <p><img src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>img/details2/youhui.png" alt=""></p>
                    <p class="big_word">尊享优惠</p>
                </div>
            </div>
        </div> -->
        <!--新房通特惠新房服务-->
        <div class="serve">
            <p class="tit">新房通特惠新房服务</p>
            <div class="line"></div>
            <div class="serve_baopei">
                <div class="serve_pic">
                    <img src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>img/details2/baopei.png" alt="">
                </div>
                <div class="serve_word">
                    <p class="serve_title">买贵包赔</p>
                    <p class="serve_text">同一时间买贵了，可向您的买家顾问投诉，确认属实可获赔偿。</p>
                </div>
            </div>
            <div class="clear"></div>
            <div class="serve_baopei">
                <div class="serve_pic">
                    <img src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>img/details2/guwen.png" alt="">
                </div>
                <div class="serve_word">
                    <p class="serve_title">专属顾问</p>
                    <p class="serve_text">全城专车免费上门接送看房，买家顾问全程陪同看房。</p>
                </div>
            </div>
            <div class="clear"></div>
            <div class="serve_baopei">
                <div class="serve_pic">
                    <img src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>img/details2/hongbao.png" alt="">
                </div>
                <div class="serve_word">
                    <p class="serve_title">购房红包</p>
                    <p class="serve_text">购买带红包标识的新房，除享开发商常规优惠外，还可额外享受新房通独家红包
                        （此优惠仅限新房通买家顾问带看并成交）。</p>
                </div>
            </div>
        </div>
        <div style="height: 1.6rem;clear: both;overflow: hidden;"></div>
        <!--隐藏尾部-->
        <div class="foot">
            <div class="foot_bot">
                <p class="foot_tel">免费咨询：<?=$user->phone?></p>
                <div class="foot_btn">
                    <img src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>img/details2/qiang.png" alt="">
                    <p>抢优惠</p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<script>
    // 百度地图API功能
    var map = new BMap.Map("dituContent");
    map.centerAndZoom(new BMap.Point(<?=$info->map_lng?>, <?=$info->map_lat?>), 14);
    function checkword() {
        if($('#uname').val()=='') {
            alert('请输入姓名');
            return false;
        }else if($('#uphone').val()=='') {
            alert('请输入手机号');
            return false;
        }else {
            $.post('/home/plot/addUser',{'PlotUser[hid]':$('#uhid').val(),'PlotUser[name]':$('#uname').val(),'PlotUser[phone]':$('#uphone').val()},function(data) {
                // console.log(data);
                data = JSON.parse(data);
                alert(data.msg);
            });
        }
            
        // alert('提交成功！');
        // return true;
    }
</script>
</html>