$('.login-login').click(function() {
    var username = $('#phonenumber').val();
    //手机号验证
    var reg = /^1[3|4|5|7|8][0-9]{9}$/;
    if (!reg.test(username)) {
        alert('请填写正确的手机号');
    } else {
        $.post("/api/user/login", {
                'name': username,
                'pwd': $('#password').val(),
                'rememberMe': $('#remember-or-not').val(),
            },
            function(data, status) {
                if (data.status == "success") {
                    alert("登陆成功！");
                    location.href = "list.html";
                } else {
                    alert(data.msg);
                }
            }
        );
    }
});
$(document).ready(function() {
    var loginimg = '';
    $.get('/api/config/index',function(data){
        if(data.data.login_img != undefined) {
            loginimg = data.data.login_img;
        }
        if(loginimg!='') {
            $('.echo-house-img').attr('src',loginimg);
        } else {
            $('.echo-house-img').attr('src','./img/echo-house.png');
        }
    });
});
