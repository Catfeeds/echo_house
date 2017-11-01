<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?=$this->pageTitle?></title>
    <meta name="viewport" content="initial-scale=1,maximum-scale=1, minimum-scale=1">
    <link rel="stylesheet" type="text/css" href="<?=$this->subwappath?>/css/reset.css">
    <link rel="stylesheet" type="text/css" href="<?=$this->subwappath?>/css/personal.css">
</head>
<body>
    <?=$content?>
    <script type="text/javascript" src="<?=$this->subwappath?>/js/jquery-3.1.1.min.js"></script>
    <script type="text/javascript" src="<?=$this->wappath?>/js/personal.js"></script>
    <script type="text/javascript" src="<?=$this->wappath?>/js/common.js"></script>
    <script type="text/javascript" src="<?=$this->subwappath?>/js/rem.js"></script>
    <script type="text/javascript">

    $(document).ready(function() {
        <?php if(!($this->staff)):?>
            QFH5.getUserInfo(function(state,data){
                  if(state==1){
                    alert('请前往认证账号');
                    location.href = '<?=Yii::app()->request->getHostInfo()?>/subwap/register.html?phone='+data.phone;
                  }else{
                    QFH5.jumpLogin(function(state,data){
                          //未登陆状态跳登陆会刷新页面，无回调
                          //已登陆状态跳登陆会回调通知已登录
                          //用户取消登陆无回调
                          if(state==2){
                              alert('您已登录');
                          }
                      })
                    //未登录
                    // alert(data.error);//data.error string
                  }
                })
            
        <?php endif;?>
    });
    </script>
</body>
</html>