var list='';
$(document).ready(function(){
//获取下拉框数据
	$.get("/api/tag/area",function(data){
		if(data.status=='success'){
			list=data.data;
			//一级下拉框
			for (var i = 0; i < list.length; i++) {
			$('.submit-select').append('<option value="'+list[i].id+'">'+list[i].name+'</option>');			
			}
			//二级下拉框
			for (var i = 0; i < list[0].childAreas.length; i++) {
			$('.area-select').append('<option value="'+list[0].childAreas[i].id+'">'+list[0].childAreas[i].name+'</option>');			
			}
		}
	});
	

//validate
	$('#form').validate();
});
//下拉框事件、插入二级下拉框
function selectChange(){
	$('.area-select').empty();
	for(var i = 0; i < list.length; i++){
		if($('.submit-select').val()==list[i].id){
			for (var j = 0; j < list[i].childAreas.length; j++) {
			$('.area-select').append('<option value="'+list[i].childAreas[j].id+'">'+list[i].childAreas[j].name+'</option>');			
			}
			break;
		}
	}
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
	QFH5.getUserInfo(function(state,data){
	  if(state==1){
	  	uid = data.uid;
	  	$.post("/api/plot/SubCompany", {
	            'CompanyExt[name]': $('#shopname').val(),
	            'CompanyExt[manager]': $('#name').val(),
	            'CompanyExt[address]': $('#address').val(),
	            'CompanyExt[phone]': $('#phone').val(),
	            'CompanyExt[type]': $('#type').val(),
	            'CompanyExt[area]': $('.area-select').val(),
	            'CompanyExt[image]': $('#img-url').val(),
	            'CompanyExt[adduid]': uid,
	        },
	        function(data, status) {
	            if (data.status == "success") {
	                alert("提交成功！");
	                location.href = "list.html";
	            } else {
	                alert(data.msg);
	            }
	        }
	    );
	  }else{
	    //未登录
	    alert(data.error);//data.error string
	  }
	})
	    
}

function checkName(obj) {
  var name = $(obj).val();
  if(name!='') {
      $.get('/api/plot/checkCompanyName?name='+name,function(data){
        if(data.status=='error') {
          alert(data.msg);
          $(obj).val('');
          $(obj).focus();
        }
      });
  } 
}

