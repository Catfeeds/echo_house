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
                    location.href = "http://www.baidu.com";
                } else {
                    alert(data.msg);
                }
            }
        );
    }
});