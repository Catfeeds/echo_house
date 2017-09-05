$(document).ready(function(){
//获取下拉框数据
	$.get("",function(data){
		if(data.msg=='success'){
			for (var i = 0; i < data.select.length; i++) {
			$('.submit-select').append('<option value="'+data.select[i]+'">'+data.select[i]+'</option>');			
			}
		}
		console.log($('#img-url').val());
	});
//validate
	$('#form').validate();
});
//下拉框事件
function selectChange(){
	var index=$('.submit-select').selectedIndex;
	var value=$('.submit-select')[index];
	console.log(value);
}
//单选框的点击事件
$('.radio1').click(function(){
	$('.radio').removeClass('active');
	$('.radio1').addClass('active');
});
$('.radio2').click(function(){
	$('.radio').removeClass('active');
	$('.radio2').addClass('active');
});
$('.radio').click(function(){
	if($('.radio1').is('.active')){
		$('#type').val('0');
	}
	if($('.radio2').is('.active')){
		$('#type').val('1');
	}
});
//提交按钮
$('.submit-submit').click(function(){
//店长姓名验证
	var name=$('#name').val()
	if(!/^[\u0391-\uFFE5]+$/.test(name)) {
        alert("姓名仅限中文");
        return false;
    }
//手机号验证
	var phonenumber = $('#phone').val();
	var reg = /^1[3|4|5|7|8][0-9]{9}$/;
	if (!reg.test(phonenumber)) {
	    alert('请填写正确的手机号');
	    return false;
	}
});
//提交数据
function regis() {
    $.post("/api/user/submit", {
            'UserExt[shopname]': $('#shopname').val(),
            'UserExt[name]': $('#name').val(),
            'UserExt[address]': $('#address').val(),
            'UserExt[phone]': $('#phone').val(),
            'UserExt[type]': $('#type').val(),
            'UserExt[area]': value,
            'UserExt[image]': $('#img-url').val(),
        },
        function(data, status) {
            if (data.status == "success") {
                alert("提交成功！");
                location.href = "login.html";
            } else {
                alert("提交失败！");
            }
        }
    );
}