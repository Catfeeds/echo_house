$(document).ready(function(){
//获取下拉框数据
	$.get("/api/tag/area",function(data){
		if(data.status=='success'){
			for (var i = 0; i < data.data.length; i++) {
			$('.submit-select').append('<option value="'+data.data[i].id+'">'+data.data[i].name+'</option>');			
			}
		}
	});
//validate
	$('#form').validate();
});
//下拉框事件
function selectChange(){
	value=$('.submit-select').val();
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
		$('#type').val('1');
	}
	if($('.radio2').is('.active')){
		$('#type').val('2');
	}
});
//提交按钮
$('.submit-submit').click(function(){
//店长姓名验证
	var name=$('#name').val()	
	if(!/^[\u0391-\uFFE5]+$/.test(name)&&name!='') {
        alert("姓名仅限中文");
        return false;
    }
//手机号验证
	var phonenumber = $('#phone').val();
	var reg = /^1[3|4|5|7|8][0-9]{9}$/;
	if (!reg.test(phonenumber)&&phonenumber!='') {
	    alert('请填写正确的手机号');
	    return false;
	}
//认证材料
	var url =$('#img-url').val();
	if(url==''||url==undefined){
		alert("请上传门店认证材料");
		return false;
	}
	submit();
});
//提交数据
function submit() {
    $.post("/api/plot/SubCompany", {
            'CompanyExt[name]': $('#shopname').val(),
            'CompanyExt[manager]': $('#name').val(),
            'CompanyExt[address]': $('#address').val(),
            'CompanyExt[phone]': $('#phone').val(),
            'CompanyExt[type]': $('#type').val(),
            'CompanyExt[area]': value,
            'CompanyExt[image]': $('#img-url').val(),
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