<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?=$info->title?></title>
    <script src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>js/jquery-1.8.3.min.js"></script>
    <script src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>js/idangerous.swiper.min.js"></script>
    <link rel="stylesheet" href="<?=Yii::app()->theme->baseUrl.'/static/home/'?>css/idangerous.swiper.css">
    <link rel="stylesheet" href="<?=Yii::app()->theme->baseUrl.'/static/home/'?>css/details.css">
    <script src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>js/details.js"></script>
    <script type="text/javascript" src="http://api.map.baidu.com/api?key=&v=1.1&services=true"></script>
</head>
<body>
<?php $hxs = $info->hxs?>
<div class="wrap">
    <!--弹框-->
    <div class="tankuang">
        <div class="tankuang_con">
            <div class="tankuang_content">
                <p class="tankuang_tit">免费电话咨询</p>
                <p class="tankuang_tel">我们的顾问将与您专线联系</p>
                <input type="hidden" id="uhid" name="PlotUser[hid]" value="<?=$info->id?>">
                <input type="text" name="PlotUser[name]" id="uname" placeholder="请输入您姓名" class="tankuang_text">
                <input type="text" name="PlotUser[phone]" id="uphone" placeholder="请输入您的手机号" class="tankuang_text">
                <button onclick="checkword()" class="tankuang_btn">免费咨询</button>
                <p class="tankuang_tel2">直接拨打电话：<?=$user->phone?></p>
                <button class="cha">关闭</button>
            </div>
        </div>
    </div>
    <!--隐藏导航-->
    <div class="hide_nav">
        <div class="hidelist_wrap">
            <ul>
                <!-- <li class="hide_list"><b>最近动态</b></li> -->
                <?php if($hxs):?><li class="hide_list"><a href="#a"><b>户型介绍</b></a></li><?php endif;?>
                <li class="hide_list"><a href="#b"><b>楼盘优势</b></a></li>
                <li class="hide_list"><a href="#c"><b>周边配套</b></a></li>
                <li class="hide_list"><a href="#d"><b>楼盘参数</b></a></li>
                <li class="hide_list"><a href="#e"><b>楼盘点评</b></a></li>
            </ul>
            <p class="hide_tel">
                <span><?=$user->phone?></span>
                <button class="nav_btn open">免费电话咨询</button>
            </p>
        </div>
        <div class="clear"></div>
    </div>
    <!--头部-->
    <div class="logo">
        <div class="logo_nav">
            <span style="font-size: 30px;"><b><?=$info->title?></b></span>
            <p class="hide_tel nav_tel">
                <span><?=$user->phone?></span>
                <button class="nav_btn open">免费电话咨询</button>
            </p>
            <div class="clear"></div>
        </div>
    </div>
    <div class="header_wrap">
        <!--头-->
        <div class="header">
            <!--左边-->
            <div class="left">
                <div class="pc-slide">
                    <div class="view">
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
                                    <img src="<?=ImageTools::fixImage($img,480,400)?>" alt="">
                                </div>
                            <?php }
                            }
                            } ?>
                            </div>
                        </div>
                    </div>
                    <div class="preview">
                        <a class="arrow-left" href="#"></a>
                        <a class="arrow-right" href="#"></a>
                        <div class="swiper-container">
                            <div class="swiper-wrapper">
                            <?php foreach($imgarr as $img) {
                            ?>
                            <div class="swiper-slide">
                                    <img src="<?=ImageTools::fixImage($img,72,66)?>" alt="">
                                </div>
                            <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--右边-->
            <div class="right">
                <span style="font-size: 30px;"><b><?=$info->title?></b></span>
                <span><?php
                $tags = $info->getTags();
                if($tags) {

                    foreach($tags as $tag ) {
                    if(in_array($tag['id'],$info->wylx))
                        echo '['.$tag['name'].']';
                    }
                } 
                
                ?></span>
                <div class="clear"></div>
                <div class="kuang bg1">品牌地产</div>
                <?php if($tags) {
                    foreach($tags as $k=>$t){?>
                        <div class="kuang bg<?=$k+2?>"><?=$t['name']?></div>
                    <?php }
                 } ?>
                <div class="clear"></div>
                <div class="price">
                    <span style="font-size: 24px;float: left;margin-left: 10px"><b><?=$info->price?$info->price:'待定'?></b></span><span
                        style="font-size: 14px;float: left;">元/平米</span>
                    <span style="font-size: 14px;float: left;margin-left: 160px">独家红包3000元</span>
                    <button class="btn open">立即领取</button>
                </div>
                <div>
                    <p class="word">开 发 商：<?=$info->developer?$info->developer:'暂无'?></p>
                    <p class="word">项目地址：<?=$info->address?>
                        <a href="#c"><span style="color: #7c82b9;vertical-align: middle;">
                            <img src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>img/icon.png" style="margin-left: 15px">
                        查看地图
                        </span></a>
                    </p>
                    <div class="word2 ">
                        <span style="float: left">主推户型：</span>
                        <?php if($hxs) {
                            $nowhxs = array_slice($hxs, 0,3);
                            foreach($nowhxs as $hx) {?>
                                <div class="word_model hover2"><?=$hx->bedroom?>室<?=$hx->livingroom?>厅<?=$hx->bathroom?>卫(<?=$hx->size?>)</div>
                        <?php    }?>
                            <div class="word_model1 hover3"><a href="#a" style="font-size: 14px">更多</a></div>
                        
                            
                            
                        <?php } else {
                            echo '暂无';
                        }?>
                        
                    </div>
                    <div class="clear"></div>
                    <div class="line"></div>
                    <p style="font-size: 22px;color: red"><b>预约看房电话：<?=$user->phone?></b></p>
                    <p class="box box1 open">预约看房有红包</p>
                    <p class="box box2 open">免费电话咨询</p>
                    <marquee height="80" direction=up scrollamount="2" behavior="scroll" style="margin-left: 10px">
                    <?php foreach ($userdata as $key => $value) { ?>
                        <p><?=$value['name'].'&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp'.$value['phone'].'&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp'.$value['time']?></p>
                    <?php } ?>
                    </marquee>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <!--顶部导航-->
        <div class="nav">
            <div class="nav_ul">
                <div>
                    <ul>
                        <!-- <li class="head_list"><b>最近动态</b></li> -->
                        <?php if($hxs):?><li class="head_list"><a href="#a"><b>户型介绍</b></a></li><?php endif;?>
                        <li class="head_list"><a href="#b"><b>楼盘优势</b></a></li>
                        <li class="head_list"><a href="#c"><b>周边配套</b></a></li>
                        <li class="head_list"><a href="#d"><b>楼盘参数</b></a></li>
                        <li class="head_list"><a href="#e"><b>楼盘点评</b></a></li>
                    </ul>
                </div>
            </div>
        </div>
        <!--户型介绍-->
        <div class="introduce_wrap">
            <!--户型介绍-->
            <?php if($hxs):?>
            <div class="house_wrap" id="a">
                <p class="title_wrap">
                    <span class="title"><b>户型介绍</b></span>
                    <!-- <span class="comment">查看全部户型></span> -->
                </p>
                <div class="clear"></div><div class="line"></div>
                <?php if(count($hxs)>2) {
                $fishxs = array_slice($hxs,0,2); $sechxs = array_slice($hxs,2,count($hxs)-2);
                } else {
                $fishxs = $hxs;
                $sechxs = [];
                } ?>
                <?php foreach($fishxs as $v) {?>
                    <div class="house_type">
                    <img src="<?=ImageTools::fixImage($v->image,190,140)?>" class="house_pic">
                    <div class="house_r">
                        <p class="house_word"><b><?=$v->bedroom?>室<?=$v->livingroom?>厅<?=$v->bathroom?>卫，约<?=$v->size?></b></p>
                        <p class="parlour">客厅朝南</p>
                        <!-- <p class="parlour parlour1">客厅朝南</p>
                        <p class="parlour parlour1">客厅朝南</p> -->
                    </div>
                    <div class="bespeak">
                        <p style="font-size: 16px;"><b>一房一价</b></p>
                        <p class="bespeak_red open">预约看房有红包</p>
                    </div>

                </div>
                
                <?php }?>
                <?php if($sechxs) {?>
                <?php foreach($sechxs as $v) {?>
                    <div class="house_type more_house">
                    <img src="<?=ImageTools::fixImage($v->image,190,140)?>" class="house_pic">
                    <div class="house_r">
                        <p class="house_word"><b><?=$v->bedroom?>室<?=$v->livingroom?>厅<?=$v->bathroom?>卫，约<?=$v->size?></b></p>
                        <p class="parlour">客厅朝南</p>
                        <!-- <p class="parlour parlour1">客厅朝南</p>
                        <p class="parlour parlour1">客厅朝南</p> -->
                    </div>
                    <div class="bespeak">
                        <p style="font-size: 16px;"><b>一房一价</b></p>
                        <p class="bespeak_red open">预约看房有红包</p>
                    </div>

                </div>
                
                <?php }?>
                <div class="more">
                    <p class="down">更多户型</p>
                    <p class="up">收起</p>
                </div>
               <?php  }?>
                
                <div class="line"></div>
            </div>
            <div class="clear"></div>
        <?php endif;?>
        <div class="clear"></div>
            <!--楼盘优势-->
            <div class="advantage" style="padding-top: 30px" id="b">
                <span class="title"><b>楼盘优势</b></span>
                <div class="clear"></div>
                <div style="width: 860px;margin: 40px auto">
                    <?=$info->peripheral?>
                    <!-- <img src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>img/merit.png"> -->
                </div>
            </div>
            <div class="clear"></div>
            <!--周边配套-->
            <div class="address_wrap" id="c">
                <p class="title_wrap">
                    <span class="title"><b>周边配套</b></span>
                    <span class="comment1"><b>电话指路：<?=$user->phone?></b></span>
                </p>
                <div class="clear"></div>
                <div class="map">
                    <!--地图-->
                    <div id="dituContent" class="concent_ditu"></div>
                    <!--右边-->
                    <div class="nearby">
                        <ul>
                            <li class="map_show" onclick="mapRoundSearch('公交')">公交</li>
                            <li onclick="mapRoundSearch('教育')">教育</li>
                            <li onclick="mapRoundSearch('医疗')">医疗</li>
                            <li onclick="mapRoundSearch('商业')">商业</li>
                        </ul>
                        <div id="list"></div>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
            <!--楼盘参数-->
            <div class="parameter_wrap" id="d">
                <div class="parameter">
                    <span class="title"> <b>楼盘参数</b></span>
                    <div class="clear"></div>
                    <table class="parameter_table">
                        <tr>
                            <td class="parameter_w110">项目名称：</td>
                            <td><?=$info->title?></td>
                            <td class="parameter_w110">开发商：</td>
                            <td><?=$info->developer?></td>
                        </tr>
                        <tr>
                            <td class="parameter_w110">项目地址：</td>
                            <td><?=$info->address?></td>
                            <td class="parameter_w110">物业公司：</td>
                            <td><?=$info->manage_company?></td>
                        </tr>
                        <tr>
                            <td class="parameter_w110">容积率：</td>
                            <td><?=$info->capacity?></td>
                            <td class="parameter_w110">绿化率：</td>
                            <td><?=$info->green?></td>
                        </tr>
                        <tr>
                            <td class="parameter_w110">楼层状况：</td>
                            <td colspan="3">
                               <?=$info->floor_desc?>
                            </td>
                        </tr>
                        <tr>
                            <td class="parameter_w110">交通：</td>
                            <td colspan="3"><?=$info->transit?></td>
                        </tr>
                        <tr>
                            <td class="parameter_w110">项目简介：</td>
                            <td colspan="3">
                                <?=$info->content?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="clear"></div>
            <!--楼盘点评-->
            <div class="review_wrap" id="e">
                <p class="title_wrap">
                    <span class="title"> <b>楼盘点评</b></span>
                    <!-- <span class="comment">查看全部10条评论</span> -->
                </p>
                <div class="clear"></div>
                <div class="review">
                    <span style="float: left">大家都再说：</span>
                    <div class="review_word review_color">发展空间大</div>
                    <div class="review_word review_color1">置业顾问很热情</div>
                    <div class="review_word review_color2">样板间很精致</div>
                    <div class="review_word review_color3">发展空间大</div>
                    <!-- <div class="review_word review_color4">发展空间大</div> -->
                    <div class="clear"></div>
                    <div class="line"></div>
                </div>
                <div>
                    <div class="user">
                        <img src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>img/user.png" alt="">
                        <p>用户 **vt</p>
                    </div>
                    <div class="detailed_comment">
                        <p style="color: #454545"><b>TA已到访售楼部</b></p>
                        <p><?=$info->dp1?></p>
                    </div>
                    <div class="clear"></div>
                    <div class="line"></div>
                    <div>
                        <div class="user">
                            <img src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>img/user.png" alt="">
                            <p>用户 ***明</p>
                        </div>
                        <div class="detailed_comment">
                            <p style="color: #454545"><b>TA已到访售楼部</b></p>
                            <p><?=$info->dp2?></p>
                        </div>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
            <div class="clear"></div>
        </div>
        <!--尾-->
        <div class="foot">
            <div class="flow">
                <p class="foot_title">底价买房 红包返现</p>
                <div class="flow_wrap">
                    <div class="foot_flow foot_flow1">
                        <p><img src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>img/online.png" alt=""></p>
                        <p class="big_word">在线申请</p>
                        <p class="small_word">选中楼盘申请优惠</p>
                    </div>
                    <div class="foot_flow foot_flow2">
                        <img src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>img/youjiantou.png" alt="">
                    </div>
                    <div class="foot_flow foot_flow1">
                        <p><img src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>img/visit.png" alt=""></p>
                        <p class="big_word">到访看房</p>
                        <p class="small_word">专属购房顾问陪同</p>
                    </div>
                    <div class="foot_flow foot_flow2">
                        <img src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>img/youjiantou.png" alt="">
                    </div>
                    <div class="foot_flow foot_flow1">
                        <p><img src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>img/buy.png" alt=""></p>
                        <p class="big_word">优惠购房</p>
                        <p class="small_word">买房优惠多折扣大</p>
                    </div>
                    <div class="foot_flow foot_flow2">
                        <img src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>img/youjiantou.png" alt="">
                    </div>
                    <div class="foot_flow foot_flow1">
                        <p><img src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>img/red-packet.png" alt=""></p>
                        <p class="big_word">红包返现</p>
                        <p class="small_word">享受优惠及红包</p>
                    </div>
                </div>
            </div>
            <div class="tel">
                <input type="text" class="text open" placeholder="留下电话，一小时内会有一对一咨询师与您联系">
                <button class="tel_btn open">免费电话咨询</button>
                <p class="telephone"><b>或者<span style="color: #f83d3d">免费咨询：<?=$user->phone?></span></b></p>
            </div>
        </div>
    </div>
    <!--最后-->
    <div class="last">
        <div class="clear"></div>
        <p><?=$user->companyinfo?('版权所有:'.$user->companyinfo->name):''?> 信息产业部备案/许可证编号：<?=$url->beian?>
            <img src="http://www.hualongxiang.com/images/beian.png"></p>
    </div>
    <!--返回顶部-->
    <div class="back">
        <div class="back_l">
            <div class="back_tel">
                <input type="text" placeholder="请输入手机号码" class="back_text open">
                <p class="open">免费咨询</p>
            </div>
        </div>
        <div class="bank_btn">
            <!-- <div class="back_btn_l">
                <div class="s-erweima">
                    <img src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>img/s-erweima.png" alt="">
                    <span>公众号</span>
                </div>
            </div> -->
            <div class="back_show">
                <img src="<?=Yii::app()->theme->baseUrl.'/static/home/'?>img/erweima.png" alt="">
            </div>
            <div class="back_wtop">
                <div class="back_top"></div>
            </div>
        </div>
    </div>
</div>
</body>
<script>
    // 百度地图API功能
    var map = new BMap.Map("dituContent");
    map.centerAndZoom(new BMap.Point(<?=$info->map_lng?>, <?=$info->map_lat?>), <?=$info->map_zoom?>);
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