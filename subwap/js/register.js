$().ready(function(){
    $('#form').validate();
});

$('.register-getmessage').click(function() {
    var phonenumber = $('#writephonenumber').val();
    //手机号验证
    var reg = /^1[3|4|5|7|8][0-9]{9}$/;
    if (!reg.test(phonenumber)) {
        alert('请填写正确的手机号');
    } else {
        $.get('/api/user/checkPhone?phone=' + phonenumber, function(data) {
            if (data.status == "error") {
                alert(data.msg);
            } else {
                send_msg(phonenumber);
            }
        });
    }
});

$('.register-register').click(function() {
    // var username = $('#username').val();
    // var phonenumber = $('#writephonenumber').val();
    // var password = $('#password').val();
    var formtype=$('#form-type').val();
    if (formtype=="") {
        alert("请至少选择一种用户类型");
    }else{
        var code = $('#code').val();
        $.get('/api/user/checkCode?phone=' + phonenumber +'&code=' + code, function(data) {
                if (data.status == "error") {
                    alert("请输入正确的验证码");
                } else {
                    var companycode = $('#companycode').val();
                    $.get('/api/user/checkCode?checkCompanyCode=' + companycode, function(data) {
                            if (data.status == "error") {
                                alert("请输入正确的门店码");
                            } else {
                                $('#form').submit();
                            }
                        });  
                }
            });    
        }
});