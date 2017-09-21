var kf_id = '';
var our_uids = '';
$(document).ready(function() {
    $.get('/api/config/index',function(data) {
        $('.register-attention-text').html(data.data.regis_words);
        our_uids = data.data.our_uids;
        kf_id = data.data.kf_id;
    });
    if(GetQueryString('phone')!=null) {
        $('.nophone').remove();
    }
    $('#form').validate();
    // $('.container-big').css('display','none');
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
    $.get('/api/user/addOne?phone=' + phonenumber, function(data) {
            if (data.status == "error") {
                alert(data.msg);
            } else {
                alert("验证码已发送，请查收");
            }
        });
}
$('.register-register').click(function() {
    if(GetQueryString('phone')!=null) {
        var username = $('#username').val().trim();
        if(!/^[\u0391-\uFFE5]+$/.test(username)) {
            alert("姓名仅限中文");
            
        } else {
            var formtype = $('#form-type').val();
            if (formtype == "") {
                alert("请至少选择一种用户类型");
                
            } else {
                var companycode = $('#companycode').val();
                var type = $('#form-type').val();
                if (type < 3) {
                    $.get('/api/user/checkCompanyCode?code=' + companycode, function(data) {
                        if (data.status == "error") {
                            alert("请输入正确的门店码");
                            
                        } else {
                            regisInfo();
                        }
                    });
                } else {
                    if($('#img-url').val() == '') {
                        alert('请上传证件');
                        
                    } else {
                        regisInfo();
                    }
                }
            }
                
        }
    } else {
        var username = $('#username').val().trim();
        var phonenumber = $('#writephonenumber').val().trim();
        var password = $('#password').val().trim();

        if(!/^[\u0391-\uFFE5]+$/.test(username)) {
            alert("姓名仅限中文");
            return false;
        }
        var formtype = $('#form-type').val();
        if (formtype == "") {
            alert("请至少选择一种用户类型");
        } else {
            var code = $('#code').val();
            var phonenumber = $('#writephonenumber').val();
            $.get('/api/user/checkCode?phone=' + phonenumber + '&code=' + code, function(data) {
                if (data.status == "error") {
                    alert("请输入正确的验证码");
                } else {
                    var companycode = $('#companycode').val();
                    var type = $('#form-type').val();
                    if (type < 3) {
                        $.get('/api/user/checkCompanyCode?code=' + companycode, function(data) {
                            if (data.status == "error") {
                                alert("请输入正确的门店码");
                                return false;
                            } else {
                                regis();
                            }
                        });
                    } else {
                        if($('#img-url').val() == '') {
                            alert('请上传证件');
                        } else 
                            regis();
                    }


                        
                }
            });
        }
    }
        
});

function regis() {
    $.post("/api/user/regis", {
            'UserExt[name]': $('#username').val(),
            'UserExt[phone]': GetQueryString('phone')!=null?GetQueryString('phone'):$('#writephonenumber').val(),
            'UserExt[pwd]': $('#password').val(),
            'UserExt[type]': $('#form-type').val(),
            'UserExt[image]': $('#img-url').val(),
            'UserExt[companycode]': $('#companycode').val(),
        },
        function(data, status) {
            if (data.status == "success") {
                alert("注册成功！");
                location.href = "login.html";
            } else {
                alert(data.msg);
            }
        }
    );
}

function regisInfo() {
    $.post("/api/user/regis", {
            'UserExt[name]': $('#username').val(),
            'UserExt[phone]': GetQueryString('phone')!=null?GetQueryString('phone'):$('#writephonenumber').val(),
            // 'UserExt[pwd]': $('#password').val(),
            'UserExt[type]': $('#form-type').val(),
            'UserExt[image]': $('#img-url').val(),
            'UserExt[companycode]': $('#companycode').val(),
        },
        function(data, status) {
            if (data.status == "success") {
                if($('#form-type').val()<3) {
                    alert("提交成功！欢迎访问经纪圈新房通");
                } else {
                    alert("提交成功！请等待管理员审核");
                    $.get('/api/index/sendNotice?words='+'有独立经纪人注册，请登录后台审核');
                }
                
                location.href = 'http://'+window.location.host;
            } else {
                alert(data.msg);
            }
        }
    );
}

function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(decodeURI(r[2]));
    return null;
}

function callkf() {
     QFH5.jumpTalk(kf_id,'','');
}