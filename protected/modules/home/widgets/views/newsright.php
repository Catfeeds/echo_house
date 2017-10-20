<style>
    .tag1 a:hover{
        color: #00b7ee !important;
        border: 1px #00b7ee solid !important;
    }
</style>
<?php $nopic = SiteExt::getAttr('qjpz','newsImg')?>
<div class="widget d_postlist">
                <div class="title">
                    <sapn class="title_span" style="padding-left: 0;padding-right: 0"><strong  style="font-weight:normal !important;background-color:#00b7ee;color: white;padding: 4px 15px;">热门文章</strong></span></div>
                <ul>
                <?php if($news) foreach ($news as $key => $value) {?>
                    <li><a style="    height: 90px;
    padding-bottom: 0;" href="<?=$this->owner->createUrl('/home/news/info',['id'=>$value->id])?>" title="<?=$value->title?>"><span class="thumbnail"  style="border: none"><img src="<?=ImageTools::fixImage($value->image)?>" style="width: 93px;height: 64px" /></span><span class="text"><?=Tools::u8_title_substr($value->title,40)?></span><span class="muted"><?=date('Y-m-d',$value->updated)?></span></a></li>
                <?php } ?>
                   
                </ul>
            </div>
            <div class="widget d_tag">
                <div class="title">
                    <sapn class="title_span" style="padding-left: 0;padding-right: 0"><strong  style="font-weight:normal !important;background-color:#00b7ee;color: white;padding: 4px 15px;">热门图库</strong></span></div>
                <div class="d_tags" style="width: 88%">
                <?php if($albums) foreach ($albums as $key => $value) { ?>
                    <a title="<?=$value->title?>" href="<?=$this->owner->createUrl('/home/album/info',['id'=>$value->id])?>" style="padding: 0;background-color:white;height: 82px;width: 48%;opacity: 1">
                        <img src="<?=ImageTools::fixImage($value->album?$value->album[0]['url']:$nopic,127,80)?>"  style="width: 127px;height: 80px" >
                    </a>
               <?php  } ?>
                
                </div>
            </div>
            <div class="widget d_tag">
                <div class="title">
                    <sapn class="title_span" style="padding-left: 0;padding-right: 0"><strong  style="font-weight:normal !important;background-color:#00b7ee;color: white;padding: 4px 15px;">热门搜索</strong></span></div>
                <div class="d_tags tag1"  style="width: 86%">
                <?php if($tags) foreach ($tags as $key => $value) {?>
                    <a style="width: auto !important;border: 1px #999 solid;background: white;color: #080808;height: 22px;    border-radius: 3px;" href="<?=$this->owner->createUrl('/home/news/list',['tag'=>Pinyin::get($value['name'])])?>"><?=$value['name']?></a>
                <?php } ?> 
                <a style="width: auto !important;border: 1px #999 solid;background: white;color: #080808;height: 22px;    border-radius: 3px;"  href="<?=$this->owner->createUrl('/home/news/alltag')?>" >查看更多</a>
                </div>
            </div>