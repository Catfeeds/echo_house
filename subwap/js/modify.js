$().ready(function(){
	$('#form').validate();
});

$('.modify-getmessage').click(function(){
	var phonenumber = $('#writephonenumber').val();
    //手机号验证
    var reg = /^1[3|4|5|7|8][0-9]{9}$/;
    if (!reg.test(phonenumber)) {
        alert('请填写正确的手机号');
    } else {
        $.get('/api/user/checkPhone?phone=' + phonenumber, function(data) {
            if (data.status == "success") {
                alert("请您先注册");
            } else {
            	onemin_clock();
                send_msg(phonenumber);
                $('.register-getmessage').css({"display":"none"});
                $('#clock').css({"display":"block"});
            }
        });
    }
});
//获取验证码倒计时
function onemin_clock() {
     var n=59;
     var clock=setInterval(function(){
        $("#clock").html(n+"s");
        n-=1;
        if (n==0) {
            clearInterval(clock);
            $("#clock").html("");
            $('#clock').css({"display":"none"});
            $('.register-getmessage').css({"display":"block"});
        }
     },1000);

 }
function send_msg(phonenumber) {
    $.get('/api/user/addOne?phone=' + phonenumber+'&type=2', function(data) {
            if (data.status == "success") {
                alert("验证码已发送，请查收");
            } else {
                alert(data.msg);
            }
        });
}
//提交
$('.modify-modify').click(function() {
	var phonenumber = $('#writephonenumber').val();
    var code=$('#code').val();
    $.get('/api/user/checkCode?phone=' + phonenumber + '&code=' + code, function(data) {
        if (data.status == "error") {
            alert("请输入正确的验证码");
            return false;
        } else {
            $.post("/api/user/editPwd", {
                    'phone': $('#writephonenumber').val(),
                    'pwd': $('#password').val()
                },
                function(data, status) {
                    if (data.status == "success") {
                        alert("修改成功！");
                        location.href = "login.html";
                    } else {
                        alert("修改失败！");
                    }
                }
            );

        }
    });
});