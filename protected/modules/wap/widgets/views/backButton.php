<?php
//2016-07-15前端说改成go-1的形式
if(!$isIcon):
   if(Yii::app()->request->getUrlReferrer() && strpos(Yii::app()->request->getUrlReferrer(), Yii::app()->request->getHostInfo())!==false):
    ?>
    <a href="javascript:history.back()" class="back iconfont nojs">&#x2568;</a>
   <?php endif; ?>
<?php else:
   if(Yii::app()->request->getUrlReferrer() && strpos(Yii::app()->request->getUrlReferrer(), Yii::app()->request->getHostInfo())!==false):
   ?>
   <a href="javascript:history.back()" class="back iconfont nojs"><i class="icon-backs"></i></a>
   <?php endif; ?>
<?php endif?>
